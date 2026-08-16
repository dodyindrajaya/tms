<?php

namespace App\Models;

use CodeIgniter\Model;

class BookingCostModel extends Model
{
    protected $table = 'booking_costs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'booking_id',
        'booking_item_id',
        'supplier_id',
        'description',
        'cost_type',
        'amount',
        'bill_id',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
}
