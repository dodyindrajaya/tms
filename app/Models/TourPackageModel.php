<?php

namespace App\Models;

use CodeIgniter\Model;

class TourPackageModel extends Model
{
    protected $table = 'tour_packages';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    protected $allowedFields = [
        'product_id','package_code','name','destination','duration_days',
        'duration_nights','description','is_active'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
