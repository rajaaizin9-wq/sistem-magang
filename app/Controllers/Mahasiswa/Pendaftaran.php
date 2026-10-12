<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\InternshipModel;
use App\Models\InternshipDocumentModel;

class Pendaftaran extends BaseController
{
    protected $studentModel;
    protected $internshipModel;
    protected $documentModel;

    public function __construct()
    {
        $this->studentModel    = new StudentModel();
        $this->internshipModel = new InternshipModel();
        $this->documentModel   = new InternshipDocumentModel();
    }

    public function index()
    {
        $userId  = session()->get('userId');
        $student = $this->studentModel->where('user_id', $userId)->first();

        if (!$student) {
            return redirect()->to('/dashboard')->with('error', 'Data profil mahasiswa tidak ditemukan.');
        }

        $internships = $this->internshipModel
            ->select('internships.*, supervisor_assignments.status as assignment_status, lecturers_user.name as supervisor_name, lecturers.nidn as supervisor_nidn')
            ->join('supervisor_assignments', 'supervisor_assignments.internship_id = internships.id', 'left')
            ->join('lecturers', 'lecturers.id = supervisor_assignments.lecturer_id', 'left')
            ->join('users as lecturers_user', 'lecturers_user.id = lecturers.user_id', 'left')
            ->where('internships.student_id', $student['id'])
            ->orderBy('internships.id', 'DESC')
            ->findAll();

        $data = [
            'title'       => 'Pendaftaran Magang',
            'student'     => $student,
            'internships' => $internships,
        ];

        return view('mahasiswa/pendaftaran/index', $data);
    }

    public function create()
    {
        $userId  = session()->get('userId');
        $student = $this->studentModel->where('user_id', $userId)->first();

        $data = [
            'title'   => 'Pengajuan Magang Baru',
            'student' => $student,
        ];

        return view('mahasiswa/pendaftaran/create', $data);
    }

    public function store()
    {
        $userId  = session()->get('userId');
        $student = $this->studentModel->where('user_id', $userId)->first();

        if (!$student) {
            return redirect()->to('/dashboard')->with('error', 'Profil mahasiswa tidak valid.');
        }

        $rules = [
            'company_name'    => 'required|min_length[3]|max_length[150]',
            'company_address' => 'required|min_length[5]',
            'position'        => 'required|min_length[2]|max_length[100]',
            'internship_type' => 'required|in_list[Online,Offline]',
            'start_date'      => 'required|valid_date',
            'end_date'        => 'required|valid_date',
            'academic_year'   => 'required',
            'semester'        => 'required|in_list[Ganjil,Genap]',
            'surat_pengantar' => 'uploaded[surat_pengantar]|max_size[surat_pengantar,2048]|ext_in[surat_pengantar,pdf,jpg,jpeg,png]',
            'surat_balasan'   => 'uploaded[surat_balasan]|max_size[surat_balasan,2048]|ext_in[surat_balasan,pdf,jpg,jpeg,png]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Save Internship Data
        $internshipData = [
            'student_id'      => $student['id'],
            'company_name'    => $this->request->getPost('company_name'),
            'company_address' => $this->request->getPost('company_address'),
            'position'        => $this->request->getPost('position'),
            'internship_type' => $this->request->getPost('internship_type'),
            'start_date'      => $this->request->getPost('start_date'),
            'end_date'        => $this->request->getPost('end_date'),
            'academic_year'   => $this->request->getPost('academic_year'),
            'semester'        => $this->request->getPost('semester'),
            'status'          => 'SUBMITTED',
        ];

        $internshipId = $this->internshipModel->insert($internshipData);

        // Upload Documents
        $uploadPath = WRITEPATH . 'uploads/documents';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $docTypes = ['surat_pengantar', 'surat_balasan'];
        foreach ($docTypes as $docType) {
            $file = $this->request->getFile($docType);
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move($uploadPath, $newName);

                $this->documentModel->insert([
                    'internship_id' => $internshipId,
                    'document_type' => $docType,
                    'file_path'     => 'uploads/documents/' . $newName,
                    'status'        => 'PENDING',
                ]);
            }
        }

        return redirect()->to('/mahasiswa/pendaftaran')->with('success', 'Pengajuan magang berhasil dikirim dan menunggu verifikasi dari Staf TU.');
    }

    public function detail($id)
    {
        $userId  = session()->get('userId');
        $student = $this->studentModel->where('user_id', $userId)->first();

        $internship = $this->internshipModel->where('id', $id)
                                             ->where('student_id', $student['id'])
                                             ->first();

        if (!$internship) {
            return redirect()->to('/mahasiswa/pendaftaran')->with('error', 'Data magang tidak ditemukan.');
        }

        $documents = $this->documentModel->where('internship_id', $id)->findAll();

        $assignmentModel = new \App\Models\SupervisorAssignmentModel();
        $assignment = $assignmentModel->select('supervisor_assignments.*, users.name as lecturer_name, users.email as lecturer_email, lecturers.nidn, lecturers.phone')
                                      ->join('lecturers', 'lecturers.id = supervisor_assignments.lecturer_id')
                                      ->join('users', 'users.id = lecturers.user_id')
                                      ->where('internship_id', $id)
                                      ->orderBy('supervisor_assignments.id', 'DESC')
                                      ->first();

        $data = [
            'title'      => 'Detail Pengajuan Magang',
            'internship' => $internship,
            'documents'  => $documents,
            'assignment' => $assignment,
        ];

        return view('mahasiswa/pendaftaran/detail', $data);
    }
}
