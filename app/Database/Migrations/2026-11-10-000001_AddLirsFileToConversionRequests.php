<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLirsFileToConversionRequests extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('conversion_requests') && !$this->db->fieldExists('lirs_file', 'conversion_requests')) {
            $fields = [
                'lirs_file' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                    'after'      => 'notes',
                ],
            ];
            $this->forge->addColumn('conversion_requests', $fields);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('conversion_requests') && $this->db->fieldExists('lirs_file', 'conversion_requests')) {
            $this->forge->dropColumn('conversion_requests', 'lirs_file');
        }
    }
}

