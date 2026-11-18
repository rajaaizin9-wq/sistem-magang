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

    /**
     * Ambil seluruh komponen penilaian & pengumpulan untuk mata kuliah tertentu
     */
    public function getComponentsByCourse(int $courseId)
    {
        return $this->where('course_id', $courseId)
                    ->orderBy('id', 'ASC')
                    ->findAll();
    }

    /**
     * Hitung total bobot (%) yang sudah dipakai oleh mata kuliah tertentu
     */
    public function getTotalWeightByCourse(int $courseId, ?int $excludeId = null): float
    {
        $builder = $this->where('course_id', $courseId);
        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }
        $row = $builder->selectSum('weight_percentage', 'total')->first();
        return (float)($row['total'] ?? 0);
    }
}
