<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\InternshipModel;
use App\Models\CourseModel;
use App\Models\ConversionRequestModel;
use App\Models\ConversionRequestCourseModel;
use App\Models\ConversionApprovalModel;

class Konversi extends BaseController
{
    protected $studentModel;
    protected $internshipModel;
    protected $courseModel;
    protected $conversionRequestModel;
    protected $conversionRequestCourseModel;
    protected $conversionApprovalModel;

    public function __construct()
    {
        $this->studentModel                 = new StudentModel();
        $this->internshipModel              = new InternshipModel();
        $this->courseModel                  = new CourseModel();
        $this->conversionRequestModel       = new ConversionRequestModel();
        $this->conversionRequestCourseModel = new ConversionRequestCourseModel();
        $this->conversionApprovalModel      = new ConversionApprovalModel();
    }

    public function index()
    {
        $userId  = session()->get('userId');
        $student = $this->studentModel->where('user_id', $userId)->first();

        if (!$student) {
            return redirect()->to('/dashboard')->with('error', 'Profil mahasiswa tidak ditemukan.');
        }

        $requests = $this->conversionRequestModel->getRequestsByStudentId($student['id']);

        // Attach course details and total credits to each request
        foreach ($requests as &$req) {
            $courses = $this->conversionRequestCourseModel->getCoursesByRequestId($req['id']);
            $req['courses']       = $courses;
            $req['total_credits'] = array_sum(array_column($courses, 'credits'));
            $req['approved_count'] = count(array_filter($courses, fn($c) => $c['status'] === 'ACCEPTED'));
        }

        $data = [
            'title'    => 'Pengajuan Konversi MK Pilihan',
            'student'  => $student,
            'requests' => $requests,
        ];

        return view('mahasiswa/konversi/index', $data);
    }

    public function create()
    {
        $userId  = session()->get('userId');
        $student = $this->studentModel->getStudentWithDetails($userId);

        if (!$student) {
            return redirect()->to('/dashboard')->with('error', 'Profil mahasiswa tidak ditemukan.');
        }

        // Get student's eligible internships (strictly INTERNSHIP_ACTIVE)
        $internships = $this->internshipModel
            ->where('student_id', $student['id'])
            ->where('status', 'INTERNSHIP_ACTIVE')
            ->orderBy('id', 'DESC')
            ->findAll();

        if (empty($internships)) {
            return redirect()->to('/mahasiswa/konversi')->with('error', 'Anda belum memiliki program magang yang sedang aktif berjalan untuk diajukan konversi.');
        }

        // Daftar seluruh dosen di program studi mahasiswa untuk opsi pemilihan Dosen Pengampu
        $lecturerModel = new \App\Models\LecturerModel();
        $lecturers     = $lecturerModel
            ->select('lecturers.id, lecturers.nidn, users.name, users.email')
            ->join('users', 'users.id = lecturers.user_id')
            ->where('lecturers.program_study_id', $student['program_study_id'])
            ->whereIn('users.role', ['dosen', 'wadek1'])
            ->where('users.is_active', 1)
            ->orderBy('users.name', 'ASC')
            ->findAll();

        // Get elective courses with lecturer info (sebagai master data)
        $electiveCourses = $this->courseModel
            ->select('courses.*, users.name as lecturer_name, lecturers.nidn as lecturer_nidn')
            ->join('lecturers', 'lecturers.id = courses.lecturer_id', 'left')
            ->join('users', 'users.id = lecturers.user_id', 'left')
            ->where('courses.program_study_id', $student['program_study_id'])
            ->where('courses.is_elective', 1)
            ->findAll();

        $data = [
            'title'           => 'Form Pengajuan Konversi MK Pilihan',
            'student'         => $student,
            'internships'     => $internships,
            'electiveCourses' => $electiveCourses,
            'lecturers'       => $lecturers,
        ];

        return view('mahasiswa/konversi/create', $data);
    }

    /**
     * AJAX endpoint untuk parsing berkas PDF LIRS
     */
    public function parseLirs()
    {
        $userId  = session()->get('userId');
        $student = $this->studentModel->getStudentWithDetails($userId);

        if (!$student) {
            return $this->response->setJSON(['success' => false, 'message' => 'Profil mahasiswa tidak valid.']);
        }

        $file = $this->request->getFile('lirs_file');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Silakan pilih berkas PDF LIRS yang valid.']);
        }

        if (strtolower($file->getClientExtension()) !== 'pdf') {
            return $this->response->setJSON(['success' => false, 'message' => 'Format berkas harus berupa dokumen PDF (.pdf).']);
        }

        $tempPath = $file->getTempName();
        $parser   = new \App\Services\LirsParserService();

        // Ambil data master MK dan Dosen dari prodi mahasiswa
        $knownCourses = [];
        $coursesDb    = $this->courseModel->where('program_study_id', $student['program_study_id'])->findAll();
        foreach ($coursesDb as $c) {
            $knownCourses[strtoupper($c['code'])] = $c;
        }

