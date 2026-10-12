<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentModel extends Model
{
    protected $table            = 'students';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'user_id',
        'nim',
        'program_study_id',
        'phone',
        'address',
    ];

    protected $useTimestamps = true;

    public function getStudentWithDetails($userId)
    {
        return $this->select('students.*, users.name, users.email, program_studies.name as prodi_name, program_studies.code as prodi_code')
                    ->join('users', 'users.id = students.user_id')
                    ->join('program_studies', 'program_studies.id = students.program_study_id')
                    ->where('students.user_id', $userId)
                    ->first();
    }
}
