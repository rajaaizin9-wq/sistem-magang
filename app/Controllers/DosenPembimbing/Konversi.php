<?php

namespace App\Controllers\DosenPembimbing;

use App\Controllers\BaseController;
use App\Models\LecturerModel;
use App\Models\ConversionRequestModel;
use App\Models\ConversionRequestCourseModel;
use App\Models\ConversionApprovalModel;
use App\Models\SupervisorAssignmentModel;

class Konversi extends BaseController
{
    protected $lecturerModel;
    protected $conversionRequestModel;
    protected $conversionRequestCourseModel;
    protected $conversionApprovalModel;
    protected $supervisorAssignmentModel;

    public function __construct()
    {
        $this->lecturerModel                = new LecturerModel();
        $this->conversionRequestModel       = new ConversionRequestModel();
        $this->conversionRequestCourseModel = new ConversionRequestCourseModel();
        $this->conversionApprovalModel      = new ConversionApprovalModel();
        $this->supervisorAssignmentModel    = new SupervisorAssignmentModel();
    }

    public function index()
    {
        $userId   = session()->get('userId');
        $lecturer = $this->lecturerModel->where('user_id', $userId)->first();

        if (!$lecturer) {
            return redirect()->to('/dashboard')->with('error', 'Profil dosen tidak ditemukan.');
        }

        $requests = $this->conversionRequestModel->getRequestsForSupervisor($lecturer['id']);

        foreach ($requests as &$req) {
            $courses = $this->conversionRequestCourseModel->getCoursesByRequestId($req['id']);
            $req['courses']        = $courses;
            $req['total_credits']  = array_sum(array_column($courses, 'credits'));
            $req['accepted_count'] = count(array_filter($courses, fn($c) => $c['status'] === 'ACCEPTED'));
        }

        $pendingCount = count(array_filter($requests, fn($r) => $r['status'] === 'SUPERVISOR_REVIEW'));

        $data = [
            'title'        => 'Verifikasi Konversi Mahasiswa Bimbingan',
            'lecturer'     => $lecturer,
            'requests'     => $requests,
            'pendingCount' => $pendingCount,
        ];

        return view('dosen_pembimbing/konversi/index', $data);
    }

    public function detail($id)
    {
        $userId   = session()->get('userId');
        $lecturer = $this->lecturerModel->where('user_id', $userId)->first();

        if (!$lecturer) {
            return redirect()->to('/dashboard')->with('error', 'Profil dosen tidak ditemukan.');
        }

        $request = $this->conversionRequestModel->getRequestWithDetails($id);

        if (!$request) {
            return redirect()->to('/dosen-pembimbing/konversi')->with('error', 'Pengajuan konversi tidak ditemukan.');
        }

        // Verify that this lecturer is the assigned supervisor
        $assignment = $this->supervisorAssignmentModel
            ->where('internship_id', $request['internship_id'])
            ->where('lecturer_id', $lecturer['id'])
            ->where('status', 'ACCEPTED')
            ->first();

        if (!$assignment) {
            return redirect()->to('/dosen-pembimbing/konversi')->with('error', 'Anda tidak tercatat sebagai Dosen Pembimbing untuk magang ini.');
        }

        $courses   = $this->conversionRequestCourseModel->getCoursesByRequestId($id);
        $approvals = $this->conversionApprovalModel->getApprovalsByRequestId($id);

        $totalCredits    = array_sum(array_column($courses, 'credits'));
        $acceptedCredits = 0;
        foreach ($courses as $c) {
            if ($c['status'] === 'ACCEPTED') {
                $acceptedCredits += $c['credits'];
            }
        }

        $data = [
            'title'           => 'Verifikasi Konversi #' . $request['id'] . ' - ' . $request['student_name'],
            'request'         => $request,
            'courses'         => $courses,
            'approvals'       => $approvals,
            'totalCredits'    => $totalCredits,
            'acceptedCredits' => $acceptedCredits,
        ];

        return view('dosen_pembimbing/konversi/detail', $data);
    }

    public function process($id)
    {
        $userId   = session()->get('userId');
        $lecturer = $this->lecturerModel->where('user_id', $userId)->first();

        if (!$lecturer) {
            return redirect()->to('/dashboard')->with('error', 'Profil dosen tidak ditemukan.');
        }

        $request = $this->conversionRequestModel->where('id', $id)->first();

        if (!$request || $request['status'] !== 'SUPERVISOR_REVIEW') {
            return redirect()->to('/dosen-pembimbing/konversi')->with('error', 'Status pengajuan konversi tidak dalam tahap verifikasi pembimbing.');
        }

        $action = $this->request->getPost('action'); // 'APPROVED' or 'REJECTED'
        $notes  = $this->request->getPost('notes');

        if (!in_array($action, ['APPROVED', 'REJECTED'])) {
            return redirect()->back()->with('error', 'Tindakan yang dipilih tidak valid.');
        }

        if ($action === 'REJECTED' && empty(trim($notes))) {
            return redirect()->back()->withInput()->with('error', 'Harap sertakan alasan penolakan.');
        }

        $nextStatus = ($action === 'APPROVED') ? 'VICE_DEAN_REVIEW' : 'REJECTED';

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Update Request Status
        $this->conversionRequestModel->update($id, [
            'status' => $nextStatus,
        ]);

        // 2. Insert into Approval Trail
        $this->conversionApprovalModel->insert([
            'conversion_request_id' => $id,
            'approver_id'           => $userId,
            'role'                  => 'dosen_pembimbing',
            'status'                => $action,
            'notes'                 => $notes ?: 'Disetujui oleh Dosen Pembimbing',
            'approved_at'           => date('Y-m-d H:i:s'),
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal memproses verifikasi konversi.');
        }

        $msg = ($action === 'APPROVED')
            ? 'Pengajuan konversi berhasil disetujui dan diteruskan ke Wakil Dekan 1.'
            : 'Pengajuan konversi telah ditolak.';

        return redirect()->to('/dosen-pembimbing/konversi')->with('success', $msg);
    }
}
