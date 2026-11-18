<?php

namespace App\Libraries;

use App\Models\AddressModel;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\OrderStatusLogModel;
use App\Models\PaymentModel;
use App\Models\ShipmentModel;
use CodeIgniter\Database\BaseConnection;

/**
 * Seluruh logika transaksi pesanan berada di sini (bukan di controller):
 *  - checkout (validasi stok, snapshot harga, hitung ulang total, kurangi stok)
 *  - pembayaran (upload bukti, verifikasi, penolakan)
 *  - pengiriman, penyelesaian, pembatalan (+ pengembalian stok)
 *
 * KEBIJAKAN STOK (BR-009): stok dikurangi saat pesanan DIBUAT/dikonfirmasi di checkout
 * (setelah validasi stok) di dalam database transaction. Jika pesanan dibatalkan,
 * stok dikembalikan.
 *
 * Semua method yang mengubah data berjalan dalam database transaction (BR-010) dan
 * melempar OrderException untuk kesalahan bisnis.
 */
class OrderService
{
    protected BaseConnection $db;
    protected OrderModel $orders;
    protected OrderItemModel $items;
    protected PaymentModel $payments;
    protected ShipmentModel $shipments;
    protected OrderStatusLogModel $logs;
    protected AddressModel $addresses;

    public function __construct()
    {
        $this->db        = \Config\Database::connect();
        $this->orders    = new OrderModel();
        $this->items     = new OrderItemModel();
        $this->payments  = new PaymentModel();
        $this->shipments = new ShipmentModel();
        $this->logs      = new OrderStatusLogModel();
        $this->addresses = new AddressModel();
    }

    // ------------------------------------------------------------------
    // CHECKOUT
    // ------------------------------------------------------------------

    /**
     * Ubah keranjang customer menjadi pesanan. Mengembalikan id pesanan.
     * Harga & total SELALU dihitung ulang dari database; tidak ada nilai dari browser.
     */
    public function placeOrder(int $userId, int $addressId, ?string $notes = null): int
    {
        $address = $this->addresses->findOwned($addressId, $userId);
        if (! $address) {
            throw new OrderException('Alamat pengiriman tidak valid.');
        }

        $shop = config('Shop');
        $now  = date('Y-m-d H:i:s');

        return $this->transaction(function () use ($userId, $address, $notes, $shop, $now) {
            // kunci baris keranjang agar submit ganda/bersamaan diproses berurutan
            $cart = $this->selectForUpdate('SELECT * FROM carts WHERE user_id = ?', [$userId]);
            if (! $cart) {
                throw new OrderException('Keranjang Anda kosong.');
            }

            // data produk diambil ulang dari database (harga, stok, status terbaru)
            $rows = $this->db->table('cart_items AS ci')
                ->select('ci.product_id, ci.quantity, p.name, p.price, p.stock, p.status')
                ->join('products AS p', 'p.id = ci.product_id')
                ->where('ci.cart_id', $cart['id'])
                ->orderBy('ci.product_id', 'ASC') // urutan konsisten -> mencegah deadlock
                ->get()->getResultArray();

            if (! $rows) {
                throw new OrderException('Keranjang Anda kosong.');
            }

            $subtotal = 0.0;
            $lines    = [];

            foreach ($rows as $row) {
                $qty = (int) $row['quantity'];

                if ($row['status'] !== 'active') {
                    throw new OrderException('Produk "' . $row['name'] . '" sudah tidak tersedia. Hapus dari keranjang.');
                }
                if ($qty < 1) {
                    throw new OrderException('Kuantitas produk "' . $row['name'] . '" tidak valid.');
                }
                if ($qty > (int) $row['stock']) {
                    throw new OrderException('Stok produk "' . $row['name'] . '" tidak mencukupi (tersisa ' . (int) $row['stock'] . ').');
                }

                // pengurangan stok atomik; gagal bila stok sudah diambil transaksi lain
                $this->db->query(
                    'UPDATE products SET stock = stock - ?, updated_at = ? WHERE id = ? AND status = ? AND stock >= ?',
                    [$qty, $now, $row['product_id'], 'active', $qty]
                );
                if ($this->db->affectedRows() !== 1) {
                    throw new OrderException('Stok produk "' . $row['name'] . '" baru saja habis atau berubah. Silakan periksa keranjang.');
                }

                $price     = round((float) $row['price'], 2);
                $lineTotal = round($price * $qty, 2);
                $subtotal += $lineTotal;

                $lines[] = [
                    'product_id'   => (int) $row['product_id'],
                    'product_name' => $row['name'],   // snapshot nama (BR-004)
                    'price'        => $price,         // snapshot harga (BR-004)
                    'quantity'     => $qty,
                    'subtotal'     => $lineTotal,     // BR-005
                ];
            }

            $shipping = (float) $shop->shippingCost;
            $total    = round($subtotal + $shipping, 2);

            $orderId = $this->orders->insert([
                'user_id'          => $userId,
                'address_id'       => $address['id'],
                'order_number'     => $this->orders->generateOrderNumber(),
                'order_date'       => $now,
                'subtotal'         => $subtotal,
                'shipping_cost'    => $shipping,
                'total'            => $total,
                'order_status'     => 'pending_payment',
                'shipping_address' => AddressModel::formatLine($address), // snapshot alamat
                'notes'            => $notes,
            ], true);

            if (! $orderId) {
                throw new OrderException('Gagal menyimpan pesanan.');
            }

            foreach ($lines as &$line) {
                $line['order_id'] = $orderId;
            }
            unset($line);
            $this->items->insertBatch($lines);

            $this->payments->insert([
                'order_id'       => $orderId,
                'method'         => 'bank_transfer',
                'amount'         => $total,
                'payment_status' => 'unpaid',
            ]);

            $this->log($orderId, null, 'pending_payment', $userId, 'Pesanan dibuat');

            // kosongkan keranjang
            $this->db->table('cart_items')->where('cart_id', $cart['id'])->delete();

            return (int) $orderId;
        });
    }

