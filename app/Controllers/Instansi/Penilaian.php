<?php

namespace App\Controllers\Instansi;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\InternshipModel;
use App\Models\InstansiAssessmentModel;

class Penilaian extends BaseController
{
    protected UserModel $userModel;
    protected InternshipModel $internshipModel;
    protected InstansiAssessmentModel $assessmentModel;

    public function __construct()
    {
        $this->userModel       = new UserModel();
        $this->internshipModel = new InternshipModel();
        $this->assessmentModel = new InstansiAssessmentModel();
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

        $data = [
            'title'            => 'Penilaian Mahasiswa Magang: ' . $activeInternship['student_name'],
            'user'             => $user,
            'students'         => $students,
            'activeInternship' => $activeInternship,
            'assessment'       => $assessment,
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
        $user = $this->getCurrentInstansiUser();
        $internship = $this->getAuthorizedInternship($internshipId, $user);

        $rules = [
            'score_field_activity'  => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
            'score_craftsmanship'   => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
            'score_initiative'      => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
            'score_communication'   => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
            'score_problem_solving' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to("instansi/penilaian?mahasiswa={$internshipId}")->withInput()->with('error', 'Semua poin nilai (1-5) wajib diisi dengan angka 0 - 100.');
        }

        $s1 = (float)$this->request->getPost('score_field_activity');
        $s2 = (float)$this->request->getPost('score_craftsmanship');
        $s3 = (float)$this->request->getPost('score_initiative');
        $s4 = (float)$this->request->getPost('score_communication');
        $s5 = (float)$this->request->getPost('score_problem_solving');

        $totalScore = $s1 + $s2 + $s3 + $s4 + $s5;
        $finalScore = round($totalScore / 5.0, 2);
        $grade      = InstansiAssessmentModel::convertScoreToGrade($finalScore);

        $evaluatorName     = trim((string)$this->request->getPost('evaluator_name')) ?: $user['name'];
        $evaluatorPosition = trim((string)$this->request->getPost('evaluator_position')) ?: 'Pembimbing Lapangan';
        $feedbackNotes     = trim((string)$this->request->getPost('feedback_notes'));
        $assessmentDate    = $this->request->getPost('assessment_date') ?: date('Y-m-d');

        $assessmentData = [
            'internship_id'         => $internshipId,
            'instansi_user_id'      => (int)$user['id'],
            'evaluator_name'        => $evaluatorName,
            'evaluator_position'    => $evaluatorPosition,
            'score_field_activity'  => $s1,
            'score_craftsmanship'   => $s2,
            'score_initiative'      => $s3,
            'score_communication'   => $s4,
            'score_problem_solving' => $s5,
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
            "Pembimbing instansi {$internship['company_name']} telah memberikan penilaian magang Anda dengan nilai rata-rata {$finalScore} (Grade {$grade}).",
            'mahasiswa/dashboard'
        );

        return redirect()->to("instansi/penilaian?mahasiswa={$internshipId}")->with('success', "Penilaian untuk mahasiswa {$internship['student_name']} berhasil disimpan! (Nilai Rata-rata: {$finalScore}, Grade: {$grade}).");
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

        $data = [
            'title'      => 'Lembar Penilaian Instansi - ' . $internship['student_name'],
            'user'       => $user,
            'internship' => $internship,
            'assessment' => $assessment,
        ];

        return view('instansi/penilaian/print', $data);
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
