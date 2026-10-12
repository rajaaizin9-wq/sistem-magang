<?php

namespace App\Models;

use CodeIgniter\Model;

class ConversionRequestModel extends Model
{
    protected $table            = 'conversion_requests';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'internship_id',
        'student_id',
        'status',
        'notes',
    ];

    protected $useTimestamps = true;

    public function getRequestsByStudentId($studentId)
    {
        return $this->select('conversion_requests.*, internships.company_name, internships.position, internships.internship_type, internships.start_date, internships.end_date, internships.academic_year, internships.semester')
                    ->join('internships', 'internships.id = conversion_requests.internship_id')
                    ->where('conversion_requests.student_id', $studentId)
                    ->orderBy('conversion_requests.created_at', 'DESC')
                    ->findAll();
    }

    public function getRequestWithDetails($id)
    {
        return $this->select('conversion_requests.*, 
                             students.nim, students.phone as student_phone, 
                             users.name as student_name, users.email as student_email, 
                             program_studies.name as prodi_name, program_studies.code as prodi_code,
                             internships.company_name, internships.company_address, internships.position, 
                             internships.internship_type, internships.start_date, internships.end_date, 
                             internships.academic_year, internships.semester, internships.status as internship_status,
                             spv_lecturers.nidn as supervisor_nidn,
                             spv_users.name as supervisor_name')
                    ->join('students', 'students.id = conversion_requests.student_id')
                    ->join('users', 'users.id = students.user_id')
                    ->join('program_studies', 'program_studies.id = students.program_study_id')
                    ->join('internships', 'internships.id = conversion_requests.internship_id')
                    ->join('supervisor_assignments', 'supervisor_assignments.internship_id = internships.id AND supervisor_assignments.status = "ACCEPTED"', 'left')
                    ->join('lecturers as spv_lecturers', 'spv_lecturers.id = supervisor_assignments.lecturer_id', 'left')
                    ->join('users as spv_users', 'spv_users.id = spv_lecturers.user_id', 'left')
                    ->where('conversion_requests.id', $id)
                    ->first();
    }

    public function getRequestsForSupervisor($lecturerId)
    {
        return $this->select('conversion_requests.*, 
                             users.name as student_name, students.nim, program_studies.name as prodi_name,
                             internships.company_name, internships.position, internships.academic_year, internships.semester')
                    ->join('internships', 'internships.id = conversion_requests.internship_id')
                    ->join('supervisor_assignments', 'supervisor_assignments.internship_id = internships.id')
                    ->join('students', 'students.id = conversion_requests.student_id')
                    ->join('users', 'users.id = students.user_id')
                    ->join('program_studies', 'program_studies.id = students.program_study_id')
                    ->where('supervisor_assignments.lecturer_id', $lecturerId)
                    ->where('supervisor_assignments.status', 'ACCEPTED')
                    ->whereIn('conversion_requests.status', ['SUPERVISOR_REVIEW', 'VICE_DEAN_REVIEW', 'APPROVED', 'REJECTED'])
                    ->orderBy('conversion_requests.updated_at', 'DESC')
                    ->findAll();
    }

    public function getRequestsForWadek1()
    {
        return $this->select('conversion_requests.*, 
                             users.name as student_name, students.nim, program_studies.name as prodi_name,
                             internships.company_name, internships.position, internships.academic_year, internships.semester')
                    ->join('students', 'students.id = conversion_requests.student_id')
                    ->join('users', 'users.id = students.user_id')
                    ->join('program_studies', 'program_studies.id = students.program_study_id')
                    ->join('internships', 'internships.id = conversion_requests.internship_id')
                    ->whereIn('conversion_requests.status', ['VICE_DEAN_REVIEW', 'APPROVED', 'REJECTED'])
                    ->orderBy('conversion_requests.updated_at', 'DESC')
                    ->findAll();
    }

    public function getRequestsForKaprodi($prodiId)
    {
        return $this->select('conversion_requests.*, 
                             users.name as student_name, students.nim, program_studies.name as prodi_name,
                             internships.company_name, internships.position, internships.academic_year, internships.semester')
                    ->join('students', 'students.id = conversion_requests.student_id')
                    ->join('users', 'users.id = students.user_id')
                    ->join('program_studies', 'program_studies.id = students.program_study_id')
                    ->join('internships', 'internships.id = conversion_requests.internship_id')
                    ->where('students.program_study_id', $prodiId)
                    ->orderBy('conversion_requests.updated_at', 'DESC')
                    ->findAll();
    }
}
