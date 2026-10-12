<?php

namespace App\Models;

use CodeIgniter\Model;

class LecturerModel extends Model
{
    protected $table            = 'lecturers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'user_id',
        'nidn',
        'program_study_id',
        'phone',
    ];

    protected $useTimestamps = true;

    public function getLecturerWithDetails($userId)
    {
        return $this->select('lecturers.*, users.name, users.email, program_studies.name as prodi_name')
                    ->join('users', 'users.id = lecturers.user_id')
                    ->join('program_studies', 'program_studies.id = lecturers.program_study_id')
                    ->where('lecturers.user_id', $userId)
                    ->first();
    }
}
