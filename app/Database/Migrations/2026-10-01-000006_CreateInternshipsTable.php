<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInternshipsTable extends Migration
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
            'student_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'company_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'company_address' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'position' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'internship_type' => [
                'type'       => 'ENUM',
                'constraint' => ['Online', 'Offline'],
                'default'    => 'Offline',
            ],
            'start_date' => [
                'type' => 'DATE',
            ],
            'end_date' => [
                'type' => 'DATE',
            ],
            'academic_year' => [
                'type'       => 'VARCHAR',
                'constraint' => 20, // Misal '2025/2026'
            ],
            'semester' => [
                'type'       => 'ENUM',
                'constraint' => ['Ganjil', 'Genap'],
                'default'    => 'Ganjil',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => [
                    'DRAFT',
                    'SUBMITTED',
                    'ADMIN_REVIEW',
                    'REVISION_REQUIRED',
                    'ADMIN_APPROVED',
                    'SUPERVISOR_ASSIGNED',
                    'INTERNSHIP_ACTIVE',
                    'INTERNSHIP_COMPLETED',
                    'REPORT_SUBMITTED',
                    'ASSESSMENT',
                    'COMPLETED'
                ],
                'default' => 'DRAFT',
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
        $this->forge->addForeignKey('student_id', 'students', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('internships');
    }

    public function down()
    {
        $this->forge->dropTable('internships');
    }
}
