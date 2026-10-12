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

        // Get elective courses with lecturer info
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
        ];

        return view('mahasiswa/konversi/create', $data);
    }

    public function store()
    {
        $userId  = session()->get('userId');
        $student = $this->studentModel->where('user_id', $userId)->first();

        if (!$student) {
            return redirect()->to('/dashboard')->with('error', 'Profil mahasiswa tidak ditemukan.');
        }

        $rules = [
            'internship_id' => 'required|numeric',
            'courses'       => 'required',
            'notes'         => 'permit_empty|max_length[1000]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $internshipId   = (int) $this->request->getPost('internship_id');
        $courseIds      = $this->request->getPost('courses');
        $justifications = $this->request->getPost('justifications');
        $notes          = $this->request->getPost('notes');

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

        if (empty($courseIds) || !is_array($courseIds)) {
            return redirect()->back()->withInput()->with('error', 'Pilih minimal satu mata kuliah pilihan untuk dikonversi.');
        }

        // Validate that each selected course has non-empty justification
        foreach ($courseIds as $cid) {
            $justificationText = trim($justifications[$cid] ?? '');
            if (empty($justificationText)) {
                return redirect()->back()->withInput()->with('error', 'Harap isi penjelasan hubungan aktivitas magang untuk setiap mata kuliah yang Anda pilih.');
            }
        }

        // DB Transaction
        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Insert Conversion Request (Status immediately goes to LECTURER_REVIEW)
        $requestId = $this->conversionRequestModel->insert([
            'internship_id' => $internshipId,
            'student_id'    => $student['id'],
            'status'        => 'LECTURER_REVIEW',
            'notes'         => $notes,
        ]);

        // 2. Insert Conversion Request Courses with student_justification
        foreach ($courseIds as $cid) {
            $this->conversionRequestCourseModel->insert([
                'conversion_request_id' => $requestId,
                'course_id'             => (int) $cid,
                'student_justification' => trim($justifications[$cid] ?? ''),
                'status'                => 'PENDING',
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan pengajuan konversi. Silakan coba kembali.');
        }

        return redirect()->to('/mahasiswa/konversi/detail/' . $requestId)->with('success', 'Pengajuan konversi MK pilihan berhasil dikirim dan masuk ke tahap verifikasi Dosen Pengampu MK.');
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
