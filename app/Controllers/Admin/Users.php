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

    public function index(string $type)
    {
        $this->guardType($type);

        $builder = $this->users->select('users.*')->where('users.role', $type)->orderBy('users.name');
        if ($type === 'kaprodi') {
            $builder->select('lecturers.nidn, program_studies.name as prodi_name')
                    ->join('lecturers', 'lecturers.user_id = users.id AND lecturers.deleted_at IS NULL', 'left')
                    ->join('program_studies', 'program_studies.id = lecturers.program_study_id', 'left');
        }

        return view('admin/users/index', [
            'title' => 'Kelola ' . self::TYPES[$type],
            'type'  => $type,
            'label' => self::TYPES[$type],
            'items' => $builder->findAll(),
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

        return view('admin/users/form', [
            'title'   => ($id ? 'Edit ' : 'Tambah ') . self::TYPES[$type],
            'type'    => $type,
            'label'   => self::TYPES[$type],
            'item'    => $item,
            'prodis'  => $type === 'kaprodi' ? (new ProgramStudyModel())->orderBy('name')->findAll() : [],
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
