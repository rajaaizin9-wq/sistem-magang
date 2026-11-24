<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIsWadekToLecturersTable extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('is_wadek', 'lecturers')) {
            $this->forge->addColumn('lecturers', [
                'is_wadek' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0,
                    'after'      => 'phone',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('is_wadek', 'lecturers')) {
            $this->forge->dropColumn('lecturers', 'is_wadek');
        }
    }
}

