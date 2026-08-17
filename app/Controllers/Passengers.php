<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;

class Passengers extends BaseController
{
    protected $db;

    private array $genders = [
        'M' => 'Male',
        'F' => 'Female',
        'O' => 'Other',
    ];

    public function __construct()
    {
        $this->db = db_connect();
    }

    public function index()
    {
        $q = trim((string)$this->request->getGet('q'));

        $builder = $this->db->table('passengers p')
            ->select('p.*, c.name AS customer_name')
            ->join('customers c', 'c.id = p.customer_id', 'left')
            ->orderBy('p.id', 'DESC');

        if ($q !== '') {
            $builder->groupStart()
                ->like('p.passenger_code', $q)
                ->orLike('p.full_name', $q)
                ->orLike('p.phone', $q)
                ->orLike('p.email', $q)
                ->orLike('p.passport_no', $q)
                ->groupEnd();
        }

        return $this->renderTms('passengers/index', [
            'passengers' => $builder->get()->getResultArray(),
            'q' => $q,
        ], 'passengers');
    }

    public function new()
    {
        return $this->renderTms('passengers/form', [
            'passenger' => ['gender' => 'M'],
            'customers' => $this->customers(),
            'genders' => $this->genders,
            'pageTitle' => 'New Passenger',
            'isEdit' => false,
        ], 'passengers');
    }

    public function create()
    {
        $data = $this->dataFromRequest();

        if (trim((string)$data['full_name']) === '') {
            return redirect()->back()->withInput()->with('error', 'Full name is required.');
        }

        $now = date('Y-m-d H:i:s');
        $data['passenger_code'] = $this->generateCode();
        $data['created_at'] = $now;
        $data['updated_at'] = $now;

        if (!$this->db->table('passengers')->insert($data)) {
            return redirect()->back()->withInput()->with('error', 'Passenger could not be saved.');
        }

        return redirect()->to(site_url('passengers'))
            ->with('success', 'Passenger created successfully.');
    }

    public function show(int $id)
    {
        $passenger = $this->db->table('passengers p')
            ->select('p.*, c.name AS customer_name')
            ->join('customers c', 'c.id = p.customer_id', 'left')
            ->where('p.id', $id)
            ->get()->getRowArray();

        if (!$passenger) {
            throw PageNotFoundException::forPageNotFound('Passenger not found.');
        }

        $bookings = $this->db->table('booking_passengers bp')
            ->select('b.id, b.booking_no, b.travel_start_date, b.travel_end_date, bp.passenger_type, bp.is_primary')
            ->join('bookings b', 'b.id = bp.booking_id')
            ->where('bp.passenger_id', $id)
            ->orderBy('b.id', 'DESC')
            ->get()->getResultArray();

        $tickets = $this->db->table('ticket_bookings tb')
            ->select('tb.id, tb.booking_id, tb.ticket_type, tb.booking_code, tb.ticket_number, tb.origin, tb.destination, tb.departure_date, tb.status, tb.selling_price, b.booking_no')
            ->join('bookings b', 'b.id = tb.booking_id')
            ->where('tb.passenger_id', $id)
            ->orderBy('tb.id', 'DESC')
            ->get()->getResultArray();

        return $this->renderTms('passengers/show', compact('passenger', 'bookings', 'tickets'), 'passengers');
    }

    public function edit(int $id)
    {
        $passenger = $this->db->table('passengers')->where('id', $id)->get()->getRowArray();

        if (!$passenger) {
            throw PageNotFoundException::forPageNotFound('Passenger not found.');
        }

        return $this->renderTms('passengers/form', [
            'passenger' => $passenger,
            'customers' => $this->customers(),
            'genders' => $this->genders,
            'pageTitle' => 'Edit Passenger',
            'isEdit' => true,
        ], 'passengers');
    }

    public function update(int $id)
    {
        $passenger = $this->db->table('passengers')->where('id', $id)->get()->getRowArray();

        if (!$passenger) {
            throw PageNotFoundException::forPageNotFound('Passenger not found.');
        }

        $data = $this->dataFromRequest();
        $data['updated_at'] = date('Y-m-d H:i:s');

        if (!$this->db->table('passengers')->where('id', $id)->update($data)) {
            return redirect()->back()->withInput()->with('error', 'Passenger could not be updated.');
        }

        return redirect()->to(site_url('passengers/show/' . $id))
            ->with('success', 'Passenger updated successfully.');
    }

    public function delete(int $id)
    {
        $links = $this->db->table('booking_passengers')->where('passenger_id', $id)->countAllResults()
            + $this->db->table('ticket_bookings')->where('passenger_id', $id)->countAllResults();

        if ($links > 0) {
            return redirect()->to(site_url('passengers'))
                ->with('error', 'Passenger cannot be deleted because it is already linked to a booking or ticket.');
        }

        $this->db->table('passengers')->where('id', $id)->delete();

        return redirect()->to(site_url('passengers'))
            ->with('success', 'Passenger deleted.');
    }

    private function renderTms(string $view, array $data = [], string $activeMenu = 'passengers')
    {
        return view('layouts/tms', [
            'contentView' => $view,
            'contentData' => $data,
            'activeMenu'  => $activeMenu,
            'pageTitle'   => $data['pageTitle'] ?? 'TMS',
        ]);
    }

    private function customers(): array
    {
        return $this->db->table('customers')
            ->select('id, customer_code, name')
            ->orderBy('name', 'ASC')
            ->get()->getResultArray();
    }

    private function dataFromRequest(): array
    {
        $gender = (string)$this->request->getPost('gender');
        if (!isset($this->genders[$gender])) {
            $gender = null;
        }

        return [
            'customer_id'       => $this->request->getPost('customer_id') !== '' ? (int)$this->request->getPost('customer_id') : null,
            'full_name'         => trim((string)$this->request->getPost('full_name')),
            'gender'            => $gender,
            'birth_date'        => $this->nullableDate($this->request->getPost('birth_date')),
            'nationality_code'  => strtoupper(trim((string)$this->request->getPost('nationality_code'))) ?: null,
            'passport_no'       => trim((string)$this->request->getPost('passport_no')) ?: null,
            'passport_expiry'   => $this->nullableDate($this->request->getPost('passport_expiry')),
            'id_number'         => trim((string)$this->request->getPost('id_number')) ?: null,
            'phone'             => trim((string)$this->request->getPost('phone')) ?: null,
            'email'             => trim((string)$this->request->getPost('email')) ?: null,
            'notes'             => trim((string)$this->request->getPost('notes')) ?: null,
        ];
    }

    private function generateCode(): string
    {
        $prefix = 'PAX-' . date('Ym') . '-';
        $last = $this->db->table('passengers')
            ->like('passenger_code', $prefix, 'after')
            ->orderBy('id', 'DESC')
            ->get()->getRowArray();

        $next = 1;
        if ($last && preg_match('/(\d+)$/', (string)$last['passenger_code'], $m)) {
            $next = (int)$m[1] + 1;
        }

        return $prefix . str_pad((string)$next, 4, '0', STR_PAD_LEFT);
    }

    private function nullableDate($value): ?string
    {
        $value = trim((string)$value);
        return ($value !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) ? $value : null;
    }
}
