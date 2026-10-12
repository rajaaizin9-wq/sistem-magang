<?php

namespace App\Models;

use CodeIgniter\Model;

class InternshipDocumentModel extends Model
{
    protected $table            = 'internship_documents';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'internship_id',
        'document_type',
        'file_path',
        'status',
        'notes',
        'verified_by',
        'verified_at',
    ];

    protected $useTimestamps = true;
}
