<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSupervisorAssignmentsTable extends Migration
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
            'lecturer_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'assigned_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['PENDING', 'ACCEPTED', 'REJECTED'],
                'default'    => 'PENDING',
            ],
            'rejection_reason' => [
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
        $this->forge->addForeignKey('lecturer_id', 'lecturers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('assigned_by', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('supervisor_assignments');
    }

    public function down()
    {
        $this->forge->dropTable('supervisor_assignments');
    }
}
