<?php

namespace App\Models;

use CodeIgniter\Model;

class SupplierBillModel extends Model
{
    protected $table = 'supplier_bills';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'bill_no',
        'supplier_id',
        'booking_id',
        'bill_date',
        'due_date',
        'status',
        'total_amount',
        'paid_amount',
        'outstanding_amount',
        'journal_entry_id',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
}
