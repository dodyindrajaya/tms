<?php

namespace App\Services;

use App\Models\CustomerModel;
use RuntimeException;

class CustomerService
{
    protected CustomerModel $model;

    public function __construct()
    {
        $this->model = new CustomerModel();
    }

    public function create(array $data): int
    {
        if (empty($data['name'])) {
            throw new RuntimeException('Customer name is required.');
        }

        if (empty($data['customer_code'])) {
            $data['customer_code'] = $this->nextCode();
        }

        if (empty($data['is_active'])) {
            $data['is_active'] = 1;
        }

        if (!$this->model->insert($data)) {
            throw new RuntimeException(implode('; ', $this->model->errors()));
        }

        return (int)$this->model->getInsertID();
    }

    protected function nextCode(): string
    {
        return 'CUS-' . date('YmdHis') . '-' . strtoupper(bin2hex(random_bytes(2)));
    }
}
