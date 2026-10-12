<?php

namespace App\Models;

use CodeIgniter\Model;

class SupervisorAssignmentModel extends Model
{
    protected $table            = 'supervisor_assignments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'internship_id',
        'lecturer_id',
        'assigned_by',
        'status',
        'rejection_reason',
    ];

    protected $useTimestamps = true;
}
