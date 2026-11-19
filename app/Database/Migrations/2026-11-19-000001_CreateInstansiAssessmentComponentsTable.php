<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInstansiAssessmentComponentsTable extends Migration
{
    public function up()
    {
        // 1. Buat tabel instansi_assessment_components jika belum ada
        if (!$this->db->tableExists('instansi_assessment_components')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'order_number' => [
                    'type'       => 'INT',
                    'constraint' => 5,
                    'default'    => 1,
                ],
                'name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                ],
                'description' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'is_active' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 1,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'deleted_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);

            $this->forge->addKey('id', true);
            $this->forge->createTable('instansi_assessment_components', true);
        }

        // 2. Tambah kolom scores_json pada tabel instansi_assessments jika belum ada
        if ($this->db->tableExists('instansi_assessments') && !$this->db->fieldExists('scores_json', 'instansi_assessments')) {
            $this->forge->addColumn('instansi_assessments', [
                'scores_json' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'evaluator_position',
                ],
            ]);
        }

        // 3. Masukkan 5 kriteria default jika tabel masih kosong
        $builder = $this->db->table('instansi_assessment_components');
        if ($builder->countAllResults() === 0) {
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

            $builder->insertBatch($defaults);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('instansi_assessment_components')) {
            $this->forge->dropTable('instansi_assessment_components', true);
        }

        if ($this->db->tableExists('instansi_assessments') && $this->db->fieldExists('scores_json', 'instansi_assessments')) {
            $this->forge->dropColumn('instansi_assessments', 'scores_json');
        }
    }
}

