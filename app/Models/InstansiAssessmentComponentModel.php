<?php

namespace App\Models;

use CodeIgniter\Model;

class InstansiAssessmentComponentModel extends Model
{
    protected $table            = 'instansi_assessment_components';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'order_number',
        'name',
        'description',
        'is_active',
    ];

    protected $useTimestamps = true;

    /**
     * Dapatkan semua komponen aktif diurutkan berdasarkan order_number
     */
    public function getActiveComponents(): array
    {
        return $this->where('is_active', 1)
                    ->orderBy('order_number', 'ASC')
                    ->orderBy('id', 'ASC')
                    ->findAll();
    }

    /**
     * Dapatkan semua komponen untuk manajemen admin
     */
    public function getAllComponents(): array
    {
        return $this->orderBy('order_number', 'ASC')
                    ->orderBy('id', 'ASC')
                    ->findAll();
    }

    /**
     * Kembalikan komponen ke 5 default
     */
    public function resetToDefault(): void
    {
        $this->db->table($this->table)->truncate();

        $now = date('Y-m-d H:i:s');
        $defaults = [
            [
                'order_number' => 1,
                'name'         => 'Aktifitas di Lapangan',
                'description'  => "1.1 Kehadiran Mahasiswa di Instansi/Perusahaan\n1.2 Tepat Waktu dalam Kehadiran (Disiplin)",
                'is_active'    => 1,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'order_number' => 2,
                'name'         => 'Kerajinan',
                'description'  => "2.1 Keterampilan yang Dimiliki Mahasiswa\n2.2 Penggunaan Keterampilan terhadap Kepentingan Instansi/Perusahaan",
                'is_active'    => 1,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'order_number' => 3,
                'name'         => 'Inisiatif',
                'description'  => "3.1 Kecepatan dan ketepatan menguasai materi/problem yang disajikan di instansi/perusahaan\n3.2 Tanggap terhadap permasalahan yang timbul dalam lingkungan KP Mahasiswa\n3.3 Penggunaan materi yang dikuasai untuk menyelesaikan pekerjaan",
                'is_active'    => 1,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'order_number' => 4,
                'name'         => 'Kemampuan Mengemukakan Ide',
                'description'  => "4.1 Beradaptasi terhadap lingkungan instansi/perusahaan\n4.2 Kesopanan dalam berbicara terhadap pegawai/karyawan dan pimpinan\n4.3 Tidak pasif",
                'is_active'    => 1,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'order_number' => 5,
                'name'         => 'Kemampuan Menganalisa Persoalan',
                'description'  => "5.1 Kemampuan Mengidentifikasi dan Memecahkan Masalah\n5.2 Kemampuan kerja sama",
                'is_active'    => 1,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
        ];

        $this->db->table($this->table)->insertBatch($defaults);
    }
}

