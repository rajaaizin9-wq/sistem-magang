<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLecturersTable extends Migration
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
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'nidn' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'program_study_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
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
        $this->forge->addUniqueKey('user_id');
        $this->forge->addUniqueKey('nidn');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('program_study_id', 'program_studies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('lecturers');
    }

    public function down()
    {
        $this->forge->dropTable('lecturers');
    }
}
