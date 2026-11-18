<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\ConversionRequestCourseModel;
use App\Models\ConversionRequestModel;
use App\Models\StudentModel;
use App\Models\LecturerModel;
use App\Models\SupervisorAssignmentModel;

class KonversiFile extends BaseController
{
    protected $courseModel;
    protected $crcModel;
    protected $requestModel;

    public function __construct()
    {
        $this->courseModel  = new CourseModel();
        $this->crcModel     = new ConversionRequestCourseModel();
        $this->requestModel = new ConversionRequestModel();
    }

    /**
     * Unduh Template Format Proposal Default
     */
    public function defaultTemplate()
    {
        $filePath = FCPATH . 'templates/Format_Proposal_Pengajuan_Konversi_Magang.docx';
        if (!file_exists($filePath)) {
            // Fallback ke file root jika di public belum ada
            $rootPath = ROOTPATH . 'Format_Proposal_Pengajuan_Konversi_Magang (1).docx';
            if (file_exists($rootPath)) {
                $filePath = $rootPath;
            } else {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Berkas template default tidak ditemukan.');
            }
        }

        return $this->response->download($filePath, null)->setFileName('Format_Proposal_Pengajuan_Konversi_Magang.docx');
    }

    /**
     * Unduh Template Proposal Khusus Mata Kuliah (jika ada dari Dosen MK, fallback ke default)
     */
    public function courseTemplate($courseId)
    {
        $course = $this->courseModel->find($courseId);
        if (!$course) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Mata kuliah tidak ditemukan.');
        }

        if (!empty($course['proposal_template'])) {
            $customPath = WRITEPATH . 'uploads/templates/courses/' . $course['proposal_template'];
            if (file_exists($customPath)) {
                return $this->response->download($customPath, null)->setFileName(
                    'Format_Proposal_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $course['code']) . '.docx'
                );
            }
        }

