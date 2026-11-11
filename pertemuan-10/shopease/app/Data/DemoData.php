<?php

namespace App\Data;

use App\Libraries\JsonStore;
use App\Models\AddressModel;
use App\Models\CategoryModel;
use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\UserModel;
use App\Services\CartService;
use App\Services\OrderService;

/**
 * Data awal untuk demonstrasi (PRD 20: seed data). Semua nama, email, dan alamat adalah data contoh.
 * Dipasang otomatis saat file data belum ada, atau lewat perintah `php spark shop:reset`.
 */
class DemoData
{
    public const ACCOUNTS = [
        ['Admin ShopEase', 'admin@shopease.test', 'admin123', 'admin', '081200000001'],
        ['Operator Gudang', 'operator@shopease.test', 'operator123', 'operator', '081200000002'],
        ['Budi Santoso', 'budi@example.test', 'customer123', 'customer', '081311110001'],
        ['Siti Rahmawati', 'siti@example.test', 'customer123', 'customer', '081311110002'],
        ['Andi Pratama', 'andi@example.test', 'customer123', 'customer', '081311110003'],
    ];

    private const CATEGORIES = [
        ['Elektronik', 'elektronik', 'bi-headphones', 'Gawai dan aksesori untuk kebutuhan harian.'],
        ['Fashion', 'fashion', 'bi-handbag', 'Pakaian, tas, dan aksesori.'],
        ['Rumah Tangga', 'rumah-tangga', 'bi-house-heart', 'Perlengkapan dapur dan rumah.'],
        ['Kecantikan', 'kecantikan', 'bi-droplet-half', 'Perawatan wajah dan tubuh.'],
        ['Olahraga', 'olahraga', 'bi-bicycle', 'Peralatan olahraga dan aktivitas luar ruang.'],
        ['Buku & Alat Tulis', 'buku-alat-tulis', 'bi-book', 'Buku, catatan, dan alat tulis.'],
    ];

    /** [kategori, nama, slug, harga, stok, ikon, status, unggulan, tanggal dibuat, deskripsi] */
    private const PRODUCTS = [
        ['elektronik', 'Earphone Nirkabel Aero', 'earphone-nirkabel-aero', 289000, 25, 'bi-earbuds', 'active', true, '2026-10-28', 'Earphone Bluetooth 5.3 dengan daya tahan baterai hingga 24 jam bersama casing pengisi daya dan mode redam bising.'],
        ['elektronik', 'Power Bank 10.000 mAh', 'power-bank-10000', 175000, 40, 'bi-battery-charging', 'active', true, '2026-10-12', 'Power bank ringkas dengan dua port USB-A dan satu USB-C, mendukung pengisian cepat 20W.'],
        ['elektronik', 'Lampu Meja LED Lipat', 'lampu-meja-led-lipat', 129000, 0, 'bi-lamp', 'active', false, '2026-09-30', 'Lampu belajar LED dengan tiga tingkat kecerahan dan leher yang dapat ditekuk.'],
        ['fashion', 'Tote Bag Kanvas', 'tote-bag-kanvas', 85000, 60, 'bi-bag', 'active', true, '2026-11-02', 'Tas kanvas tebal dengan saku dalam, cocok untuk kuliah maupun belanja.'],
        ['fashion', 'Kaos Katun Basic', 'kaos-katun-basic', 69000, 120, 'bi-person-standing', 'active', false, '2026-10-05', 'Kaos katun combed 30s yang adem, tersedia dalam ukuran S sampai XL.'],
        ['fashion', 'Topi Baseball Polos', 'topi-baseball-polos', 55000, 35, 'bi-sunglasses', 'inactive', false, '2026-08-20', 'Produk nonaktif. Tidak tampil di katalog (BR-002).'],
        ['rumah-tangga', 'Botol Minum Stainless 750 ml', 'botol-minum-stainless', 99000, 48, 'bi-cup-straw', 'active', true, '2026-11-06', 'Botol dua lapis yang menjaga minuman tetap dingin 24 jam atau panas 12 jam.'],
        ['rumah-tangga', 'Set Wadah Makan Kaca', 'set-wadah-makan-kaca', 149000, 18, 'bi-box-seam', 'active', true, '2026-09-18', 'Isi tiga wadah kaca borosilikat yang aman untuk microwave dan freezer.'],
        ['kecantikan', 'Sabun Cuci Muka Gentle', 'sabun-cuci-muka-gentle', 45000, 75, 'bi-droplet', 'active', true, '2026-10-21', 'Pembersih wajah berbahan lembut dengan pH seimbang untuk pemakaian harian.'],
        ['kecantikan', 'Sunscreen SPF 50', 'sunscreen-spf-50', 79000, 9, 'bi-sun', 'active', false, '2026-11-08', 'Tabir surya ringan yang tidak lengket dengan perlindungan UVA dan UVB.'],
        ['olahraga', 'Matras Yoga 6 mm', 'matras-yoga-6mm', 159000, 22, 'bi-heart-pulse', 'active', true, '2026-10-15', 'Matras TPE anti selip setebal 6 mm, lengkap dengan tali pembawa.'],
        ['olahraga', 'Tali Skipping Digital', 'tali-skipping-digital', 89000, 30, 'bi-stopwatch', 'active', false, '2026-09-25', 'Tali lompat dengan penghitung putaran dan kalori pada gagangnya.'],
        ['buku-alat-tulis', 'Buku Catatan Dot Grid A5', 'buku-catatan-dot-grid', 39000, 90, 'bi-journal-bookmark', 'active', true, '2026-11-04', 'Buku catatan 160 halaman kertas 100 gsm dengan pola titik.'],
        ['buku-alat-tulis', 'Set Pulpen Gel 0.5', 'set-pulpen-gel', 32000, 150, 'bi-pen', 'active', false, '2026-10-01', 'Isi enam pulpen gel warna dengan tinta yang cepat kering.'],
    ];

