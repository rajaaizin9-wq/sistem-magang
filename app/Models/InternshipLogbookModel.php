<?php

namespace App\Models;

use CodeIgniter\Model;

class InternshipLogbookModel extends Model
{
    protected $table            = 'internship_logbooks';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'internship_id',
        'week_number',
        'date',
        'activity_description',
        'result',
        'obstacle',
        'attachment',
        'status',
        'lecturer_notes',
        'verified_at',
    ];

    protected $useTimestamps = true;

    /**
     * Get all logbooks for a given internship, ordered by date descending
     */
    public function getLogbooksByInternshipId(int $internshipId): array
    {
        return $this->where('internship_id', $internshipId)
                    ->orderBy('date', 'DESC')
                    ->orderBy('id', 'DESC')
                    ->findAll();
    }
}
