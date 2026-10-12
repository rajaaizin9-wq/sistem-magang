<?php

namespace App\Models;

use CodeIgniter\Model;

class ProgramStudyModel extends Model
{
    protected $table            = 'program_studies';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'code',
        'name',
    ];

    protected $useTimestamps = true;
}
