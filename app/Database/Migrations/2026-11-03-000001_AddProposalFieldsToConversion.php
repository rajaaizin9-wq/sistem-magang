<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProposalFieldsToConversion extends Migration
{
    public function up()
    {
        // 1. Tambah kolom proposal_template di tabel courses (untuk template kustom dari Dosen MK)
        if ($this->db->tableExists('courses')) {
            $fields = [
                'proposal_template' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                    'after'      => 'is_elective',
                ],
            ];
            $this->forge->addColumn('courses', $fields);
        }

        // 2. Tambah kolom proposal_file di tabel conversion_request_courses (untuk berkas proposal mahasiswa)
        if ($this->db->tableExists('conversion_request_courses')) {
            $fields = [
                'proposal_file' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                    'after'      => 'student_justification',
                ],
            ];
            $this->forge->addColumn('conversion_request_courses', $fields);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('courses') && $this->db->fieldExists('proposal_template', 'courses')) {
            $this->forge->dropColumn('courses', 'proposal_template');
        }

        if ($this->db->tableExists('conversion_request_courses') && $this->db->fieldExists('proposal_file', 'conversion_request_courses')) {
            $this->forge->dropColumn('conversion_request_courses', 'proposal_file');
        }
    }
}
