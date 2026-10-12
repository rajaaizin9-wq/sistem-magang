<?php

namespace App\Models;

use CodeIgniter\Model;

class AssessmentModel extends Model
{
    protected $table            = 'assessments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'internship_id',
        'course_id',
        'assessor_id',
        'assessment_component_id',
        'score',
        'final_grade',
        'verified_status',
        'notes',
    ];

    protected $useTimestamps = true;
}
