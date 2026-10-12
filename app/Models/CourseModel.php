<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table            = 'courses';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'code',
        'name',
        'credits',
        'program_study_id',
        'lecturer_id',
        'is_elective',
    ];

    protected $useTimestamps = true;

    public function getElectiveCoursesByProdi($prodiId)
    {
        return $this->where('program_study_id', $prodiId)
                    ->where('is_elective', 1)
                    ->findAll();
    }
}
