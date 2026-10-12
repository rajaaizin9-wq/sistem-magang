<?php

namespace App\Models;

use CodeIgniter\Model;

class AssessmentComponentModel extends Model
{
    protected $table            = 'assessment_components';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'course_id',
        'title',
        'description',
        'weight_percentage',
        'max_score',
    ];

    protected $useTimestamps = true;
}
