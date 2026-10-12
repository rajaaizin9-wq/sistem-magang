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
}
