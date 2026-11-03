<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Config\Shop;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $shop = config(Shop::class);
        $buyers = $this->db->table('users')->where('role', 'buyer')->orderBy('id')->get()->getResultArray();
        $products = array_column($this->db->table('products')->get()->getResultArray(), null, 'slug');

        $orders = [
            [0, -165, 'completed', [['gayo-wine-process', 1], ['robusta-temanggung', 2]]],
            [1, -150, 'completed', [['kintamani-natural', 2]]],
            [2, -138, 'completed', [['nusantara-espresso-blend', 1]]],
            [3, -120, 'cancelled', [['papua-wamena', 1]]],
            [4, -104, 'completed', [['kopi-tubruk-aceh', 3], ['drip-bag-sampler-arabika', 1]]],
            [5, -90, 'completed', [['toraja-sapan', 1], ['flores-bajawa', 1]]],
            [6, -72, 'completed', [['paket-hampers-nusantara', 1]]],
            [7, -60, 'completed', [['kopi-susu-blend', 1], ['robusta-lampung-fine', 1]]],
            [8, -44, 'completed', [['liberika-meranti', 2]]],
            [9, -30, 'completed', [['java-preanger', 2], ['drip-bag-sampler-arabika', 1]]],
            [0, -18, 'shipped', [['gayo-anaerobic-micro-lot', 1], ['kerinci-red-honey', 1]]],
            [1, -9, 'processing', [['cold-brew-coarse-ground', 2]]],
            [2, -4, 'awaiting_verification', [['mandailing-giling-basah', 1], ['robusta-dampit', 1]]],
            [3, -1, 'awaiting_payment', [['decaf-sumatra', 1]]],
            [4, 0, 'awaiting_payment', [['kopi-tubruk-aceh', 2]]],
        ];

        $couriers = $shop->couriers;

        foreach ($orders as $index => [$buyerIndex, $offset, $status, $lines]) {
            $buyer = $buyers[$buyerIndex];
            $createdAt = strtotime($offset . ' days 10:30');
            $subtotal = 0;
            $items = [];

            foreach ($lines as [$slug, $quantity]) {
                $product = $products[$slug];
                $lineTotal = (int) $product['price'] * $quantity;
                $subtotal += $lineTotal;
                $items[] = [
                    'product_id'   => $product['id'],
                    'product_name' => $product['name'] . ' (' . $product['weight_gram'] . ' g)',
                    'price'        => $product['price'],
                    'quantity'     => $quantity,
                    'subtotal'     => $lineTotal,
                ];
            }

            $shipping = $subtotal > $shop->freeShippingThreshold ? 0 : $shop->shippingCost;
            $reached = array_search($status, ['awaiting_payment', 'awaiting_verification', 'processing', 'shipped', 'completed'], true);
            $time = static fn (int $hours): string => date('Y-m-d H:i:s', $createdAt + $hours * 3600);
            [$street, $postal] = $this->splitAddress($buyer['address']);
            $code = 'KN-' . date('Ymd', $createdAt) . '-' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT);
            $proof = $reached !== false && $reached >= 1 ? $this->makeProof($code, $subtotal + $shipping, $buyer['name']) : null;

            $this->db->table('orders')->insert([
                'user_id'         => $buyer['id'],
                'code'            => $code,
                'status'          => $status,
                'recipient_name'  => $buyer['name'],
                'phone'           => $buyer['phone'],
                'address'         => $street,
                'city'            => $this->cityOf($buyer['address']),
                'postal_code'     => $postal,
                'note'            => $index % 3 === 0 ? 'Mohon digiling untuk V60' : null,
                'subtotal'        => $subtotal,
                'shipping_cost'   => $shipping,
                'total'           => $subtotal + $shipping,
                'payment_proof'   => $proof,
                'courier'         => $reached !== false && $reached >= 3 ? $couriers[$index % count($couriers)] : null,
                'tracking_number' => $reached !== false && $reached >= 3 ? 'KNX' . str_pad((string) (48213 + $index * 97), 10, '0', STR_PAD_LEFT) : null,
                'paid_at'         => $reached !== false && $reached >= 1 ? $time(3) : null,
                'verified_at'     => $reached !== false && $reached >= 2 ? $time(6) : null,
                'shipped_at'      => $reached !== false && $reached >= 3 ? $time(26) : null,
                'completed_at'    => $reached !== false && $reached >= 4 ? $time(96) : null,
                'cancelled_at'    => $status === 'cancelled' ? $time(20) : null,
                'created_at'      => $time(0),
                'updated_at'      => $time(0),
            ]);

            $orderId = $this->db->insertID();

            foreach ($items as &$item) {
                $item['order_id'] = $orderId;
                $item['created_at'] = $time(0);
                $item['updated_at'] = $time(0);
            }

            $this->db->table('order_items')->insertBatch($items);
        }
    }

    private function makeProof(string $code, int $total, string $name): ?string
    {
        if (! function_exists('imagecreatetruecolor')) {
            return null;
        }

        $directory = WRITEPATH . 'uploads/payments/';

        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $image = imagecreatetruecolor(480, 300);
        $background = imagecolorallocate($image, 245, 248, 252);
        $primary = imagecolorallocate($image, 0, 82, 155);
        $text = imagecolorallocate($image, 40, 40, 40);
        imagefill($image, 0, 0, $background);
        imagefilledrectangle($image, 0, 0, 480, 56, $primary);
        imagestring($image, 5, 20, 20, 'BUKTI TRANSFER (DATA CONTOH)', imagecolorallocate($image, 255, 255, 255));
        imagestring($image, 4, 20, 90, 'Pesanan  : ' . $code, $text);
        imagestring($image, 4, 20, 120, 'Pengirim : ' . $name, $text);
        imagestring($image, 4, 20, 150, 'Tujuan   : BCA 1234567890', $text);
        imagestring($image, 5, 20, 190, 'Nominal  : Rp' . number_format($total, 0, ',', '.'), $primary);
        imagestring($image, 3, 20, 250, 'Status   : BERHASIL', $text);

        $name = bin2hex(random_bytes(10)) . '.png';
        imagepng($image, $directory . $name);
        imagedestroy($image);

        return $name;
    }

    private function splitAddress(string $address): array
    {
        preg_match('/^(.*),\s*[^,]+?\s(\d{5})$/', $address, $matches);

        return [$matches[1] ?? $address, $matches[2] ?? '00000'];
    }

    private function cityOf(string $address): string
    {
        preg_match('/,\s*([^,]+?)\s\d{5}$/', $address, $matches);

        return $matches[1] ?? 'Surabaya';
    }
}
