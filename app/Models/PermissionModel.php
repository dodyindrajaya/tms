<?php

namespace App\Models;

use CodeIgniter\Model;

class PermissionModel extends Model
{
    protected $table = 'permissions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'code',
        'name',
        'description',
        'created_at',
        'updated_at'
    ];

    protected $validationRules = [
        'code' => 'required|max_length[100]',
        'name' => 'required|max_length[150]'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
}
