<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDriveUrlToInternshipSubmissionsTable extends Migration
{
    public function up()
    {
        // Pastikan file_path nullable dan tambahkan drive_url
        $fields = [
            'file_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'drive_url' => [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'file_path',
            ],
        ];

        $this->forge->modifyColumn('internship_submissions', ['file_path' => $fields['file_path']]);
        if (!$this->db->fieldExists('drive_url', 'internship_submissions')) {
            $this->forge->addColumn('internship_submissions', ['drive_url' => $fields['drive_url']]);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('internship_submissions', 'drive_url');
    }
}

