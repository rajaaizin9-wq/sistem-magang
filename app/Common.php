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
