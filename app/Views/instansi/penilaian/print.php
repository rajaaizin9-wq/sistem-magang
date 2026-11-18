<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background-color: #f8fafc;
            margin: 0;
            padding: 20px;
            font-size: 12pt;
            line-height: 1.4;
        }

        .page-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 40px 50px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
        }

        .no-print {
            max-width: 800px;
            margin: 0 auto 15px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            font-family: sans-serif;
            font-size: 13px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            border: 1px solid transparent;
        }

        .btn-primary {
            background-color: #2563eb;
            color: #fff;
        }
        .btn-primary:hover {
            background-color: #1d4ed8;
        }

        .btn-secondary {
            background-color: #fff;
            color: #475569;
            border-color: #cbd5e1;
        }
        .btn-secondary:hover {
            background-color: #f1f5f9;
        }

        .header-title {
            text-align: center;
            margin-bottom: 25px;
        }
        .header-title h2 {
            margin: 0;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header-title h3 {
            margin: 4px 0 0 0;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
            font-size: 11pt;
        }
        .info-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .info-table td.label {
            width: 220px;
        }
        .info-table td.colon {
            width: 15px;
        }

        table.evaluation-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            font-size: 11pt;
        }
        table.evaluation-table th, 
        table.evaluation-table td {
            border: 1px solid #000;
            padding: 6px 10px;
        }
        table.evaluation-table th {
            text-align: center;
            font-weight: bold;
        }

        .sub-criteria {
            margin: 4px 0 0 15px;
            padding: 0;
            list-style-type: none;
            font-size: 10pt;
        }
        .sub-criteria li {
            margin-bottom: 2px;
        }

        .notes-box {
            margin-top: 15px;
            margin-bottom: 30px;
            font-size: 11pt;
        }
        .notes-box-content {
            border: 1px solid #94a3b8;
            min-height: 80px;
            padding: 10px;
            margin-top: 8px;
            white-space: pre-line;
            border-radius: 4px;
        }

        .signature-section {
            width: 100%;
            margin-top: 30px;
            display: flex;
            justify-content: flex-end;
            font-size: 11pt;
        }
        .signature-box {
            width: 280px;
            text-align: center;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .page-container {
                box-shadow: none;
                border: none;
                padding: 0;
                margin: 0;
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <a href="<?= base_url('instansi/penilaian?mahasiswa=' . $internship['id']) ?>" class="btn btn-secondary">&larr; Kembali ke Form Penilaian</a>
        <button onclick="window.print()" class="btn btn-primary">
            🖨️ Cetak / Simpan PDF
        </button>
    </div>

    <div class="page-container">
        <div class="header-title">
            <h2>Lembar Penilaian Praktik Kerja / Magang</h2>
            <h3>Pembimbing di Instansi / Perusahaan</h3>
        </div>

        <table class="info-table">
            <tr>
                <td class="label">Nama Mahasiswa</td>
                <td class="colon">:</td>
                <td><strong><?= esc($internship['student_name']) ?></strong></td>
            </tr>
            <tr>
                <td class="label">NIM</td>
                <td class="colon">:</td>
                <td><?= esc($internship['nim']) ?></td>
            </tr>
            <tr>
                <td class="label">Program Studi</td>
                <td class="colon">:</td>
                <td><?= esc($internship['prodi_name']) ?></td>
            </tr>
            <tr>
                <td class="label">Instansi / Perusahaan</td>
                <td class="colon">:</td>
                <td><?= esc($internship['company_name']) ?></td>
            </tr>
            <tr>
                <td class="label">Posisi / Divisi</td>
                <td class="colon">:</td>
                <td><?= esc($internship['position'] ?: 'Praktikan Magang') ?></td>
            </tr>
            <tr>
                <td class="label">Waktu Pelaksanaan Magang</td>
                <td class="colon">:</td>
                <td><?= format_indo($internship['start_date'], false) ?> &ndash; <?= format_indo($internship['end_date'], false) ?></td>
            </tr>
        </table>

        <table class="evaluation-table">
            <thead>
                <tr>
                    <th style="width: 40px;">No.</th>
                    <th>Kriteria Penilaian</th>
                    <th style="width: 140px;">Nilai (dalam angka)<br>1 - 100</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center; vertical-align: top;">1.</td>
                    <td>
                        <strong>Aktifitas di Lapangan</strong>
                        <ul class="sub-criteria">
                            <li>1.1 Kehadiran Mahasiswa di Instansi/Perusahaan</li>
                            <li>1.2 Tepat Waktu dalam Kehadiran (Disiplin)</li>
                        </ul>
                    </td>
                    <td style="text-align: center; font-weight: bold; font-size: 12pt; vertical-align: middle;">
                        <?= number_format($assessment['score_field_activity'], 2) ?>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center; vertical-align: top;">2.</td>
                    <td>
                        <strong>Kerajinan</strong>
                        <ul class="sub-criteria">
                            <li>2.1 Keterampilan yang Dimiliki Mahasiswa</li>
                            <li>2.2 Penggunaan Keterampilan terhadap Kepentingan Instansi/Perusahaan</li>
                        </ul>
                    </td>
                    <td style="text-align: center; font-weight: bold; font-size: 12pt; vertical-align: middle;">
                        <?= number_format($assessment['score_craftsmanship'], 2) ?>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center; vertical-align: top;">3.</td>
                    <td>
                        <strong>Inisiatif</strong>
                        <ul class="sub-criteria">
                            <li>3.1 Kecepatan dan ketepatan menguasai materi/problem yang disajikan di instansi/perusahaan</li>
                            <li>3.2 Tanggap terhadap permasalahan yang timbul dalam lingkungan KP Mahasiswa</li>
                            <li>3.3 Penggunaan materi yang dikuasai untuk menyelesaikan pekerjaan</li>
                        </ul>
                    </td>
                    <td style="text-align: center; font-weight: bold; font-size: 12pt; vertical-align: middle;">
                        <?= number_format($assessment['score_initiative'], 2) ?>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center; vertical-align: top;">4.</td>
                    <td>
                        <strong>Kemampuan Mengemukakan Ide</strong>
                        <ul class="sub-criteria">
                            <li>4.1 Beradaptasi terhadap lingkungan instansi/perusahaan</li>
                            <li>4.2 Kesopanan dalam berbicara terhadap pegawai/karyawan dan pimpinan</li>
                            <li>4.3 Tidak pasif</li>
                        </ul>
                    </td>
                    <td style="text-align: center; font-weight: bold; font-size: 12pt; vertical-align: middle;">
                        <?= number_format($assessment['score_communication'], 2) ?>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center; vertical-align: top;">5.</td>
                    <td>
                        <strong>Kemampuan Menganalisa Persoalan</strong>
                        <ul class="sub-criteria">
                            <li>5.1 Kemampuan Mengidentifikasi dan Memecahkan Masalah</li>
                            <li>5.2 Kemampuan kerja sama</li>
                        </ul>
                    </td>
                    <td style="text-align: center; font-weight: bold; font-size: 12pt; vertical-align: middle;">
                        <?= number_format($assessment['score_problem_solving'], 2) ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: right; font-weight: bold; padding-right: 15px;">
                        Jumlah Nilai
                    </td>
                    <td style="text-align: center; font-weight: bold; font-size: 12pt;">
                        <?= number_format($assessment['total_score'], 2) ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: right; font-weight: bold; padding-right: 15px;">
                        Total = Jumlah Nilai / 5
                    </td>
                    <td style="text-align: center; font-weight: bold; font-size: 13pt;">
                        <?= number_format($assessment['final_score'], 2) ?> (<?= esc($assessment['grade']) ?>)
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="notes-box">
            <strong>B. Uraian lain dari Pembimbing di Instansi/Perusahaan :</strong>
            <div class="notes-box-content">
                <?= !empty($assessment['feedback_notes']) ? nl2br(esc($assessment['feedback_notes'])) : '<em>(Tidak ada catatan uraian tambahan)</em>' ?>
            </div>
        </div>

        <div class="signature-section">
            <div class="signature-box">
                <div><?= !empty($internship['company_address']) ? explode(',', $internship['company_address'])[0] : 'Di Tempat' ?>, <?= format_indo($assessment['assessment_date'] ?: date('Y-m-d'), false) ?></div>
                <div style="margin-top: 5px; font-weight: bold;">Pembimbing di Instansi/Perusahaan,</div>
                <div style="height: 70px;"></div>
                <div style="font-weight: bold; text-decoration: underline;">
                    <?= esc($assessment['evaluator_name'] ?: $user['name']) ?>
                </div>
                <div><?= esc($assessment['evaluator_position'] ?: 'Pembimbing Lapangan') ?></div>
            </div>
        </div>
    </div>

</body>
</html>

