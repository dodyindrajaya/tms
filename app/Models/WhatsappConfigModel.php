<?php

namespace App\Models;

use CodeIgniter\Model;

class WhatsappConfigModel extends Model
{
    protected $table = 'whatsapp_configs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'name',
        'phone_number_id',
        'business_account_id',
        'api_base_url',
        'encrypted_access_token',
        'webhook_verify_token',
        'is_active',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
}
