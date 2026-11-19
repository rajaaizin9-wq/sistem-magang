<?php

namespace App\Controllers\Instansi;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\InternshipModel;
use App\Models\InstansiAssessmentModel;
use App\Models\InstansiAssessmentComponentModel;

class Penilaian extends BaseController
{
    protected UserModel $userModel;
    protected InternshipModel $internshipModel;
    protected InstansiAssessmentModel $assessmentModel;
    protected InstansiAssessmentComponentModel $criteriaModel;

    public function __construct()
    {
        $this->userModel       = new UserModel();
        $this->internshipModel = new InternshipModel();
        $this->assessmentModel = new InstansiAssessmentModel();
        $this->criteriaModel   = new InstansiAssessmentComponentModel();
    }

    /**
     * Dapatkan instansi user yang sedang login
     */
    private function getCurrentInstansiUser(): array
    {
        $userId = session()->get('userId');
        $user   = $this->userModel->find($userId);

        if (!$user || $user['role'] !== 'instansi') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Akun instansi tidak valid.');
        }

        return $user;
    }

    /**
     * Halaman Utama Instansi: Langsung Membuka Formulir Penilaian Mahasiswa
     */
    public function index()
    {
        $user   = $this->getCurrentInstansiUser();
        $userId = (int)$user['id'];
        $companyNameEscaped = $this->internshipModel->db->escape(strtolower(trim($user['name'])));

        // Ambil semua mahasiswa magang yang terhubung ke akun instansi ini
        $students = $this->internshipModel
            ->select('internships.*, students.nim, students.phone as student_phone, users.name as student_name, users.email as student_email, users.id as student_user_id, program_studies.name as prodi_name,
                      instansi_assessments.id as assessment_id, instansi_assessments.final_score, instansi_assessments.grade, instansi_assessments.updated_at as assessed_at')
            ->join('students', 'students.id = internships.student_id')
            ->join('users', 'users.id = students.user_id')
            ->join('program_studies', 'program_studies.id = students.program_study_id')
            ->join('instansi_assessments', 'instansi_assessments.internship_id = internships.id', 'left')
            ->where("(internships.company_user_id = {$userId} OR LOWER(TRIM(internships.company_name)) = {$companyNameEscaped})")
            ->orderBy('internships.id', 'DESC')
            ->findAll();

        if (empty($students)) {
            return view('instansi/penilaian/empty', [
                'title' => 'Portal Penilaian Instansi',
                'user'  => $user,
            ]);
        }

        // Tentukan mahasiswa aktif yang sedang dibuka formulirnya
        $targetId = (int)($this->request->getGet('mahasiswa') ?: 0);
        $activeInternship = null;

        if ($targetId > 0) {
            foreach ($students as $s) {
                if ((int)$s['id'] === $targetId) {
                    $activeInternship = $s;
                    break;
                }
            }
        }

        // Jika tidak ada target spesifik, utamakan mahasiswa yang belum dinilai, atau mahasiswa pertama
        if (!$activeInternship) {
            $unassessed = array_filter($students, fn($s) => empty($s['assessment_id']));
            $activeInternship = !empty($unassessed) ? reset($unassessed) : reset($students);
        }

        // Ambil nilai yang sudah tersimpan untuk mahasiswa aktif ini (jika ada)
        $assessment = $this->assessmentModel->where('internship_id', $activeInternship['id'])->first();
        $criteria   = $this->criteriaModel->getActiveComponents();
        $scores     = $this->getAssessmentScoresArray($assessment, $criteria);

        $data = [
            'title'            => 'Penilaian Mahasiswa Magang: ' . $activeInternship['student_name'],
            'user'             => $user,
            'students'         => $students,
            'activeInternship' => $activeInternship,
            'assessment'       => $assessment,
            'criteria'         => $criteria,
            'scores'           => $scores,
        ];

        return view('instansi/penilaian/form', $data);
    }

    /**
     * Alias form() agar kompatibel jika ada URL lama
     */
    public function form($internshipId)
    {
        return redirect()->to('instansi/penilaian?mahasiswa=' . $internshipId);
    }

    /**
     * Simpan / Perbarui Nilai Mahasiswa
     */
    public function save($internshipId)
    {
        $user       = $this->getCurrentInstansiUser();
        $internship = $this->getAuthorizedInternship($internshipId, $user);
        $criteria   = $this->criteriaModel->getActiveComponents();

        if (empty($criteria)) {
            return redirect()->to("instansi/penilaian?mahasiswa={$internshipId}")->with('error', 'Komponen penilaian instansi belum diatur oleh Admin.');
        }

        $postedScores = (array)$this->request->getPost('scores');
        $totalScore   = 0.0;
        $savedScores  = [];

        foreach ($criteria as $c) {
            $val = $postedScores[$c['id']] ?? null;
            if ($val === null || $val === '' || !is_numeric($val) || (float)$val < 0 || (float)$val > 100) {
                return redirect()->to("instansi/penilaian?mahasiswa={$internshipId}")
                    ->withInput()
                    ->with('error', 'Semua poin kriteria penilaian wajib diisi dengan angka 0 - 100.');
            }
            $fVal = round((float)$val, 2);
            $totalScore += $fVal;
            $savedScores[$c['id']] = $fVal;
        }

        $countCriteria = count($criteria) ?: 1;
        $finalScore    = round($totalScore / (float)$countCriteria, 2);
        $grade         = InstansiAssessmentModel::convertScoreToGrade($finalScore);

        $evaluatorName     = trim((string)$this->request->getPost('evaluator_name')) ?: $user['name'];
        $evaluatorPosition = trim((string)$this->request->getPost('evaluator_position')) ?: 'Pembimbing Lapangan';
        $feedbackNotes     = trim((string)$this->request->getPost('feedback_notes'));
        $assessmentDate    = $this->request->getPost('assessment_date') ?: date('Y-m-d');

        // Petakan 5 nilai pertama ke kolom legacy untuk kompatibilitas data
        $criteriaValues = array_values($savedScores);
        $assessmentData = [
            'internship_id'         => $internshipId,
            'instansi_user_id'      => (int)$user['id'],
            'evaluator_name'        => $evaluatorName,
            'evaluator_position'    => $evaluatorPosition,
            'scores_json'           => json_encode($savedScores),
            'score_field_activity'  => $criteriaValues[0] ?? 0.00,
            'score_craftsmanship'   => $criteriaValues[1] ?? 0.00,
            'score_initiative'      => $criteriaValues[2] ?? 0.00,
            'score_communication'   => $criteriaValues[3] ?? 0.00,
            'score_problem_solving' => $criteriaValues[4] ?? 0.00,
            'total_score'           => $totalScore,
            'final_score'           => $finalScore,
            'grade'                 => $grade,
            'feedback_notes'        => $feedbackNotes,
            'assessment_date'       => $assessmentDate,
        ];

        $existing = $this->assessmentModel->where('internship_id', $internshipId)->first();

        if ($existing) {
            $this->assessmentModel->update($existing['id'], $assessmentData);
        } else {
            $this->assessmentModel->insert($assessmentData);
        }

        // Pastikan relasi company_user_id terisi pada magang
        if (empty($internship['company_user_id'])) {
            $this->internshipModel->update($internshipId, ['company_user_id' => $user['id']]);
        }

        // Kirim notifikasi ke mahasiswa
        send_notification(
            (int)$internship['student_user_id'],
            'Nilai dari Instansi Telah Terbit',
            "Pembimbing instansi {$internship['company_name']} telah memberikan penilaian magang Anda dengan nilai rata-rata: {$finalScore}.",
            'mahasiswa/dashboard'
        );

        return redirect()->to("instansi/penilaian?mahasiswa={$internshipId}")->with('success', "Penilaian untuk mahasiswa {$internship['student_name']} berhasil disimpan! (Nilai Rata-rata: {$finalScore}).");
    }

    /**
     * Halaman Cetak Lembar Penilaian Instansi (Format Resmi)
     */
    public function print($internshipId)
    {
        $user = $this->getCurrentInstansiUser();
        $internship = $this->getAuthorizedInternship($internshipId, $user);

        $assessment = $this->assessmentModel->where('internship_id', $internshipId)->first();
        if (!$assessment) {
            return redirect()->to('instansi/penilaian?mahasiswa=' . $internshipId)->with('error', 'Penilaian untuk mahasiswa ini belum diisi.');
        }

        $criteria = $this->criteriaModel->getActiveComponents();
        $scores   = $this->getAssessmentScoresArray($assessment, $criteria);

        $data = [
            'title'      => 'Lembar Penilaian Instansi - ' . $internship['student_name'],
            'user'       => $user,
            'internship' => $internship,
            'assessment' => $assessment,
            'criteria'   => $criteria,
            'scores'     => $scores,
        ];

        return view('instansi/penilaian/print', $data);
    }

    /**
     * Helper: Ekstrak nilai per kriteria baik dari scores_json maupun kolom legacy
     */
    private function getAssessmentScoresArray(?array $assessment, array $criteria): array
    {
        $scores = [];
        if (!$assessment) {
            return $scores;
        }

        if (!empty($assessment['scores_json'])) {
            $decoded = json_decode($assessment['scores_json'], true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        // Fallback untuk penilaian lama yang masih menggunakan 5 kolom flat
        $legacyMap = [
            1 => $assessment['score_field_activity'] ?? null,
            2 => $assessment['score_craftsmanship'] ?? null,
            3 => $assessment['score_initiative'] ?? null,
            4 => $assessment['score_communication'] ?? null,
            5 => $assessment['score_problem_solving'] ?? null,
        ];

        foreach ($criteria as $c) {
            $order = (int)$c['order_number'];
            if (isset($legacyMap[$order]) && $legacyMap[$order] !== null) {
                $scores[$c['id']] = (float)$legacyMap[$order];
            }
        }

        return $scores;
    }

    /**
     * Helper: Validasi kepemilikan data magang terhadap instansi
     */
    private function getAuthorizedInternship($internshipId, array $user): array
    {
        $userId = (int)$user['id'];
        $companyNameEscaped = $this->internshipModel->db->escape(strtolower(trim($user['name'])));

        $internship = $this->internshipModel
            ->select('internships.*, students.nim, students.phone as student_phone, users.name as student_name, users.email as student_email, users.id as student_user_id, program_studies.name as prodi_name')
            ->join('students', 'students.id = internships.student_id')
            ->join('users', 'users.id = students.user_id')
            ->join('program_studies', 'program_studies.id = students.program_study_id')
            ->where('internships.id', $internshipId)
            ->where("(internships.company_user_id = {$userId} OR LOWER(TRIM(internships.company_name)) = {$companyNameEscaped})")
            ->first();

        if (!$internship) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Data magang mahasiswa tidak ditemukan atau Anda tidak memiliki akses.');
        }

        return $internship;
    }
}
