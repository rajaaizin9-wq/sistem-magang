<?php

namespace App\Models;

use CodeIgniter\Model;

class InstansiAssessmentModel extends Model
{
    protected $table            = 'instansi_assessments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'internship_id',
        'instansi_user_id',
        'evaluator_name',
        'evaluator_position',
        'scores_json',
        'score_field_activity',
        'score_craftsmanship',
        'score_initiative',
        'score_communication',
        'score_problem_solving',
        'total_score',
        'final_score',
        'grade',
        'feedback_notes',
        'assessment_date',
    ];

    protected $useTimestamps = true;

    /**
     * Konversi nilai angka (0-100) ke huruf mutu standar
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

    /**
     * Ambil penilaian instansi untuk 1 magang tertentu
     */
    public function getAssessmentByInternship(int $internshipId): ?array
    {
        return $this->where('internship_id', $internshipId)->first();
    }
}

