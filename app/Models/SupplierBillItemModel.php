<?php

namespace App\Models;

use CodeIgniter\Model;

class SupplierBillItemModel extends Model
{
    protected $table = 'supplier_bill_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'supplier_bill_id',
        'booking_cost_id',
        'description',
        'quantity',
        'unit_cost',
        'line_total',
        'expense_account_id'
    ];

    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
}
