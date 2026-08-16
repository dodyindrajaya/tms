<?php

namespace App\Models;

use CodeIgniter\Model;

class AccountModel extends Model
{
    protected $table = 'accounts';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'code',
        'name',
        'account_type',
        'parent_id',
        'account_group_id',
        'is_control_account',
        'allow_manual_posting',
        'is_active',
        'created_at',
        'updated_at'
    ];

    protected $validationRules = [
        'code' => 'required|max_length[20]',
        'name' => 'required|max_length[190]',
        'account_type' => 'required|max_length[20]'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
}
