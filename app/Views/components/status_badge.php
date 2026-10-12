<?php
/**
 * Component: Status Badge
 * Reusable clean UI badge for workflow statuses with Indonesian labels
 */
$statusLabels = [
    'DRAFT'                => 'Draft',
    'SUBMITTED'            => 'Diajukan (Menunggu TU)',
    'ADMIN_REVIEW'         => 'Ditinjau TU',
    'REVISION_REQUIRED'    => 'Perlu Revisi',
    'ADMIN_APPROVED'       => 'Disetujui TU',
    'SUPERVISOR_ASSIGNED'  => 'Pembimbing Ditentukan: Menunggu Verifikasi Dosen Pembimbing',
    'INTERNSHIP_ACTIVE'    => 'Magang Aktif',
    'INTERNSHIP_COMPLETED' => 'Pelaksanaan Selesai',
    'REPORT_SUBMITTED'     => 'Laporan Dikirim',
    'ASSESSMENT'           => 'Tahap Penilaian',
    'COMPLETED'            => 'Magang Selesai',
    'PENDING'              => 'Menunggu Verifikasi',
    'APPROVED'             => 'Disetujui',
    'REJECTED'             => 'Ditolak',
    'VERIFIED'             => 'Terverifikasi',
    'ACCEPTED'             => 'Diterima Dosen MK',
    'LECTURER_REVIEW'      => 'Verifikasi Dosen MK',
    'SUPERVISOR_REVIEW'    => 'Verifikasi Pembimbing',
    'VICE_DEAN_REVIEW'     => 'Verifikasi Wadek 1',
    'NOT_REQUIRED'         => 'Tidak Diperlukan',
    'SCHEDULED'            => 'Dijadwalkan',
];

$statusColors = [
    'DRAFT'                => 'bg-slate-100 text-slate-700 border-slate-300',
    'SUBMITTED'            => 'bg-blue-50 text-blue-700 border-blue-200',
    'ADMIN_REVIEW'         => 'bg-amber-50 text-amber-700 border-amber-200',
    'REVISION_REQUIRED'    => 'bg-amber-100 text-amber-800 border-amber-300',
    'ADMIN_APPROVED'       => 'bg-emerald-50 text-emerald-700 border-emerald-200',
    'SUPERVISOR_ASSIGNED'  => 'bg-cyan-50 text-cyan-700 border-cyan-200',
    'INTERNSHIP_ACTIVE'    => 'bg-emerald-100 text-emerald-800 border-emerald-300',
    'INTERNSHIP_COMPLETED' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
    'REPORT_SUBMITTED'     => 'bg-purple-50 text-purple-700 border-purple-200',
    'ASSESSMENT'           => 'bg-blue-100 text-blue-800 border-blue-300',
    'COMPLETED'            => 'bg-emerald-600 text-white border-emerald-600',
    'PENDING'              => 'bg-amber-50 text-amber-700 border-amber-200',
    'ACCEPTED'             => 'bg-emerald-50 text-emerald-700 border-emerald-200',
    'APPROVED'             => 'bg-emerald-50 text-emerald-700 border-emerald-200',
    'REJECTED'             => 'bg-red-50 text-red-700 border-red-200',
    'VERIFIED'             => 'bg-emerald-50 text-emerald-700 border-emerald-200',
    'LECTURER_REVIEW'      => 'bg-indigo-50 text-indigo-700 border-indigo-200',
    'SUPERVISOR_REVIEW'    => 'bg-sky-50 text-sky-700 border-sky-200',
    'VICE_DEAN_REVIEW'     => 'bg-purple-50 text-purple-700 border-purple-200',
];

$label      = $statusLabels[$status] ?? str_replace('_', ' ', $status);
$badgeClass = $statusColors[$status] ?? 'bg-slate-100 text-slate-700 border-slate-200';
?>
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border <?= $badgeClass ?>">
    <?= esc($label) ?>
</span>
