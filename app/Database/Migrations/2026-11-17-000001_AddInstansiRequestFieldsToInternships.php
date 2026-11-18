<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddInstansiRequestFieldsToInternships extends Migration
{
    public function up()
    {
        $fields = [];
        if (!$this->db->fieldExists('instansi_pic_name', 'internships')) {
            $fields['instansi_pic_name'] = [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'company_user_id',
            ];
        }
        if (!$this->db->fieldExists('instansi_pic_email', 'internships')) {
            $fields['instansi_pic_email'] = [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'instansi_pic_name',
            ];
        }
        if (!$this->db->fieldExists('instansi_pic_phone', 'internships')) {
            $fields['instansi_pic_phone'] = [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
                'after'      => 'instansi_pic_email',
            ];
        }
        if (!$this->db->fieldExists('instansi_account_requested_at', 'internships')) {
            $fields['instansi_account_requested_at'] = [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'instansi_pic_phone',
            ];
        }

        if (!empty($fields)) {
            $this->forge->addColumn('internships', $fields);
        }
    }

    public function down()
    {
        $cols = ['instansi_pic_name', 'instansi_pic_email', 'instansi_pic_phone', 'instansi_account_requested_at'];
        foreach ($cols as $col) {
            if ($this->db->fieldExists($col, 'internships')) {
                $this->forge->dropColumn('internships', $col);
            }
        }
    }
}

