<?php

namespace App\Models;

use CodeIgniter\Model;

class SupplierModel extends Model
{
    protected $table = 'suppliers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'supplier_code',
        'name',
        'supplier_type',
        'phone',
        'email',
        'address',
        'payment_terms_days',
        'notes',
        'is_active',
        'created_at',
        'updated_at'
    ];

    protected $validationRules = [
        'supplier_code' => 'required|max_length[30]',
        'name' => 'required|max_length[190]',
        'supplier_type' => 'required|max_length[30]'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
}
