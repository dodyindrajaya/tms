<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'name',
        'description',
        'created_at',
        'updated_at'
    ];

    protected $validationRules = [
        'name' => 'required|max_length[100]'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
}
