<?php

namespace App\Services;

use Smalot\PdfParser\Parser;

class LirsParserService
{
    protected Parser $parser;

    public function __construct()
    {
        $this->parser = new Parser();
    }

    /**
     * Parse file PDF LIRS dan kembalikan data terstruktur
     *
     * @param string $pdfFilePath Lokasi absolute file PDF
     * @param array $knownCourses Daftar master MK dari DB ['INF11020' => ['name' => ..., 'credits' => ..., ...]]
     * @param array $knownLecturers Daftar dosen dari DB [['id' => 1, 'name' => 'Nurfalinda', 'nidn' => '...'], ...]
     * @return array
     */
    public function parse(string $pdfFilePath, array $knownCourses = [], array $knownLecturers = []): array
    {
        if (!file_exists($pdfFilePath)) {
            return [
                'success' => false,
                'message' => 'Berkas PDF LIRS tidak ditemukan di server.',
                'header'  => [],
                'courses' => [],
            ];
        }

        try {
            $pdf  = $this->parser->parseFile($pdfFilePath);
            $text = $pdf->getText();
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Gagal membaca berkas PDF: ' . $e->getMessage(),
                'header'  => [],
                'courses' => [],
            ];
        }

        if (empty(trim($text))) {
            return [
                'success' => false,
                'message' => 'Berkas PDF kosong atau teks tidak dapat diekstrak.',
                'header'  => [],
                'courses' => [],
            ];
        }

        // 1. Ekstrak Header
        $header = $this->extractHeader($text);

        // 2. Ekstrak Baris Mata Kuliah dengan pencocokan cerdas
        $courses = $this->extractCourses($text, $knownCourses, $knownLecturers);

        if (empty($courses)) {
            return [
                'success' => false,
                'message' => 'Tabel mata kuliah tidak terdeteksi pada berkas LIRS. Pastikan Anda mengunggah PDF resmi LIRS SIPA UMRAH.',
                'header'  => $header,
                'courses' => [],
            ];
        }

