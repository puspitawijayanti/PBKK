<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        $messages = [
            ['Rina Marlina', 'rina@mahasiswa.ac.id', 'Jam buka laboratorium saat UAS', 'Apakah laboratorium tetap buka pada hari Sabtu selama minggu UAS?'],
            ['Taufik Hidayat', 'taufik@mahasiswa.ac.id', 'Penambahan stok ESP32', 'Mohon pertimbangkan penambahan ESP32 karena banyak kelompok IoT yang membutuhkan.'],
            ['Nadia Putri', 'nadia@mahasiswa.ac.id', 'Peminjaman untuk lomba', 'Bolehkah meminjam Raspberry Pi lebih dari 14 hari untuk persiapan lomba nasional?'],
            ['Yoga Pratama', 'yoga@mahasiswa.ac.id', 'Probe osiloskop rusak', 'Salah satu probe osiloskop yang saya kembalikan kemarin kabelnya agak longgar.'],
            ['Maya Sari', 'maya@mahasiswa.ac.id', 'Akun belum terdaftar', 'Saya mahasiswa pindahan dan belum memiliki akun LabLend. Bagaimana cara mendaftarnya?'],
            ['Rizky Ramadhan', 'rizky@mahasiswa.ac.id', 'Pelatihan solder', 'Apakah ada jadwal pelatihan solder untuk mahasiswa baru semester ini?'],
            ['Laras Ayu', 'laras@mahasiswa.ac.id', 'Kamera untuk dokumentasi', 'Apakah kamera bisa dipinjam untuk dokumentasi kegiatan himpunan?'],
            ['Bayu Aji', 'bayu@mahasiswa.ac.id', 'Konfirmasi pengembalian', 'Saya sudah mengembalikan multimeter, tetapi statusnya belum berubah.'],
            ['Citra Dewi', 'citra@mahasiswa.ac.id', 'Peminjaman kelompok', 'Apakah satu pengajuan bisa digunakan untuk seluruh anggota kelompok?'],
            ['Galih Prakoso', 'galih@mahasiswa.ac.id', 'Usulan alat baru', 'Mohon menambahkan printer 3D ke inventaris laboratorium untuk praktikum robotika.'],
        ];

        $rows = [];

        foreach ($messages as $index => [$name, $email, $subject, $body]) {
            $timestamp = date('Y-m-d H:i:s', strtotime('-' . (30 - $index * 3) . ' days 10:15'));
            $rows[] = [
                'name'       => $name,
                'email'      => $email,
                'subject'    => $subject,
                'body'       => $body,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        $this->db->table('messages')->insertBatch($rows);
    }
}
