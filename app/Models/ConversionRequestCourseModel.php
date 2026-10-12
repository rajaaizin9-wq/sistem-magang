<?php

namespace App\Models;

use CodeIgniter\Model;

class ConversionRequestCourseModel extends Model
{
    protected $table            = 'conversion_request_courses';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'conversion_request_id',
        'course_id',
        'student_justification',
        'status',
        'rejection_reason',
    ];

    protected $useTimestamps = true;

    public function getCoursesByRequestId($requestId)
    {
        return $this->select('conversion_request_courses.*, courses.code as course_code, courses.name as course_name, courses.credits, users.name as lecturer_name, lecturers.nidn as lecturer_nidn, courses.lecturer_id')
                    ->join('courses', 'courses.id = conversion_request_courses.course_id')
                    ->join('lecturers', 'lecturers.id = courses.lecturer_id', 'left')
                    ->join('users', 'users.id = lecturers.user_id', 'left')
                    ->where('conversion_request_courses.conversion_request_id', $requestId)
                    ->findAll();
    }

    public function getRequestsForLecturer($lecturerId)
    {
        return $this->select('conversion_request_courses.*, courses.code as course_code, courses.name as course_name, courses.credits,
                             conversion_requests.status as request_status, conversion_requests.created_at as request_date,
                             students.nim, users.name as student_name,
                             internships.company_name, internships.position')
                    ->join('courses', 'courses.id = conversion_request_courses.course_id')
                    ->join('conversion_requests', 'conversion_requests.id = conversion_request_courses.conversion_request_id')
                    ->join('students', 'students.id = conversion_requests.student_id')
                    ->join('users', 'users.id = students.user_id')
                    ->join('internships', 'internships.id = conversion_requests.internship_id')
                    ->where('courses.lecturer_id', $lecturerId)
                    ->orderBy('conversion_requests.created_at', 'DESC')
                    ->findAll();
    }
}
