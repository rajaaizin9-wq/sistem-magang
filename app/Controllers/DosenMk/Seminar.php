<?php

namespace App\Controllers\DosenMk;

use App\Controllers\BaseController;
use App\Models\LecturerModel;
use App\Models\CourseModel;
use App\Models\SeminarModel;

class Seminar extends BaseController
{
    protected $lecturerModel;
    protected $courseModel;
    protected $seminarModel;

    public function __construct()
    {
        $this->lecturerModel = new LecturerModel();
        $this->courseModel   = new CourseModel();
        $this->seminarModel  = new SeminarModel();
    }

    public function index()
    {
        $userId   = session()->get('userId');
        $lecturer = $this->lecturerModel->where('user_id', $userId)->first();

        if (!$lecturer) {
            return redirect()->to('/dashboard')->with('error', 'Profil dosen tidak ditemukan.');
        }

        $candidates = $this->seminarModel->getCandidateSeminarsForLecturer($lecturer['id']);

        $pendingCount     = count(array_filter($candidates, fn($c) => empty($c['seminar_status'])));
        $scheduledCount   = count(array_filter($candidates, fn($c) => ($c['seminar_status'] ?? '') === 'SCHEDULED'));
        $completedCount   = count(array_filter($candidates, fn($c) => ($c['seminar_status'] ?? '') === 'COMPLETED'));
        $notRequiredCount = count(array_filter($candidates, fn($c) => ($c['seminar_status'] ?? '') === 'NOT_REQUIRED'));

        $data = [
            'title'            => 'Pengaturan & Penilaian Seminar Magang',
            'lecturer'         => $lecturer,
            'candidates'       => $candidates,
            'pendingCount'     => $pendingCount,
            'scheduledCount'   => $scheduledCount,
            'completedCount'   => $completedCount,
            'notRequiredCount' => $notRequiredCount,
        ];

        return view('dosen_mk/seminar/index', $data);
    }

    public function setDecision()
    {
        $userId   = session()->get('userId');
        $lecturer = $this->lecturerModel->where('user_id', $userId)->first();

        if (!$lecturer) {
            return redirect()->to('/dashboard')->with('error', 'Profil dosen tidak ditemukan.');
        }

        $internshipId = (int)$this->request->getPost('internship_id');
        $courseId     = (int)$this->request->getPost('course_id');
        $decision     = $this->request->getPost('decision'); // 'NOT_REQUIRED' or 'SCHEDULED'
        $notes        = $this->request->getPost('notes');

        // Validasi bahwa dosen ini benar mengampu mata kuliah yang dimaksud
        $course = $this->courseModel->where('id', $courseId)
                                    ->where('lecturer_id', $lecturer['id'])
                                    ->first();

        if (!$course) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak untuk mengatur seminar pada mata kuliah ini.');
        }

        if (!in_array($decision, ['NOT_REQUIRED', 'SCHEDULED'])) {
            return redirect()->back()->with('error', 'Pilihan keputusan seminar tidak valid.');
        }

        $existing = $this->seminarModel->where('internship_id', $internshipId)
                                       ->where('course_id', $courseId)
                                       ->first();

        if ($decision === 'NOT_REQUIRED') {
            $dataSave = [
                'internship_id' => $internshipId,
                'course_id'     => $courseId,
                'is_required'   => 0,
                'requested_by'  => $userId,
                'schedule_date' => null,
                'location'      => null,
                'status'        => 'NOT_REQUIRED',
                'notes'         => $notes ?: 'Ditetapkan bebas seminar oleh Dosen Pengampu MK.',
            ];

            if ($existing) {
                $this->seminarModel->update($existing['id'], $dataSave);
            } else {
                $this->seminarModel->insert($dataSave);
            }

            return redirect()->to('/dosen-mk/seminar')->with('success', 'Mata kuliah ' . $course['name'] . ' berhasil ditetapkan: Bebas Seminar (Tidak Diperlukan).');
        }

        // Jika SCHEDULED, validasi tanggal dan ruangan
        $rules = [
            'schedule_date' => 'required',
            'location'      => 'required|min_length[3]|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $scheduleDate = $this->request->getPost('schedule_date');
        $location     = $this->request->getPost('location');

        $dataSave = [
            'internship_id' => $internshipId,
            'course_id'     => $courseId,
            'is_required'   => 1,
            'requested_by'  => $userId,
            'schedule_date' => date('Y-m-d H:i:s', strtotime($scheduleDate)),
            'location'      => $location,
            'status'        => 'SCHEDULED',
            'notes'         => $notes ?: null,
        ];

        if ($existing) {
            $this->seminarModel->update($existing['id'], $dataSave);
        } else {
            $this->seminarModel->insert($dataSave);
        }

        return redirect()->to('/dosen-mk/seminar')->with('success', 'Jadwal seminar untuk ' . $course['name'] . ' berhasil ditetapkan.');
    }

    public function submitScore($seminarId)
    {
        $userId   = session()->get('userId');
        $lecturer = $this->lecturerModel->where('user_id', $userId)->first();

        if (!$lecturer) {
            return redirect()->to('/dashboard')->with('error', 'Profil dosen tidak ditemukan.');
        }

        $seminar = $this->seminarModel->find($seminarId);
        if (!$seminar) {
            return redirect()->back()->with('error', 'Data seminar tidak ditemukan.');
        }

        // Verifikasi kepemilikan mata kuliah
        $course = $this->courseModel->where('id', $seminar['course_id'])
                                    ->where('lecturer_id', $lecturer['id'])
                                    ->first();

        if (!$course) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak untuk menilai seminar ini.');
        }

        $rules = [
            'score' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
            'notes' => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $score = (float)$this->request->getPost('score');
        $notes = $this->request->getPost('notes');

        $this->seminarModel->update($seminarId, [
            'score'  => $score,
            'notes'  => $notes ?: $seminar['notes'],
            'status' => 'COMPLETED',
        ]);

        return redirect()->to('/dosen-mk/seminar')->with('success', 'Nilai seminar berhasil disimpan dengan skor ' . $score . '.');
    }
}
