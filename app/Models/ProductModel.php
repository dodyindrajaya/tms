<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'product_code',
        'name',
        'category',
        'unit',
        'default_sale_price',
        'default_cost_price',
        'revenue_account_id',
        'cost_account_id',
        'is_active',
        'created_at',
        'updated_at'
    ];

    protected $validationRules = [
        'product_code' => 'required|max_length[50]',
        'name' => 'required|max_length[190]',
        'category' => 'required|max_length[30]'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
}
