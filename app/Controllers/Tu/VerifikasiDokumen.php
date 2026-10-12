<?php

namespace App\Controllers\Tu;

use App\Controllers\BaseController;
use App\Models\InternshipModel;
use App\Models\InternshipDocumentModel;

class VerifikasiDokumen extends BaseController
{
    protected $internshipModel;
    protected $documentModel;

    public function __construct()
    {
        $this->internshipModel = new InternshipModel();
        $this->documentModel   = new InternshipDocumentModel();
    }

    public function index()
    {
        $statusFilter = $this->request->getGet('status') ?? '';

        $builder = $this->internshipModel->select('internships.*, students.nim, users.name as student_name, program_studies.code as prodi_code')
                                          ->join('students', 'students.id = internships.student_id')
                                          ->join('users', 'users.id = students.user_id')
                                          ->join('program_studies', 'program_studies.id = students.program_study_id')
                                          ->orderBy('internships.id', 'DESC');

        if (!empty($statusFilter)) {
            $builder->where('internships.status', $statusFilter);
        } else {
            // Default tampilkan pengajuan yang perlu tindakan TU (SUBMITTED, REVISION_REQUIRED, ADMIN_APPROVED)
            $builder->whereIn('internships.status', ['SUBMITTED', 'ADMIN_REVIEW', 'REVISION_REQUIRED', 'ADMIN_APPROVED']);
        }

        $internships = $builder->findAll();

        $data = [
            'title'        => 'Verifikasi Dokumen Pendaftaran Magang (TU)',
            'internships'  => $internships,
            'statusFilter' => $statusFilter,
        ];

        return view('tu/verifikasi/index', $data);
    }

    public function detail($id)
    {
        $internship = $this->internshipModel->getInternshipWithDetails($id);

        if (!$internship) {
            return redirect()->to('/tu/verifikasi-dokumen')->with('error', 'Data permohonan magang tidak ditemukan.');
        }

        $documents = $this->documentModel->where('internship_id', $id)->findAll();

        $data = [
            'title'      => 'Detail Verifikasi Administrasi',
            'internship' => $internship,
            'documents'  => $documents,
        ];

        return view('tu/verifikasi/detail', $data);
    }

    public function process($id)
    {
        $internship = $this->internshipModel->find($id);

        if (!$internship) {
            return redirect()->to('/tu/verifikasi-dokumen')->with('error', 'Data magang tidak valid.');
        }

        $action = $this->request->getPost('action');
        $notes  = $this->request->getPost('notes');

        if ($action === 'approve') {
            $this->internshipModel->update($id, [
                'status' => 'ADMIN_APPROVED',
            ]);

            // Update status dokumen menjadi APPROVED
            $this->documentModel->where('internship_id', $id)->set([
                'status'      => 'APPROVED',
                'verified_by' => session()->get('userId'),
                'verified_at' => date('Y-m-d H:i:s'),
                'notes'       => $notes ?: 'Dokumen telah diverifikasi dan disetujui oleh Staf TU.',
            ])->update();

            return redirect()->to('/tu/verifikasi-dokumen')->with('success', 'Permohonan magang berhasil disetujui dan diteruskan ke Kaprodi.');
        } elseif ($action === 'revision') {
            if (empty($notes)) {
                return redirect()->back()->withInput()->with('error', 'Wajib mengisi catatan perbaikan/revisi untuk mahasiswa.');
            }

            $this->internshipModel->update($id, [
                'status' => 'REVISION_REQUIRED',
            ]);

            $this->documentModel->where('internship_id', $id)->set([
                'status'      => 'REVISION_REQUIRED',
                'verified_by' => session()->get('userId'),
                'verified_at' => date('Y-m-d H:i:s'),
                'notes'       => $notes,
            ])->update();

            return redirect()->to('/tu/verifikasi-dokumen')->with('success', 'Status diubah menjadi Perlunya Revisi, catatan perbaikan dikirim ke mahasiswa.');
        }

        return redirect()->back()->with('error', 'Aksi tidak valid.');
    }

    public function serveDocument($id)
    {
        $doc = $this->documentModel->find($id);
        if (!$doc) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Dokumen tidak ditemukan.');
        }

        $fullPath = WRITEPATH . $doc['file_path'];
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
