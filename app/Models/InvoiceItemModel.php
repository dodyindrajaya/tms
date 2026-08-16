<?php

namespace App\Models;

use CodeIgniter\Model;

class InvoiceItemModel extends Model
{
    protected $table = 'invoice_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'invoice_id',
        'booking_item_id',
        'description',
        'quantity',
        'unit_price',
        'tax_amount',
        'line_total',
        'revenue_account_id'
    ];

    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
}
