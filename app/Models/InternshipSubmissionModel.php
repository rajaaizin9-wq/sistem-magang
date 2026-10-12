<?php

namespace App\Models;

use CodeIgniter\Model;

class InternshipSubmissionModel extends Model
{
    protected $table            = 'internship_submissions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'assessment_component_id',
        'internship_id',
        'file_path',
        'student_notes',
        'status',
        'feedback_notes',
    ];

    protected $useTimestamps = true;
}
