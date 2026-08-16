<?php

namespace App\Models;

use CodeIgniter\Model;

class BookingPassengerModel extends Model
{
    protected $table = 'booking_passengers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'booking_id',
        'passenger_id',
        'passenger_type',
        'is_primary'
    ];

    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
}
