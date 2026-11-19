<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\InternshipModel;
use App\Models\ConversionRequestModel;
use App\Models\ConversionRequestCourseModel;
use App\Models\AssessmentComponentModel;
use App\Models\InternshipSubmissionModel;
use App\Models\AssessmentModel;
use App\Models\CourseModel;
use App\Models\LecturerModel;

class Penilaian extends BaseController
{
    protected StudentModel $studentModel;
    protected InternshipModel $internshipModel;
    protected ConversionRequestModel $conversionRequestModel;
    protected ConversionRequestCourseModel $crcModel;
    protected AssessmentComponentModel $componentModel;
    protected InternshipSubmissionModel $submissionModel;
    protected AssessmentModel $assessmentModel;
    protected CourseModel $courseModel;
    protected LecturerModel $lecturerModel;

    public function __construct()
    {
        $this->studentModel           = new StudentModel();
        $this->internshipModel        = new InternshipModel();
        $this->conversionRequestModel = new ConversionRequestModel();
        $this->crcModel               = new ConversionRequestCourseModel();
        $this->componentModel         = new AssessmentComponentModel();
        $this->submissionModel        = new InternshipSubmissionModel();
        $this->assessmentModel        = new AssessmentModel();
        $this->courseModel            = new CourseModel();
        $this->lecturerModel          = new LecturerModel();
    }