    // ------------------------------------------------------------------
    // PEMBAYARAN
    // ------------------------------------------------------------------

    /**
     * Customer mengunggah bukti pembayaran. File sudah tersimpan oleh controller.
     * Mengembalikan nama file bukti lama (jika ada) agar bisa dihapus dari disk.
     */
    public function submitPayment(int $orderId, int $userId, string $proofFile): ?string
    {
        return $this->transaction(function () use ($orderId, $userId, $proofFile) {
            $order = $this->lockOrder($orderId);
            if (! $order || (int) $order['user_id'] !== $userId) {
                throw new OrderException('Pesanan tidak ditemukan.');
            }
            if ($order['order_status'] !== 'pending_payment') {
                throw new OrderException('Bukti pembayaran hanya dapat diunggah untuk pesanan yang menunggu pembayaran.');
            }

            $payment = $this->payments->where('order_id', $orderId)->first();
            if (! $payment || ! in_array($payment['payment_status'], ['unpaid', 'rejected'], true)) {
                throw new OrderException('Status pembayaran tidak memungkinkan unggah bukti baru.');
            }

            $this->payments->update($payment['id'], [
                'proof_file'     => $proofFile,
                'payment_status' => 'submitted',
                'note'           => null,
                'verified_by'    => null,
                'verified_at'    => null,
            ]);
            $this->changeStatus($order, 'waiting_verification', $userId, 'Bukti pembayaran diunggah');

            return $payment['proof_file'];
        });
    }

    /** Admin menyetujui pembayaran -> pesanan diproses. */
    public function verifyPayment(int $orderId, int $adminId): void
    {
        $this->transaction(function () use ($orderId, $adminId) {
            $order   = $this->requireOrder($orderId);
            $payment = $this->requireSubmittedPayment($orderId);

            $this->payments->update($payment['id'], [
                'payment_status' => 'verified',
                'verified_by'    => $adminId,
                'verified_at'    => date('Y-m-d H:i:s'),
                'note'           => null,
            ]);
            $this->changeStatus($order, 'processing', $adminId, 'Pembayaran diverifikasi');
        });
    }

    /** Admin menolak bukti -> customer harus mengunggah ulang. */
    public function rejectPayment(int $orderId, int $adminId, string $reason): void
    {
        $this->transaction(function () use ($orderId, $adminId, $reason) {
            $order   = $this->requireOrder($orderId);
            $payment = $this->requireSubmittedPayment($orderId);

            $this->payments->update($payment['id'], [
                'payment_status' => 'rejected',
                'verified_by'    => $adminId,
                'verified_at'    => date('Y-m-d H:i:s'),
                'note'           => mb_substr($reason, 0, 255),
            ]);
            $this->changeStatus($order, 'pending_payment', $adminId, 'Bukti pembayaran ditolak: ' . $reason);
        });
    }

    // ------------------------------------------------------------------
    // PENGIRIMAN & PENYELESAIAN
    // ------------------------------------------------------------------

    /**
     * Isi kurir & nomor resi. Pesanan "processing" menjadi "shipped".
     * Jika sudah "shipped", hanya memperbarui data kurir/resi.
     */
    public function ship(int $orderId, int $actorId, string $courier, string $tracking): void
    {
        $this->transaction(function () use ($orderId, $actorId, $courier, $tracking) {
            $order = $this->requireOrder($orderId);
            $now   = date('Y-m-d H:i:s');

            if (! in_array($order['order_status'], ['processing', 'shipped'], true)) {
                throw new OrderException('Pengiriman hanya dapat diisi untuk pesanan yang sedang diproses atau dikirim.');
            }

            $shipment = $this->shipments->where('order_id', $orderId)->first();
            $data     = [
                'courier'         => $courier,
                'tracking_number' => $tracking,
                'shipment_status' => 'shipped',
            ];

            if ($shipment) {
                if (empty($shipment['shipped_at'])) {
                    $data['shipped_at'] = $now;
                }
                $this->shipments->update($shipment['id'], $data);
            } else {
                $this->shipments->insert($data + ['order_id' => $orderId, 'shipped_at' => $now]);
            }

            if ($order['order_status'] === 'processing') {
                $this->changeStatus($order, 'shipped', $actorId, "Dikirim via {$courier}, resi {$tracking}");
            } else {
                $this->log($orderId, 'shipped', 'shipped', $actorId, "Data pengiriman diperbarui: {$courier}, resi {$tracking}");
            }
        });
    }

