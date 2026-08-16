<?php

namespace App\Models;

use CodeIgniter\Model;

class TourItineraryModel extends Model
{
    protected $table = 'tour_itineraries';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'tour_package_id',
        'day_no',
        'title',
        'description',
        'location',
        'meal',
        'hotel',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
}
