<?php

namespace App\Services;

use App\Libraries\JsonStore;
use App\Models\AddressModel;
use App\Models\OrderItemModel;
use App\Models\OrderLogModel;
use App\Models\OrderModel;
use App\Models\PaymentModel;
use App\Models\ProductModel;
use App\Models\ShipmentModel;
use RuntimeException;

/**
 * Siklus transaksi: checkout, pembayaran, verifikasi, pengiriman, penyelesaian, dan pembatalan.
 * Setiap perubahan status divalidasi terhadap OrderModel::FLOW dan dicatat di order_logs (BR-012).
 * Semua langkah dijalankan di dalam JsonStore::transaction() sehingga gagal sebagian = batal semua (BR-010).
 */
class OrderService
{
    private OrderModel $orders;
    private JsonStore $store;

    public function __construct()
    {
        $this->orders = model(OrderModel::class);
        $this->store  = service('store');
    }

    /**
     * Membuat pesanan dari keranjang (FR-012, FR-013).
     * Harga diambil ulang dari data produk (BR-005), stok dicek dan dikurangi saat pesanan dikonfirmasi (BR-009),
     * dan nama/harga produk disimpan sebagai snapshot (BR-004).
     */
    public function checkout(int $userId, int $addressId, ?string $notes, CartService $cart): array
    {
        return $this->store->transaction(function () use ($userId, $addressId, $notes, $cart): array {
            $address = model(AddressModel::class)->findOwned($addressId, $userId);

            if ($address === null) {
                throw new RuntimeException('Pilih alamat pengiriman yang valid.');
            }

            $items = $cart->items();

            if ($items === []) {
                throw new RuntimeException('Keranjang belanja masih kosong.');
            }

            foreach ($items as $item) {
                if (! $item['available']) {
                    throw new RuntimeException($item['name'] . ' sudah tidak dijual. Hapus dari keranjang terlebih dahulu.');
                }

                if (! $item['enoughStock']) {
                    throw new RuntimeException('Stok ' . $item['name'] . ' tidak mencukupi. Sisa stok: ' . $item['stock'] . '.');
                }
            }

            $totals  = $cart->totals($items);
            $orderId = $this->orders->insert([
                'user_id'          => $userId,
                'address_id'       => $address['id'],
                'order_number'     => $this->orders->generateNumber(),
                'order_date'       => date('Y-m-d H:i:s'),
                'recipient_name'   => $address['recipient_name'],
                'phone'            => $address['phone'],
                'shipping_address' => AddressModel::format($address),
                'subtotal'         => $totals['subtotal'],
                'shipping_cost'    => $totals['shipping'],
                'total'            => $totals['total'],
                'order_status'     => 'pending_payment',
                'notes'            => ($notes ?? '') !== '' ? $notes : null,
            ]);

            foreach ($items as $item) {
                model(ProductModel::class)->adjustStock($item['product_id'], -$item['quantity']);

                model(OrderItemModel::class)->insert([
                    'order_id'     => $orderId,
                    'product_id'   => $item['product_id'],
                    'product_name' => $item['name'],
                    'price'        => $item['price'],
                    'quantity'     => $item['quantity'],
                    'subtotal'     => $item['subtotal'],
                ]);
            }

            model(PaymentModel::class)->insert([
                'order_id'       => $orderId,
                'method'         => 'bank_transfer',
                'amount'         => $totals['total'],
                'proof_file'     => null,
                'payment_status' => 'unpaid',
                'submitted_at'   => null,
                'verified_by'    => null,
                'verified_at'    => null,
                'notes'          => null,
            ]);

            $this->log($orderId, $userId, null, 'pending_payment', 'Pesanan dibuat.');
            $cart->clear();

            return $this->orders->find($orderId);
        });
    }

    /**
     * Customer mengunggah bukti transfer (FR-014).
     */
    public function submitPayment(array $order, int $userId, string $proofFile): void
    {
        $payment = model(PaymentModel::class)->forOrder($order['id']);

        if ($payment === null || ! in_array($payment['payment_status'], ['unpaid', 'rejected'], true)) {
            throw new RuntimeException('Bukti pembayaran untuk pesanan ini tidak dapat diunggah lagi.');
        }

        $this->transition($order, 'waiting_verification', $userId, 'Bukti pembayaran diunggah.', static function () use ($payment, $proofFile): void {
            model(PaymentModel::class)->update($payment['id'], [
                'proof_file'     => $proofFile,
                'payment_status' => 'submitted',
                'submitted_at'   => date('Y-m-d H:i:s'),
                'notes'          => null,
            ]);
        });
    }

