<?php

namespace App\Controllers\DosenMk;

use App\Controllers\BaseController;
use App\Models\LecturerModel;
use App\Models\CourseModel;
use App\Models\AssessmentComponentModel;
use App\Models\InternshipSubmissionModel;
use App\Models\AssessmentModel;
use App\Models\ConversionRequestCourseModel;
use App\Models\InternshipModel;
use App\Models\StudentModel;

class Penilaian extends BaseController
{
    protected LecturerModel $lecturerModel;
    protected CourseModel $courseModel;
    protected AssessmentComponentModel $componentModel;
    protected InternshipSubmissionModel $submissionModel;
    protected AssessmentModel $assessmentModel;
    protected ConversionRequestCourseModel $crcModel;
    protected InternshipModel $internshipModel;
    protected StudentModel $studentModel;

    public function __construct()
    {
        $this->lecturerModel   = new LecturerModel();
        $this->courseModel     = new CourseModel();
        $this->componentModel  = new AssessmentComponentModel();
        $this->submissionModel = new InternshipSubmissionModel();
        $this->assessmentModel = new AssessmentModel();
        $this->crcModel        = new ConversionRequestCourseModel();
        $this->internshipModel = new InternshipModel();
        $this->studentModel    = new StudentModel();
    }

    /**
     * Halaman Utama: Daftar Mata Kuliah yang diampu & ringkasan pengumpulan/penilaian
     */
    public function index()
    {
        $userId   = session()->get('userId');
        $lecturer = $this->lecturerModel->where('user_id', $userId)->first();

        if (!$lecturer) {
            return redirect()->to('/dashboard')->with('error', 'Profil dosen tidak ditemukan.');
        }

        // Ambil semua mata kuliah yang diampu dosen ini
        $courses = $this->courseModel
            ->where('lecturer_id', $lecturer['id'])
            ->orderBy('code', 'ASC')
            ->findAll();

        $coursesData = [];
        $totalPendingReviews = 0;

        foreach ($courses as $c) {
            $components  = $this->componentModel->getComponentsByCourse($c['id']);
            $totalWeight = array_sum(array_column($components, 'weight_percentage'));
            $students    = $this->crcModel->getAcceptedStudentsByCourse($c['id']);

            // Hitung status pengumpulan & penilaian untuk setiap mahasiswa
            $pendingReviews = 0;
            $gradedCount    = 0;

            if (!empty($students)) {
                $internshipIds = array_column($students, 'internship_id');

                // 1 Single Query: Eager load 1 submisi link Google Drive per mahasiswa untuk MK ini
                $subsByInternship = $this->submissionModel->getSubmissionsByCourse($c['id'], $internshipIds);

                // 1 Single Query: Eager load nilai akhir yang tersimpan
                $allAssessments = $this->assessmentModel
                    ->whereIn('internship_id', $internshipIds)
                    ->where('course_id', $c['id'])
                    ->where('assessment_component_id IS NULL', null, false)
                    ->findAll();
                $assessmentsByInternship = [];
                foreach ($allAssessments as $ass) {
                    $assessmentsByInternship[$ass['internship_id']] = $ass;
                }

                foreach ($students as &$s) {
                    $submission = $subsByInternship[$s['internship_id']] ?? null;
                    $s['submission'] = $submission;

                    // Status penilaian akhir
                    $finalAssessment = $assessmentsByInternship[$s['internship_id']] ?? null;
                    $s['final_assessment'] = $finalAssessment;
                    if ($finalAssessment && !empty($finalAssessment['verified_status'])) {
                        $gradedCount++;
                        if ($submission && $submission['status'] === 'SUBMITTED') {
                            $submission['status'] = 'APPROVED';
                            $s['submission']['status'] = 'APPROVED';
                        }
                    }

                    $subStatus = $submission['status'] ?? 'BELUM';
                    if ($subStatus === 'SUBMITTED') {
                        $pendingReviews++;
                        $totalPendingReviews++;
                    }
                }
            }

            $coursesData[] = [
                'course'          => $c,
                'components'      => $components,
                'total_weight'    => $totalWeight,
                'students'        => $students,
                'student_count'   => count($students),
                'pending_reviews' => $pendingReviews,
                'graded_count'    => $gradedCount,
            ];
        }

        $data = [
            'title'               => 'Penilaian & Pengumpulan Magang',
            'lecturer'            => $lecturer,
            'coursesData'         => $coursesData,
            'totalPendingReviews' => $totalPendingReviews,
        ];

        return view('dosen_mk/penilaian/index', $data);
    }

