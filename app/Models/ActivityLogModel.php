<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table            = 'activity_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'user_id',
        'action',
        'module',
        'description',
        'ip_address',
    ];

    protected $useTimestamps = true;
    protected $updatedField  = false; // Only created_at
}
