<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAssessmentComponentsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'course_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 150, // Misal 'Laporan Akhir', 'Source Code Proyek', 'Sertifikat'
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'weight_percentage' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0.00,
            ],
            'max_score' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 100.00,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('course_id', 'courses', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('assessment_components');
    }

    public function down()
    {
        $this->forge->dropTable('assessment_components');
    }
}