    /**
     * Halaman Utama Mahasiswa: Daftar Mata Kuliah Konversi untuk Pengumpulan & Nilai
     */
    public function index()
    {
        $userId  = session()->get('userId');
        $student = $this->studentModel->getStudentWithDetails($userId);

        if (!$student) {
            return redirect()->to('/dashboard')->with('error', 'Profil mahasiswa tidak ditemukan.');
        }

        // Ambil magang mahasiswa yang aktif atau sudah selesai
        $internships = $this->internshipModel
            ->where('student_id', $student['id'])
            ->whereIn('status', ['INTERNSHIP_ACTIVE', 'INTERNSHIP_COMPLETED', 'REPORT_SUBMITTED', 'ASSESSMENT', 'COMPLETED'])
            ->orderBy('id', 'DESC')
            ->findAll();

        $selectedPeriodeId = (int)$this->request->getGet('periode');
        $activeInternship = null;

        if ($selectedPeriodeId > 0) {
            foreach ($internships as $item) {
                if ((int)$item['id'] === $selectedPeriodeId) {
                    $activeInternship = $item;
                    break;
                }
            }
        }

        if (!$activeInternship && !empty($internships)) {
            // Utamakan yang berstatus INTERNSHIP_ACTIVE, jika tidak ada pilih yang terbaru
            $activeList = array_filter($internships, fn($i) => $i['status'] === 'INTERNSHIP_ACTIVE');
            $activeInternship = !empty($activeList) ? reset($activeList) : $internships[0];
        }

        $coursesData = [];

        if ($activeInternship) {
            // Ambil semua MK konversi yang disetujui untuk magang ini
            $conversionCourses = $this->crcModel
                ->select('conversion_request_courses.*, courses.code as course_code, courses.name as course_name, courses.credits, courses.lecturer_id,
                         users.name as lecturer_name, lecturers.nidn as lecturer_nidn')
                ->join('conversion_requests', 'conversion_requests.id = conversion_request_courses.conversion_request_id')
                ->join('courses', 'courses.id = conversion_request_courses.course_id')
                ->join('lecturers', 'lecturers.id = courses.lecturer_id', 'left')
                ->join('users', 'users.id = lecturers.user_id', 'left')
                ->where('conversion_requests.internship_id', $activeInternship['id'])
                ->where('conversion_requests.student_id', $student['id'])
                ->where('conversion_request_courses.status', 'ACCEPTED')
                ->findAll();

            foreach ($conversionCourses as $cc) {
                $components = $this->componentModel->getComponentsByCourse($cc['course_id']);
                $submission = $this->submissionModel->getCourseSubmission($cc['course_id'], $activeInternship['id']);
                $finalAssessment = $this->assessmentModel->getFinalAssessment($activeInternship['id'], $cc['course_id']);

                $coursesData[] = [
                    'crc'              => $cc,
                    'components'       => $components,
                    'component_count'  => count($components),
                    'submission'       => $submission,
                    'final_assessment' => $finalAssessment,
                ];
            }
        }

        $instansiAssessment = null;
        if ($activeInternship) {
            $instansiAssessmentModel = new \App\Models\InstansiAssessmentModel();
            $instansiAssessment = $instansiAssessmentModel->getAssessmentByInternship($activeInternship['id']);
        }

        $data = [
            'title'              => 'Pengumpulan Berkas & Penilaian Magang',
            'student'            => $student,
            'internships'        => $internships,
            'internship'         => $activeInternship,
            'coursesData'        => $coursesData,
            'instansiAssessment' => $instansiAssessment,
        ];

        return view('mahasiswa/penilaian/index', $data);
    }

    /**
     * Halaman Detail Pengumpulan Link Drive & Nilai untuk suatu Mata Kuliah
     */
    public function detail($crcId)
    {
        $userId  = session()->get('userId');
        $student = $this->studentModel->where('user_id', $userId)->first();

        if (!$student) {
            return redirect()->to('/dashboard')->with('error', 'Profil mahasiswa tidak ditemukan.');
        }

        // Ambil data mata kuliah konversi mahasiswa ini
        $crc = $this->crcModel
            ->select('conversion_request_courses.*, conversion_requests.internship_id, conversion_requests.student_id,
                     courses.code as course_code, courses.name as course_name, courses.credits, courses.lecturer_id,
                     users.name as lecturer_name, users.email as lecturer_email, lecturers.nidn as lecturer_nidn,
                     internships.company_name, internships.position, internships.academic_year, internships.semester')
            ->join('conversion_requests', 'conversion_requests.id = conversion_request_courses.conversion_request_id')
            ->join('courses', 'courses.id = conversion_request_courses.course_id')
            ->join('lecturers', 'lecturers.id = courses.lecturer_id', 'left')
            ->join('users', 'users.id = lecturers.user_id', 'left')
            ->join('internships', 'internships.id = conversion_requests.internship_id')
            ->where('conversion_request_courses.id', $crcId)
            ->where('conversion_requests.student_id', $student['id'])
            ->first();

        if (!$crc) {
            return redirect()->to('/mahasiswa/penilaian')->with('error', 'Data mata kuliah konversi tidak ditemukan.');
        }

        $courseId     = (int)$crc['course_id'];
        $internshipId = (int)$crc['internship_id'];

        // Komponen pengumpulan yang ditentukan oleh Dosen MK
        $components  = $this->componentModel->getComponentsByCourse($courseId);
        $totalWeight = array_sum(array_column($components, 'weight_percentage'));

        // 1 Submisi link Google Drive mahasiswa untuk mata kuliah ini
        $submission = $this->submissionModel->getCourseSubmission($courseId, $internshipId);

        // Skor per komponen (hanya tampil jika penilaian sudah ada / diverifikasi)
        $componentScores = $this->assessmentModel->getComponentScores($internshipId, $courseId);
        $finalAssessment = $this->assessmentModel->getFinalAssessment($internshipId, $courseId);

        $data = [
            'title'           => 'Pengumpulan & Penilaian: ' . $crc['course_name'],
            'crc'             => $crc,
            'components'      => $components,
            'totalWeight'     => $totalWeight,
            'submission'      => $submission,
            'componentScores' => $componentScores,
            'finalAssessment' => $finalAssessment,
        ];

        return view('mahasiswa/penilaian/detail', $data);
    }

    /**
     * Aksi Submit / Re-submit 1 Tautan Google Drive untuk Mata Kuliah ini
     */
    public function submitDrive($courseId)
    {
        $userId  = session()->get('userId');
        $student = $this->studentModel->where('user_id', $userId)->first();

        if (!$student) {
            return redirect()->back()->with('error', 'Profil mahasiswa tidak ditemukan.');
        }

        $course = $this->courseModel->find($courseId);
        if (!$course) {
            return redirect()->back()->with('error', 'Mata kuliah tidak valid.');
        }

        $internshipId = (int)$this->request->getPost('internship_id');
        $internship   = $this->internshipModel->where('id', $internshipId)->where('student_id', $student['id'])->first();

        if (!$internship) {
            return redirect()->back()->with('error', 'Data magang tidak valid.');
        }

        $driveUrl     = trim((string)$this->request->getPost('drive_url'));
        $studentNotes = trim((string)$this->request->getPost('student_notes'));

        if (empty($driveUrl)) {
            return redirect()->back()->withInput()->with('error', 'Tautan (Link) Google Drive wajib diisi.');
        }

        if (!filter_var($driveUrl, FILTER_VALIDATE_URL)) {
            return redirect()->back()->withInput()->with('error', 'Format tautan Google Drive tidak valid. Pastikan diawali http:// atau https://');
        }

        // Cek apakah sudah pernah submit sebelumnya (untuk update/re-submit revisi)
        $existing = $this->submissionModel->getCourseSubmission($courseId, $internshipId);

        if ($existing) {
            $this->submissionModel->update($existing['id'], [
                'drive_url'      => $driveUrl,
                'student_notes'  => $studentNotes,
                'status'         => 'SUBMITTED',
                'feedback_notes' => null, // Reset feedback setelah diperbaiki mahasiswa
            ]);
        } else {
            $this->submissionModel->insert([
                'course_id'     => $courseId,
                'internship_id' => $internshipId,
                'drive_url'     => $driveUrl,
                'student_notes' => $studentNotes,
                'status'        => 'SUBMITTED',
            ]);
        }

        // Kirim notifikasi ke Dosen Pengampu MK
        if (!empty($course['lecturer_id'])) {
            $lecturer = $this->lecturerModel->find($course['lecturer_id']);
            if ($lecturer && !empty($lecturer['user_id'])) {
                send_notification(
                    (int)$lecturer['user_id'],
                    'Pengumpulan Berkas Mahasiswa',
                    session()->get('userName') . " telah mengumpulkan tautan Google Drive berkas magang untuk MK {$course['name']}.",
                    'dosen-mk/penilaian/mahasiswa/' . $course['id'] . '/' . $internshipId
                );
            }
        }

        return redirect()->back()->with('success', "Tautan Google Drive berkas magang berhasil dikumpulkan dan dikirim ke Dosen Pengampu MK.");
    }

    /**
     * Cetak Lembar Penilaian Instansi oleh Mahasiswa
     */
    public function cetakInstansi($internshipId)
    {
        $userId  = session()->get('userId');
        $student = $this->studentModel->where('user_id', $userId)->first();
        if (!$student) {
            return redirect()->to('/dashboard')->with('error', 'Profil mahasiswa tidak ditemukan.');
        }

        $internship = $this->internshipModel
            ->select('internships.*, students.nim, students.phone as student_phone, users.name as student_name, users.email as student_email, program_studies.name as prodi_name')
            ->join('students', 'students.id = internships.student_id')
            ->join('users', 'users.id = students.user_id')
            ->join('program_studies', 'program_studies.id = students.program_study_id')
            ->where('internships.id', $internshipId)
            ->where('internships.student_id', $student['id'])
            ->first();

        if (!$internship) {
            return redirect()->to('/mahasiswa/penilaian')->with('error', 'Data magang tidak ditemukan.');
        }

        $assessmentModel = new \App\Models\InstansiAssessmentModel();
        $assessment = $assessmentModel->where('internship_id', $internshipId)->first();
        if (!$assessment) {
            return redirect()->to('/mahasiswa/penilaian')->with('error', 'Penilaian instansi belum diisi.');
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($assessment['instansi_user_id']) ?: ['name' => $internship['company_name']];

        $criteriaModel = new \App\Models\InstansiAssessmentComponentModel();
        $criteria = $criteriaModel->getActiveComponents();

        $scores = [];
        if (!empty($assessment['scores_json'])) {
            $decoded = json_decode($assessment['scores_json'], true);
            if (is_array($decoded)) {
                $scores = $decoded;
            }
        }
        if (empty($scores)) {
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
        }

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
     * Mahasiswa mengajukan permintaan pembuatan akun instansi ke Admin
     */
    public function requestInstansiAccount()
    {
        $userId  = session()->get('userId');
        $student = $this->studentModel->where('user_id', $userId)->first();
        if (!$student) {
            return redirect()->to('/dashboard')->with('error', 'Profil mahasiswa tidak ditemukan.');
        }

        $internshipId = (int)$this->request->getPost('internship_id');
        $internship = $this->internshipModel
            ->where('id', $internshipId)
            ->where('student_id', $student['id'])
            ->first();

        if (!$internship) {
            return redirect()->back()->with('error', 'Data magang tidak ditemukan.');
        }

        if ($internship['status'] !== 'INTERNSHIP_ACTIVE' && !in_array($internship['status'], ['INTERNSHIP_COMPLETED', 'REPORT_SUBMITTED', 'ASSESSMENT', 'COMPLETED'])) {
            return redirect()->back()->with('error', 'Permintaan akun instansi dapat diajukan saat magang sedang berjalan.');
        }

        $rules = [
            'instansi_pic_name'  => 'required|min_length[3]|max_length[100]',
            'instansi_pic_email' => 'required|valid_email|max_length[100]',
            'instansi_pic_phone' => 'permit_empty|max_length[30]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Harap isi nama pembimbing dan email instansi yang valid.');
        }

        $picName  = trim((string)$this->request->getPost('instansi_pic_name'));
        $picEmail = trim((string)$this->request->getPost('instansi_pic_email'));
        $picPhone = trim((string)$this->request->getPost('instansi_pic_phone'));

        $this->internshipModel->update($internshipId, [
            'instansi_pic_name'            => $picName,
            'instansi_pic_email'           => $picEmail,
            'instansi_pic_phone'           => $picPhone,
            'instansi_account_requested_at' => date('Y-m-d H:i:s'),
        ]);

        // Kirim notifikasi ke Admin
        $userName = session()->get('userName');
        send_notification_to_role(
            'admin',
            'Permintaan Akun Instansi Baru',
            "Mahasiswa {$userName} ({$student['nim']}) mengajukan pembuatan akun instansi untuk {$internship['company_name']} (PIC: {$picName}, Email: {$picEmail}).",
            'admin/users/instansi'
        );

        return redirect()->back()->with('success', "Permintaan pembuatan akun instansi untuk {$picName} ({$picEmail}) berhasil dikirim ke Admin. Notifikasi telah diteruskan.");
    }
}

