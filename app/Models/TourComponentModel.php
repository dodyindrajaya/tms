<?php

namespace App\Models;

use CodeIgniter\Model;

class TourComponentModel extends Model
{
    protected $table = 'tour_components';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'tour_package_id',
        'product_id',
        'supplier_id',
        'component_type',
        'quantity',
        'estimated_cost',
        'notes',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
}