        $lecturerModel = new \App\Models\LecturerModel();
        $lecturers     = $lecturerModel
            ->select('lecturers.id, lecturers.nidn, users.name')
            ->join('users', 'users.id = lecturers.user_id')
            ->where('lecturers.program_study_id', $student['program_study_id'])
            ->whereIn('users.role', ['dosen', 'wadek1'])
            ->where('users.is_active', 1)
            ->orderBy('users.name', 'ASC')
            ->findAll();

        $result = $parser->parse($tempPath, $knownCourses, $lecturers);

        if (!$result['success']) {
            return $this->response->setJSON($result);
        }

        // Validasi NIM: jika di dokumen terdeteksi NIM dan tidak cocok dengan akun login
        if (!empty($result['header']['nim'])) {
            $cleanDocNim  = preg_replace('/\D/', '', $result['header']['nim']);
            $cleanUserNim = preg_replace('/\D/', '', $student['nim']);

            if (!empty($cleanDocNim) && !empty($cleanUserNim) && $cleanDocNim !== $cleanUserNim) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => "NIM pada dokumen LIRS ({$result['header']['nim']}) tidak cocok dengan NIM akun Anda ({$student['nim']}). Pastikan Anda mengunggah berkas LIRS Anda sendiri.",
                ]);
            }
        }

        return $this->response->setJSON([
            'success'   => true,
            'header'    => $result['header'],
            'courses'   => $result['courses'],
            'lecturers' => $lecturers,
            'message'   => $result['message'],
        ]);
    }

    public function store()
    {
        $userId  = session()->get('userId');
        $student = $this->studentModel->where('user_id', $userId)->first();

        if (!$student) {
            return redirect()->to('/dashboard')->with('error', 'Profil mahasiswa tidak ditemukan.');
        }

        $rules = [
            'internship_id'    => 'required|numeric',
            'selected_courses' => 'required',
            'notes'            => 'permit_empty|max_length[1000]',
            'lirs_file'        => 'uploaded[lirs_file]|max_size[lirs_file,5120]|ext_in[lirs_file,pdf]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $internshipId    = (int) $this->request->getPost('internship_id');
        $selectedCodes   = $this->request->getPost('selected_courses');
        $courseNames     = $this->request->getPost('course_names');
        $courseCredits   = $this->request->getPost('course_credits');
        $courseLecturers = $this->request->getPost('course_lecturers');
        $justifications  = $this->request->getPost('justifications');
        $notes           = $this->request->getPost('notes');

        // Check internship ownership and status == 'INTERNSHIP_ACTIVE'
        $internship = $this->internshipModel
            ->where('id', $internshipId)
            ->where('student_id', $student['id'])
            ->where('status', 'INTERNSHIP_ACTIVE')
            ->first();
            
        if (!$internship) {
            return redirect()->back()->withInput()->with('error', 'Data magang yang dipilih tidak valid atau belum dalam masa magang aktif.');
        }

        // Prevent duplicate active conversion request for the same internship
        $existingReq = $this->conversionRequestModel
            ->where('internship_id', $internshipId)
            ->whereIn('status', ['DRAFT', 'SUBMITTED', 'LECTURER_REVIEW', 'SUPERVISOR_REVIEW', 'VICE_DEAN_REVIEW'])
            ->first();

        if ($existingReq) {
            return redirect()->back()->withInput()->with('error', 'Sudah ada permohonan konversi yang sedang diproses untuk periode magang ini.');
        }

        if (empty($selectedCodes) || !is_array($selectedCodes)) {
            return redirect()->back()->withInput()->with('error', 'Pilih minimal satu mata kuliah untuk diajukan konversi.');
        }

        // Validasi setiap MK yang dipilih
        foreach ($selectedCodes as $code) {
            $justificationText = trim($justifications[$code] ?? '');
            if (empty($justificationText)) {
                return redirect()->back()->withInput()->with('error', "Harap isi penjelasan hubungan aktivitas magang untuk mata kuliah [{$code}].");
            }

            $lecturerId = $courseLecturers[$code] ?? null;
            if (empty($lecturerId)) {
                return redirect()->back()->withInput()->with('error', "Harap pilih Dosen Pengampu untuk mata kuliah [{$code}].");
            }

            $proposalFile = $this->request->getFile("proposals_{$code}");
            if (!$proposalFile || !$proposalFile->isValid()) {
                return redirect()->back()->withInput()->with('error', "Harap unggah berkas proposal untuk mata kuliah [{$code}].");
            }

            $ext = strtolower($proposalFile->getClientExtension());
            if (!in_array($ext, ['pdf', 'doc', 'docx'])) {
                return redirect()->back()->withInput()->with('error', "Format berkas proposal untuk [{$code}] harus berupa PDF, DOC, atau DOCX.");
            }

            if ($proposalFile->getSizeByUnit('mb') > 5) {
                return redirect()->back()->withInput()->with('error', "Ukuran berkas proposal untuk [{$code}] tidak boleh melebihi 5 MB.");
            }
        }

        // Simpan Berkas LIRS
        $uploadLirsDir = WRITEPATH . 'uploads/conversions/lirs';
        if (!is_dir($uploadLirsDir)) {
            mkdir($uploadLirsDir, 0775, true);
        }

        $lirsFile = $this->request->getFile('lirs_file');
        $lirsName = 'lirs_' . $student['id'] . '_' . time() . '_' . $lirsFile->getRandomName();
        $lirsFile->move($uploadLirsDir, $lirsName);
        $lirsRelativePath = 'uploads/conversions/lirs/' . $lirsName;

        // Direktori Proposal
        $uploadTargetDir = WRITEPATH . 'uploads/conversions/proposals';
        if (!is_dir($uploadTargetDir)) {
            mkdir($uploadTargetDir, 0775, true);
        }

        // DB Transaction
        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Insert Conversion Request
        $requestId = $this->conversionRequestModel->insert([
            'internship_id' => $internshipId,
            'student_id'    => $student['id'],
            'status'        => 'LECTURER_REVIEW',
            'notes'         => $notes,
            'lirs_file'     => $lirsRelativePath,
        ]);

        $lecturerModel           = new \App\Models\LecturerModel();
        $notifiedLecturerUserIds = [];

        // 2. Insert Courses & Proposal Files
        foreach ($selectedCodes as $code) {
            $code       = strtoupper(trim($code));
            $name       = trim($courseNames[$code] ?? $code);
            $credits    = (int)($courseCredits[$code] ?? 3);
            $lecturerId = (int)$courseLecturers[$code];

            // Cek apakah MK sudah terdaftar di tabel courses
            $existingCourse = $this->courseModel->where('code', $code)->first();
            if (!$existingCourse) {
                $courseId = $this->courseModel->insert([
                    'code'             => $code,
                    'name'             => $name,
                    'credits'          => $credits,
                    'program_study_id' => $student['program_study_id'],
                    'lecturer_id'      => $lecturerId,
                    'is_elective'      => 1,
                ]);
            } else {
                $courseId = $existingCourse['id'];
                // Update pengampu di tabel master courses dengan yang dikonfirmasi
                if (!empty($lecturerId)) {
                    $this->courseModel->update($courseId, ['lecturer_id' => $lecturerId]);
                }
            }

            // Simpan Berkas Proposal
            $proposalFile = $this->request->getFile("proposals_{$code}");
            $cleanCode    = preg_replace('/[^a-zA-Z0-9]/', '', $code);
            $newFileName  = 'proposal_' . $student['id'] . '_' . $cleanCode . '_' . time() . '_' . $proposalFile->getRandomName();
            $proposalFile->move($uploadTargetDir, $newFileName);

            $this->conversionRequestCourseModel->insert([
                'conversion_request_id' => $requestId,
                'course_id'             => $courseId,
                'student_justification' => trim($justifications[$code] ?? ''),
                'proposal_file'         => $newFileName,
                'status'                => 'PENDING',
            ]);

            // Ambil user_id Dosen Pengampu untuk notifikasi
            $lec = $lecturerModel->select('user_id')->find($lecturerId);
            if ($lec && !empty($lec['user_id'])) {
                $notifiedLecturerUserIds[$lec['user_id']] = $name;
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan pengajuan konversi. Silakan coba kembali.');
        }

        // Notifikasi ke masing-masing Dosen Pengampu MK
        foreach ($notifiedLecturerUserIds as $uId => $cName) {
            send_notification((int)$uId, 'Pengajuan Konversi MK', session()->get('userName') . ' mengajukan konversi MK ' . $cName . '.', 'dosen-mk/konversi');
        }

        return redirect()->to('/mahasiswa/konversi/detail/' . $requestId)->with('success', 'Pengajuan konversi berdasarkan LIRS berhasil dikirim dan masuk ke tahap verifikasi Dosen Pengampu MK.');
    }

    public function detail($id)
    {
        $userId  = session()->get('userId');
        $student = $this->studentModel->where('user_id', $userId)->first();

        if (!$student) {
            return redirect()->to('/dashboard')->with('error', 'Profil mahasiswa tidak ditemukan.');
        }

        $request = $this->conversionRequestModel->getRequestWithDetails($id);

        if (!$request || $request['student_id'] != $student['id']) {
            return redirect()->to('/mahasiswa/konversi')->with('error', 'Pengajuan konversi tidak ditemukan atau Anda tidak memiliki akses.');
        }

        $courses   = $this->conversionRequestCourseModel->getCoursesByRequestId($id);
        $approvals = $this->conversionApprovalModel->getApprovalsByRequestId($id);

        $totalCredits    = array_sum(array_column($courses, 'credits'));
        $approvedCredits = 0;
        foreach ($courses as $c) {
            if ($c['status'] === 'ACCEPTED') {
                $approvedCredits += $c['credits'];
            }
        }

        $data = [
            'title'           => 'Detail Konversi MK Pilihan #' . $request['id'],
            'request'         => $request,
            'courses'         => $courses,
            'approvals'       => $approvals,
            'totalCredits'    => $totalCredits,
            'approvedCredits' => $approvedCredits,
        ];

        return view('mahasiswa/konversi/detail', $data);
    }
}
