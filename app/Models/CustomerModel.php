<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $table = 'customers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'customer_code',
        'name',
        'customer_type',
        'phone',
        'email',
        'address',
        'city',
        'country_code',
        'tax_id',
        'notes',
        'is_active',
        'created_at',
        'updated_at'
    ];

    protected $validationRules = [
        'customer_code' => 'required|max_length[30]',
        'name' => 'required|max_length[190]'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
}
