<?php

namespace App\Database\Seeds;

use App\Libraries\OrderService;
use CodeIgniter\Database\Seeder;

/**
 * Pesanan contoh dengan berbagai status, dibuat melalui OrderService yang sama
 * dengan yang dipakai aplikasi (jadi stok, snapshot harga, dan log status konsisten).
 * Dipakai agar dashboard, laporan, dan produk terlaris langsung terisi saat demo.
 */
class SampleOrderSeeder extends Seeder
{
    public function run(): void
    {
        if ($this->db->table('orders')->countAllResults() > 0) {
            return;
        }

        $svc   = new OrderService();
        $admin = (int) $this->db->table('users')->where('role', 'admin')->get()->getRow('id');
        $op    = (int) $this->db->table('users')->where('role', 'operator')->get()->getRow('id');

        $customers = [];
        foreach ($this->db->table('users')->where('role', 'customer')->orderBy('id')->get()->getResultArray() as $i => $u) {
            $addr = $this->db->table('addresses')->where('user_id', $u['id'])->orderBy('is_default', 'DESC')->get()->getRowArray();
            $customers[$i + 1] = ['id' => (int) $u['id'], 'address' => (int) $addr['id']];
        }

        $pid = fn (string $slug) => (int) $this->db->table('products')->where('slug', $slug)->get()->getRow('id');

        // [customer, hari lalu, item[slug => qty], skenario]
        $scenarios = [
            [1, 12, ['wireless-headphone' => 1, 'mug-keramik' => 2], 'completed'],
            [2, 9, ['kaos-polos-premium' => 2, 'topi-baseball' => 1], 'completed'],
            [1, 7, ['smart-watch' => 1], 'completed'],
            [2, 5, ['sneakers-casual' => 1, 'botol-minum-stainless' => 1], 'shipped'],
            [1, 4, ['power-bank-20000-mah' => 1], 'processing'],
            [2, 3, ['jaket-hoodie' => 1], 'cancelled'],
            [1, 1, ['lampu-meja-led' => 1, 'mug-keramik' => 1], 'waiting_verification'],
            [2, 0, ['speaker-bluetooth-mini' => 1], 'pending_payment'],
        ];

        foreach ($scenarios as [$cIdx, $daysAgo, $items, $final]) {
            $c = $customers[$cIdx];

            // isi keranjang
            $cartId = $this->db->table('carts')->where('user_id', $c['id'])->get()->getRow('id');
            if (! $cartId) {
                $this->db->table('carts')->insert(['user_id' => $c['id'], 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
                $cartId = $this->db->insertID();
            }
            foreach ($items as $slug => $qty) {
                $this->db->table('cart_items')->insert(['cart_id' => $cartId, 'product_id' => $pid($slug), 'quantity' => $qty]);
            }

            $orderId = $svc->placeOrder($c['id'], $c['address'], null);

            if ($final === 'cancelled') {
                $svc->cancel($orderId, $c['id'], 'customer', 'Dibatalkan oleh customer');
            }
            if (in_array($final, ['waiting_verification', 'processing', 'shipped', 'completed'], true)) {
                $svc->submitPayment($orderId, $c['id'], $this->makeProof($orderId));
            }
            if (in_array($final, ['processing', 'shipped', 'completed'], true)) {
                $svc->verifyPayment($orderId, $admin);
            }
            if (in_array($final, ['shipped', 'completed'], true)) {
                $svc->ship($orderId, $op, 'JNE', 'JNE' . str_pad((string) (1000000 + $orderId * 37), 10, '0', STR_PAD_LEFT));
            }
            if ($final === 'completed') {
                $svc->complete($orderId, $c['id'], 'customer');
            }

            $this->backdate($orderId, $daysAgo);
        }
    }

    /** Geser tanggal pesanan & log agar grafik dashboard tampak realistis. */
    private function backdate(int $orderId, int $daysAgo): void
    {
        $base = strtotime("-{$daysAgo} days 10:00:00");
        $this->db->table('orders')->where('id', $orderId)->update([
            'order_date' => date('Y-m-d H:i:s', $base),
            'created_at' => date('Y-m-d H:i:s', $base),
            'updated_at' => date('Y-m-d H:i:s', $base + 3600),
        ]);

        $n = 0;
        foreach ($this->db->table('order_status_logs')->where('order_id', $orderId)->orderBy('id')->get()->getResultArray() as $log) {
            $this->db->table('order_status_logs')->where('id', $log['id'])->update([
                'created_at' => date('Y-m-d H:i:s', $base + $n++ * 1800),
            ]);
        }
    }

    /** Buat gambar "bukti transfer" demo di writable/uploads/payments. */
    private function makeProof(int $orderId): string
    {
        $dir = WRITEPATH . 'uploads/payments';
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $name = 'seed-proof-' . bin2hex(random_bytes(4)) . '.png';

        if (function_exists('imagecreatetruecolor')) {
            $im = imagecreatetruecolor(360, 200);
            imagefill($im, 0, 0, imagecolorallocate($im, 238, 240, 246));
            $ink = imagecolorallocate($im, 31, 35, 64);
            imagestring($im, 5, 20, 30, 'BUKTI TRANSFER (DEMO)', $ink);
            imagestring($im, 4, 20, 80, 'Order ID: ' . $orderId, $ink);
            imagestring($im, 4, 20, 110, 'Data fiktif untuk pengujian', $ink);
            imagepng($im, $dir . '/' . $name);
            imagedestroy($im);
        } else { // PNG 1x1 jika GD tidak aktif
            file_put_contents($dir . '/' . $name, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='));
        }

        return $name;
    }
}
