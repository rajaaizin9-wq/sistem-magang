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
        'course_id',
        'internship_id',
        'file_path',
        'drive_url',
        'student_notes',
        'status',
        'feedback_notes',
    ];

    protected $useTimestamps = true;

    /**
     * Dapatkan 1 pengumpulan link drive mahasiswa untuk mata kuliah tertentu
     */
    public function getCourseSubmission(int $courseId, int $internshipId)
    {
        return $this->where('course_id', $courseId)
                    ->where('internship_id', $internshipId)
                    ->first();
    }

    /**
     * Dapatkan semua pengumpulan untuk kumpulan mahasiswa pada mata kuliah tertentu (key by internship_id)
     */
    public function getSubmissionsByCourse(int $courseId, array $internshipIds = [])
    {
        $builder = $this->where('course_id', $courseId);
        if (!empty($internshipIds)) {
            $builder->whereIn('internship_id', $internshipIds);
        }
        $rows = $builder->findAll();
        $map = [];
        foreach ($rows as $r) {
            $map[$r['internship_id']] = $r;
        }
        return $map;
    }
}
