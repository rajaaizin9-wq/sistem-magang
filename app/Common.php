<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

if (!function_exists('format_indo')) {
    /**
     * Format timestamp or date string to Indonesian formatted date.
     * Example outputs:
     * - format_indo('2026-10-07') => '07 Oktober 2026'
     * - format_indo('2026-10-07 14:30:00', true) => '07 Oktober 2026, 14:30 WIB'
     * - format_indo('2026-10-07', false, true) => '07 Okt 2026'
     *
     * @param string|int|null $date
     * @param bool $withTime
     * @param bool $shortMonth
     * @return string
     */
    function format_indo($date, bool $withTime = false, bool $shortMonth = false): string
    {
        if (empty($date) || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
            return '-';
        }

        $timestamp = is_numeric($date) ? (int)$date : strtotime($date);
        if (!$timestamp) {
            return '-';
        }

        $bulanLengkap = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $bulanPendek = [
            1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
            'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
        ];

        $day   = date('d', $timestamp);
        $month = (int)date('m', $timestamp);
        $year  = date('Y', $timestamp);

        $monthName = $shortMonth ? ($bulanPendek[$month] ?? '') : ($bulanLengkap[$month] ?? '');
        $formatted = "{$day} {$monthName} {$year}";

        if ($withTime) {
            $time = date('H:i', $timestamp);
            $formatted .= ", {$time} WIB";
        }

        return $formatted;
    }
}

if (!function_exists('tanggal_indo')) {
    /**
     * Alias for full Indonesian date
     */
    function tanggal_indo($date, bool $withTime = false): string
    {
        return format_indo($date, $withTime, false);
    }
}

if (!function_exists('send_notification')) {
    /**
     * Kirim notifikasi ke user tertentu
     */
    function send_notification(int $userId, string $title, string $message, ?string $link = null): bool
    {
        if ($userId <= 0) {
            return false;
        }

        $db = \Config\Database::connect();
        return (bool)$db->table('notifications')->insert([
            'user_id'    => $userId,
            'title'      => $title,
            'message'    => $message,
            'link'       => $link,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}

if (!function_exists('send_notification_to_role')) {
    /**
     * Kirim notifikasi massal ke seluruh user dengan role tertentu (opsional filter prodi)
     */
    function send_notification_to_role(string $role, string $title, string $message, ?string $link = null, ?int $programStudyId = null): int
    {
        $db = \Config\Database::connect();
        $builder = $db->table('users')->select('users.id')->where('users.role', $role)->where('users.is_active', 1);

        if ($programStudyId !== null && in_array($role, ['kaprodi', 'dosen'])) {
            $builder->join('lecturers', 'lecturers.user_id = users.id')->where('lecturers.program_study_id', $programStudyId);
        }

        $users = $builder->get()->getResultArray();
        $count = 0;
        foreach ($users as $u) {
            if (send_notification((int)$u['id'], $title, $message, $link)) {
                $count++;
            }
        }
        return $count;
    }
}

if (!function_exists('get_user_notifications')) {
    /**
     * Ambil daftar notifikasi untuk user yang sedang aktif
     */
    function get_user_notifications(int $userId, int $limit = 6): array
    {
        if ($userId <= 0) {
            return [];
        }

        $db = \Config\Database::connect();
        return $db->table('notifications')
                  ->where('user_id', $userId)
                  ->orderBy('id', 'DESC')
                  ->limit($limit)
                  ->get()
                  ->getResultArray();
    }
}

if (!function_exists('get_unread_notifications_count')) {
    /**
     * Hitung total notifikasi belum dibaca untuk user
     */
    function get_unread_notifications_count(int $userId): int
    {
        if ($userId <= 0) {
            return 0;
        }

        $db = \Config\Database::connect();
        return (int)$db->table('notifications')
                       ->where('user_id', $userId)
                       ->where('read_at IS NULL')
                       ->countAllResults();
    }
}

if (!function_exists('time_ago')) {
    /**
     * Format waktu relatif ringkas dan ramah UX
     */
    function time_ago($datetime): string
    {
        if (empty($datetime)) {
            return '-';
        }
        $time = is_numeric($datetime) ? (int)$datetime : strtotime($datetime);
        if (!$time) {
            return '-';
        }

        $diff = time() - $time;
        if ($diff < 60) {
            return 'Baru saja';
        }
        if ($diff < 3600) {
            return floor($diff / 60) . ' mnt lalu';
        }
        if ($diff < 86400) {
            return floor($diff / 3600) . ' jam lalu';
        }
        if ($diff < 604800) {
            return floor($diff / 86400) . ' hari lalu';
        }

        return format_indo($datetime, false, true);
    }
}
