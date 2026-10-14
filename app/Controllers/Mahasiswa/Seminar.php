<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\InternshipModel;
use App\Models\SeminarModel;

class Seminar extends BaseController
{
    protected $studentModel;
    protected $internshipModel;
    protected $seminarModel;

    public function __construct()
    {
        $this->studentModel    = new StudentModel();
        $this->internshipModel = new InternshipModel();
        $this->seminarModel    = new SeminarModel();
    }

    public function index()
    {
        $userId  = session()->get('userId');
        $student = $this->studentModel->where('user_id', $userId)->first();

        if (!$student) {
            return redirect()->to('/dashboard')->with('error', 'Profil mahasiswa tidak ditemukan.');
        }

        // Ambil hanya periode magang mahasiswa yang berstatus AKTIF (sedang berjalan)
        $activeInternships = $this->internshipModel
            ->where('student_id', $student['id'])
            ->where('status', 'INTERNSHIP_ACTIVE')
            ->orderBy('id', 'DESC')
            ->findAll();

        $selectedInternship = null;
        $seminars           = [];

        if (!empty($activeInternships)) {
            $reqInternshipId = $this->request->getGet('internship_id');
            if ($reqInternshipId) {
                foreach ($activeInternships as $item) {
                    if ((int)$item['id'] === (int)$reqInternshipId) {
                        $selectedInternship = $item;
                        break;
                    }
                }
            }

            if (!$selectedInternship) {
                $selectedInternship = $activeInternships[0];
            }

            // Hanya tarik data mata kuliah konversi & seminar dari periode magang aktif terpilih
            $seminars = $this->seminarModel->getSeminarsByInternship($selectedInternship['id']);
        }

        $data = [
            'title'              => 'Informasi & Jadwal Seminar Magang',
            'student'            => $student,
            'activeInternships'  => $activeInternships,
            'selectedInternship' => $selectedInternship,
            'seminars'           => $seminars,
        ];

        return view('mahasiswa/seminar/index', $data);
    }
}
