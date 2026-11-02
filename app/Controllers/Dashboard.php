<?php

namespace App\Controllers;

use App\Models\InternshipModel;
use App\Models\StudentModel;
use App\Models\LecturerModel;
use App\Models\CourseModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $role   = session()->get('role');
        $userId = session()->get('userId');

        $data = [
            'title'    => 'Dashboard SIMAGANG',
            'role'     => $role,
            'userName' => session()->get('userName'),
        ];

        switch ($role) {
            case 'mahasiswa':
                $studentModel    = new StudentModel();
                $internshipModel = new InternshipModel();
                $student         = $studentModel->getStudentWithDetails($userId);
                $data['student'] = $student;

                $internships = $student ? $internshipModel
                    ->select('internships.*, supervisor_assignments.status as assignment_status, lecturers_user.name as supervisor_name')
                    ->join('supervisor_assignments', 'supervisor_assignments.internship_id = internships.id', 'left')
                    ->join('lecturers', 'lecturers.id = supervisor_assignments.lecturer_id', 'left')
                    ->join('users as lecturers_user', 'lecturers_user.id = lecturers.user_id', 'left')
                    ->where('internships.student_id', $student['id'])
                    ->orderBy('internships.id', 'DESC')
                    ->findAll() : [];
                $data['internships'] = $internships;

                // Hitung statistik penting
                $data['activeInternshipsCount'] = count(array_filter($internships, fn($i) => $i['status'] === 'INTERNSHIP_ACTIVE'));

                // Total Logbook yang sudah terisi
                $internshipIds = array_column($internships, 'id');
                $logbookCount  = 0;
                if (!empty($internshipIds)) {
                    $logbookModel = new \App\Models\InternshipLogbookModel();
                    $logbookCount = $logbookModel->whereIn('internship_id', $internshipIds)->countAllResults();
                }
                $data['logbookCount'] = $logbookCount;

                // Konversi MK
                $conversionModel         = new \App\Models\ConversionRequestModel();
                $data['conversionCount'] = $student ? $conversionModel->where('student_id', $student['id'])->countAllResults() : 0;

                return view('mahasiswa/dashboard', $data);

            case 'tu':
                $internshipModel          = new InternshipModel();
                $data['pendingApprovals'] = $internshipModel->where('status', 'SUBMITTED')->countAllResults();
                $data['totalActive']      = $internshipModel->where('status', 'INTERNSHIP_ACTIVE')->countAllResults();
                return view('tu/dashboard', $data);

            case 'kaprodi':
                $internshipModel            = new InternshipModel();
                $conversionModel            = new \App\Models\ConversionRequestModel();
                $lecturerModel              = new LecturerModel();
                $lecturer                   = $lecturerModel->where('user_id', $userId)->first();
                $data['pendingAssignments'] = $internshipModel->where('status', 'ADMIN_APPROVED')->countAllResults();
                $data['conversionCount']    = $lecturer ? count($conversionModel->getRequestsForKaprodi($lecturer['program_study_id'])) : 0;
                return view('kaprodi/dashboard', $data);

            case 'dosen':
            case 'dosen_pembimbing':
            case 'dosen_mk':
                $lecturerModel           = new LecturerModel();
                $assignmentModel         = new \App\Models\SupervisorAssignmentModel();
                $courseModel             = new CourseModel();
                $conversionCourseModel   = new \App\Models\ConversionRequestCourseModel();
                $conversionModel         = new \App\Models\ConversionRequestModel();

                // Pastikan active_role terinisialisasi
                $activeRole = session()->get('active_role');
                if (!$activeRole || !in_array($activeRole, ['dosen_pembimbing', 'dosen_mk'])) {
                    $activeRole = 'dosen_pembimbing';
                    session()->set('active_role', $activeRole);
                }
                $data['activeRole'] = $activeRole;

                $lecturer = $lecturerModel->where('user_id', $userId)->first();
                $data['lecturer'] = $lecturer;

                if ($lecturer) {
                    // Data Mahasiswa Bimbingan (Konteks: Dosen Pembimbing)
                    $data['assignments'] = $assignmentModel->select('supervisor_assignments.*, supervisor_assignments.id as assignment_id, supervisor_assignments.status as assignment_status, internships.company_name, internships.position, internships.internship_type, internships.start_date, internships.end_date, internships.status as internship_status, students.nim, users.name as student_name')
                                                          ->join('internships', 'internships.id = supervisor_assignments.internship_id')
                                                          ->join('students', 'students.id = internships.student_id')
                                                          ->join('users', 'users.id = students.user_id')
                                                          ->where('supervisor_assignments.lecturer_id', $lecturer['id'])
                                                          ->orderBy('supervisor_assignments.id', 'DESC')
                                                          ->findAll();
                    $data['pendingAssignmentsCount'] = $assignmentModel->where('lecturer_id', $lecturer['id'])
                                                                       ->where('status', 'PENDING')
                                                                       ->countAllResults();
                    $data['activeStudentsCount']     = $assignmentModel->where('lecturer_id', $lecturer['id'])
                                                                       ->where('status', 'ACCEPTED')
                                                                       ->countAllResults();

                    // Data Mata Kuliah Pengampuan (Konteks: Dosen MK)
                    $data['courses'] = $courseModel->where('lecturer_id', $lecturer['id'])->findAll();

                    // Konversi Stats
                    $lecConversionReqs = $conversionCourseModel->getRequestsForLecturer($lecturer['id']);
                    $data['pendingCourseConversionCount'] = count(array_filter($lecConversionReqs, fn($r) => $r['status'] === 'PENDING'));

                    $spvConversionReqs = $conversionModel->getRequestsForSupervisor($lecturer['id']);
                    $data['pendingSupervisorConversionCount'] = count(array_filter($spvConversionReqs, fn($r) => $r['status'] === 'SUPERVISOR_REVIEW'));

                    // Seminar Stats untuk Dosen MK
                    $seminarModel = new \App\Models\SeminarModel();
                    $candidateSeminars = $seminarModel->getCandidateSeminarsForLecturer($lecturer['id']);
                    $data['candidateSeminarsCount'] = count($candidateSeminars);
                } else {
                    $data['assignments']                      = [];
                    $data['pendingAssignmentsCount']          = 0;
                    $data['activeStudentsCount']              = 0;
                    $data['courses']                          = [];
                    $data['pendingCourseConversionCount']     = 0;
                    $data['pendingSupervisorConversionCount'] = 0;
                    $data['candidateSeminarsCount']           = 0;
                }

                return view('dosen/dashboard', $data);

            case 'wadek1':
                $internshipModel = new InternshipModel();
                $conversionModel = new \App\Models\ConversionRequestModel();
                $data['totalActive'] = $internshipModel->where('status', 'INTERNSHIP_ACTIVE')->countAllResults();
                $data['totalSupervisorAssigned'] = $internshipModel->where('status', 'SUPERVISOR_ASSIGNED')->countAllResults();
                $data['totalAll'] = $internshipModel->countAllResults();
                $data['totalCompleted'] = $internshipModel->whereIn('status', ['INTERNSHIP_COMPLETED', 'COMPLETED'])->countAllResults();
                $data['pendingConversionCount'] = $conversionModel->where('status', 'VICE_DEAN_REVIEW')->countAllResults();

                // 5 Data Magang Aktif Terkini untuk Quick Monitoring (hanya status INTERNSHIP_ACTIVE)
                $data['recentActiveInternships'] = $internshipModel->select('internships.*, students.nim, users.name as student_name, program_studies.name as prodi_name, lecturers_user.name as supervisor_name')
                                                                   ->join('students', 'students.id = internships.student_id')
                                                                   ->join('users', 'users.id = students.user_id')
                                                                   ->join('program_studies', 'program_studies.id = students.program_study_id')
                                                                   ->join('supervisor_assignments', 'supervisor_assignments.internship_id = internships.id', 'left')
                                                                   ->join('lecturers', 'lecturers.id = supervisor_assignments.lecturer_id', 'left')
                                                                   ->join('users as lecturers_user', 'lecturers_user.id = lecturers.user_id', 'left')
                                                                   ->where('internships.status', 'INTERNSHIP_ACTIVE')
                                                                   ->orderBy('internships.id', 'DESC')
                                                                   ->findAll(5);

                return view('wadek1/dashboard', $data);

            default:
                return view('mahasiswa/dashboard', $data);
        }
    }
}
