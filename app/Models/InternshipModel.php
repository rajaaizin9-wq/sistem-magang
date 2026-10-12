<?php

namespace App\Models;

use CodeIgniter\Model;

class InternshipModel extends Model
{
    protected $table            = 'internships';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'student_id',
        'company_name',
        'company_address',
        'position',
        'internship_type',
        'start_date',
        'end_date',
        'academic_year',
        'semester',
        'status',
    ];

    protected $useTimestamps = true;

    public function getInternshipWithDetails($id)
    {
        return $this->select('internships.*, students.nim, users.name as student_name, users.email as student_email, program_studies.name as prodi_name')
                    ->join('students', 'students.id = internships.student_id')
                    ->join('users', 'users.id = students.user_id')
                    ->join('program_studies', 'program_studies.id = students.program_study_id')
                    ->where('internships.id', $id)
                    ->first();
    }
}