    /**
     * Halaman Pengaturan Komponen Pengumpulan Dinamis untuk suatu Mata Kuliah
     */
    public function components($courseId)
    {
        $userId   = session()->get('userId');
        $lecturer = $this->lecturerModel->where('user_id', $userId)->first();

        if (!$lecturer) {
            return redirect()->to('/dashboard')->with('error', 'Profil dosen tidak ditemukan.');
        }

        $course = $this->courseModel->where('id', $courseId)->where('lecturer_id', $lecturer['id'])->first();
        if (!$course) {
            return redirect()->to('/dosen-mk/penilaian')->with('error', 'Mata kuliah tidak ditemukan atau Anda bukan pengampunya.');
        }

        $components  = $this->componentModel->getComponentsByCourse($courseId);
        $totalWeight = $this->componentModel->getTotalWeightByCourse($courseId);

        $data = [
            'title'       => 'Komponen Pengumpulan & Rubrik: ' . $course['name'],
            'course'      => $course,
            'components'  => $components,
            'totalWeight' => $totalWeight,
        ];

        return view('dosen_mk/penilaian/components', $data);
    }

    /**
     * Tambah Komponen Pengumpulan Baru
     */
    public function storeComponent($courseId)
    {
        $userId   = session()->get('userId');
        $lecturer = $this->lecturerModel->where('user_id', $userId)->first();

        $course = $this->courseModel->where('id', $courseId)->where('lecturer_id', $lecturer['id'])->first();
        if (!$course) {
            return redirect()->to('/dosen-mk/penilaian')->with('error', 'Mata kuliah tidak valid.');
        }

        $title       = trim((string)$this->request->getPost('title'));
        $description = trim((string)$this->request->getPost('description'));
        $weight      = (float)$this->request->getPost('weight_percentage');
        $maxScore    = (float)($this->request->getPost('max_score') ?: 100);

        if (empty($title)) {
            return redirect()->back()->withInput()->with('error', 'Nama/Judul komponen pengumpulan wajib diisi.');
        }

        if ($weight <= 0 || $weight > 100) {
            return redirect()->back()->withInput()->with('error', 'Bobot komponen harus berada di antara 1% sampai 100%.');
        }

        $currentTotal = $this->componentModel->getTotalWeightByCourse($courseId);
        if (($currentTotal + $weight) > 100.0) {
            $sisa = 100.0 - $currentTotal;
            return redirect()->back()->withInput()->with('error', "Total bobot melebihi 100%. Sisa bobot yang tersedia adalah {$sisa}%.");
        }

        $this->componentModel->insert([
            'course_id'         => $courseId,
            'title'             => $title,
            'description'       => $description,
            'weight_percentage' => $weight,
            'max_score'         => $maxScore,
        ]);

        return redirect()->back()->with('success', 'Komponen pengumpulan berhasil ditambahkan.');
    }

