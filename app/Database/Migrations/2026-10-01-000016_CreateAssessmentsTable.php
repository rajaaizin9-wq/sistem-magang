<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAssessmentsTable extends Migration
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
            'internship_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'course_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'assessor_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'assessment_component_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'score' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0.00,
            ],
            'final_grade' => [
                'type'       => 'VARCHAR',
                'constraint' => 5, // 'A', 'B+', 'B', dll
                'null'       => true,
            ],
            'verified_status' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->addForeignKey('internship_id', 'internships', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('course_id', 'courses', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('assessor_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('assessment_component_id', 'assessment_components', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('assessments');
    }

    public function down()
    {
        $this->forge->dropTable('assessments');
    }
}