    private const ADDRESSES = [
        'budi@example.test' => [
            ['Budi Santoso', '081311110001', 'Jl. Kenanga No. 12, RT 03/RW 05, Kel. Keputih', 'Surabaya', 'Jawa Timur', '60111', true],
            ['Budi (Kantor)', '081311110001', 'Gedung Contoh Lt. 4, Jl. Raya Darmo No. 88', 'Surabaya', 'Jawa Timur', '60241', false],
        ],
        'siti@example.test' => [
            ['Siti Rahmawati', '081311110002', 'Jl. Melati Indah Blok C-7, Kel. Sukajadi', 'Bandung', 'Jawa Barat', '40162', true],
        ],
        'andi@example.test' => [
            ['Andi Pratama', '081311110003', 'Perumahan Contoh Asri No. 21, Kec. Depok', 'Sleman', 'DI Yogyakarta', '55281', true],
        ],
    ];

    /** [email customer, [[slug, jumlah], ...], berapa hari lalu, status akhir] */
    private const ORDERS = [
        ['budi@example.test', [['earphone-nirkabel-aero', 1], ['power-bank-10000', 1]], 150, 'completed'],
        ['siti@example.test', [['sabun-cuci-muka-gentle', 2], ['tote-bag-kanvas', 1]], 120, 'completed'],
        ['andi@example.test', [['matras-yoga-6mm', 1]], 95, 'completed'],
        ['budi@example.test', [['buku-catatan-dot-grid', 3], ['set-pulpen-gel', 2]], 70, 'completed'],
        ['siti@example.test', [['botol-minum-stainless', 2]], 45, 'completed'],
        ['andi@example.test', [['kaos-katun-basic', 2]], 30, 'cancelled'],
        ['budi@example.test', [['set-wadah-makan-kaca', 1], ['botol-minum-stainless', 1]], 12, 'completed'],
        ['siti@example.test', [['tali-skipping-digital', 1], ['sunscreen-spf-50', 1]], 6, 'shipped'],
        ['andi@example.test', [['earphone-nirkabel-aero', 1]], 3, 'processing'],
        ['budi@example.test', [['tote-bag-kanvas', 2]], 2, 'waiting_verification'],
        ['siti@example.test', [['buku-catatan-dot-grid', 1]], 1, 'rejected'],
        ['andi@example.test', [['power-bank-10000', 1], ['set-pulpen-gel', 1]], 0, 'pending_payment'],
    ];

    public static function install(JsonStore $store, bool $withOrders = true): void
    {
        helper('shop');

        $store->transaction(static function (): void {
            self::catalog();
            self::accounts();
        });

        if ($withOrders) {
            self::orders($store);
        }
    }

    private static function catalog(): void
    {
        $categories  = model(CategoryModel::class);
        $products    = model(ProductModel::class);
        $categoryIds = [];

        foreach (self::CATEGORIES as [$name, $slug, $icon, $description]) {
            $categoryIds[$slug] = $categories->insert(['name' => $name, 'slug' => $slug, 'icon' => $icon, 'description' => $description, 'is_active' => true]);
        }

        foreach (self::PRODUCTS as [$category, $name, $slug, $price, $stock, $icon, $status, $featured, $date, $description]) {
            $products->insert([
                'category_id' => $categoryIds[$category],
                'name'        => $name,
                'slug'        => $slug,
                'description' => $description,
                'price'       => (float) $price,
                'stock'       => $stock,
                'image'       => null,
                'icon'        => $icon,
                'status'      => $status,
                'is_featured' => $featured,
                'created_at'  => $date . ' 09:00:00',
                'updated_at'  => $date . ' 09:00:00',
            ]);
        }
    }

    private static function accounts(): void
    {
        $users     = model(UserModel::class);
        $addresses = model(AddressModel::class);

        foreach (self::ACCOUNTS as [$name, $email, $password, $role, $phone]) {
            $userId = $users->insert(['name' => $name, 'email' => $email, 'password' => $password, 'role' => $role, 'phone' => $phone, 'is_active' => true]);

            foreach (self::ADDRESSES[$email] ?? [] as [$recipient, $addrPhone, $address, $city, $province, $postal, $default]) {
                $addresses->insert([
                    'user_id'        => $userId,
                    'recipient_name' => $recipient,
                    'phone'          => $addrPhone,
                    'address'        => $address,
                    'city'           => $city,
                    'province'       => $province,
                    'postal_code'    => $postal,
                    'is_default'     => $default,
                ]);
            }
        }
    }

