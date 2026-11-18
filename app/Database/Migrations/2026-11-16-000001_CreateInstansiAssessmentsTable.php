<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInstansiAssessmentsTable extends Migration
{
    public function up()
    {
        // 1. Tambah kolom company_user_id pada tabel internships jika belum ada
        if (!$this->db->fieldExists('company_user_id', 'internships')) {
            $this->forge->addColumn('internships', [
                'company_user_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'student_id',
                ],
            ]);
            $this->db->query('ALTER TABLE `internships` ADD CONSTRAINT `fk_internships_company_user` FOREIGN KEY (`company_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE');
        }

        // 2. Buat tabel instansi_assessments
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
            'instansi_user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'evaluator_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'evaluator_position' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'score_field_activity' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0.00,
            ],
            'score_craftsmanship' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0.00,
            ],
            'score_initiative' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0.00,
            ],
            'score_communication' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0.00,
            ],
            'score_problem_solving' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0.00,
            ],
            'total_score' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0.00,
            ],
            'final_score' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0.00,
            ],
            'grade' => [
                'type'       => 'VARCHAR',
                'constraint' => 5,
                'null'       => true,
            ],
            'feedback_notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'assessment_date' => [
                'type' => 'DATE',
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
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('internship_id', 'internships', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('instansi_user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('instansi_assessments', true);
    }

    public function down()
    {
        $this->forge->dropTable('instansi_assessments', true);

        if ($this->db->fieldExists('company_user_id', 'internships')) {
            $this->db->query('ALTER TABLE `internships` DROP FOREIGN KEY `fk_internships_company_user`');
            $this->forge->dropColumn('internships', 'company_user_id');
        }
    }
}

