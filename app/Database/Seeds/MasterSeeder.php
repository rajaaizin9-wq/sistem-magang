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

        $ifProdi   = $this->db->table('program_studies')->where('code', 'IF')->get()->getRowArray();
        $ifProdiId = $ifProdi['id'];

        // 2. Users (Password: password123)
        $hashedPassword = password_hash('password123', PASSWORD_BCRYPT);
        
        $usersData = [
            // Admin & TU
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
                'name'       => 'Siti Aminah (Staf TU)',
                'email'      => 'tu@simagang.ac.id',
                'password'   => $hashedPassword,
                'role'       => 'tu',
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Mahasiswa (Sesuai LIRS)
            [
                'name'       => 'Raja Aizin Nofrizivaldy',
                'email'      => 'mahasiswa@simagang.ac.id',
                'password'   => $hashedPassword,
                'role'       => 'mahasiswa',
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Kaprodi (Koordinator Prodi IF di LIRS)
            [
                'name'       => 'Feri Irawan, S.Kom., M.Kom.',
                'email'      => 'kaprodi@simagang.ac.id',
                'password'   => $hashedPassword,
                'role'       => 'kaprodi',
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Dosen Pembimbing / Penasehat Akademik (Sesuai LIRS)
            [
                'name'       => 'Ferdi Chahyadi, S.Kom., M.Cs.',
                'email'      => 'dosbem@simagang.ac.id',
                'password'   => $hashedPassword,
                'role'       => 'dosen',
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Dosen Ketua Data Sains (Sesuai LIRS)
            [
                'name'       => 'Nurfalinda, S.Kom., M.Cs.',
                'email'      => 'dosenmk@simagang.ac.id',
                'password'   => $hashedPassword,
                'role'       => 'dosen',
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Dosen Ketua Teknologi IoT (Sesuai LIRS)
            [
                'name'       => 'Berta Erwin Slam, S.Kom., M.Cs.',
                'email'      => 'berta@simagang.ac.id',
                'password'   => $hashedPassword,
                'role'       => 'dosen',
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Dosen Ketua Proyek Pengembangan Sistem & KP (Sesuai LIRS)
            [
                'name'       => 'Rifaldi Herikson, S.Kom., M.Kom.',
                'email'      => 'rifaldi@simagang.ac.id',
                'password'   => $hashedPassword,
                'role'       => 'dosen',
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Wadek 1 (Dosen dengan penugasan Wadek 1)
            [
                'name'       => 'Prof. Dr. Ir. Bambang (Wadek 1)',
                'email'      => 'wadek1@simagang.ac.id',
                'password'   => $hashedPassword,
                'role'       => 'dosen',
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

        // Helper get user ID by email
        $getUser = fn($email) => $this->db->table('users')->where('email', $email)->get()->getRowArray()['id'];

        // 3. Students (Sesuai LIRS: NIM 2301020027, Tahun Masuk 2023)
        $mhsUserId       = $getUser('mahasiswa@simagang.ac.id');
        $existingStudent = $this->db->table('students')->where('user_id', $mhsUserId)->get()->getRowArray();
        if ($existingStudent) {
            $this->db->table('students')->where('id', $existingStudent['id'])->update([
                'nim'              => '2301020027',
                'program_study_id' => $ifProdiId,
                'phone'            => '081234567890',
                'address'          => 'Jl. Politeknik Senggarang KM 24, Tanjungpinang',
                'updated_at'       => date('Y-m-d H:i:s'),
            ]);
            $studentId = $existingStudent['id'];
        } else {
            $this->db->table('students')->insert([
                'user_id'          => $mhsUserId,
                'nim'              => '2301020027',
                'program_study_id' => $ifProdiId,
                'phone'            => '081234567890',
                'address'          => 'Jl. Politeknik Senggarang KM 24, Tanjungpinang',
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ]);
            $studentId = $this->db->insertID();
        }

        // 4. Lecturers (Data NIDN Dosen Sesuai LIRS)
        $lecturersData = [
            // Feri Irawan (Kaprodi)
            [
                'email' => 'kaprodi@simagang.ac.id',
                'nidn'  => '0020108501',
                'phone' => '08111111111',
            ],
            // Ferdi Chahyadi (Dospem / PA)
            [
                'email' => 'dosbem@simagang.ac.id',
                'nidn'  => '0025088202',
                'phone' => '08222222222',
            ],
            // Nurfalinda (Dosen Ketua Data Sains)
            [
                'email' => 'dosenmk@simagang.ac.id',
                'nidn'  => '0012058903',
                'phone' => '08333333333',
            ],
            // Berta Erwin Slam (Dosen Ketua IoT)
            [
                'email' => 'berta@simagang.ac.id',
                'nidn'  => '0018098204',
                'phone' => '08444444444',
            ],
            // Rifaldi Herikson (Dosen Ketua PPS & KP)
            [
                'email' => 'rifaldi@simagang.ac.id',
                'nidn'  => '0015039005',
                'phone' => '08555555555',
            ],
            // Prof. Dr. Ir. Bambang (Wadek 1)
            [
                'email'    => 'wadek1@simagang.ac.id',
                'nidn'     => '0010017001',
                'phone'    => '08666666666',
                'is_wadek' => 1,
            ],
        ];

        foreach ($lecturersData as $ld) {
            $uId      = $getUser($ld['email']);
            $existing = $this->db->table('lecturers')->where('user_id', $uId)->get()->getRowArray();
            $isWadek  = $ld['is_wadek'] ?? 0;
            if ($existing) {
                $this->db->table('lecturers')->where('id', $existing['id'])->update([
                    'nidn'             => $ld['nidn'],
                    'program_study_id' => $ifProdiId,
                    'phone'            => $ld['phone'],
                    'is_wadek'         => $isWadek,
                    'updated_at'       => date('Y-m-d H:i:s'),
                ]);
            } else {
                $this->db->table('lecturers')->insert([
                    'user_id'          => $uId,
                    'nidn'             => $ld['nidn'],
                    'program_study_id' => $ifProdiId,
                    'phone'            => $ld['phone'],
                    'is_wadek'         => $isWadek,
                    'created_at'       => date('Y-m-d H:i:s'),
                    'updated_at'       => date('Y-m-d H:i:s'),
                ]);
            }
        }

        // Ambil ID profil Dosen untuk mapping ke mata kuliah
        $getLecId = fn($email) => $this->db->table('lecturers')->where('user_id', $getUser($email))->get()->getRowArray()['id'];
        $nurfalindaId = $getLecId('dosenmk@simagang.ac.id'); // Nurfalinda
        $rifaldiId    = $getLecId('rifaldi@simagang.ac.id'); // Rifaldi Herikson
        $bertaId      = $getLecId('berta@simagang.ac.id');   // Berta Erwin Slam
        $ferdiId      = $getLecId('dosbem@simagang.ac.id');  // Ferdi Chahyadi

        // 5. Courses (Sesuai Persis dengan Data LIRS)
        // Dosen pengampu yang ditentukan adalah Dosen Paling Atas (Dosen Ketua)
        $courses = [
            [
                'code'             => 'INF11020',
                'name'             => 'Data Sains',
                'credits'          => 3,
                'program_study_id' => $ifProdiId,
                'lecturer_id'      => $nurfalindaId, // Dosen Ketua: Nurfalinda
                'is_elective'      => 1,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ],
            [
                'code'             => 'INF11029',
                'name'             => 'Proyek Pengembangan Sistem',
                'credits'          => 3,
                'program_study_id' => $ifProdiId,
                'lecturer_id'      => $rifaldiId,    // Dosen Ketua: Rifaldi Herikson
                'is_elective'      => 1,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ],
            [
                'code'             => 'INF11033',
                'name'             => 'Teknologi IoT',
                'credits'          => 3,
                'program_study_id' => $ifProdiId,
                'lecturer_id'      => $bertaId,      // Dosen Ketua: Berta Erwin Slam
                'is_elective'      => 1,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ],
            [
                'code'             => 'INF12008',
                'name'             => 'Kerja Praktik',
                'credits'          => 3,
                'program_study_id' => $ifProdiId,
                'lecturer_id'      => $rifaldiId,    // Dosen Ketua: Rifaldi Herikson
                'is_elective'      => 0,             // MK Wajib
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

        // 6. Periode Magang Aktif untuk Mahasiswa (Agar langsung siap mencoba Konversi LIRS)
        $existingInternship = $this->db->table('internships')->where('student_id', $studentId)->get()->getRowArray();
        if ($existingInternship) {
            $this->db->table('internships')->where('id', $existingInternship['id'])->update([
                'company_name'    => 'PT Telkom Indonesia (Witel Kepulauan Riau)',
                'company_address' => 'Jl. Basuki Rahmat No. 1, Kota Tanjungpinang',
                'position'        => 'Software Engineer & IoT Intern',
                'internship_type' => 'Offline',
                'start_date'      => '2026-09-01',
                'end_date'        => '2026-12-31',
                'academic_year'   => '2026/2027',
                'semester'        => 'Ganjil',
                'status'          => 'INTERNSHIP_ACTIVE',
                'updated_at'      => date('Y-m-d H:i:s'),
            ]);
            $internshipId = $existingInternship['id'];
        } else {
            $this->db->table('internships')->insert([
                'student_id'      => $studentId,
                'company_name'    => 'PT Telkom Indonesia (Witel Kepulauan Riau)',
                'company_address' => 'Jl. Basuki Rahmat No. 1, Kota Tanjungpinang',
                'position'        => 'Software Engineer & IoT Intern',
                'internship_type' => 'Offline',
                'start_date'      => '2026-09-01',
                'end_date'        => '2026-12-31',
                'academic_year'   => '2026/2027',
                'semester'        => 'Ganjil',
                'status'          => 'INTERNSHIP_ACTIVE',
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ]);
            $internshipId = $this->db->insertID();
        }

        // Penugasan Dosen Pembimbing (Ferdi Chahyadi)
        $existingSpv = $this->db->table('supervisor_assignments')->where('internship_id', $internshipId)->get()->getRowArray();
        if ($existingSpv) {
            $this->db->table('supervisor_assignments')->where('id', $existingSpv['id'])->update([
                'lecturer_id' => $ferdiId,
                'status'      => 'ACCEPTED',
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);
        } else {
            $this->db->table('supervisor_assignments')->insert([
                'internship_id' => $internshipId,
                'lecturer_id'   => $ferdiId,
                'assigned_by'   => $getUser('kaprodi@simagang.ac.id'),
                'status'        => 'ACCEPTED',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
        }

        // 7. Assessment Components (Komponen Pengumpulan Contoh untuk MK INF11020 Data Sains)
        $dsCourse = $this->db->table('courses')->where('code', 'INF11020')->get()->getRowArray();
        if ($dsCourse) {
            $existingComp = $this->db->table('assessment_components')->where('course_id', $dsCourse['id'])->countAllResults();
            if ($existingComp === 0) {
                $this->db->table('assessment_components')->insertBatch([
                    [
                        'course_id'         => $dsCourse['id'],
                        'title'             => 'Laporan Akhir Analisis Data & Metodologi',
                        'description'       => "Unggah berkas dokumen Laporan Akhir Magang (format PDF) yang memuat tahapan perumusan masalah, metodologi pengumpulan dataset, preprocessing, eksplorasi data (EDA), dan evaluasi algoritma Data Science sesuai aktivitas magang.",
                        'weight_percentage' => 40.00,
                        'max_score'         => 100.00,
                        'created_at'        => date('Y-m-d H:i:s'),
                        'updated_at'        => date('Y-m-d H:i:s'),
                    ],
                    [
                        'course_id'         => $dsCourse['id'],
                        'title'             => 'Repositori Source Code & Demo Model / Dashboard',
                        'description'       => "Sertakan tautan folder Google Drive atau file README/link repositori GitHub yang memuat script Python / Jupyter Notebook (.ipynb), link dashboard demo, serta petunjuk cara menjalankan model data science.",
                        'weight_percentage' => 40.00,
                        'max_score'         => 100.00,
                        'created_at'        => date('Y-m-d H:i:s'),
                        'updated_at'        => date('Y-m-d H:i:s'),
                    ],
                    [
                        'course_id'         => $dsCourse['id'],
                        'title'             => 'Presentasi & Surat Keterangan / Evaluasi Instansi',
                        'description'       => "Unggah file slide presentasi hasil magang (.pptx / .pdf) serta scan Surat Keterangan Selesai Magang dan Lembar Evaluasi dari mentor lapangan instansi.",
                        'weight_percentage' => 20.00,
                        'max_score'         => 100.00,
                        'created_at'        => date('Y-m-d H:i:s'),
                        'updated_at'        => date('Y-m-d H:i:s'),
                    ],
                ]);
            }
        }
    }
}
