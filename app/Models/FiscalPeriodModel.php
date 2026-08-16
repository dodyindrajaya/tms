<?php

namespace App\Models;

use CodeIgniter\Model;

class FiscalPeriodModel extends Model
{
    protected $table = 'fiscal_periods';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'fiscal_year_id',
        'period_no',
        'start_date',
        'end_date',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
}
