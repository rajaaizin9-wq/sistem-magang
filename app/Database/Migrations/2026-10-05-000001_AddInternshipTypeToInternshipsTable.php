<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddInternshipTypeToInternshipsTable extends Migration
{
    public function up()
    {
        $fields = [
            'internship_type' => [
                'type'       => 'ENUM',
                'constraint' => ['Online', 'Offline'],
                'default'    => 'Offline',
                'after'      => 'position',
            ],
        ];
        $this->forge->addColumn('internships', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('internships', 'internship_type');
    }
}
