<?php

namespace App\Models;

use CodeIgniter\Model;

class JournalEntryLineModel extends Model
{
    protected $table = 'journal_entry_lines';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'journal_entry_id',
        'account_id',
        'customer_id',
        'supplier_id',
        'booking_id',
        'description',
        'debit',
        'credit',
        'line_date'
    ];

    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
}
