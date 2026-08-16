<?php

namespace App\Models;

use CodeIgniter\Model;

class WhatsappTemplateModel extends Model
{
    protected $table = 'whatsapp_templates';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'name',
        'provider_template_name',
        'language',
        'event_code',
        'body',
        'active',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
}
