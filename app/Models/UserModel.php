<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'username',
        'email',
        'password_hash',
        'role_id',
        'is_active',
        'last_login_at',
        'created_at',
        'updated_at'
    ];

    protected $validationRules = [
        'username' => 'required|max_length[100]',
        'email' => 'required|valid_email|max_length[190]',
        'password_hash' => 'required'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
}
