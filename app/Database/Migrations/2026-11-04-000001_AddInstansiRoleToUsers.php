<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddInstansiRoleToUsers extends Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('mahasiswa', 'tu', 'kaprodi', 'dosen', 'wadek1', 'admin', 'dosen_pembimbing', 'dosen_mk', 'instansi') NOT NULL DEFAULT 'mahasiswa'");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('mahasiswa', 'tu', 'kaprodi', 'dosen', 'wadek1', 'admin', 'dosen_pembimbing', 'dosen_mk') NOT NULL DEFAULT 'mahasiswa'");
    }
}

