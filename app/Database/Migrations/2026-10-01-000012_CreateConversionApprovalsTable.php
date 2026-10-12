<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateConversionApprovalsTable extends Migration
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
            'conversion_request_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'approver_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'role' => [
                'type'       => 'ENUM',
                'constraint' => ['dosen_mk', 'dosen_pembimbing', 'wadek1', 'kaprodi'],
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['APPROVED', 'REJECTED'],
                'default'    => 'APPROVED',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'approved_at' => [
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
        $this->forge->addForeignKey('conversion_request_id', 'conversion_requests', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('approver_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('conversion_approvals');
    }

    public function down()
    {
        $this->forge->dropTable('conversion_approvals');
    }
}
