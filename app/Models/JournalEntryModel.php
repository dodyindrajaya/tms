<?php

namespace App\Models;

use CodeIgniter\Model;

class JournalEntryModel extends Model
{
    protected $table = 'journal_entries';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'journal_id',
        'entry_no',
        'entry_date',
        'reference_type',
        'reference_id',
        'description',
        'status',
        'posted_at',
        'posted_by',
        'created_by',
        'created_at',
        'updated_at'
    ];

    protected $validationRules = [
        'journal_id' => 'required|is_natural_no_zero',
        'entry_no' => 'required|max_length[40]',
        'entry_date' => 'required'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
}
