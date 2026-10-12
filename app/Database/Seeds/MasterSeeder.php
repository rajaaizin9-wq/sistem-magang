<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MasterSeeder extends Seeder
{
    public function run()
    {
        // 1. Program Studies
        $prodiData = [
            [
                'code'       => 'IF',
                'name'       => 'Teknik Informatika',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'code'       => 'SI',
                'name'       => 'Sistem Informasi',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($prodiData as $prodi) {
            $existing = $this->db->table('program_studies')->where('code', $prodi['code'])->get()->getRowArray();
            if (!$existing) {
                $this->db->table('program_studies')->insert($prodi);
            }
        }

        $ifProdi = $this->db->table('program_studies')->where('code', 'IF')->get()->getRowArray();
        $ifProdiId = $ifProdi['id'];

        // 2. Users (Password: password123)
        $hashedPassword = password_hash('password123', PASSWORD_BCRYPT);
        
        $usersData = [
            [
                'name'       => 'Administrator System',
                'email'      => 'admin@simagang.ac.id',
                'password'   => $hashedPassword,
                'role'       => 'admin',
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Budi Santoso (Mahasiswa)',
                'email'      => 'mahasiswa@simagang.ac.id',
                'password'   => $hashedPassword,
                'role'       => 'mahasiswa',
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Siti Aminah (Staf TU)',
                'email'      => 'tu@simagang.ac.id',
                'password'   => $hashedPassword,
                'role'       => 'tu',
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Dr. Hendra Wijaya (Kaprodi IF)',
                'email'      => 'kaprodi@simagang.ac.id',
                'password'   => $hashedPassword,
                'role'       => 'kaprodi',
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Ahmad Fauzi, M.T. (Dosen)',
                'email'      => 'dosbem@simagang.ac.id',
                'password'   => $hashedPassword,
                'role'       => 'dosen',
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Rina Wulandari, M.Kom. (Dosen)',
                'email'      => 'dosenmk@simagang.ac.id',
                'password'   => $hashedPassword,
                'role'       => 'dosen',
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Prof. Dr. Ir. Bambang (Wadek 1)',
                'email'      => 'wadek1@simagang.ac.id',
                'password'   => $hashedPassword,
                'role'       => 'wadek1',
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($usersData as $user) {
            $existing = $this->db->table('users')->where('email', $user['email'])->get()->getRowArray();
            if ($existing) {
                $this->db->table('users')->where('id', $existing['id'])->update([
                    'name'       => $user['name'],
                    'role'       => $user['role'],
                    'password'   => $user['password'],
                    'is_active'  => $user['is_active'],
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            } else {
                $this->db->table('users')->insert($user);
            }
        }

        // Get user IDs
        $getUser = fn($email) => $this->db->table('users')->where('email', $email)->get()->getRowArray()['id'];

        // 3. Students
        $mhsUserId = $getUser('mahasiswa@simagang.ac.id');
        $existingStudent = $this->db->table('students')->where('user_id', $mhsUserId)->get()->getRowArray();
        if (!$existingStudent) {
            $this->db->table('students')->insert([
                'user_id'          => $mhsUserId,
                'nim'              => '220101001',
                'program_study_id' => $ifProdiId,
                'phone'            => '081234567890',
                'address'          => 'Jl. Merdeka No. 45, Kota Bandung',
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ]);
        }

        // 4. Lecturers
        $kaprodiUserId = $getUser('kaprodi@simagang.ac.id');
        $dosbemUserId  = $getUser('dosbem@simagang.ac.id');
        $dosenMkUserId = $getUser('dosenmk@simagang.ac.id');

        $lecturers = [
            [
                'user_id'          => $kaprodiUserId,
                'nidn'             => '0011223301',
                'program_study_id' => $ifProdiId,
                'phone'            => '08111111111',
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ],
            [
                'user_id'          => $dosbemUserId,
                'nidn'             => '0044556602',
                'program_study_id' => $ifProdiId,
                'phone'            => '08222222222',
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ],
            [
                'user_id'          => $dosenMkUserId,
                'nidn'             => '0077889903',
                'program_study_id' => $ifProdiId,
                'phone'            => '08333333333',
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($lecturers as $lec) {
            $existing = $this->db->table('lecturers')->where('user_id', $lec['user_id'])->get()->getRowArray();
            if ($existing) {
                $this->db->table('lecturers')->where('id', $existing['id'])->update([
                    'nidn'             => $lec['nidn'],
                    'program_study_id' => $lec['program_study_id'],
                    'phone'            => $lec['phone'],
                    'updated_at'       => date('Y-m-d H:i:s'),
                ]);
            } else {
                $this->db->table('lecturers')->insert($lec);
            }
        }

        $dosbemRow  = $this->db->table('lecturers')->where('user_id', $dosbemUserId)->get()->getRowArray();
        $dosbemId   = $dosbemRow['id'];

        $dosenMkRow = $this->db->table('lecturers')->where('user_id', $dosenMkUserId)->get()->getRowArray();
        $dosenMkId  = $dosenMkRow['id'];

        // 5. Elective Courses (MK Pilihan) - Diampu oleh Dosen
        $courses = [
            [
                'code'             => 'IF401',
                'name'             => 'Pengembangan Aplikasi Web Lanjut',
                'credits'          => 3,
                'program_study_id' => $ifProdiId,
                'lecturer_id'      => $dosbemId, // Ahmad Fauzi
                'is_elective'      => 1,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ],
            [
                'code'             => 'IF402',
                'name'             => 'Manajemen Proyek Perangkat Lunak',
                'credits'          => 3,
                'program_study_id' => $ifProdiId,
                'lecturer_id'      => $dosbemId, // Ahmad Fauzi
                'is_elective'      => 1,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ],
            [
                'code'             => 'IF403',
                'name'             => 'Cloud Computing & DevOps',
                'credits'          => 3,
                'program_study_id' => $ifProdiId,
                'lecturer_id'      => $dosenMkId, // Rina Wulandari
                'is_elective'      => 1,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ],
            [
                'code'             => 'IF404',
                'name'             => 'Kecerdasan Buatan & Data Mining',
                'credits'          => 3,
                'program_study_id' => $ifProdiId,
                'lecturer_id'      => $dosenMkId, // Rina Wulandari
                'is_elective'      => 1,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($courses as $c) {
            $existing = $this->db->table('courses')->where('code', $c['code'])->get()->getRowArray();
            if ($existing) {
                $this->db->table('courses')->where('id', $existing['id'])->update([
                    'name'             => $c['name'],
                    'credits'          => $c['credits'],
                    'program_study_id' => $c['program_study_id'],
                    'lecturer_id'      => $c['lecturer_id'],
                    'is_elective'      => $c['is_elective'],
                    'updated_at'       => date('Y-m-d H:i:s'),
                ]);
            } else {
                $this->db->table('courses')->insert($c);
            }
        }
    }
}