    /**
     * Update Komponen Pengumpulan
     */
    public function updateComponent($id)
    {
        $userId   = session()->get('userId');
        $lecturer = $this->lecturerModel->where('user_id', $userId)->first();

        $component = $this->componentModel->find($id);
        if (!$component) {
            return redirect()->back()->with('error', 'Komponen tidak ditemukan.');
        }

        $course = $this->courseModel->where('id', $component['course_id'])->where('lecturer_id', $lecturer['id'])->first();
        if (!$course) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $title       = trim((string)$this->request->getPost('title'));
        $description = trim((string)$this->request->getPost('description'));
        $weight      = (float)$this->request->getPost('weight_percentage');

        if (empty($title)) {
            return redirect()->back()->with('error', 'Nama komponen tidak boleh kosong.');
        }

        $otherTotal = $this->componentModel->getTotalWeightByCourse($course['id'], $id);
        if (($otherTotal + $weight) > 100.0) {
            $sisa = 100.0 - $otherTotal;
            return redirect()->back()->with('error', "Total bobot melebihi 100%. Maksimal bobot yang bisa diisi adalah {$sisa}%.");
        }

        $this->componentModel->update($id, [
            'title'             => $title,
            'description'       => $description,
            'weight_percentage' => $weight,
        ]);

        return redirect()->back()->with('success', 'Komponen pengumpulan berhasil diperbarui.');
    }

    /**
     * Hapus Komponen Pengumpulan
     */
    public function deleteComponent($id)
    {
        $userId   = session()->get('userId');
        $lecturer = $this->lecturerModel->where('user_id', $userId)->first();

        $component = $this->componentModel->find($id);
        if (!$component) {
            return redirect()->back()->with('error', 'Komponen tidak ditemukan.');
        }

        $course = $this->courseModel->where('id', $component['course_id'])->where('lecturer_id', $lecturer['id'])->first();
        if (!$course) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $this->componentModel->delete($id);
        return redirect()->back()->with('success', 'Komponen pengumpulan berhasil dihapus.');
    }

    /**
     * Halaman Detail Pengumpulan & Form Penilaian untuk 1 Mahasiswa
     */
    public function student($courseId, $internshipId)
    {
        $userId   = session()->get('userId');
        $lecturer = $this->lecturerModel->where('user_id', $userId)->first();

        if (!$lecturer) {
            return redirect()->to('/dashboard')->with('error', 'Profil dosen tidak ditemukan.');
        }

        $course = $this->courseModel->where('id', $courseId)->where('lecturer_id', $lecturer['id'])->first();
        if (!$course) {
            return redirect()->to('/dosen-mk/penilaian')->with('error', 'Mata kuliah tidak valid.');
        }

        // Ambil data magang & mahasiswa
        $internship = $this->internshipModel
            ->select('internships.*, students.nim, students.phone as student_phone, users.name as student_name, users.email as student_email, users.id as student_user_id, program_studies.name as prodi_name')
            ->join('students', 'students.id = internships.student_id')
            ->join('users', 'users.id = students.user_id')
            ->join('program_studies', 'program_studies.id = students.program_study_id')
            ->where('internships.id', $internshipId)
            ->first();

        if (!$internship) {
            return redirect()->to('/dosen-mk/penilaian')->with('error', 'Data magang mahasiswa tidak ditemukan.');
        }

        // Komponen pengumpulan MK ini (sebagai keterangan & rubrik penilaian)
        $components = $this->componentModel->getComponentsByCourse($courseId);
        $totalWeight = array_sum(array_column($components, 'weight_percentage'));

        // 1 Submisi link Google Drive mahasiswa untuk mata kuliah ini
        $submission = $this->submissionModel->getCourseSubmission($courseId, $internshipId);

        // Skor per komponen yang sudah tersimpan
        $componentScores = $this->assessmentModel->getComponentScores($internshipId, $courseId);

        // Nilai akhir / summary
        $finalAssessment = $this->assessmentModel->getFinalAssessment($internshipId, $courseId);

        // Sinkronisasi otomatis: Jika nilai akhir sudah diverifikasi tapi status submisi berkas masih SUBMITTED, selaraskan menjadi APPROVED
        if ($submission && $submission['status'] === 'SUBMITTED' && $finalAssessment && !empty($finalAssessment['verified_status'])) {
            $this->submissionModel->update($submission['id'], [
                'status'         => 'APPROVED',
                'feedback_notes' => $submission['feedback_notes'] ?: 'Berkas pengumpulan Google Drive telah diperiksa dan disetujui bersamaan dengan penilaian.',
            ]);
            $submission['status'] = 'APPROVED';
        }

        $data = [
            'title'           => 'Pemeriksaan Berkas & Penilaian: ' . $internship['student_name'],
            'course'          => $course,
            'internship'      => $internship,
            'components'      => $components,
            'totalWeight'     => $totalWeight,
            'submission'      => $submission,
            'componentScores' => $componentScores,
            'finalAssessment' => $finalAssessment,
        ];

        return view('dosen_mk/penilaian/student', $data);
    }