    /**
     * Admin menyetujui atau menolak bukti pembayaran (FR-015, BR-007).
     */
    public function verifyPayment(array $order, int $adminId, bool $approve, ?string $note): void
    {
        $payment = model(PaymentModel::class)->forOrder($order['id']);

        if ($payment === null || $payment['payment_status'] !== 'submitted') {
            throw new RuntimeException('Tidak ada bukti pembayaran yang menunggu verifikasi.');
        }

        if (! $approve && ($note ?? '') === '') {
            throw new RuntimeException('Tuliskan alasan penolakan agar customer dapat menindaklanjuti.');
        }

        $target = $approve ? 'processing' : 'pending_payment';
        $label  = $approve ? 'Pembayaran diverifikasi.' : 'Bukti pembayaran ditolak: ' . $note;

        $this->transition($order, $target, $adminId, $label, static function () use ($payment, $order, $adminId, $approve, $note): void {
            model(PaymentModel::class)->update($payment['id'], [
                'payment_status' => $approve ? 'verified' : 'rejected',
                'verified_by'    => $adminId,
                'verified_at'    => date('Y-m-d H:i:s'),
                'notes'          => $approve ? null : $note,
            ]);

            if ($approve) {
                model(ShipmentModel::class)->insert([
                    'order_id'        => $order['id'],
                    'courier'         => null,
                    'tracking_number' => null,
                    'shipment_status' => 'preparing',
                    'shipped_at'      => null,
                    'delivered_at'    => null,
                ]);
            }
        });
    }

    /**
     * Operator/admin mengisi kurir dan nomor resi (FR-016).
     */
    public function ship(array $order, int $userId, string $courier, string $trackingNumber): void
    {
        if ($courier === '' || $trackingNumber === '') {
            throw new RuntimeException('Kurir dan nomor resi wajib diisi.');
        }

        $this->transition($order, 'shipped', $userId, 'Dikirim via ' . $courier . ' (resi ' . $trackingNumber . ').', static function () use ($order, $courier, $trackingNumber): void {
            $shipments = model(ShipmentModel::class);
            $shipment  = $shipments->forOrder($order['id']);
            $data      = [
                'courier'         => $courier,
                'tracking_number' => $trackingNumber,
                'shipment_status' => 'shipped',
                'shipped_at'      => date('Y-m-d H:i:s'),
            ];

            $shipment === null
                ? $shipments->insert($data + ['order_id' => $order['id'], 'delivered_at' => null])
                : $shipments->update($shipment['id'], $data);
        });
    }

    public function complete(array $order, int $userId, string $note): void
    {
        $this->transition($order, 'completed', $userId, $note, static function () use ($order): void {
            $shipments = model(ShipmentModel::class);
            $shipment  = $shipments->forOrder($order['id']);

            if ($shipment !== null) {
                $shipments->update($shipment['id'], ['shipment_status' => 'delivered', 'delivered_at' => date('Y-m-d H:i:s')]);
            }
        });
    }

    /**
     * Pembatalan mengembalikan stok. Pesanan tidak pernah dihapus (BR-011).
     */
    public function cancel(array $order, int $userId, string $note): void
    {
        $this->transition($order, 'cancelled', $userId, $note, static function () use ($order): void {
            foreach (model(OrderItemModel::class)->where(['order_id' => $order['id']]) as $item) {
                if ($item['product_id'] !== null) {
                    model(ProductModel::class)->adjustStock($item['product_id'], $item['quantity']);
                }
            }
        });
    }

    /**
     * Mengubah status pesanan secara atomik: transisi harus valid dan status tersimpan belum berubah.
     */
    private function transition(array $order, string $target, int $userId, string $note, callable $sideEffects): void
    {
        $from = $order['order_status'];

        if (! OrderModel::canMove($from, $target)) {
            throw new RuntimeException('Status pesanan tidak dapat diubah dari "' . order_status_label($from) . '" ke "' . order_status_label($target) . '".');
        }

        $this->store->transaction(function () use ($order, $from, $target, $userId, $note, $sideEffects): void {
            // Dibaca ulang di dalam lock: mencegah dua admin memproses pesanan yang sama bersamaan.
            if (($this->orders->find($order['id'])['order_status'] ?? null) !== $from) {
                throw new RuntimeException('Status pesanan sudah berubah. Muat ulang halaman.');
            }

            $this->orders->update($order['id'], ['order_status' => $target]);
            $sideEffects();
            $this->log($order['id'], $userId, $from, $target, $note);
        });
    }

    private function log(int $orderId, ?int $userId, ?string $from, string $to, string $note): void
    {
        model(OrderLogModel::class)->insert([
            'order_id'    => $orderId,
            'user_id'     => $userId,
            'from_status' => $from,
            'to_status'   => $to,
            'note'        => mb_substr($note, 0, 255),
        ]);
    }
}
