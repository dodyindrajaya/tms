<?php

namespace App\Models;

use CodeIgniter\Model;

class FiscalYearModel extends Model
{
    protected $table = 'fiscal_years';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'year',
        'start_date',
        'end_date',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
}