    /**
     * Aksi Review Submisi: Menyetujui atau Meminta Perbaikan Pengumpulan
     */
    public function reviewSubmission($submissionId)
    {
        $userId   = session()->get('userId');
        $lecturer = $this->lecturerModel->where('user_id', $userId)->first();

        $submission = $this->submissionModel->find($submissionId);
        if (!$submission) {
            return redirect()->back()->with('error', 'Pengumpulan tidak ditemukan.');
        }

        $courseId = $submission['course_id'];
        $course   = $this->courseModel->where('id', $courseId)->where('lecturer_id', $lecturer['id'])->first();
        if (!$course) {
            return redirect()->back()->with('error', 'Anda tidak memiliki wewenang memeriksa pengumpulan ini.');
        }

        $action        = $this->request->getPost('action'); // 'APPROVE' atau 'REVISE'
        $feedbackNotes = trim((string)$this->request->getPost('feedback_notes'));

        $internship = $this->internshipModel->find($submission['internship_id']);
        $student    = $this->studentModel->find($internship['student_id']);

        if ($action === 'APPROVE') {
            $this->submissionModel->update($submissionId, [
                'status'         => 'APPROVED',
                'feedback_notes' => $feedbackNotes ?: 'Pengumpulan berkas Google Drive telah diperiksa dan disetujui.',
            ]);

            send_notification(
                (int)$student['user_id'],
                'Pengumpulan Disetujui',
                "Pengumpulan berkas Google Drive pada MK {$course['name']} telah DISETUJUI oleh Dosen Pengampu.",
                'mahasiswa/penilaian'
            );

            return redirect()->back()->with('success', "Pengumpulan berkas Google Drive berhasil disetujui.");
        } else {
            if (empty($feedbackNotes)) {
                return redirect()->back()->with('error', 'Harap masukkan keterangan/alasan perbaikan yang perlu diperbaiki mahasiswa.');
            }

            $this->submissionModel->update($submissionId, [
                'status'         => 'REVISION_REQUIRED',
                'feedback_notes' => $feedbackNotes,
            ]);

            send_notification(
                (int)$student['user_id'],
                'Perbaikan Pengumpulan Diminta',
                "Dosen Pengampu MK {$course['name']} meminta perbaikan berkas di Google Drive: \"{$feedbackNotes}\"",
                'mahasiswa/penilaian'
            );

            return redirect()->back()->with('success', "Permintaan perbaikan telah dikirim ke mahasiswa.");
        }
    }

