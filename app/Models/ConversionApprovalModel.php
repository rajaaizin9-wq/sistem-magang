<?php

namespace App\Models;

use CodeIgniter\Model;

class ConversionApprovalModel extends Model
{
    protected $table            = 'conversion_approvals';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'conversion_request_id',
        'approver_id',
        'role',
        'status',
        'notes',
        'approved_at',
    ];

    protected $useTimestamps = true;

    public function getApprovalsByRequestId($requestId)
    {
        return $this->select('conversion_approvals.*, users.name as approver_name, users.email as approver_email')
                    ->join('users', 'users.id = conversion_approvals.approver_id')
                    ->where('conversion_approvals.conversion_request_id', $requestId)
                    ->orderBy('conversion_approvals.created_at', 'ASC')
                    ->findAll();
    }
}
