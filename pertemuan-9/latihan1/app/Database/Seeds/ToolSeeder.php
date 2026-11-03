<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ToolSeeder extends Seeder
{
    public function run(): void
    {
        helper('url');
        $categoryIds = array_column($this->db->table('categories')->get()->getResultArray(), 'id', 'name');

        $tools = [
            ['Multimeter Digital Sanwa CD800a', 'Alat Ukur Listrik', 8, 'good', 'Multimeter digital untuk mengukur tegangan AC/DC, arus, resistansi, kapasitansi, dan frekuensi. Dilengkapi probe standar.'],
            ['Osiloskop Digital Rigol DS1054Z', 'Alat Ukur Listrik', 3, 'good', 'Osiloskop 4 kanal 50 MHz untuk mengamati bentuk gelombang sinyal. Termasuk 4 probe pasif.'],
            ['LCR Meter DE-5000', 'Alat Ukur Listrik', 2, 'fair', 'Pengukur induktansi, kapasitansi, dan resistansi dengan frekuensi uji hingga 100 kHz.'],
            ['Arduino Uno R3', 'Mikrokontroler', 15, 'good', 'Papan mikrokontroler ATmega328P lengkap dengan kabel USB. Cocok untuk praktikum sistem tertanam dasar.'],
            ['ESP32 DevKit V1', 'Mikrokontroler', 12, 'good', 'Mikrokontroler dual-core dengan Wi-Fi dan Bluetooth untuk proyek Internet of Things.'],
            ['Raspberry Pi 4 Model B 4GB', 'Mikrokontroler', 5, 'good', 'Komputer papan tunggal lengkap dengan adaptor daya, kartu microSD 32 GB, dan casing.'],
            ['Kit Sensor 37 in 1', 'Sensor dan Modul', 6, 'good', 'Kumpulan 37 modul sensor: suhu, cahaya, suara, getaran, ultrasonik, dan lainnya.'],
            ['Sensor Ultrasonik HC-SR04', 'Sensor dan Modul', 20, 'good', 'Sensor jarak ultrasonik dengan jangkauan 2 cm sampai 4 m.'],
            ['Router MikroTik hAP ac2', 'Jaringan Komputer', 4, 'good', 'Router dual-band untuk praktikum konfigurasi jaringan, VLAN, dan firewall.'],
            ['Switch Cisco Catalyst 2960', 'Jaringan Komputer', 2, 'fair', 'Switch managed 24 port untuk praktikum VLAN dan spanning tree.'],
            ['Tang Crimping RJ45 dan Tester', 'Jaringan Komputer', 10, 'good', 'Paket tang crimping, LAN tester, dan konektor RJ45 untuk membuat kabel jaringan.'],
            ['Power Supply DC 30V 5A', 'Catu Daya', 6, 'good', 'Catu daya laboratorium dengan pengaturan tegangan dan arus serta tampilan digital.'],
            ['Solder Station Hakko FX-888D', 'Perkakas Tangan', 7, 'good', 'Stasiun solder dengan pengatur suhu digital, lengkap dengan dudukan dan spons.'],
            ['Bor Listrik Mini', 'Perkakas Tangan', 1, 'damaged', 'Bor mini untuk membuat lubang PCB. Kecepatan putar sedikit tidak stabil.'],
            ['Kamera Mirrorless Sony A6400', 'Multimedia', 2, 'good', 'Kamera mirrorless 24 MP dengan lensa kit 16-50 mm untuk dokumentasi kegiatan.'],
            ['Proyektor Epson EB-X500', 'Multimedia', 3, 'good', 'Proyektor XGA 3600 lumen untuk presentasi dan seminar.'],
            ['Kit Robot Line Follower', 'Robotika', 0, 'good', 'Paket robot pengikut garis lengkap dengan sasis, motor, driver, dan sensor garis.'],
            ['Lengan Robot 6 DOF', 'Robotika', 1, 'fair', 'Lengan robot enam derajat kebebasan dengan servo MG996R untuk praktikum kinematika.'],
            ['Set Lensa dan Bangku Optik', 'Optik dan Fisika', 4, 'good', 'Bangku optik dengan lensa cembung, cekung, dan sumber cahaya untuk percobaan pembiasan.'],
            ['Kacamata Pelindung dan Sarung Tangan', 'Keselamatan Kerja', 25, 'good', 'Perlengkapan keselamatan kerja wajib saat praktikum solder dan perkakas listrik.'],
        ];

        $rows = [];
        $base = strtotime('-180 days');

        foreach ($tools as $index => [$name, $category, $stock, $condition, $description]) {
            $timestamp = date('Y-m-d H:i:s', $base + $index * 8 * 86400);
            $rows[] = [
                'category_id' => $categoryIds[$category],
                'name'        => $name,
                'slug'        => url_title($name, '-', true),
                'stock'       => $stock,
                'condition'   => $condition,
                'photo'       => null,
                'description' => $description,
                'created_at'  => $timestamp,
                'updated_at'  => $timestamp,
            ];
        }

        $this->db->table('tools')->insertBatch($rows);
    }
}
