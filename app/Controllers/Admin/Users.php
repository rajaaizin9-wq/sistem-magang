<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LecturerModel;
use App\Models\ProgramStudyModel;
use App\Models\UserModel;

/**
 * CRUD akun Kaprodi & Instansi Magang oleh Admin.
 * $type: 'kaprodi' | 'instansi' (role instansi: penilaian belum diimplementasikan).
 */
class Users extends BaseController
{
    private const TYPES = ['kaprodi' => 'Kaprodi', 'instansi' => 'Instansi Magang'];

    private UserModel $users;
    private LecturerModel $lecturers;

    public function __construct()
    {
        $this->users     = new UserModel();
        $this->lecturers = new LecturerModel();
    }

    private function guardType(string $type): void
    {
        if (!isset(self::TYPES[$type])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    public function searchStudents()
    {
        $q = trim((string)$this->request->getGet('q'));
        if (strlen($q) < 1) {
            return $this->response->setJSON([]);
        }

        $internshipModel = new \App\Models\InternshipModel();
        $results = $internshipModel
            ->select('internships.id, internships.company_name, internships.position, students.nim, users.name as student_name')
            ->join('students', 'students.id = internships.student_id')
            ->join('users', 'users.id = students.user_id')
            ->groupStart()
                ->like('users.name', $q)
                ->orLike('students.nim', $q)
                ->orLike('internships.company_name', $q)
            ->groupEnd()
            ->orderBy('internships.id', 'DESC')
            ->limit(15)
            ->findAll();

        return $this->response->setJSON($results);
    }

    public function index(string $type)
    {
        $this->guardType($type);

        $builder = $this->users->select('users.*')->where('users.role', $type)->orderBy('users.name');
        if ($type === 'kaprodi') {
            $builder->select('lecturers.nidn, program_studies.name as prodi_name')
                    ->join('lecturers', 'lecturers.user_id = users.id AND lecturers.deleted_at IS NULL', 'left')
                    ->join('program_studies', 'program_studies.id = lecturers.program_study_id', 'left');
        } elseif ($type === 'instansi') {
            $builder->select('users.*, COUNT(DISTINCT internships.id) as student_count, GROUP_CONCAT(DISTINCT users_student.name SEPARATOR ", ") as student_names')
                    ->join('internships', '(internships.company_user_id = users.id OR LOWER(TRIM(internships.company_name)) = LOWER(TRIM(users.name))) AND internships.deleted_at IS NULL', 'left')
                    ->join('students', 'students.id = internships.student_id', 'left')
                    ->join('users as users_student', 'users_student.id = students.user_id', 'left')
                    ->groupBy('users.id');
        }

        $pendingRequests = [];
        if ($type === 'instansi') {
            $internshipModel = new \App\Models\InternshipModel();
            $pendingRequests = $internshipModel
                ->select('internships.*, students.nim, users.name as student_name')
                ->join('students', 'students.id = internships.student_id')
                ->join('users', 'users.id = students.user_id')
                ->where('internships.instansi_account_requested_at IS NOT NULL')
                ->where('internships.company_user_id IS NULL')
                ->orderBy('internships.instansi_account_requested_at', 'DESC')
                ->findAll();
        }

        return view('admin/users/index', [
            'title'           => 'Kelola ' . self::TYPES[$type],
            'type'            => $type,
            'label'           => self::TYPES[$type],
            'items'           => $builder->findAll(),
            'pendingRequests' => $pendingRequests,
        ]);
    }

    public function form(string $type, ?int $id = null)
    {
        $this->guardType($type);

        $item = null;
        if ($id) {
            $item = $this->users->where('role', $type)->find($id) ?? throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            $lecturer = $this->lecturers->where('user_id', $id)->first();
            $item    += ['nidn' => $lecturer['nidn'] ?? '', 'program_study_id' => $lecturer['program_study_id'] ?? ''];
        }

        $selectedInternshipIds = [];
        $selectedStudents      = [];
        if ($type === 'instansi') {
            $internshipModel = new \App\Models\InternshipModel();

            // Cek jika dibuka dari tombol permintaan akun mahasiswa (?from_internship=X)
            $fromInternshipId = (int)$this->request->getGet('from_internship');
            if ($fromInternshipId > 0 && !$item) {
                $fromReq = $internshipModel
                    ->select('internships.*, students.nim, users.name as student_name')
                    ->join('students', 'students.id = internships.student_id')
                    ->join('users', 'users.id = students.user_id')
                    ->find($fromInternshipId);

                if ($fromReq) {
                    $item = [
                        'name'      => $fromReq['company_name'],
                        'email'     => $fromReq['instansi_pic_email'] ?: '',
                        'is_active' => 1,
                    ];
                    $selectedInternshipIds = [$fromInternshipId];
                }
            } elseif ($id && $item) {
                $compNameEscaped = $internshipModel->db->escape(strtolower(trim($item['name'])));
                $selectedInternshipIds = $internshipModel
                    ->where("(company_user_id = {$id} OR LOWER(TRIM(company_name)) = {$compNameEscaped})")
                    ->findColumn('id') ?: [];
            }

            if (!empty($selectedInternshipIds)) {
                $selectedStudents = $internshipModel
                    ->select('internships.id, internships.company_name, internships.position, students.nim, users.name as student_name')
                    ->join('students', 'students.id = internships.student_id')
                    ->join('users', 'users.id = students.user_id')
                    ->whereIn('internships.id', $selectedInternshipIds)
                    ->findAll();
            }
        }

        return view('admin/users/form', [
            'title'            => ($id ? 'Edit ' : 'Tambah ') . self::TYPES[$type],
            'type'             => $type,
            'label'            => self::TYPES[$type],
            'item'             => $item,
            'prodis'           => $type === 'kaprodi' ? (new ProgramStudyModel())->orderBy('name')->findAll() : [],
            'selectedStudents' => $selectedStudents,
        ]);
    }

    public function save(string $type, ?int $id = null)
    {
        $this->guardType($type);
        if ($id && !$this->users->where('role', $type)->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $lecturer = $id ? $this->lecturers->where('user_id', $id)->first() : null;
        $rules = [
            'name'     => 'required|max_length[100]',
            'email'    => 'required|valid_email|is_unique[users.email,id,' . ($id ?? 0) . ']',
            'password' => ($id ? 'permit_empty' : 'required') . '|min_length[8]',
        ];
        if ($type === 'kaprodi') {
            $rules['nidn']             = 'required|max_length[30]|is_unique[lecturers.nidn,id,' . ($lecturer['id'] ?? 0) . ']';
            $rules['program_study_id'] = 'required|is_not_unique[program_studies.id]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $userData = [
            'name'      => $this->request->getPost('name'),
            'email'     => $this->request->getPost('email'),
            'role'      => $type,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ];
        if ($pw = $this->request->getPost('password')) {
            $userData['password'] = password_hash($pw, PASSWORD_DEFAULT);
        }

        $db = \Config\Database::connect();
        $db->transStart();
        $id ? $this->users->update($id, $userData) : $id = $this->users->insert($userData);
        if ($type === 'kaprodi') {
            $this->lecturers->save([
                'id'               => $lecturer['id'] ?? null,
                'user_id'          => $id,
                'nidn'             => $this->request->getPost('nidn'),
                'program_study_id' => $this->request->getPost('program_study_id'),
            ]);
        } elseif ($type === 'instansi') {
            $internshipModel     = new \App\Models\InternshipModel();
            $postedInternshipIds = $this->request->getPost('internship_ids') ?: [];

            // Lepaskan tautan lama
            $internshipModel->where('company_user_id', $id)->set(['company_user_id' => null])->update();

            // Hubungkan yang dipilih
            if (!empty($postedInternshipIds)) {
                $internshipModel->whereIn('id', $postedInternshipIds)->set(['company_user_id' => $id])->update();
            } else {
                $companyName = trim((string)$userData['name']);
                if (!empty($companyName)) {
                    $internshipModel->where('LOWER(TRIM(company_name))', strtolower($companyName))
                                    ->set(['company_user_id' => $id])
                                    ->update();
                }
            }
        }
        $db->transComplete();

        return redirect()->to("admin/users/$type")->with('success', self::TYPES[$type] . ' berhasil disimpan.');
    }

    public function delete(string $type, int $id)
    {
        $this->guardType($type);
        if ($this->users->where('role', $type)->find($id)) {
            $this->lecturers->where('user_id', $id)->delete();
            $this->users->delete($id);
        }

        return redirect()->to("admin/users/$type")->with('success', self::TYPES[$type] . ' berhasil dihapus.');
    }
}
