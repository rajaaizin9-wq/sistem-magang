<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInternshipSubmissionsTable extends Migration
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
            'assessment_component_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'internship_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'file_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'student_notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['SUBMITTED', 'APPROVED', 'REVISION_REQUIRED'],
                'default'    => 'SUBMITTED',
            ],
            'feedback_notes' => [
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
        $this->forge->addForeignKey('assessment_component_id', 'assessment_components', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('internship_id', 'internships', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('internship_submissions');
    }

    public function down()
    {
        $this->forge->dropTable('internship_submissions');
    }
}
