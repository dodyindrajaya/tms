<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'payment_no',
        'payment_date',
        'payment_type',
        'booking_id',
        'customer_id',
        'supplier_id',
        'account_id',
        'amount',
        'payment_method_id',
        'reference_no',
        'notes',
        'journal_entry_id',
        'created_by',
        'created_at',
        'updated_at'
    ];

    protected $validationRules = [
        'payment_no' => 'required|max_length[40]',
        'payment_date' => 'required',
        'amount' => 'required|decimal'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
}
