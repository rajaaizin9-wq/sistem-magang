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
            return redirect()->to('/dashboard')->with('error', 'Profil Dosen Pembimbing tidak ditemukan.');
        }

        // Penugasan Pembimbing (Pending & Accepted)
        $assignments = $this->assignmentModel->select('supervisor_assignments.*, supervisor_assignments.id as assignment_id, supervisor_assignments.status as assignment_status, internships.company_name, internships.position, internships.internship_type, internships.start_date, internships.end_date, internships.status as internship_status, students.nim, users.name as student_name')
                                              ->join('internships', 'internships.id = supervisor_assignments.internship_id')
                                              ->join('students', 'students.id = internships.student_id')
                                              ->join('users', 'users.id = students.user_id')
                                              ->where('supervisor_assignments.lecturer_id', $lecturer['id'])
                                              ->orderBy('supervisor_assignments.id', 'DESC')
                                              ->findAll();

        $data = [
            'title'       => 'Pemantauan Logbook & Respon Pembimbingan',
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

            return redirect()->to('/dosen-pembimbing/pemantauan-logbook')->with('success', 'Anda telah menerima penugasan pembimbingan. Status magang mahasiswa kini aktif.');
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
}