        // Jika tidak ada template kustom, alihkan ke template default
        return $this->defaultTemplate();
    }

    /**
     * Akses / Unduh Berkas Proposal Mahasiswa yang telah disubmit
     */
    public function studentProposal($crcId)
    {
        $userId = session()->get('userId');
        $role   = session()->get('role');

        $crc = $this->crcModel->select('conversion_request_courses.*, conversion_requests.student_id, conversion_requests.internship_id, courses.lecturer_id, courses.code as course_code')
                              ->join('conversion_requests', 'conversion_requests.id = conversion_request_courses.conversion_request_id')
                              ->join('courses', 'courses.id = conversion_request_courses.course_id')
                              ->where('conversion_request_courses.id', $crcId)
                              ->first();

        if (!$crc || empty($crc['proposal_file'])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Berkas proposal tidak ditemukan.');
        }

        // Pengecekan Otorisasi:
        $hasAccess = false;

        if (in_array($role, ['tu', 'kaprodi', 'wadek1', 'admin'])) {
            $hasAccess = true;
        } elseif ($role === 'mahasiswa') {
            $studentModel = new StudentModel();
            $student      = $studentModel->where('user_id', $userId)->first();
            if ($student && $student['id'] == $crc['student_id']) {
                $hasAccess = true;
            }
        } elseif ($role === 'dosen') {
            $lecturerModel = new LecturerModel();
            $lecturer      = $lecturerModel->where('user_id', $userId)->first();
            if ($lecturer) {
                // Bisa akses jika Dosen Pengampu MK
                if ($crc['lecturer_id'] == $lecturer['id']) {
                    $hasAccess = true;
                } else {
                    // Atau jika Dosen Pembimbing magang ini
                    $assignmentModel = new SupervisorAssignmentModel();
                    $assignment      = $assignmentModel->where('internship_id', $crc['internship_id'])
                                                       ->where('lecturer_id', $lecturer['id'])
                                                       ->first();
                    if ($assignment) {
                        $hasAccess = true;
                    }
                }
            }
        }

        if (!$hasAccess) {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki hak akses untuk melihat berkas proposal ini.');
        }

        $fullPath = WRITEPATH . 'uploads/conversions/proposals/' . $crc['proposal_file'];
        if (!file_exists($fullPath)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File fisik proposal tidak ditemukan di server.');
        }

        $ext = strtolower(pathinfo($crc['proposal_file'], PATHINFO_EXTENSION));
        $cleanCode = preg_replace('/[^a-zA-Z0-9_-]/', '_', $crc['course_code']);

        // Pratinjau langsung di modal jika format PDF dan tidak meminta download paksa
        if ($this->request->getGet('download') != '1' && $ext === 'pdf') {
            return $this->response
                ->setHeader('Content-Type', 'application/pdf')
                ->setHeader('Content-Disposition', 'inline; filename="Proposal_' . $cleanCode . '_' . $crc['id'] . '.pdf"')
                ->setBody(file_get_contents($fullPath));
        }

        $downloadName = 'Proposal_' . $cleanCode . '_' . $crc['id'] . '.' . ($ext ?: 'pdf');
        return $this->response->download($fullPath, null)->setFileName($downloadName);
    }

    /**
     * Akses / Pratinjau Berkas LIRS Mahasiswa
     */
    public function studentLirs($requestId)
    {
        $userId = session()->get('userId');
        $role   = session()->get('role');

        $req = $this->requestModel->getRequestWithDetails($requestId);
        if (!$req || empty($req['lirs_file'])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Berkas LIRS tidak ditemukan.');
        }

        // Pengecekan Otorisasi Pengaksesan Berkas LIRS
        $hasAccess = false;
        if (in_array($role, ['tu', 'kaprodi', 'wadek1', 'admin'])) {
            $hasAccess = true;
        } elseif ($role === 'mahasiswa') {
            $studentModel = new StudentModel();
            $student      = $studentModel->where('user_id', $userId)->first();
            if ($student && $student['id'] == $req['student_id']) {
                $hasAccess = true;
            }
        } elseif ($role === 'dosen') {
            $lecturerModel = new LecturerModel();
            $lecturer      = $lecturerModel->where('user_id', $userId)->first();
            if ($lecturer) {
                // Cek apakah dosen pembimbing magang ini
                $assignmentModel = new SupervisorAssignmentModel();
                $assignment      = $assignmentModel->where('internship_id', $req['internship_id'])
                                                   ->where('lecturer_id', $lecturer['id'])
                                                   ->first();
                if ($assignment) {
                    $hasAccess = true;
                } else {
                    // Cek apakah dosen pengampu salah satu MK dalam permohonan konversi ini
                    $isLecturerInReq = $this->crcModel
                        ->join('courses', 'courses.id = conversion_request_courses.course_id')
                        ->where('conversion_request_courses.conversion_request_id', $requestId)
                        ->where('courses.lecturer_id', $lecturer['id'])
                        ->countAllResults();
                    if ($isLecturerInReq > 0) {
                        $hasAccess = true;
                    }
                }
            }
        }

        if (!$hasAccess) {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki hak akses untuk melihat berkas LIRS ini.');
        }

        $fullPath = WRITEPATH . $req['lirs_file'];
        if (!file_exists($fullPath)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File fisik LIRS tidak ditemukan di server.');
        }

        // Jika diminta unduh paksa (?download=1)
        if ($this->request->getGet('download') == '1') {
            $ext = pathinfo($req['lirs_file'], PATHINFO_EXTENSION);
            return $this->response->download($fullPath, null)->setFileName('LIRS_Mahasiswa_' . $req['id'] . '.' . ($ext ?: 'pdf'));
        }

        // Tampilkan inline di modal / tab browser
        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="LIRS_Mahasiswa_' . $req['id'] . '.pdf"')
            ->setBody(file_get_contents($fullPath));
    }
}
