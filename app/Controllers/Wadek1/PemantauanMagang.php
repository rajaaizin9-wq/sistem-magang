<?php

namespace App\Controllers\Wadek1;

use App\Controllers\BaseController;
use App\Models\InternshipModel;
use App\Models\InternshipDocumentModel;
use App\Models\SupervisorAssignmentModel;
use App\Models\ProgramStudyModel;

class PemantauanMagang extends BaseController
{
    protected $internshipModel;
    protected $documentModel;
    protected $assignmentModel;
    protected $prodiModel;

    public function __construct()
    {
        $this->internshipModel = new InternshipModel();
        $this->documentModel   = new InternshipDocumentModel();
        $this->assignmentModel = new SupervisorAssignmentModel();
        $this->prodiModel      = new ProgramStudyModel();
    }

    /**
     * Tampilan daftar pemantauan magang tingkat fakultas (Read-Only)
     */
    public function index()
    {
        $tab     = $this->request->getGet('tab') ?? 'active';
        $prodiId = $this->request->getGet('prodi') ?? '';

        $builder = $this->internshipModel->select('internships.*, students.nim, users.name as student_name, program_studies.name as prodi_name, program_studies.code as prodi_code, lecturers_user.name as supervisor_name, lecturers.nidn as supervisor_nidn, supervisor_assignments.status as assignment_status')
                                          ->join('students', 'students.id = internships.student_id')
                                          ->join('users', 'users.id = students.user_id')
                                          ->join('program_studies', 'program_studies.id = students.program_study_id')
                                          ->join('supervisor_assignments', 'supervisor_assignments.internship_id = internships.id', 'left')
                                          ->join('lecturers', 'lecturers.id = supervisor_assignments.lecturer_id', 'left')
                                          ->join('users as lecturers_user', 'lecturers_user.id = lecturers.user_id', 'left')
                                          ->orderBy('internships.id', 'DESC');

        if ($tab === 'active') {
            // Hanya magang yang berstatus INTERNSHIP_ACTIVE (magang aktif berjalan)
            $builder->where('internships.status', 'INTERNSHIP_ACTIVE');
        }

        if (!empty($prodiId)) {
            $builder->where('students.program_study_id', $prodiId);
        }

        $internships = $builder->findAll();
        $prodis      = $this->prodiModel->findAll();

        // Metrik cepat untuk overview Wadek 1
        $totalActiveFaculty   = $this->internshipModel->where('status', 'INTERNSHIP_ACTIVE')->countAllResults();
        $totalAllRegistrations = $this->internshipModel->countAllResults();

        $data = [
            'title'                 => 'Pemantauan Pendaftaran Magang Fakultas (Wadek 1)',
            'internships'           => $internships,
            'prodis'                => $prodis,
            'currentTab'            => $tab,
            'selectedProdi'         => $prodiId,
            'totalActiveFaculty'    => $totalActiveFaculty,
            'totalAllRegistrations' => $totalAllRegistrations,
        ];

        return view('wadek1/pemantauan/index', $data);
    }

    /**
     * Tampilan detail pendaftaran magang mahasiswa (Read-Only)
     */
    public function detail($id)
    {
        $internship = $this->internshipModel->getInternshipWithDetails($id);

        if (!$internship) {
            return redirect()->to('/wadek1/pemantauan-magang')->with('error', 'Data pendaftaran magang tidak ditemukan.');
        }

        $documents  = $this->documentModel->where('internship_id', $id)->findAll();
        $assignment = $this->assignmentModel->select('supervisor_assignments.*, users.name as lecturer_name, users.email as lecturer_email, lecturers.nidn, lecturers.phone')
                                            ->join('lecturers', 'lecturers.id = supervisor_assignments.lecturer_id')
                                            ->join('users', 'users.id = lecturers.user_id')
                                            ->where('internship_id', $id)
                                            ->orderBy('id', 'DESC')
                                            ->first();

        $data = [
            'title'      => 'Detail Pemantauan Magang Mahasiswa',
            'internship' => $internship,
            'documents'  => $documents,
            'assignment' => $assignment,
        ];

        return view('wadek1/pemantauan/detail', $data);
    }
}
