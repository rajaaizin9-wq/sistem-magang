<?php

namespace App\Models;

use CodeIgniter\Model;

class SeminarModel extends Model
{
    protected $table            = 'seminars';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'internship_id',
        'course_id',
        'is_required',
        'requested_by',
        'schedule_date',
        'location',
        'status',
        'score',
        'notes',
    ];

    protected $useTimestamps = true;

    /**
     * Ambil seluruh calon seminar untuk mata kuliah yang diampu dosen
     */
    public function getCandidateSeminarsForLecturer(int $lecturerId): array
    {
        return $this->db->table('conversion_request_courses')
            ->select('conversion_request_courses.id as crc_id,
                      conversion_request_courses.status as crc_status,
                      conversion_requests.id as conversion_request_id,
                      conversion_requests.internship_id,
                      conversion_requests.student_id,
                      courses.id as course_id,
                      courses.code as course_code,
                      courses.name as course_name,
                      courses.credits as course_credits,
                      students.nim,
                      users.name as student_name,
                      internships.company_name,
                      internships.position,
                      internships.academic_year,
                      internships.semester,
                      internships.status as internship_status,
                      seminars.id as seminar_id,
                      seminars.is_required,
                      seminars.schedule_date,
                      seminars.location,
                      seminars.status as seminar_status,
                      seminars.score as seminar_score,
                      seminars.notes as seminar_notes')
            ->join('courses', 'courses.id = conversion_request_courses.course_id')
            ->join('conversion_requests', 'conversion_requests.id = conversion_request_courses.conversion_request_id')
            ->join('internships', 'internships.id = conversion_requests.internship_id')
            ->join('students', 'students.id = conversion_requests.student_id')
            ->join('users', 'users.id = students.user_id')
            ->join('seminars', 'seminars.internship_id = conversion_requests.internship_id AND seminars.course_id = courses.id', 'left')
            ->where('courses.lecturer_id', $lecturerId)
            ->where('conversion_request_courses.status', 'ACCEPTED')
            ->where('internships.status', 'INTERNSHIP_ACTIVE')
            ->orderBy('conversion_request_courses.id', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Ambil data seminar khusus untuk satu periode magang aktif tertentu
     */
    public function getSeminarsByInternship(int $internshipId): array
    {
        return $this->db->table('conversion_request_courses')
            ->select('conversion_request_courses.id as crc_id,
                      conversion_requests.internship_id,
                      courses.id as course_id,
                      courses.code as course_code,
                      courses.name as course_name,
                      courses.credits as course_credits,
                      lecturer_users.name as lecturer_name,
                      seminars.id as seminar_id,
                      seminars.is_required,
                      seminars.schedule_date,
                      seminars.location,
                      seminars.status as seminar_status,
                      seminars.score as seminar_score,
                      seminars.notes as seminar_notes')
            ->join('courses', 'courses.id = conversion_request_courses.course_id')
            ->join('lecturers', 'lecturers.id = courses.lecturer_id', 'left')
            ->join('users as lecturer_users', 'lecturer_users.id = lecturers.user_id', 'left')
            ->join('conversion_requests', 'conversion_requests.id = conversion_request_courses.conversion_request_id')
            ->join('seminars', 'seminars.internship_id = conversion_requests.internship_id AND seminars.course_id = courses.id', 'left')
            ->where('conversion_requests.internship_id', $internshipId)
            ->where('conversion_request_courses.status', 'ACCEPTED')
            ->orderBy('courses.code', 'ASC')
            ->get()
            ->getResultArray();
    }
}
