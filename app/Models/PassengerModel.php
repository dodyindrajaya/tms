<?php

namespace App\Models;

use CodeIgniter\Model;

class PassengerModel extends Model
{
    protected $table = 'passengers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'customer_id',
        'passenger_code',
        'full_name',
        'gender',
        'birth_date',
        'nationality_code',
        'passport_no',
        'passport_expiry',
        'id_number',
        'phone',
        'email',
        'notes',
        'created_at',
        'updated_at'
    ];

    protected $validationRules = [
        'passenger_code' => 'required|max_length[30]',
        'full_name' => 'required|max_length[190]'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
}
