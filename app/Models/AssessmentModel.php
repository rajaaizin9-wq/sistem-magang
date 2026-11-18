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

    /**
     * Dapatkan nilai per komponen untuk mahasiswa pada mata kuliah tertentu
     */
    public function getComponentScores(int $internshipId, int $courseId)
    {
        $rows = $this->where('internship_id', $internshipId)
                     ->where('course_id', $courseId)
                     ->where('assessment_component_id IS NOT NULL', null, false)
                     ->findAll();

        $map = [];
        foreach ($rows as $r) {
            $map[$r['assessment_component_id']] = (float)$r['score'];
        }
        return $map;
    }

    /**
     * Dapatkan rekap nilai akhir utama (summary)
     */
    public function getFinalAssessment(int $internshipId, int $courseId)
    {
        return $this->where('internship_id', $internshipId)
                    ->where('course_id', $courseId)
                    ->where('assessment_component_id IS NULL', null, false)
                    ->first();
    }

    /**
     * Konversi nilai angka (0 - 100) menjadi huruf mutu resmi UMRAH
     */
    public static function convertScoreToGrade(float $score): string
    {
        if ($score >= 85.0) return 'A';
        if ($score >= 80.0) return 'A-';
        if ($score >= 75.0) return 'B+';
        if ($score >= 70.0) return 'B';
        if ($score >= 65.0) return 'B-';
        if ($score >= 60.0) return 'C+';
        if ($score >= 55.0) return 'C';
        if ($score >= 40.0) return 'D';
        return 'E';
    }
}