    /**
     * Pesanan contoh dibuat lewat CartService dan OrderService agar mengikuti aturan bisnis yang sama dengan aplikasi.
     */
    private static function orders(JsonStore $store): void
    {
        $users    = model(UserModel::class);
        $orders   = model(OrderModel::class);
        $service  = new OrderService();
        $admin    = $users->findByEmail('admin@shopease.test');
        $operator = $users->findByEmail('operator@shopease.test');

        foreach (self::ORDERS as $i => [$email, $items, $daysAgo, $final]) {
            $customer = $users->findByEmail($email);
            $address  = model(AddressModel::class)->forUser($customer['id'])[0];
            $cart     = new CartService($customer['id']);

            foreach ($items as [$slug, $qty]) {
                $cart->add(model(ProductModel::class)->firstWhere(['slug' => $slug])['id'], $qty);
            }

            $order = $service->checkout($customer['id'], $address['id'], $i % 4 === 0 ? 'Mohon dikemas rapi, terima kasih.' : null, $cart);
            $id    = $order['id'];
            $fresh = static fn (): array => $orders->find($id);

            if ($final === 'cancelled') {
                $service->cancel($fresh(), $customer['id'], 'Dibatalkan oleh customer.');
            }

            if (in_array($final, ['waiting_verification', 'processing', 'shipped', 'completed', 'rejected'], true)) {
                $service->submitPayment($fresh(), $customer['id'], self::makeProof($id, (float) $order['total']));
            }

            if ($final === 'rejected') {
                $service->verifyPayment($fresh(), $admin['id'], false, 'Nominal transfer tidak sesuai dengan total pesanan.');
            }

            if (in_array($final, ['processing', 'shipped', 'completed'], true)) {
                $service->verifyPayment($fresh(), $admin['id'], true, null);
            }

            if (in_array($final, ['shipped', 'completed'], true)) {
                $service->ship($fresh(), $operator['id'], 'JNE', 'JNE' . str_pad((string) (1000000 + $id * 7919), 10, '0', STR_PAD_LEFT));
            }

            if ($final === 'completed') {
                $service->complete($fresh(), $customer['id'], 'Pesanan diterima customer.');
            }

            self::backdate($store, $id, $daysAgo);
        }
    }

    /**
     * Menggeser tanggal pesanan beserta riwayatnya ke masa lalu agar rekap bulanan terisi.
     */
    private static function backdate(JsonStore $store, int $orderId, int $daysAgo): void
    {
        if ($daysAgo === 0) {
            return;
        }

        $date = date('Y-m-d H:i:s', strtotime('-' . $daysAgo . ' days 10:15'));

        $store->transaction(static function (JsonStore $store) use ($orderId, $date): void {
            $store->update('orders', $orderId, [
                'order_number' => 'SE-' . date('Ymd', strtotime($date)) . '-' . str_pad((string) $orderId, 4, '0', STR_PAD_LEFT),
                'order_date'   => $date,
                'created_at'   => $date,
                'updated_at'   => $date,
            ]);

            $step = 0;

            foreach ($store->all('order_logs') as $log) {
                if ($log['order_id'] === $orderId) {
                    $store->update('order_logs', $log['id'], ['created_at' => date('Y-m-d H:i:s', strtotime($date . ' +' . ($step++ * 26) . ' hours'))]);
                }
            }
        });
    }

    /**
     * Gambar bukti transfer contoh dibuat dengan GD (jika ekstensi tersedia).
     */
    private static function makeProof(int $orderId, float $total): string
    {
        $directory = WRITEPATH . 'uploads/payments/';
        $fileName  = 'contoh-bukti-' . $orderId . '.png';

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        if (function_exists('imagecreatetruecolor')) {
            $image = imagecreatetruecolor(360, 480);
            imagefill($image, 0, 0, imagecolorallocate($image, 245, 248, 252));
            $dark = imagecolorallocate($image, 31, 78, 121);
            $grey = imagecolorallocate($image, 90, 90, 90);
            imagefilledrectangle($image, 0, 0, 360, 70, $dark);
            imagestring($image, 5, 20, 26, 'BUKTI TRANSFER (CONTOH)', imagecolorallocate($image, 255, 255, 255));
            imagestring($image, 4, 20, 110, 'Bank tujuan : BCA 123 456 7890', $grey);
            imagestring($image, 4, 20, 140, 'Atas nama   : PT ShopEase Contoh', $grey);
            imagestring($image, 4, 20, 170, 'ID pesanan  : ' . $orderId, $grey);
            imagestring($image, 5, 20, 220, 'Jumlah : ' . rupiah($total), $dark);
            imagestring($image, 3, 20, 440, 'Data dummy untuk pengujian.', $grey);
            imagepng($image, $directory . $fileName);
            imagedestroy($image);
        } else {
            file_put_contents($directory . $fileName, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+ip1sAAAAASUVORK5CYII='));
        }

        return $fileName;
    }
}
