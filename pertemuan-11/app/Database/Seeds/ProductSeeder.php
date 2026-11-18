<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        if ($this->db->table('products')->countAllResults() > 0) {
            return;
        }

        $cat = [];
        foreach ($this->db->table('categories')->get()->getResultArray() as $c) {
            $cat[$c['slug']] = (int) $c['id'];
        }

        // [kategori, nama, deskripsi, harga, stok, status]
        $products = [
            ['elektronik', 'Wireless Headphone', 'Headphone Bluetooth 5.3 dengan noise reduction dan baterai hingga 30 jam.', 299000, 20, 'active'],
            ['elektronik', 'Smart Watch', 'Jam tangan digital dengan pemantau detak jantung, langkah, dan notifikasi.', 450000, 10, 'active'],
            ['elektronik', 'Power Bank 20000 mAh', 'Power bank fast charging dengan dua port USB dan satu USB-C.', 235000, 25, 'active'],
            ['elektronik', 'Speaker Bluetooth Mini', 'Speaker portabel tahan percikan air dengan suara jernih dan bass kuat.', 275000, 14, 'active'],
            ['fashion', 'Sneakers Casual', 'Sepatu casual pria yang nyaman untuk aktivitas sehari-hari.', 350000, 15, 'active'],
            ['fashion', 'Kaos Polos Premium', 'Kaos katun combed 30s, adem, lembut, dan tidak mudah melar.', 89000, 40, 'active'],
            ['fashion', 'Jaket Hoodie', 'Hoodie fleece tebal dengan kantong depan dan tali serut.', 245000, 18, 'active'],
            ['fashion', 'Celana Chino', 'Celana chino slim fit dengan bahan stretch yang nyaman.', 199000, 22, 'active'],
            ['aksesoris', 'Tas Ransel Urban', 'Ransel anti air dengan slot laptop 15 inci dan banyak kompartemen.', 275000, 12, 'active'],
            ['aksesoris', 'Topi Baseball', 'Topi baseball bordir dengan strap belakang yang dapat diatur.', 65000, 35, 'active'],
            ['aksesoris', 'Dompet Kulit', 'Dompet kulit sintetis premium dengan banyak slot kartu.', 129000, 3, 'active'],
            ['aksesoris', 'Kacamata Hitam', 'Kacamata hitam lensa polarized dengan proteksi UV400.', 150000, 17, 'active'],
            ['rumah-tangga', 'Botol Minum Stainless', 'Botol minum 750 ml menjaga suhu dingin 24 jam dan panas 12 jam.', 85000, 30, 'active'],
            ['rumah-tangga', 'Lampu Meja LED', 'Lampu belajar LED dengan 3 tingkat kecerahan dan port USB.', 120000, 16, 'active'],
            ['rumah-tangga', 'Mug Keramik', 'Mug keramik 350 ml, aman untuk microwave dan mesin cuci piring.', 45000, 50, 'active'],
            ['rumah-tangga', 'Set Tempat Makan', 'Set tempat makan 3 sekat bebas BPA dengan penutup rapat.', 175000, 0, 'active'],
            ['elektronik', 'Earphone Kabel Lama', 'Produk lama yang sudah tidak dijual (contoh produk nonaktif).', 59000, 8, 'inactive'],
        ];

        $now = date('Y-m-d H:i:s');
        foreach ($products as [$catSlug, $name, $desc, $price, $stock, $status]) {
            $slug  = url_title($name, '-', true);
            $image = is_file(FCPATH . "uploads/products/seed-{$slug}.svg") ? "seed-{$slug}.svg" : null;

            $this->db->table('products')->insert([
                'category_id' => $cat[$catSlug],
                'name'        => $name,
                'slug'        => $slug,
                'description' => $desc,
                'price'       => $price,
                'stock'       => $stock,
                'image'       => $image,
                'status'      => $status,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }
    }
}
