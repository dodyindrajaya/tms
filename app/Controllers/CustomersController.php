<?php

namespace App\Controllers;

use App\Models\CustomerModel;
use App\Services\CustomerService;

class CustomersController extends BaseController
{
    protected CustomerModel $customerModel;
    protected CustomerService $customerService;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
        $this->customerService = new CustomerService();
    }

    /**
     * Customer List + Search
     */
    public function index()
    {
        $keyword = trim((string) $this->request->getGet('q'));

        $builder = $this->customerModel
            ->orderBy('id', 'DESC');

        if ($keyword !== '') {
            $builder->groupStart()
                ->like('customer_code', $keyword)
                ->orLike('name', $keyword)
                ->orLike('phone', $keyword)
                ->orLike('email', $keyword)
                ->groupEnd();
        }

        return view('customers/index', [
            'title'     => 'Customers',
            'customers' => $builder->findAll(),
            'keyword'   => $keyword,
        ]);
    }

    /**
     * Form Create
     */
    public function create()
    {
        return view('customers/form', [
            'title'    => 'New Customer',
            'customer' => null,
            'mode'     => 'create',
        ]);
    }

    /**
     * Save Customer
     */
    public function store()
    {
        $data = $this->getCustomerData();

        try {
            $id = $this->customerService->create($data);

            return redirect()
                ->to('/customers/' . $id)
                ->with('success', 'Customer berhasil dibuat.');
        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Detail Customer
     */
    public function show(int $id)
    {
        $customer = $this->customerModel->find($id);

        if (!$customer) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                'Customer tidak ditemukan.'
            );
        }

        return view('customers/show', [
            'title'    => 'Customer Detail',
            'customer' => $customer,
        ]);
    }

    /**
     * Form Edit
     */
    public function edit(int $id)
    {
        $customer = $this->customerModel->find($id);

        if (!$customer) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                'Customer tidak ditemukan.'
            );
        }

        return view('customers/form', [
            'title'    => 'Edit Customer',
            'customer' => $customer,
            'mode'     => 'edit',
        ]);
    }

    /**
     * Update Customer
     */
    public function update(int $id)
    {
        $customer = $this->customerModel->find($id);

        if (!$customer) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                'Customer tidak ditemukan.'
            );
        }

        $data = $this->getCustomerData();

        try {
            $this->customerModel->update($id, $data);

            return redirect()
                ->to('/customers/' . $id)
                ->with('success', 'Customer berhasil diupdate.');
        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Deactivate Customer
     */
    public function deactivate(int $id)
    {
        $customer = $this->customerModel->find($id);

        if (!$customer) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                'Customer tidak ditemukan.'
            );
        }

        try {
            $this->customerModel->update($id, [
                'is_active' => 0,
            ]);

            return redirect()
                ->to('/customers/' . $id)
                ->with('success', 'Customer berhasil dinonaktifkan.');
        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Activate Customer
     */
    public function activate(int $id)
    {
        $customer = $this->customerModel->find($id);

        if (!$customer) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                'Customer tidak ditemukan.'
            );
        }

        try {
            $this->customerModel->update($id, [
                'is_active' => 1,
            ]);

            return redirect()
                ->to('/customers/' . $id)
                ->with('success', 'Customer berhasil diaktifkan.');
        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Ambil data Customer dari POST
     */
    private function getCustomerData(): array
    {
        return [
            'customer_code' => trim((string) $this->request->getPost('customer_code')),
            'name'          => trim((string) $this->request->getPost('name')),
            'customer_type' => $this->request->getPost('customer_type') ?: 'individual',
            'phone'         => trim((string) $this->request->getPost('phone')),
            'email'         => trim((string) $this->request->getPost('email')),
            'address'       => trim((string) $this->request->getPost('address')),
        ];
    }
}