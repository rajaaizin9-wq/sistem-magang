<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ClearConversion extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'conversion:clear';
    protected $description = 'Clears all conversion test records (conversion_approvals, conversion_request_courses, conversion_requests).';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        CLI::write('Clearing conversion tables...', 'yellow');

        $db->query('SET FOREIGN_KEY_CHECKS = 0');
        $db->table('conversion_approvals')->truncate();
        $db->table('conversion_request_courses')->truncate();
        $db->table('conversion_requests')->truncate();
        $db->query('SET FOREIGN_KEY_CHECKS = 1');

        CLI::write('Successfully cleared conversion records!', 'green');
    }
}
