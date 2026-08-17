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
        'updated_at',
    ];

    protected $validationRules = [
        'code'         => 'required|max_length[20]',
        'name'         => 'required|max_length[190]',
        'account_type' => 'required|in_list[asset,liability,equity,revenue,cogs,expense]',
    ];

    protected $validationMessages = [
        'code.required' => 'Account code is required.',
        'code.max_length' => 'Account code cannot exceed 20 characters.',
        'name.required' => 'Account name is required.',
        'account_type.in_list' => 'Invalid account type.',
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
}
