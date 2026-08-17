<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PassengerModel;

class Passengers extends BaseController
{
    protected PassengerModel $model;
    public function __construct() { $this->model = new PassengerModel(); }

    public function index()
    {
        $q = trim((string)$this->request->getGet('q'));
        $builder = $this->model;
        if ($q !== '') $builder = $builder->groupStart()->like('full_name', $q)->orLike('passenger_code', $q)->groupEnd();

        $passengers = $builder->orderBy('id', 'DESC')->paginate(25);
        return view('passengers/index', [
            'title' => 'Passengers',
            'passengers' => $passengers,
            'pager' => $this->model->pager,
            'q' => $q
        ]);
    }

    public function create()
    {
        return view('passengers/create', ['title' => 'New Passenger']);
    }

    public function store()
    {
        if (!$this->validate([
            'full_name' => 'required|min_length[2]',
            'passenger_code' => 'permit_empty',
            'phone' => 'permit_empty',
            'passport_no' => 'permit_empty',
        ])) return redirect()->back()->withInput()->with('error', 'Please check the passenger form.');

        $data = [
            'passenger_code' => trim((string)$this->request->getPost('passenger_code')) ?: null,
            'full_name' => trim((string)$this->request->getPost('full_name')),
            'gender' => $this->request->getPost('gender') ?: null,
            'birth_date' => $this->request->getPost('birth_date') ?: null,
            'passport_no' => trim((string)$this->request->getPost('passport_no')) ?: null,
            'phone' => trim((string)$this->request->getPost('phone')) ?: null,
            'email' => trim((string)$this->request->getPost('email')) ?: null,
        ];

        if (!$this->model->insert($data)) return redirect()->back()->withInput()->with('error', 'Passenger could not be saved.');
        return redirect()->to(site_url('passengers'))->with('success', 'Passenger created.');
    }

    public function edit(int $id)
    {
        $p = $this->model->find($id);
        if (!$p) return redirect()->to(site_url('passengers'))->with('error', 'Passenger not found.');
        return view('passengers/edit', ['title' => 'Edit Passenger', 'passenger' => $p]);
    }

    public function update(int $id)
    {
        if (!$this->model->find($id)) return redirect()->to(site_url('passengers'))->with('error', 'Passenger not found.');
        if (!$this->validate(['full_name' => 'required|min_length[2]'])) return redirect()->back()->withInput()->with('error', 'Please check the passenger form.');

        $data = [
            'passenger_code' => trim((string)$this->request->getPost('passenger_code')) ?: null,
            'full_name' => trim((string)$this->request->getPost('full_name')),
            'gender' => $this->request->getPost('gender') ?: null,
            'birth_date' => $this->request->getPost('birth_date') ?: null,
            'passport_no' => trim((string)$this->request->getPost('passport_no')) ?: null,
            'phone' => trim((string)$this->request->getPost('phone')) ?: null,
            'email' => trim((string)$this->request->getPost('email')) ?: null,
        ];

        $this->model->update($id, $data);
        return redirect()->to(site_url('passengers'))->with('success', 'Passenger updated.');
    }

    public function delete(int $id)
    {
        if ($this->model->find($id)) $this->model->delete($id);
        return redirect()->to(site_url('passengers'))->with('success', 'Passenger deleted.');
    }
}