    /** Tandai pesanan selesai (oleh customer pemilik, atau admin/operator). */
    public function complete(int $orderId, int $actorId, string $actorRole): void
    {
        $this->transaction(function () use ($orderId, $actorId, $actorRole) {
            $order = $this->requireOrder($orderId);
            $this->assertOwnerIfCustomer($order, $actorId, $actorRole);

            $this->changeStatus($order, 'completed', $actorId, 'Pesanan diterima/selesai');

            $this->db->table('shipments')->where('order_id', $orderId)->update([
                'shipment_status' => 'delivered',
                'delivered_at'    => date('Y-m-d H:i:s'),
            ]);
        });
    }

    /**
     * Batalkan pesanan & kembalikan stok.
     *  - customer: hanya pesanan miliknya yang masih "pending_payment"
     *  - admin   : pesanan yang belum dikirim
     * Pesanan tidak pernah dihapus fisik (BR-011).
     */
    public function cancel(int $orderId, int $actorId, string $actorRole, ?string $reason = null): void
    {
        $this->transaction(function () use ($orderId, $actorId, $actorRole, $reason) {
            $order = $this->requireOrder($orderId);
            $this->assertOwnerIfCustomer($order, $actorId, $actorRole);

            if ($actorRole === 'customer' && $order['order_status'] !== 'pending_payment') {
                throw new OrderException('Pesanan hanya dapat dibatalkan sendiri selama masih menunggu pembayaran. Hubungi admin.');
            }
            if ($actorRole === 'operator') {
                throw new OrderException('Operator tidak berwenang membatalkan pesanan.');
            }

            $this->changeStatus($order, 'cancelled', $actorId, $reason ?: 'Pesanan dibatalkan');

            // kembalikan stok
            foreach ($this->items->where('order_id', $orderId)->findAll() as $item) {
                $this->db->query(
                    'UPDATE products SET stock = stock + ?, updated_at = ? WHERE id = ?',
                    [(int) $item['quantity'], date('Y-m-d H:i:s'), $item['product_id']]
                );
            }
        });
    }

    // ------------------------------------------------------------------
    // INTERNAL
    // ------------------------------------------------------------------

    /** Ubah status pesanan dengan validasi transisi + catat log (BR-012). */
    protected function changeStatus(array $order, string $newStatus, ?int $actorId, ?string $note = null): void
    {
        if (! OrderModel::canTransition($order['order_status'], $newStatus)) {
            throw new OrderException(sprintf(
                'Perubahan status dari "%s" ke "%s" tidak diizinkan.',
                order_status_label($order['order_status']),
                order_status_label($newStatus)
            ));
        }

        $this->orders->update($order['id'], ['order_status' => $newStatus]);
        $this->log((int) $order['id'], $order['order_status'], $newStatus, $actorId, $note);
    }

    protected function log(int $orderId, ?string $old, string $new, ?int $actorId, ?string $note): void
    {
        $this->logs->insert([
            'order_id'   => $orderId,
            'old_status' => $old,
            'new_status' => $new,
            'changed_by' => $actorId,
            'note'       => $note !== null ? mb_substr($note, 0, 255) : null,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    protected function requireOrder(int $orderId): array
    {
        $order = $this->lockOrder($orderId);
        if (! $order) {
            throw new OrderException('Pesanan tidak ditemukan.');
        }

        return $order;
    }

    protected function requireSubmittedPayment(int $orderId): array
    {
        $payment = $this->payments->where('order_id', $orderId)->first();
        if (! $payment || $payment['payment_status'] !== 'submitted') {
            throw new OrderException('Tidak ada bukti pembayaran yang menunggu verifikasi untuk pesanan ini.');
        }

        return $payment;
    }

    protected function assertOwnerIfCustomer(array $order, int $actorId, string $actorRole): void
    {
        if ($actorRole === 'customer' && (int) $order['user_id'] !== $actorId) {
            throw new OrderException('Pesanan tidak ditemukan.'); // tidak membocorkan keberadaan pesanan orang lain
        }
    }

    protected function lockOrder(int $orderId): ?array
    {
        return $this->selectForUpdate('SELECT * FROM orders WHERE id = ?', [$orderId]);
    }

    /** SELECT ... FOR UPDATE (MySQL/MariaDB) untuk mengunci baris selama transaksi. */
    protected function selectForUpdate(string $sql, array $binds): ?array
    {
        if ($this->db->DBDriver === 'MySQLi') {
            $sql .= ' FOR UPDATE';
        }

        return $this->db->query($sql, $binds)->getRowArray() ?: null;
    }

    /** Jalankan callback di dalam transaksi; rollback bila gagal. */
    protected function transaction(callable $callback)
    {
        $this->db->transBegin();

        try {
            $result = $callback();

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Transaksi database gagal.');
            }
            $this->db->transCommit();

            return $result;
        } catch (\Throwable $e) {
            $this->db->transRollback();

            throw $e;
        }
    }
}
