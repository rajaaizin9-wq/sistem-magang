<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateUserRoleToIncludeDosen extends Migration
{
    public function up()
    {
        // Modify ENUM on role column
        $this->db->query("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('mahasiswa', 'tu', 'kaprodi', 'dosen', 'wadek1', 'admin', 'dosen_pembimbing', 'dosen_mk') NOT NULL DEFAULT 'mahasiswa'");

        // Update existing lecturer accounts from 'dosen_pembimbing' or 'dosen_mk' to 'dosen'
        $this->db->query("UPDATE `users` SET `role` = 'dosen' WHERE `role` IN ('dosen_pembimbing', 'dosen_mk')");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('mahasiswa', 'tu', 'kaprodi', 'dosen_pembimbing', 'dosen_mk', 'wadek1', 'admin') NOT NULL DEFAULT 'mahasiswa'");
    }
}
