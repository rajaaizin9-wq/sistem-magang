<?php

namespace App\Controllers\Kaprodi;

use App\Controllers\BaseController;
use App\Models\LecturerModel;
use App\Models\ConversionRequestModel;
use App\Models\ConversionRequestCourseModel;
use App\Models\ConversionApprovalModel;

class Konversi extends BaseController
{
    protected $lecturerModel;
    protected $conversionRequestModel;
    protected $conversionRequestCourseModel;
    protected $conversionApprovalModel;

    public function __construct()
    {
        $this->lecturerModel                = new LecturerModel();
        $this->conversionRequestModel       = new ConversionRequestModel();
        $this->conversionRequestCourseModel = new ConversionRequestCourseModel();
        $this->conversionApprovalModel      = new ConversionApprovalModel();
    }

    public function index()
    {
        $userId   = session()->get('userId');
        $lecturer = $this->lecturerModel->where('user_id', $userId)->first();

        if (!$lecturer) {
            return redirect()->to('/dashboard')->with('error', 'Profil kaprodi tidak ditemukan.');
        }

        $requests = $this->conversionRequestModel->getRequestsForKaprodi($lecturer['program_study_id']);

        foreach ($requests as &$req) {
            $courses = $this->conversionRequestCourseModel->getCoursesByRequestId($req['id']);
            $req['courses']        = $courses;
            $req['total_credits']  = array_sum(array_column($courses, 'credits'));
            $req['accepted_count'] = count(array_filter($courses, fn($c) => $c['status'] === 'ACCEPTED'));
        }

        $data = [
            'title'    => 'Pemantauan Konversi MK Prodi (Kaprodi)',
            'lecturer' => $lecturer,
            'requests' => $requests,
        ];

        return view('kaprodi/konversi/index', $data);
    }

    public function detail($id)
    {
        $request = $this->conversionRequestModel->getRequestWithDetails($id);

        if (!$request) {
            return redirect()->to('/kaprodi/konversi')->with('error', 'Pengajuan konversi tidak ditemukan.');
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
            'title'           => 'Detail Konversi #' . $request['id'] . ' - ' . $request['student_name'],
            'request'         => $request,
            'courses'         => $courses,
            'approvals'       => $approvals,
            'totalCredits'    => $totalCredits,
            'acceptedCredits' => $acceptedCredits,
        ];

        return view('kaprodi/konversi/detail', $data);
    }
}
