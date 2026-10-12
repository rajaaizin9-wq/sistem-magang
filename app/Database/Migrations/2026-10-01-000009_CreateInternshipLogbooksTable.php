<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInternshipLogbooksTable extends Migration
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
            'week_number' => [
                'type'       => 'INT',
                'constraint' => 3,
            ],
            'date' => [
                'type' => 'DATE',
            ],
            'activity_description' => [
                'type' => 'TEXT',
            ],
            'result' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'obstacle' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'attachment' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['DRAFT', 'SUBMITTED', 'VERIFIED', 'REVISION_REQUIRED'],
                'default'    => 'DRAFT',
            ],
            'lecturer_notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'verified_at' => [
                'type' => 'DATETIME',
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
        $this->forge->createTable('internship_logbooks');
    }

    public function down()
    {
        $this->forge->dropTable('internship_logbooks');
    }
}
