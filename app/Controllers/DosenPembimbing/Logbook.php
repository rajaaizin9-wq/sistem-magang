<?php

namespace App\Controllers\DosenPembimbing;

use App\Controllers\BaseController;
use App\Models\LecturerModel;
use App\Models\InternshipModel;
use App\Models\SupervisorAssignmentModel;
use App\Models\InternshipLogbookModel;

class Logbook extends BaseController
{
    protected $lecturerModel;
    protected $internshipModel;
    protected $assignmentModel;
    protected $logbookModel;

    public function __construct()
    {
        $this->lecturerModel   = new LecturerModel();
        $this->internshipModel = new InternshipModel();
        $this->assignmentModel = new SupervisorAssignmentModel();
        $this->logbookModel    = new InternshipLogbookModel();
    }

    public function index()
    {
        $userId   = session()->get('userId');
        $lecturer = $this->lecturerModel->where('user_id', $userId)->first();

        if (!$lecturer) {
            return redirect()->to('/dashboard')->with('error', 'Profil Dosen tidak ditemukan.');
        }

        // Penugasan Pembimbing / Pemantau
        $assignments = $this->assignmentModel->select('supervisor_assignments.*, supervisor_assignments.id as assignment_id, supervisor_assignments.status as assignment_status, internships.company_name, internships.position, internships.internship_type, internships.start_date, internships.end_date, internships.status as internship_status, students.nim, users.name as student_name')
                                              ->join('internships', 'internships.id = supervisor_assignments.internship_id')
                                              ->join('students', 'students.id = internships.student_id')
                                              ->join('users', 'users.id = students.user_id')
                                              ->where('supervisor_assignments.lecturer_id', $lecturer['id'])
                                              ->orderBy('supervisor_assignments.id', 'DESC')
                                              ->findAll();

        // Hitung jumlah logbook per penugasan untuk informasi cepat dosen
        foreach ($assignments as &$item) {
            $item['total_logs'] = $this->logbookModel->where('internship_id', $item['internship_id'])->countAllResults();
        }

        $data = [
            'title'       => 'Pemantauan Logbook Mahasiswa Magang',
            'lecturer'    => $lecturer,
            'assignments' => $assignments,
        ];

        return view('dosen_pembimbing/pemantauan/index', $data);
    }

    public function responAssignment($assignmentId)
    {
        $action = $this->request->getPost('action');
        $reason = $this->request->getPost('rejection_reason');

        $assignment = $this->assignmentModel->find($assignmentId);

        if (!$assignment) {
            return redirect()->back()->with('error', 'Data penugasan tidak ditemukan.');
        }

        if ($action === 'accept') {
            $this->assignmentModel->update($assignmentId, [
                'status' => 'ACCEPTED',
            ]);

            // Update status magang menjadi INTERNSHIP_ACTIVE
            $this->internshipModel->update($assignment['internship_id'], [
                'status' => 'INTERNSHIP_ACTIVE',
            ]);

            return redirect()->to('/dosen-pembimbing/pemantauan-logbook')->with('success', 'Anda telah menerima penugasan pemantauan. Status magang mahasiswa kini aktif.');
        } elseif ($action === 'reject') {
            if (empty($reason)) {
                return redirect()->back()->with('error', 'Wajib melampirkan alasan penolakan penugasan.');
            }

            $this->assignmentModel->update($assignmentId, [
                'status'           => 'REJECTED',
                'rejection_reason' => $reason,
            ]);

            // Kembalikan status magang ke ADMIN_APPROVED agar Kaprodi bisa menunjuk dosen lain
            $this->internshipModel->update($assignment['internship_id'], [
                'status' => 'ADMIN_APPROVED',
            ]);

            return redirect()->to('/dosen-pembimbing/pemantauan-logbook')->with('success', 'Penolakan penugasan berhasil dikirim. Kaprodi akan menerima notifikasi untuk menunjuk dosen lain.');
        }

        return redirect()->back()->with('error', 'Aksi tidak valid.');
    }

    public function detail($assignmentId)
    {
        $userId   = session()->get('userId');
        $lecturer = $this->lecturerModel->where('user_id', $userId)->first();

        if (!$lecturer) {
            return redirect()->to('/dashboard')->with('error', 'Profil Dosen tidak ditemukan.');
        }

        $assignment = $this->assignmentModel->select('supervisor_assignments.*, supervisor_assignments.id as assignment_id, supervisor_assignments.status as assignment_status, internships.company_name, internships.company_address, internships.position, internships.internship_type, internships.start_date, internships.end_date, internships.academic_year, internships.semester, internships.status as internship_status, students.nim, students.phone as student_phone, users.name as student_name, users.email as student_email, program_studies.name as prodi_name')
            ->join('internships', 'internships.id = supervisor_assignments.internship_id')
            ->join('students', 'students.id = internships.student_id')
            ->join('users', 'users.id = students.user_id')
            ->join('program_studies', 'program_studies.id = students.program_study_id')
            ->where('supervisor_assignments.id', $assignmentId)
            ->where('supervisor_assignments.lecturer_id', $lecturer['id'])
            ->first();

        if (!$assignment) {
            return redirect()->to('/dosen-pembimbing/pemantauan-logbook')->with('error', 'Data penugasan tidak ditemukan atau Anda tidak memiliki hak akses.');
        }

        if ($assignment['assignment_status'] !== 'ACCEPTED' || $assignment['internship_status'] !== 'INTERNSHIP_ACTIVE') {
            return redirect()->to('/dosen-pembimbing/pemantauan-logbook')->with('error', 'Pemantauan logbook hanya dapat diakses untuk program magang yang berstatus aktif (sedang berjalan).');
        }

        $logbooks = $this->logbookModel->getLogbooksByInternshipId($assignment['internship_id']);

        $data = [
            'title'      => 'Pemantauan Logbook: ' . $assignment['student_name'],
            'lecturer'   => $lecturer,
            'assignment' => $assignment,
            'logbooks'   => $logbooks,
        ];

        return view('dosen_pembimbing/pemantauan/detail', $data);
    }

    public function addNote($logbookId)
    {
        $userId   = session()->get('userId');
        $lecturer = $this->lecturerModel->where('user_id', $userId)->first();

        if (!$lecturer) {
            return redirect()->to('/dashboard')->with('error', 'Profil Dosen tidak ditemukan.');
        }

        $logbook = $this->logbookModel->find($logbookId);
        if (!$logbook) {
            return redirect()->back()->with('error', 'Catatan logbook tidak ditemukan.');
        }

        // Verifikasi bahwa logbook ini adalah milik mahasiswa bimbingan dosen yang login
        $assignment = $this->assignmentModel
            ->where('internship_id', $logbook['internship_id'])
            ->where('lecturer_id', $lecturer['id'])
            ->where('status', 'ACCEPTED')
            ->first();

        if (!$assignment) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk memantau mahasiswa ini.');
        }

        $note = $this->request->getPost('lecturer_notes');

        $this->logbookModel->update($logbookId, [
            'lecturer_notes' => $note,
            'status'         => 'VERIFIED',
            'verified_at'    => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->with('success', 'Catatan pemantauan berhasil disimpan.');
    }

    public function serveAttachment($logbookId)
    {
        $logbook = $this->logbookModel->find($logbookId);
        if (!$logbook || empty($logbook['attachment'])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File lampiran tidak ditemukan.');
        }

        $fullPath = WRITEPATH . $logbook['attachment'];
        if (!file_exists($fullPath)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File fisik tidak ditemukan.');
        }

        $mime = mime_content_type($fullPath);
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($fullPath));
        readfile($fullPath);
        exit;
    }
}
