<?php

namespace App\Models;

use CodeIgniter\Model;

class AccountGroupModel extends Model
{
    protected $table = 'account_groups';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'code',
        'name',
        'report_type',
        'parent_id',
        'sort_order',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
}
