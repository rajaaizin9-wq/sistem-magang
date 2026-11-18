<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateInternshipSubmissionsForSingleDriveLink extends Migration
{
    public function up()
    {
        // 1. Modifikasi assessment_component_id agar nullable
        $this->forge->modifyColumn('internship_submissions', [
            'assessment_component_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
        ]);

        // 2. Tambah kolom course_id jika belum ada
        if (!$this->db->fieldExists('course_id', 'internship_submissions')) {
            $this->forge->addColumn('internship_submissions', [
                'course_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'assessment_component_id',
                ],
            ]);

            // Foreign key
            $this->db->query("ALTER TABLE `internship_submissions` ADD CONSTRAINT `fk_sub_course_id` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");
        }
    }

    public function down()
    {
        $this->db->query("ALTER TABLE `internship_submissions` DROP FOREIGN KEY `fk_sub_course_id`");
        $this->forge->dropColumn('internship_submissions', 'course_id');
    }
}

