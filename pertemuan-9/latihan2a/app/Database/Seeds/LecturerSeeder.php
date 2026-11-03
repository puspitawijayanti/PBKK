<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LecturerSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');
        $password = password_hash('dosen123', PASSWORD_DEFAULT);
        $programIds = array_column($this->db->table('study_programs')->get()->getResultArray(), 'id', 'code');

        $lecturers = [
            ['0012038501', 'Dr. Ahmad Fauzi, S.Kom., M.Kom.', 'IF', 'ahmad.fauzi@unr.ac.id', '081234500001'],
            ['0023078402', 'Rina Wulandari, S.T., M.T.', 'IF', 'rina.wulandari@unr.ac.id', '081234500002'],
            ['0005118803', 'Bambang Setiawan, S.Kom., M.Sc.', 'IF', 'bambang.setiawan@unr.ac.id', '081234500003'],
            ['0017029004', 'Dr. Lestari Handayani, S.Si., M.Kom.', 'IF', 'lestari.handayani@unr.ac.id', '081234500004'],
            ['0029088605', 'Yusuf Maulana, S.Kom., M.T.', 'IF', 'yusuf.maulana@unr.ac.id', '081234500005'],
            ['0010128706', 'Prof. Dr. Hartono Wibisono, M.Sc.', 'IF', 'hartono.wibisono@unr.ac.id', '081234500006'],
            ['0003069107', 'Nurul Hidayah, S.Kom., M.Cs.', 'IF', 'nurul.hidayah@unr.ac.id', '081234500007'],
            ['0014048908', 'Dewi Kartikasari, S.SI., M.MSI.', 'SI', 'dewi.kartikasari@unr.ac.id', '081234500008'],
            ['0026098309', 'Agus Prasetyo, S.Kom., M.BA.', 'SI', 'agus.prasetyo@unr.ac.id', '081234500009'],
            ['0008019210', 'Fitri Ramadhani, S.SI., M.Kom.', 'SI', 'fitri.ramadhani@unr.ac.id', '081234500010'],
            ['0019058211', 'Ir. Joko Purnomo, M.T.', 'TE', 'joko.purnomo@unr.ac.id', '081234500011'],
            ['0021108512', 'Dr. Sri Mulyani, M.Si.', 'MA', 'sri.mulyani@unr.ac.id', '081234500012'],
        ];

        foreach ($lecturers as [$number, $name, $programCode, $email, $phone]) {
            $this->db->table('users')->insert([
                'name'       => $name,
                'username'   => $number,
                'password'   => $password,
                'role'       => 'lecturer',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $this->db->table('lecturers')->insert([
                'user_id'          => $this->db->insertID(),
                'study_program_id' => $programIds[$programCode],
                'lecturer_number'  => $number,
                'email'            => $email,
                'phone'            => $phone,
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);
        }
    }
}
