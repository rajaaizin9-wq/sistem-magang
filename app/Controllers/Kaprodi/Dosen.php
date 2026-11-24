<?php

namespace App\Controllers\Kaprodi;

use App\Controllers\BaseController;
use App\Models\LecturerModel;
use App\Models\ProgramStudyModel;
use App\Models\UserModel;

class Dosen extends BaseController
{
    protected LecturerModel $lecturerModel;
    protected UserModel $userModel;
    protected ProgramStudyModel $programStudyModel;

    public function __construct()
    {
        $this->lecturerModel     = new LecturerModel();
        $this->userModel         = new UserModel();
        $this->programStudyModel = new ProgramStudyModel();
    }

    public function index()
    {
        $userId  = session()->get('userId');
        $kaprodi = $this->lecturerModel->where('user_id', $userId)->first();

        if (!$kaprodi) {
            return redirect()->to('/dashboard')->with('error', 'Profil Kaprodi tidak ditemukan.');
        }

        $prodiId     = $kaprodi['program_study_id'];
        $filterProdi = $this->request->getGet('prodi');
        $search      = trim((string)$this->request->getGet('q'));

        $builder = $this->lecturerModel
            ->select('lecturers.id as lecturer_id, lecturers.nidn, lecturers.phone, lecturers.program_study_id, lecturers.is_wadek, users.id as user_id, users.name, users.email, users.role, program_studies.name as prodi_name')
            ->join('users', 'users.id = lecturers.user_id')
            ->join('program_studies', 'program_studies.id = lecturers.program_study_id', 'left')
            ->where('users.is_active', 1)
            ->where('users.role', 'dosen')
            ->orderBy('lecturers.is_wadek', 'DESC')
            ->orderBy('users.name', 'ASC');

        if (!empty($filterProdi)) {
            $builder->where('lecturers.program_study_id', $filterProdi);
        } else {
            // Default: tampilkan dosen di prodi Kaprodi
            $builder->where('lecturers.program_study_id', $prodiId);
        }

        if (!empty($search)) {
            $builder->groupStart()
                ->like('users.name', $search)
                ->orLike('users.email', $search)
                ->orLike('lecturers.nidn', $search)
                ->groupEnd();
        }

        $lecturers = $builder->findAll();
        $allProdis = $this->programStudyModel->orderBy('name', 'ASC')->findAll();

        $data = [
            'title'        => 'Kelola Role Dosen',
            'kaprodi'      => $kaprodi,
            'lecturers'    => $lecturers,
            'allProdis'    => $allProdis,
            'currentProdi' => $filterProdi ?: $prodiId,
            'search'       => $search,
        ];

        return view('kaprodi/dosen/index', $data);
    }

    public function updateRole($lecturerId)
    {
        $newRole = $this->request->getPost('role');

        if (!in_array($newRole, ['dosen', 'wadek1'])) {
            return redirect()->back()->with('error', 'Pilihan role tidak valid.');
        }

        $lecturer = $this->lecturerModel->find($lecturerId);
        if (!$lecturer) {
            return redirect()->back()->with('error', 'Data dosen tidak ditemukan.');
        }

        $user = $this->userModel->find($lecturer['user_id']);
        if (!$user) {
            return redirect()->back()->with('error', 'Akun user dosen tidak ditemukan.');
        }

        $targetIsWadek = ($newRole === 'wadek1') ? 1 : 0;

        if ((int)($lecturer['is_wadek'] ?? 0) === $targetIsWadek) {
            return redirect()->back()->with('info', "Peran dosen {$user['name']} sudah bernilai " . ($newRole === 'wadek1' ? 'Wakil Dekan 1' : 'Dosen') . '.');
        }

        if ($targetIsWadek === 1) {
            // Reset wadek1 sebelumnya di fakultas agar hanya ada 1 Wadek 1 aktif
            $this->lecturerModel->where('is_wadek', 1)
                                ->where('id !=', $lecturerId)
                                ->set(['is_wadek' => 0])
                                ->update();

            // Set dosen ini memiliki role/tugas struktural Wadek 1
            $this->lecturerModel->update($lecturerId, ['is_wadek' => 1]);

            // Akun login tetap akun dosen (role = 'dosen')
            $this->userModel->update($user['id'], ['role' => 'dosen']);

            return redirect()->back()->with('success', "Dosen {$user['name']} berhasil ditugaskan memiliki role Wakil Dekan 1 (Wadek 1). Beliau login menggunakan akun dosennya dan dapat beralih ke peran Wadek 1.");
        }

        // Kembalikan ke Dosen biasa
        $this->lecturerModel->update($lecturerId, ['is_wadek' => 0]);
        $this->userModel->update($user['id'], ['role' => 'dosen']);

        return redirect()->back()->with('success', "Peran Wakil Dekan 1 untuk {$user['name']} berhasil dicabut. Akun kembali menjadi Dosen biasa.");
    }
}

