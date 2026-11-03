<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LoanSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = array_column($this->db->table('users')->where('role', 'borrower')->get()->getResultArray(), 'id', 'username');
        $toolIds = array_column($this->db->table('tools')->get()->getResultArray(), 'id', 'slug');

        $loans = [
            ['5025231001', 'arduino-uno-r3', 2, -160, 7, 'returned', 'Praktikum Sistem Tertanam modul 1'],
            ['5025231002', 'osiloskop-digital-rigol-ds1054z', 1, -140, 3, 'returned', 'Pengukuran sinyal PWM untuk tugas akhir'],
            ['5025231003', 'router-mikrotik-hap-ac2', 1, -120, 5, 'returned', 'Praktikum Jaringan Komputer konfigurasi VLAN'],
            ['5025231004', 'kamera-mirrorless-sony-a6400', 1, -100, 2, 'rejected', 'Dokumentasi acara himpunan mahasiswa'],
            ['5025231005', 'esp32-devkit-v1', 3, -90, 10, 'returned', 'Proyek IoT pemantauan suhu ruang'],
            ['5025231006', 'multimeter-digital-sanwa-cd800a', 2, -70, 4, 'returned', 'Praktikum Rangkaian Listrik modul 2'],
            ['5025231007', 'raspberry-pi-4-model-b-4gb', 1, -55, 14, 'returned', 'Pengembangan server mini untuk lomba'],
            ['5025231008', 'solder-station-hakko-fx-888d', 1, -40, 3, 'returned', 'Perakitan PCB robot'],
            ['5025231009', 'proyektor-epson-eb-x500', 1, -25, 1, 'rejected', 'Seminar kelompok studi'],
            ['5025231001', 'sensor-ultrasonik-hc-sr04', 4, -20, 7, 'borrowed', 'Tugas besar robot penghindar halangan'],
            ['5025231002', 'esp32-devkit-v1', 2, -12, 10, 'borrowed', 'Penelitian sistem irigasi otomatis'],
            ['5025231010', 'power-supply-dc-30v-5a', 1, -6, 3, 'approved', 'Pengujian rangkaian penguat audio'],
            ['5025231011', 'kit-sensor-37-in-1', 1, -2, 5, 'pending', 'Eksplorasi sensor untuk mata kuliah Sistem Tertanam'],
            ['5025231012', 'lengan-robot-6-dof', 1, -1, 4, 'pending', 'Praktikum kinematika robot'],
            ['5025231003', 'arduino-uno-r3', 2, 0, 7, 'pending', 'Workshop Arduino untuk mahasiswa baru'],
        ];

        $rows = [];

        foreach ($loans as [$username, $slug, $quantity, $offset, $days, $status, $note]) {
            $createdAt = strtotime($offset . ' days 09:00');
            $borrowDate = date('Y-m-d', strtotime('+1 day', $createdAt));
            $returnDate = date('Y-m-d', strtotime('+' . $days . ' days', strtotime($borrowDate)));

            $rows[] = [
                'user_id'     => $userIds[$username],
                'tool_id'     => $toolIds[$slug],
                'quantity'    => $quantity,
                'borrow_date' => $borrowDate,
                'return_date' => $returnDate,
                'returned_at' => $status === 'returned' ? $returnDate . ' 15:00:00' : null,
                'status'      => $status,
                'note'        => $note,
                'created_at'  => date('Y-m-d H:i:s', $createdAt),
                'updated_at'  => date('Y-m-d H:i:s', $createdAt),
            ];
        }

        $this->db->table('loans')->insertBatch($rows);
    }
}
