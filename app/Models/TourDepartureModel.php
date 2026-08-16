<?php

namespace App\Models;

use CodeIgniter\Model;

class TourDepartureModel extends Model
{
    protected $table = 'tour_departures';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    protected $allowedFields = [
        'tour_package_id','departure_date','return_date','capacity',
        'status','meeting_point','notes'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
