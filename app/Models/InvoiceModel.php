<?php

namespace App\Models;

use CodeIgniter\Model;

class InvoiceModel extends Model
{
    protected $table = 'invoices';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'invoice_no',
        'booking_id',
        'customer_id',
        'invoice_date',
        'due_date',
        'status',
        'subtotal',
        'tax_amount',
        'total_amount',
        'paid_amount',
        'outstanding_amount',
        'journal_entry_id',
        'created_at',
        'updated_at'
    ];

    protected $validationRules = [
        'invoice_no' => 'required|max_length[40]',
        'booking_id' => 'required|is_natural_no_zero',
        'customer_id' => 'required|is_natural_no_zero'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
}
