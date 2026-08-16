<?php

namespace App\Models;

use CodeIgniter\Model;

class TicketBookingModel extends Model
{
    protected $table = 'ticket_bookings';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    protected $allowedFields = [
        'booking_id','passenger_id','ticket_type','supplier_id','booking_code',
        'ticket_number','issue_date','departure_date','departure_time','arrival_date',
        'arrival_time','origin','destination','carrier','travel_class','seat',
        'status','cost_price','selling_price'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
