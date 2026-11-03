<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        helper('url');
        $now = date('Y-m-d H:i:s');

        $categories = [
            ['Arabika Single Origin', 'Biji arabika dari satu daerah asal.'],
            ['Robusta', 'Kopi robusta dengan body tebal dan kafein tinggi.'],
            ['Liberika', 'Kopi liberika langka dengan aroma buah nangka.'],
            ['House Blend', 'Racikan khas untuk espresso dan kopi susu.'],
            ['Kopi Bubuk', 'Sudah digiling, siap seduh tubruk atau mesin.'],
            ['Drip Bag', 'Kopi saring sekali pakai, praktis di mana saja.'],
            ['Specialty Lot', 'Lot mikro dengan skor cupping di atas 85.'],
            ['Decaf', 'Kopi rendah kafein untuk diminum malam hari.'],
            ['Cold Brew', 'Konsentrat dan gilingan kasar untuk seduh dingin.'],
            ['Paket Hadiah', 'Paket sampler untuk hadiah dan hampers.'],
        ];

        $categoryRows = [];

        foreach ($categories as [$name, $description]) {
            $categoryRows[] = ['name' => $name, 'slug' => url_title($name, '-', true), 'description' => $description, 'created_at' => $now, 'updated_at' => $now];
        }

        $this->db->table('categories')->insertBatch($categoryRows);
        $categoryIds = array_column($this->db->table('categories')->get()->getResultArray(), 'id', 'name');

        $products = [
            ['Arabika Single Origin', 'Gayo Wine Process', 'Gayo, Aceh', 'natural', 'light', 200, 125000, 18, 'Fermentasi panjang menghasilkan rasa anggur merah, cokelat hitam, dan aftertaste manis.'],
            ['Arabika Single Origin', 'Kintamani Natural', 'Kintamani, Bali', 'natural', 'light', 200, 98000, 25, 'Ditanam berdampingan dengan jeruk. Catatan rasa jeruk, gula aren, dan madu.'],
            ['Arabika Single Origin', 'Toraja Sapan', 'Tana Toraja, Sulawesi Selatan', 'washed', 'medium', 250, 115000, 14, 'Body sedang dengan rasa rempah, karamel, dan sedikit herbal.'],
            ['Arabika Single Origin', 'Flores Bajawa', 'Bajawa, Nusa Tenggara Timur', 'washed', 'medium', 250, 105000, 20, 'Tumbuh di tanah vulkanik. Cokelat susu, kacang mete, dan keasaman lembut.'],
            ['Arabika Single Origin', 'Java Preanger', 'Pangalengan, Jawa Barat', 'honey', 'medium', 250, 99000, 30, 'Kopi bersejarah dari Priangan. Gula merah, apel hijau, dan teh hitam.'],
            ['Arabika Single Origin', 'Mandailing Giling Basah', 'Mandailing Natal, Sumatra Utara', 'wet_hulled', 'dark', 250, 92000, 22, 'Proses giling basah khas Sumatra. Earthy, tembakau, dan cokelat pekat.'],
            ['Arabika Single Origin', 'Papua Wamena', 'Lembah Baliem, Papua', 'washed', 'medium', 200, 135000, 4, 'Ditanam secara organik oleh petani Baliem. Floral, cokelat, dan bersih.'],
            ['Robusta', 'Robusta Temanggung', 'Temanggung, Jawa Tengah', 'natural', 'dark', 250, 65000, 40, 'Body tebal dengan rasa cokelat pahit dan kacang panggang.'],
            ['Robusta', 'Robusta Lampung Fine', 'Lampung Barat, Lampung', 'natural', 'dark', 500, 85000, 35, 'Robusta fine petik merah. Cocok untuk kopi susu gula aren.'],
            ['Robusta', 'Robusta Dampit', 'Dampit, Malang', 'honey', 'medium', 250, 62000, 28, 'Manis gula jawa dengan keasaman rendah.'],
            ['Liberika', 'Liberika Meranti', 'Kepulauan Meranti, Riau', 'natural', 'medium', 200, 110000, 9, 'Tumbuh di lahan gambut. Aroma nangka matang dan rasa manis buah.'],
            ['Liberika', 'Liberika Kayong', 'Kayong Utara, Kalimantan Barat', 'honey', 'medium', 200, 105000, 3, 'Profil unik dengan rasa buah tropis dan rempah.'],
            ['House Blend', 'Nusantara Espresso Blend', 'Gayo, Toraja, Temanggung', 'washed', 'dark', 1000, 245000, 15, 'Racikan 70 persen arabika dan 30 persen robusta untuk espresso dengan crema tebal.'],
            ['House Blend', 'Kopi Susu Blend', 'Flores, Lampung', 'natural', 'dark', 1000, 210000, 12, 'Dirancang untuk kopi susu gula aren. Cokelat, karamel, dan body tebal.'],
            ['Kopi Bubuk', 'Kopi Tubruk Aceh', 'Gayo, Aceh', 'wet_hulled', 'dark', 250, 55000, 50, 'Digiling halus untuk seduh tubruk tradisional.'],
            ['Kopi Bubuk', 'Bubuk Toraja Kalosi', 'Enrekang, Sulawesi Selatan', 'washed', 'medium', 250, 75000, 26, 'Gilingan medium cocok untuk french press dan mesin tetes.'],
            ['Drip Bag', 'Drip Bag Sampler Arabika', 'Gayo, Kintamani, Flores', 'washed', 'medium', 120, 69000, 45, 'Isi 10 sachet drip bag dari tiga daerah asal.'],
            ['Drip Bag', 'Drip Bag Java Preanger', 'Pangalengan, Jawa Barat', 'honey', 'medium', 120, 59000, 0, 'Isi 10 sachet. Seduh dengan 150 ml air 92 derajat.'],
            ['Specialty Lot', 'Gayo Anaerobic Micro Lot', 'Bener Meriah, Aceh', 'natural', 'light', 100, 155000, 6, 'Lot mikro skor 87. Stroberi, yoghurt, dan dark chocolate.'],
            ['Specialty Lot', 'Kerinci Red Honey', 'Kerinci, Jambi', 'honey', 'light', 100, 145000, 5, 'Skor cupping 86. Rasa ceri, gula tebu, dan bunga melati.'],
            ['Decaf', 'Decaf Sumatra', 'Aceh Tengah, Aceh', 'washed', 'medium', 250, 120000, 10, 'Diproses dengan metode air sehingga kafein tersisa kurang dari 3 persen.'],
            ['Cold Brew', 'Cold Brew Coarse Ground', 'Flores, Toraja', 'washed', 'medium', 500, 95000, 19, 'Gilingan kasar untuk seduh dingin 12 sampai 18 jam.'],
            ['Cold Brew', 'Konsentrat Cold Brew 1 Liter', 'Gayo, Aceh', 'washed', 'medium', 1000, 135000, 8, 'Konsentrat siap campur. Simpan di kulkas maksimal 7 hari.'],
            ['Paket Hadiah', 'Paket Hampers Nusantara', 'Aceh, Bali, Toraja, Flores', 'washed', 'medium', 800, 285000, 7, 'Empat kemasan 200 gram dalam kotak kayu beserta kartu ucapan.'],
        ];

        $rows = [];
        $base = strtotime('-200 days');

        foreach ($products as $index => [$category, $name, $origin, $process, $roast, $weight, $price, $stock, $description]) {
            $slug = url_title($name, '-', true);
            $timestamp = date('Y-m-d H:i:s', $base + $index * 7 * 86400);
            $rows[] = [
                'category_id' => $categoryIds[$category],
                'name'        => $name,
                'slug'        => $slug,
                'origin'      => $origin,
                'process'     => $process,
                'roast_level' => $roast,
                'weight_gram' => $weight,
                'price'       => $price,
                'stock'       => $stock,
                'photo'       => $this->copyPhoto($slug),
                'description' => $description,
                'is_active'   => 1,
                'created_at'  => $timestamp,
                'updated_at'  => $timestamp,
            ];
        }

        $this->db->table('products')->insertBatch($rows);
    }

    private function copyPhoto(string $slug): ?string
    {
        $source = __DIR__ . '/images/' . $slug . '.jpg';

        if (! is_file($source)) {
            return null;
        }

        $directory = FCPATH . 'uploads/products/';

        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $name = 'seed-' . $slug . '.jpg';
        copy($source, $directory . $name);

        return $name;
    }
}
