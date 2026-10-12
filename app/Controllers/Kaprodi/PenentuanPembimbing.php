<?php

namespace App\Controllers\Kaprodi;

use App\Controllers\BaseController;
use App\Models\LecturerModel;
use App\Models\InternshipModel;
use App\Models\InternshipDocumentModel;
use App\Models\SupervisorAssignmentModel;

class PenentuanPembimbing extends BaseController
{
    protected $lecturerModel;
    protected $internshipModel;
    protected $documentModel;
    protected $assignmentModel;

    public function __construct()
    {
        $this->lecturerModel   = new LecturerModel();
        $this->internshipModel = new InternshipModel();
        $this->documentModel   = new InternshipDocumentModel();
        $this->assignmentModel = new SupervisorAssignmentModel();
    }

    public function index()
    {
        $userId   = session()->get('userId');
        $kaprodi  = $this->lecturerModel->where('user_id', $userId)->first();
        $tab      = $this->request->getGet('tab') ?? 'pending';

        if (!$kaprodi) {
            return redirect()->to('/dashboard')->with('error', 'Profil Kaprodi tidak ditemukan.');
        }

        $prodiId = $kaprodi['program_study_id'];

        $builder = $this->internshipModel->select('internships.*, students.nim, users.name as student_name, lecturers_user.name as supervisor_name, supervisor_assignments.status as assignment_status, supervisor_assignments.rejection_reason')
                                          ->join('students', 'students.id = internships.student_id')
                                          ->join('users', 'users.id = students.user_id')
                                          ->join('program_studies', 'program_studies.id = students.program_study_id')
                                          ->join('supervisor_assignments', 'supervisor_assignments.internship_id = internships.id', 'left')
                                          ->join('lecturers', 'lecturers.id = supervisor_assignments.lecturer_id', 'left')
                                          ->join('users as lecturers_user', 'lecturers_user.id = lecturers.user_id', 'left')
                                          ->where('students.program_study_id', $prodiId)
                                          ->orderBy('internships.id', 'DESC');

        if ($tab === 'pending') {
            // Hanya pengajuan yang SUDAH DISERUJUI TU yang muncul untuk penentuan pembimbing
            $builder->whereIn('internships.status', ['ADMIN_APPROVED', 'SUPERVISOR_ASSIGNED']);
        }

        $internships = $builder->findAll();

        $lecturers = $this->lecturerModel->select('lecturers.id, users.name, lecturers.nidn')
                                         ->join('users', 'users.id = lecturers.user_id')
                                         ->where('lecturers.program_study_id', $prodiId)
                                         ->where('users.role', 'dosen')
                                         ->where('users.is_active', 1)
                                         ->orderBy('users.name', 'ASC')
                                         ->findAll();

        $data = [
            'title'       => 'Penentuan Dosen Pembimbing & Pemantauan (Kaprodi)',
            'internships' => $internships,
            'lecturers'   => $lecturers,
            'tab'         => $tab,
        ];

        return view('kaprodi/pembimbing/index', $data);
    }

    public function detail($id)
    {
        $userId  = session()->get('userId');
        $kaprodi = $this->lecturerModel->where('user_id', $userId)->first();

        $internship = $this->internshipModel->getInternshipWithDetails($id);

        if (!$internship) {
            return redirect()->to('/kaprodi/penentuan-pembimbing')->with('error', 'Data magang tidak ditemukan.');
        }

        $documents = $this->documentModel->where('internship_id', $id)->findAll();

        $assignment = $this->assignmentModel->select('supervisor_assignments.*, users.name as lecturer_name, lecturers.nidn')
                                            ->join('lecturers', 'lecturers.id = supervisor_assignments.lecturer_id')
                                            ->join('users', 'users.id = lecturers.user_id')
                                            ->where('internship_id', $id)
                                            ->orderBy('id', 'DESC')
                                            ->first();

        $lecturers = $this->lecturerModel->select('lecturers.id, users.name, lecturers.nidn')
                                         ->join('users', 'users.id = lecturers.user_id')
                                         ->where('lecturers.program_study_id', $kaprodi['program_study_id'])
                                         ->where('users.role', 'dosen')
                                         ->where('users.is_active', 1)
                                         ->orderBy('users.name', 'ASC')
                                         ->findAll();

        // Pengecekan apakah status sudah diverifikasi TU sehingga Dosen Pembimbing boleh ditentukan
        $canAssign = in_array($internship['status'], ['ADMIN_APPROVED', 'SUPERVISOR_ASSIGNED', 'INTERNSHIP_ACTIVE', 'INTERNSHIP_COMPLETED', 'REPORT_SUBMITTED', 'ASSESSMENT', 'COMPLETED']);

        $data = [
            'title'      => 'Detail Pengajuan & Penentuan Pembimbing',
            'internship' => $internship,
            'documents'  => $documents,
            'assignment' => $assignment,
            'lecturers'  => $lecturers,
            'canAssign'  => $canAssign,
        ];

        return view('kaprodi/pembimbing/detail', $data);
    }

    public function assign($id)
    {
        $internship = $this->internshipModel->find($id);

        if (!$internship) {
            return redirect()->back()->with('error', 'Data magang tidak valid.');
        }

        // Pengecekan backend strict: Jika belum diverifikasi TU, tolak penunjukan Dospem
        if (!in_array($internship['status'], ['ADMIN_APPROVED', 'SUPERVISOR_ASSIGNED'])) {
            return redirect()->back()->with('error', 'Penetapan Dosen Pembimbing hanya dapat dilakukan jika permohonan magang telah diverifikasi dan disetujui oleh Staf TU.');
        }

        $lecturerId = $this->request->getPost('lecturer_id');

        if (empty($lecturerId)) {
            return redirect()->back()->with('error', 'Silakan pilih Dosen Pembimbing terlebih dahulu.');
        }

        $selectedLecturer = $this->lecturerModel->select('lecturers.*, users.role')
                                               ->join('users', 'users.id = lecturers.user_id')
                                               ->where('lecturers.id', $lecturerId)
                                               ->where('users.role', 'dosen')
                                               ->where('users.is_active', 1)
                                               ->first();

        if (!$selectedLecturer) {
            return redirect()->back()->with('error', 'Dosen yang dipilih tidak valid.');
        }

        // Hapus penugasan lama jika ada, buat penugasan baru
        $this->assignmentModel->where('internship_id', $id)->delete();

        $this->assignmentModel->insert([
            'internship_id' => $id,
            'lecturer_id'   => $lecturerId,
            'assigned_by'   => session()->get('userId'),
            'status'        => 'PENDING',
        ]);

        // Update status magang menjadi SUPERVISOR_ASSIGNED
        $this->internshipModel->update($id, [
            'status' => 'SUPERVISOR_ASSIGNED',
        ]);

        return redirect()->to('/kaprodi/penentuan-pembimbing')->with('success', 'Dosen Pembimbing berhasil ditentukan. Menunggu konfirmasi kesediaan dari Dosen terkait.');
    }
}
