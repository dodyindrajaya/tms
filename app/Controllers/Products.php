<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProductModel;

class Products extends BaseController
{
    protected ProductModel $productModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
    }

    public function index()
    {
        $q = trim((string) $this->request->getGet('q'));
        $category = trim((string) $this->request->getGet('category'));
        $status = trim((string) $this->request->getGet('status'));

        $builder = $this->productModel;

        if ($q !== '') {
            $builder = $builder->groupStart()
                ->like('product_code', $q)
                ->orLike('name', $q)
                ->orLike('unit', $q)
                ->groupEnd();
        }

        if ($category !== '') {
            $builder = $builder->where('category', $category);
        }

        if ($status === 'active') {
            $builder = $builder->where('is_active', 1);
        } elseif ($status === 'inactive') {
            $builder = $builder->where('is_active', 0);
        }

        $products = $builder->orderBy('id', 'DESC')->paginate(15);

        return view('products/index', [
            'title' => 'Products',
            'products' => $products,
            'pager' => $this->productModel->pager,
            'q' => $q,
            'category' => $category,
            'status' => $status,
            'categories' => $this->categories(),
        ]);
    }

    public function create()
    {
        return view('products/create', [
            'title' => 'New Product',
            'product' => [],
            'categories' => $this->categories(),
        ]);
    }

    public function store()
    {
        if (! $this->validate($this->validationRules())) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please check the form. Some fields are invalid.');
        }

        $data = $this->productInput();

        if (! $this->productModel->insert($data)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Product could not be saved.');
        }

        return redirect()->to(site_url('products'))
            ->with('success', 'Product created successfully.');
    }

    public function edit(int $id)
    {
        $product = $this->productModel->find($id);

        if (! $product) {
            return redirect()->to(site_url('products'))
                ->with('error', 'Product not found.');
        }

        return view('products/edit', [
            'title' => 'Edit Product',
            'product' => $product,
            'categories' => $this->categories(),
        ]);
    }

    public function update(int $id)
    {
        if (! $this->productModel->find($id)) {
            return redirect()->to(site_url('products'))
                ->with('error', 'Product not found.');
        }

        if (! $this->validate($this->validationRules($id))) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please check the form. Some fields are invalid.');
        }

        if (! $this->productModel->update($id, $this->productInput())) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Product could not be updated.');
        }

        return redirect()->to(site_url('products'))
            ->with('success', 'Product updated successfully.');
    }

    public function delete(int $id)
    {
        if (! $this->productModel->find($id)) {
            return redirect()->to(site_url('products'))
                ->with('error', 'Product not found.');
        }

        if (! $this->productModel->delete($id)) {
            return redirect()->to(site_url('products'))
                ->with('error', 'Product could not be deleted.');
        }

        return redirect()->to(site_url('products'))
            ->with('success', 'Product deleted successfully.');
    }

    private function productInput(): array
    {
        return [
            'product_code' => trim((string) $this->request->getPost('product_code')),
            'name' => trim((string) $this->request->getPost('name')),
            'category' => trim((string) $this->request->getPost('category')),
            'unit' => trim((string) $this->request->getPost('unit')),
            'default_sale_price' => (float) ($this->request->getPost('default_sale_price') ?: 0),
            'default_cost_price' => (float) ($this->request->getPost('default_cost_price') ?: 0),
            'revenue_account_id' => $this->nullableInt($this->request->getPost('revenue_account_id')),
            'cost_account_id' => $this->nullableInt($this->request->getPost('cost_account_id')),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ];
    }

    private function nullableInt($value): ?int
    {
        return ($value === null || $value === '') ? null : (int) $value;
    }

    private function validationRules(?int $id = null): array
    {
        $codeRule = 'required|max_length[50]';

        if ($id === null) {
            $codeRule .= '|is_unique[products.product_code]';
        } else {
            $codeRule .= '|is_unique[products.product_code,id,' . $id . ']';
        }

        return [
            'product_code' => $codeRule,
            'name' => 'required|min_length[2]|max_length[190]',
            'category' => 'required|in_list[tour,flight,train,bus,hotel,transport,rental,guide,other]',
            'unit' => 'required|max_length[30]',
            'default_sale_price' => 'required|numeric|greater_than_equal_to[0]',
            'default_cost_price' => 'required|numeric|greater_than_equal_to[0]',
            'revenue_account_id' => 'permit_empty|integer',
            'cost_account_id' => 'permit_empty|integer',
        ];
    }

    private function categories(): array
    {
        return [
            'tour' => 'Tour',
            'flight' => 'Flight',
            'train' => 'Train',
            'bus' => 'Bus',
            'hotel' => 'Hotel',
            'transport' => 'Transport',
            'rental' => 'Rental',
            'guide' => 'Guide',
            'other' => 'Other',
        ];
    }
}
