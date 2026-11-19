<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\InstansiAssessmentComponentModel;

class KriteriaInstansi extends BaseController
{
    protected InstansiAssessmentComponentModel $componentModel;

    public function __construct()
    {
        $this->componentModel = new InstansiAssessmentComponentModel();
    }

    public function index()
    {
        $components = $this->componentModel->getAllComponents();

        return view('admin/kriteria_instansi/index', [
            'title'      => 'Komponen Penilaian Instansi',
            'components' => $components,
        ]);
    }

    public function save(?int $id = null)
    {
        $rules = [
            'name'         => 'required|max_length[150]',
            'order_number' => 'required|is_natural_no_zero',
            'description'  => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $data = [
            'name'         => trim((string)$this->request->getPost('name')),
            'order_number' => (int)$this->request->getPost('order_number'),
            'description'  => trim((string)$this->request->getPost('description')),
            'is_active'    => $this->request->getPost('is_active') ? 1 : 0,
        ];

        if ($id) {
            $this->componentModel->update($id, $data);
            $msg = 'Komponen penilaian berhasil diperbarui.';
        } else {
            $this->componentModel->insert($data);
            $msg = 'Komponen penilaian baru berhasil ditambahkan.';
        }

        return redirect()->to('admin/kriteria-instansi')->with('success', $msg);
    }

    public function delete(int $id)
    {
        $component = $this->componentModel->find($id);
        if (!$component) {
            return redirect()->to('admin/kriteria-instansi')->with('error', 'Komponen penilaian tidak ditemukan.');
        }

        $this->componentModel->delete($id);

        return redirect()->to('admin/kriteria-instansi')->with('success', 'Komponen penilaian berhasil dihapus.');
    }

    public function resetDefault()
    {
        $this->componentModel->resetToDefault();

        return redirect()->to('admin/kriteria-instansi')->with('success', 'Komponen penilaian instansi berhasil dikembalikan ke 5 kriteria standar (default).');
    }
}

