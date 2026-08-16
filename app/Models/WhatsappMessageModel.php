<?php

namespace App\Models;

use CodeIgniter\Model;

class WhatsappMessageModel extends Model
{
    protected $table = 'whatsapp_messages';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'config_id',
        'customer_id',
        'booking_id',
        'template_id',
        'direction',
        'phone_number',
        'message_text',
        'media_url',
        'provider_message_id',
        'status',
        'error_message',
        'sent_at',
        'delivered_at',
        'read_at',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
}
