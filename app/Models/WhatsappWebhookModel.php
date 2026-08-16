<?php

namespace App\Models;

use CodeIgniter\Model;

class WhatsappWebhookModel extends Model
{
    protected $table = 'whatsapp_webhooks';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'provider_event_id',
        'payload_json',
        'event_type',
        'processed',
        'processed_at',
        'error_message',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
}