        return [
            'success' => true,
            'message' => 'Berhasil mendeteksi ' . count($courses) . ' mata kuliah dari LIRS.',
            'header'  => $header,
            'courses' => $courses,
        ];
    }

    /**
     * Ekstraksi informasi header LIRS
     */
    protected function extractHeader(string $text): array
    {
        $header = [
            'nim'            => '',
            'nama'           => '',
            'tahun_akademik' => '',
            'semester'       => '',
            'prodi'          => '',
        ];

        // NIM
        if (preg_match('/NIM\s*:\s*([0-9A-Za-z]+)/i', $text, $m)) {
            $header['nim'] = trim($m[1]);
        }

        // Nama Mahasiswa
        if (preg_match('/Nama\s+Mahasiswa\s*:\s*([^\r\n]+?)(?:Tahun\s+Masuk|Fakultas|$)/i', $text, $m)) {
            $header['nama'] = trim($m[1]);
        }

        // Tahun Akademik
        if (preg_match('/Tahun\s+Akademik\s*:\s*([^\r\n]+)/i', $text, $m)) {
            $header['tahun_akademik'] = trim($m[1]);
        }

        // Semester Ke
        if (preg_match('/Semester\s+Ke\s*:\s*([0-9]+)/i', $text, $m)) {
            $header['semester'] = trim($m[1]);
        }

        // Program Studi
        if (preg_match('/Program\s+Studi\s*:\s*([^\r\n]+?)(?:Semester|$)/i', $text, $m)) {
            $header['prodi'] = trim($m[1]);
        }

        return $header;
    }

    /**
     * Ekstraksi baris mata kuliah dari tabel LIRS
     */
    protected function extractCourses(string $text, array $knownCourses = [], array $knownLecturers = []): array
    {
        $courses = [];

        // Potong teks dari area tabel mata kuliah (setelah "Kode MK" sampai "Jumlah Beban SKS")
        $tableArea = $text;
        if (preg_match('/Kode\s*MK.*?\n(.*?)Jumlah\s+Beban/is', $text, $matchTable)) {
            $tableArea = $matchTable[1];
        } elseif (preg_match('/LEMBAR\s+ISIAN\s+RENCANA\s+STUDI.*?\n(.*?)Jumlah\s+Beban/is', $text, $matchTable)) {
            $tableArea = $matchTable[1];
        }

        // Cari pola baris: [No]? [Kode MK] [Content]
        // Catatan: Pada PDF LIRS SIPA UMRAH, nomor urut sering menempel langsung dengan Kode MK (contoh: 1INF11020)
        preg_match_all('/(?:\b(\d+)\s*)?([A-Z]{2,5}\d{4,6})\s+(.*?)(?=(?:\d*\s*[A-Z]{2,5}\d{4,6})|Jumlah\s+Beban|potong\s+disini|$)/is', $tableArea, $matches, PREG_SET_ORDER);

        foreach ($matches as $row) {
            $number   = !empty($row[1]) ? (int)$row[1] : (count($courses) + 1);
            $code     = strtoupper(trim($row[2]));
            $content  = trim($row[3]);

            if (isset($courses[$code])) {
                continue; // Jangan parse ulang jika ada duplikat (misal potongan kedua LIRS)
            }

            // SKS di ujung content
            $sks = 3;
            if (preg_match('/(\d+)\s*$/', $content, $sksMatch)) {
                $sks     = (int)$sksMatch[1];
                $content = preg_replace('/\d+\s*$/', '', $content);
            }

            // Kode Ruangan di ujung (misal R5-FTTK, R5)
            $content = preg_replace('/[A-Z0-9\-]+\s*$/i', '', trim($content));

            // Jika kode MK sudah ada di database master courses, gunakan data master resmi
            if (isset($knownCourses[$code])) {
                $courseName          = $knownCourses[$code]['name'];
                $sks                 = (int)($knownCourses[$code]['credits'] ?? $sks);
                $isElective          = (int)($knownCourses[$code]['is_elective'] ?? 1);
                $suggestedLecturerId = $knownCourses[$code]['lecturer_id'] ?? null;
            } else {
                // Ekstrak nama MK dari teks:
                $lines     = array_values(array_filter(array_map('trim', preg_split('/[\r\n]+/', $content))));
                $firstLine = $lines[0] ?? $content;

                // Jika baris pertama dipisahkan tab antara nama MK dan dosen (contoh: "Kerja Praktik\tRifaldi Herikson")
                if (strpos($firstLine, "\t") !== false) {
                    $tabParts   = explode("\t", $firstLine);
                    $courseName = trim($tabParts[0]);
                } elseif (preg_match('/^(.*?)\*(.*)$/s', $content, $starMatch)) {
                    // Jika ada tanda bintang (*) seperti "Data Sains*", teks sebelum bintang adalah nama MK
                    $courseName = trim($starMatch[1]);
                    $content    = trim($starMatch[2]);
                } else {
                    $courseName = $firstLine;
                    // Cek jika ada nama dosen dikenal di dalam nama MK, potong
                    foreach ($knownLecturers as $lec) {
                        $shortName = trim(explode(' ', trim(explode(',', $lec['name'])[0]))[0] ?? '');
                        if (!empty($shortName) && strlen($shortName) > 3 && stripos($courseName, $shortName) !== false) {
                            $courseName = trim(preg_replace('/' . preg_quote($shortName, '/') . '.*$/i', '', $courseName));
                            break;
                        }
                    }
                }
                $isElective          = 1;
                $suggestedLecturerId = null;
            }

            // Bersihkan nama MK dari karakter aneh / newline
            $courseName = trim(preg_replace('/[\r\n\t]+/', ' ', $courseName));

            // Cari dosen yang paling cocok dari teks baris tersebut (mengutamakan dosen teratas/dosen ketua)
            $suggestedLecturerId = $suggestedLecturerId ?: $this->matchLecturer($content, $knownLecturers);

            $courses[$code] = [
                'number'                => $number,
                'code'                  => $code,
                'name'                  => $courseName ?: $code,
                'sks'                   => $sks,
                'is_elective'           => $isElective,
                'suggested_lecturer_id' => $suggestedLecturerId,
                'raw_content'           => $content,
            ];
        }

        // Fallback cerdas: jika regex tabel tidak menemukan hasil, cari kode MK master di dalam seluruh teks
        if (empty($courses) && !empty($knownCourses)) {
            foreach ($knownCourses as $code => $c) {
                if (stripos($text, $code) !== false) {
                    $courses[$code] = [
                        'number'                => count($courses) + 1,
                        'code'                  => $code,
                        'name'                  => $c['name'],
                        'sks'                   => (int)($c['credits'] ?? 3),
                        'is_elective'           => (int)($c['is_elective'] ?? 1),
                        'suggested_lecturer_id' => $c['lecturer_id'] ?? $this->matchLecturer($text, $knownLecturers),
                        'raw_content'           => $code . ' ' . $c['name'],
                    ];
                }
            }
        }

        return array_values($courses);
    }

    /**
     * Mencocokkan nama dosen dari teks LIRS ke daftar dosen SIMAGANG.
     * Mengutamakan dosen paling atas (posisi teks paling awal) karena merupakan dosen ketua/pengampu utama.
     */
    protected function matchLecturer(string $content, array $lecturers): ?int
    {
        if (empty($lecturers)) {
            return null;
        }

        $earliestPos       = PHP_INT_MAX;
        $matchedLecturerId = null;

        foreach ($lecturers as $lec) {
            $fullName = $lec['name'] ?? '';
            // Nama tanpa gelar, misal "Nurfalinda", "Rifaldi Herikson", "Berta Erwin Slam"
            $cleanName = trim(explode(',', $fullName)[0]);
            $firstWord = trim(explode(' ', $cleanName)[0]);

            $pos = false;

            // 1. Cek nama lengkap tanpa gelar
            if (!empty($cleanName) && strlen($cleanName) >= 4) {
                $pos = stripos($content, $cleanName);
            }

            // 2. Jika tidak ditemukan, cek nama depan (minimal 4 karakter)
            if ($pos === false && !empty($firstWord) && strlen($firstWord) >= 4) {
                $pos = stripos($content, $firstWord);
            }

            // Pilih dosen yang posisinya PALING AWAL di baris LIRS (dosen ketua/paling atas)
            if ($pos !== false && $pos < $earliestPos) {
                $earliestPos       = $pos;
                $matchedLecturerId = (int)$lec['id'];
            }
        }

        return $matchedLecturerId;
    }
}
