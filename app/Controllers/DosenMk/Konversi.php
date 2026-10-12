<?php

namespace App\Controllers\DosenMk;

use App\Controllers\BaseController;
use App\Models\LecturerModel;
use App\Models\CourseModel;
use App\Models\ConversionRequestModel;
use App\Models\ConversionRequestCourseModel;
use App\Models\ConversionApprovalModel;

class Konversi extends BaseController
{
    protected $lecturerModel;
    protected $courseModel;
    protected $conversionRequestModel;
    protected $conversionRequestCourseModel;
    protected $conversionApprovalModel;

    public function __construct()
    {
        $this->lecturerModel                = new LecturerModel();
        $this->courseModel                  = new CourseModel();
        $this->conversionRequestModel       = new ConversionRequestModel();
        $this->conversionRequestCourseModel = new ConversionRequestCourseModel();
        $this->conversionApprovalModel      = new ConversionApprovalModel();
    }

    public function index()
    {
        $userId   = session()->get('userId');
        $lecturer = $this->lecturerModel->where('user_id', $userId)->first();

        if (!$lecturer) {
            return redirect()->to('/dashboard')->with('error', 'Profil dosen tidak ditemukan.');
        }

        // Get all conversion course requests for courses taught by this lecturer
        $requests = $this->conversionRequestCourseModel->getRequestsForLecturer($lecturer['id']);

        $pendingCount  = count(array_filter($requests, fn($r) => $r['status'] === 'PENDING'));
        $reviewedCount = count(array_filter($requests, fn($r) => $r['status'] !== 'PENDING'));

        $data = [
            'title'         => 'Verifikasi Konversi MK Pengampu',
            'lecturer'      => $lecturer,
            'requests'      => $requests,
            'pendingCount'  => $pendingCount,
            'reviewedCount' => $reviewedCount,
        ];

        return view('dosen_mk/konversi/index', $data);
    }

    public function detail($id)
    {
        $userId   = session()->get('userId');
        $lecturer = $this->lecturerModel->where('user_id', $userId)->first();

        if (!$lecturer) {
            return redirect()->to('/dashboard')->with('error', 'Profil dosen tidak ditemukan.');
        }

        // Get conversion course request item
        $item = $this->conversionRequestCourseModel
            ->select('conversion_request_courses.*, courses.code as course_code, courses.name as course_name, courses.credits, courses.lecturer_id,
                     conversion_requests.id as request_id, conversion_requests.status as request_status, conversion_requests.notes as student_notes, conversion_requests.created_at as request_date,
                     students.nim, students.phone as student_phone, users.name as student_name, users.email as student_email, program_studies.name as prodi_name,
                     internships.company_name, internships.company_address, internships.position, internships.internship_type, internships.start_date, internships.end_date, internships.academic_year, internships.semester')
            ->join('courses', 'courses.id = conversion_request_courses.course_id')
            ->join('conversion_requests', 'conversion_requests.id = conversion_request_courses.conversion_request_id')
            ->join('students', 'students.id = conversion_requests.student_id')
            ->join('users', 'users.id = students.user_id')
            ->join('program_studies', 'program_studies.id = students.program_study_id')
            ->join('internships', 'internships.id = conversion_requests.internship_id')
            ->where('conversion_request_courses.id', $id)
            ->where('courses.lecturer_id', $lecturer['id'])
            ->first();

        if (!$item) {
            return redirect()->to('/dosen-mk/konversi')->with('error', 'Data pengajuan konversi tidak ditemukan atau bukan merupakan mata kuliah yang Anda ampu.');
        }

        $data = [
            'title' => 'Verifikasi Pengajuan MK: ' . $item['course_name'],
            'item'  => $item,
        ];

        return view('dosen_mk/konversi/detail', $data);
    }

    public function process($id)
    {
        $userId   = session()->get('userId');
        $lecturer = $this->lecturerModel->where('user_id', $userId)->first();

        if (!$lecturer) {
            return redirect()->to('/dashboard')->with('error', 'Profil dosen tidak ditemukan.');
        }

        $item = $this->conversionRequestCourseModel
            ->select('conversion_request_courses.*, courses.lecturer_id, courses.name as course_name')
            ->join('courses', 'courses.id = conversion_request_courses.course_id')
            ->where('conversion_request_courses.id', $id)
            ->where('courses.lecturer_id', $lecturer['id'])
            ->first();

        if (!$item) {
            return redirect()->to('/dosen-mk/konversi')->with('error', 'Data pengajuan tidak ditemukan.');
        }

        $action = $this->request->getPost('action'); // 'ACCEPTED' or 'REJECTED'
        $notes  = $this->request->getPost('notes');

        if (!in_array($action, ['ACCEPTED', 'REJECTED'])) {
            return redirect()->back()->with('error', 'Tindakan yang dipilih tidak valid.');
        }

        if ($action === 'REJECTED' && empty(trim($notes))) {
            return redirect()->back()->withInput()->with('error', 'Harap sertakan alasan/catatan penolakan untuk mahasiswa.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Update status pada conversion_request_courses
        $this->conversionRequestCourseModel->update($id, [
            'status'           => $action,
            'rejection_reason' => $notes,
        ]);

        // 2. Log ke conversion_approvals
        $this->conversionApprovalModel->insert([
            'conversion_request_id' => $item['conversion_request_id'],
            'approver_id'           => $userId,
            'role'                  => 'dosen_mk',
            'status'                => ($action === 'ACCEPTED' ? 'APPROVED' : 'REJECTED'),
            'notes'                 => 'Mata Kuliah (' . $item['course_name'] . '): ' . ($notes ?: ($action === 'ACCEPTED' ? 'Disetujui Dosen Pengampu MK' : 'Ditolak')),
            'approved_at'           => date('Y-m-d H:i:s'),
        ]);

        // 3. Cek apakah seluruh mata kuliah dalam conversion_request ini telah direspon
        $allCourses = $this->conversionRequestCourseModel
            ->where('conversion_request_id', $item['conversion_request_id'])
            ->findAll();

        $hasPending  = false;
        $hasAccepted = false;

        foreach ($allCourses as $c) {
            // Update current item in evaluation array
            $cStatus = ($c['id'] == $id) ? $action : $c['status'];
            if ($cStatus === 'PENDING') {
                $hasPending = true;
            }
            if ($cStatus === 'ACCEPTED') {
                $hasAccepted = true;
            }
        }

        // Jika semua MK sudah direspon oleh para dosen MK masing-masing
        if (!$hasPending) {
            if ($hasAccepted) {
                // Ada minimal 1 yang disetujui -> lanjut ke Pembimbing
                $this->conversionRequestModel->update($item['conversion_request_id'], [
                    'status' => 'SUPERVISOR_REVIEW',
                ]);
            } else {
                // Semua MK ditolak
                $this->conversionRequestModel->update($item['conversion_request_id'], [
                    'status' => 'REJECTED',
                ]);
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal memproses verifikasi konversi.');
        }

        $msg = ($action === 'ACCEPTED') 
            ? 'Mata kuliah berhasil disetujui untuk konversi magang.'
            : 'Mata kuliah ditolak untuk konversi magang.';

        return redirect()->to('/dosen-mk/konversi')->with('success', $msg);
    }
}
