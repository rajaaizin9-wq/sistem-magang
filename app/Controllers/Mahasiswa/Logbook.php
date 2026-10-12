<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\InternshipModel;
use App\Models\InternshipLogbookModel;
use App\Models\SupervisorAssignmentModel;

class Logbook extends BaseController
{
    protected $studentModel;
    protected $internshipModel;
    protected $logbookModel;
    protected $assignmentModel;

    public function __construct()
    {
        $this->studentModel    = new StudentModel();
        $this->internshipModel = new InternshipModel();
        $this->logbookModel    = new InternshipLogbookModel();
        $this->assignmentModel = new SupervisorAssignmentModel();
    }

    public function index()
    {
        $userId  = session()->get('userId');
        $student = $this->studentModel->where('user_id', $userId)->first();

        if (!$student) {
            return redirect()->to('/dashboard')->with('error', 'Profil mahasiswa tidak ditemukan.');
        }

        // Hanya ambil periode magang milik mahasiswa yang berstatus AKTIF (sedang berjalan)
        $internships = $this->internshipModel
            ->where('student_id', $student['id'])
            ->where('status', 'INTERNSHIP_ACTIVE')
            ->orderBy('id', 'DESC')
            ->findAll();

        $selectedInternshipId = $this->request->getGet('internship_id');
        $selectedInternship   = null;

        if (!empty($internships)) {
            if ($selectedInternshipId) {
                foreach ($internships as $item) {
                    if ((int)$item['id'] === (int)$selectedInternshipId) {
                        $selectedInternship = $item;
                        break;
                    }
                }
            }

            // Jika tidak ada ID di GET param, gunakan data magang aktif pertama
            if (!$selectedInternship) {
                $selectedInternship = $internships[0];
            }
        }

        $supervisor = null;
        $logbooks   = [];
        $suggestedWeek = 1;

        if ($selectedInternship) {
            // Ambil data dosen pembimbing / pemantau jika ada
            $supervisor = $this->assignmentModel
                ->select('supervisor_assignments.*, lecturers.nidn, users.name as supervisor_name, users.email as supervisor_email')
                ->join('lecturers', 'lecturers.id = supervisor_assignments.lecturer_id')
                ->join('users', 'users.id = lecturers.user_id')
                ->where('supervisor_assignments.internship_id', $selectedInternship['id'])
                ->where('supervisor_assignments.status', 'ACCEPTED')
                ->first();

            $logbooks = $this->logbookModel->getLogbooksByInternshipId($selectedInternship['id']);

            // Hitung estimasi minggu berjalan
            if (!empty($selectedInternship['start_date'])) {
                $start = strtotime($selectedInternship['start_date']);
                $now   = time();
                if ($now > $start) {
                    $diffWeeks = (int)ceil(($now - $start) / (7 * 86400));
                    $suggestedWeek = max(1, $diffWeeks);
                }
            }
        }

        $data = [
            'title'              => 'Jurnal Aktivitas / Logbook Magang',
            'student'            => $student,
            'internships'        => $internships,
            'selectedInternship' => $selectedInternship,
            'supervisor'         => $supervisor,
            'logbooks'           => $logbooks,
            'suggestedWeek'      => $suggestedWeek,
        ];

        return view('mahasiswa/logbook/index', $data);
    }

    public function store()
    {
        $userId  = session()->get('userId');
        $student = $this->studentModel->where('user_id', $userId)->first();

        if (!$student) {
            return redirect()->to('/dashboard')->with('error', 'Profil mahasiswa tidak valid.');
        }

        $rules = [
            'internship_id'        => 'required|numeric',
            'date'                 => 'required|valid_date',
            'week_number'          => 'permit_empty|numeric|greater_than_equal_to[1]',
            'activity_description' => 'required|min_length[5]',
            'result'               => 'permit_empty',
            'obstacle'             => 'permit_empty',
            'attachment'           => 'permit_empty|max_size[attachment,3072]|ext_in[attachment,pdf,jpg,jpeg,png]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $internshipId = (int)$this->request->getPost('internship_id');
        $internship   = $this->internshipModel
            ->where('id', $internshipId)
            ->where('student_id', $student['id'])
            ->where('status', 'INTERNSHIP_ACTIVE')
            ->first();

        if (!$internship) {
            return redirect()->back()->withInput()->with('error', 'Pencatatan logbook hanya diizinkan untuk periode magang yang berstatus aktif (sedang berjalan).');
        }

        $date       = $this->request->getPost('date');
        $weekNumber = (int)$this->request->getPost('week_number');
        if ($weekNumber <= 0) {
            $weekNumber = 1;
        }

        // Validasi Duplikasi: Cegah penambahan entri logbook pada tanggal yang sama
        $existingLog = $this->logbookModel
            ->where('internship_id', $internshipId)
            ->where('date', $date)
            ->first();

        if ($existingLog) {
            return redirect()->back()->withInput()->with('error', 'Catatan aktivitas untuk tanggal ' . format_indo($date, false, false) . ' (Minggu ke-' . $existingLog['week_number'] . ') sudah pernah dibuat. Anda tidak dapat menambahkan catatan duplikat pada tanggal yang sama.');
        }

        // Upload attachment jika ada
        $attachmentPath = null;
        $file = $this->request->getFile('attachment');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $uploadPath = WRITEPATH . 'uploads/logbooks';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);
            $attachmentPath = 'uploads/logbooks/' . $newName;
        }

        $this->logbookModel->insert([
            'internship_id'        => $internshipId,
            'week_number'          => $weekNumber,
            'date'                 => $this->request->getPost('date'),
            'activity_description' => $this->request->getPost('activity_description'),
            'result'               => $this->request->getPost('result') ?: null,
            'obstacle'             => $this->request->getPost('obstacle') ?: null,
            'attachment'           => $attachmentPath,
            'status'               => 'SUBMITTED',
        ]);

        return redirect()->to('/mahasiswa/logbook?internship_id=' . $internshipId)
            ->with('success', 'Catatan jurnal aktivitas berhasil disimpan.');
    }

    public function delete($id)
    {
        $userId  = session()->get('userId');
        $student = $this->studentModel->where('user_id', $userId)->first();

        if (!$student) {
            return redirect()->to('/dashboard')->with('error', 'Profil tidak valid.');
        }

        $logbook = $this->logbookModel->find($id);
        if (!$logbook) {
            return redirect()->back()->with('error', 'Catatan logbook tidak ditemukan.');
        }

        // Verifikasi kepemilikan
        $internship = $this->internshipModel
            ->where('id', $logbook['internship_id'])
            ->where('student_id', $student['id'])
            ->first();

        if (!$internship) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak untuk menghapus catatan ini.');
        }

        // Hapus file fisik jika ada
        if (!empty($logbook['attachment'])) {
            $fullPath = WRITEPATH . $logbook['attachment'];
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }
        }

        $this->logbookModel->delete($id);

        return redirect()->to('/mahasiswa/logbook?internship_id=' . $internship['id'])
            ->with('success', 'Catatan logbook berhasil dihapus.');
    }

    public function serveAttachment($id)
    {
        $logbook = $this->logbookModel->find($id);
        if (!$logbook || empty($logbook['attachment'])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File lampiran tidak ditemukan.');
        }

        $fullPath = WRITEPATH . $logbook['attachment'];
        if (!file_exists($fullPath)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File fisik tidak ditemukan di server.');
        }

        $mime = mime_content_type($fullPath);
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($fullPath));
        readfile($fullPath);
        exit;
    }
}