    /**
     * Aksi Simpan / Verifikasi Nilai Akhir Mahasiswa
     */
    public function saveGrade($courseId, $internshipId)
    {
        $userId   = session()->get('userId');
        $lecturer = $this->lecturerModel->where('user_id', $userId)->first();

        $course = $this->courseModel->where('id', $courseId)->where('lecturer_id', $lecturer['id'])->first();
        if (!$course) {
            return redirect()->back()->with('error', 'Mata kuliah tidak valid.');
        }

        $components = $this->componentModel->getComponentsByCourse($courseId);
        if (empty($components)) {
            return redirect()->back()->with('error', 'Belum ada komponen penilaian pada mata kuliah ini.');
        }

        $scores      = $this->request->getPost('scores') ?: [];
        $notes       = trim((string)$this->request->getPost('notes'));
        $isVerified  = (int)$this->request->getPost('is_verified'); // 1 = Verifikasi/Kunci, 0 = Simpan Draft

        $weightedTotal = 0.0;
        $totalWeight   = 0.0;

        $db = \Config\Database::connect();
        $db->transStart();

        foreach ($components as $c) {
            $compId = $c['id'];
            $score  = isset($scores[$compId]) ? (float)$scores[$compId] : 0.0;

            if ($score < 0 || $score > 100) {
                return redirect()->back()->withInput()->with('error', "Nilai untuk [{$c['title']}] harus di antara 0 sampai 100.");
            }

            // Simpan skor per komponen
            $existing = $this->assessmentModel
                ->where('internship_id', $internshipId)
                ->where('course_id', $courseId)
                ->where('assessment_component_id', $compId)
                ->first();

            if ($existing) {
                $this->assessmentModel->update($existing['id'], [
                    'score'       => $score,
                    'assessor_id' => $userId,
                ]);
            } else {
                $this->assessmentModel->insert([
                    'internship_id'           => $internshipId,
                    'course_id'               => $courseId,
                    'assessor_id'             => $userId,
                    'assessment_component_id' => $compId,
                    'score'                   => $score,
                    'verified_status'         => 0,
                ]);
            }

            $weightedTotal += ($score * ($c['weight_percentage'] / 100.0));
            $totalWeight   += $c['weight_percentage'];
        }

        // Hitung huruf mutu resmi
        $finalGrade = AssessmentModel::convertScoreToGrade($weightedTotal);

        // Simpan / update summary assessment (assessment_component_id = NULL)
        $summary = $this->assessmentModel->getFinalAssessment($internshipId, $courseId);

        if ($summary) {
            $this->assessmentModel->update($summary['id'], [
                'score'           => round($weightedTotal, 2),
                'final_grade'     => $finalGrade,
                'verified_status' => $isVerified,
                'notes'           => $notes,
                'assessor_id'     => $userId,
            ]);
        } else {
            $this->assessmentModel->insert([
                'internship_id'           => $internshipId,
                'course_id'               => $courseId,
                'assessor_id'             => $userId,
                'assessment_component_id' => null,
                'score'                   => round($weightedTotal, 2),
                'final_grade'             => $finalGrade,
                'verified_status'         => $isVerified,
                'notes'                   => $notes,
            ]);
        }

        // 1 Button: Setujui Berkas & Menilai Sekaligus!
        // Jika dosen memverifikasi/mengunci nilai, otomatis setujui submisi berkas mahasiswa jika ada
        if ($isVerified) {
            $submission = $this->submissionModel->getCourseSubmission($courseId, $internshipId);
            if ($submission) {
                $this->submissionModel->update($submission['id'], [
                    'status'         => 'APPROVED',
                    'feedback_notes' => !empty($notes) ? $notes : ($submission['feedback_notes'] ?: 'Berkas pengumpulan Google Drive telah diperiksa dan disetujui bersamaan dengan penerbitan nilai akhir.'),
                ]);
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal menyimpan nilai. Silakan coba kembali.');
        }

        // Jika diverifikasi, kirim notifikasi ke mahasiswa
        if ($isVerified) {
            $internship = $this->internshipModel->find($internshipId);
            $student    = $this->studentModel->find($internship['student_id']);
            send_notification(
                (int)$student['user_id'],
                'Berkas Disetujui & Nilai Akhir Diterbitkan',
                "Berkas pengumpulan MK {$course['name']} telah disetujui dan nilai akhir resmi telah diverifikasi: {$weightedTotal} (Grade {$finalGrade}).",
                'mahasiswa/penilaian'
            );
        }

        $msg = $isVerified 
            ? "Berkas pengumpulan berhasil disetujui dan nilai akhir berhasil dikunci (Nilai: {$weightedTotal}, Grade: {$finalGrade})." 
            : "Draft nilai berhasil disimpan (Nilai sementara: {$weightedTotal}, Grade: {$finalGrade}).";

        return redirect()->back()->with('success', $msg);
    }
}

