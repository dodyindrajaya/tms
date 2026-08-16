<?php

namespace App\Models;

use CodeIgniter\Model;

class TicketSegmentModel extends Model
{
    protected $table = 'ticket_segments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    protected $allowedFields = [
        'ticket_booking_id','segment_no','origin','destination','carrier','service_no',
        'departure_date','departure_time','arrival_date','arrival_time','travel_class','seat'
    ];
}
