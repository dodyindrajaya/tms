<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;

class Ticketing extends BaseController
{
    protected $db;

    private array $ticketTypes = [
        'flight' => 'Flight',
        'train'  => 'Train',
        'bus'    => 'Bus',
        'ferry'  => 'Ferry',
        'other'  => 'Other',
    ];

    private array $statuses = [
        'request'     => 'Request',
        'quoted'      => 'Quoted',
        'booked'      => 'Booked',
        'paid'        => 'Paid',
        'issued'      => 'Issued',
        'completed'   => 'Completed',
        'cancelled'   => 'Cancelled',
        'void'        => 'Void',
        'refunded'    => 'Refunded',
        'rescheduled' => 'Rescheduled',
    ];

    public function __construct()
    {
        $this->db = db_connect();
    }

    public function index()
    {
        $q      = trim((string) $this->request->getGet('q'));
        $type   = (string) $this->request->getGet('ticket_type');
        $status = (string) $this->request->getGet('status');

        $builder = $this->db->table('ticket_bookings tb')
            ->select('tb.*, b.booking_no, p.full_name AS passenger_name, p.passenger_code, s.name AS supplier_name')
            ->join('bookings b', 'b.id = tb.booking_id')
            ->join('passengers p', 'p.id = tb.passenger_id')
            ->join('suppliers s', 's.id = tb.supplier_id', 'left')
            ->orderBy('tb.id', 'DESC');

        if ($q !== '') {
            $builder->groupStart()
                ->like('tb.booking_code', $q)
                ->orLike('tb.ticket_number', $q)
                ->orLike('tb.origin', $q)
                ->orLike('tb.destination', $q)
                ->orLike('tb.carrier', $q)
                ->orLike('b.booking_no', $q)
                ->orLike('p.full_name', $q)
                ->groupEnd();
        }

        if (isset($this->ticketTypes[$type])) {
            $builder->where('tb.ticket_type', $type);
        }

        if (isset($this->statuses[$status])) {
            $builder->where('tb.status', $status);
        }

        $tickets = $builder->get()->getResultArray();

        foreach ($tickets as &$ticket) {
            $ticket['segments'] = $this->db->table('ticket_segments')
                ->where('ticket_booking_id', $ticket['id'])
                ->orderBy('segment_no', 'ASC')
                ->get()->getResultArray();
        }

        $stats = [
            'total'   => $this->db->table('ticket_bookings')->countAllResults(),
            'booked'  => $this->db->table('ticket_bookings')->whereIn('status', ['booked', 'paid', 'issued'])->countAllResults(),
            'issued'  => $this->db->table('ticket_bookings')->where('status', 'issued')->countAllResults(),
            'revenue' => (float) ($this->db->table('ticket_bookings')->selectSum('selling_price')->get()->getRow('selling_price') ?? 0),
        ];

        return $this->renderTms('ticketing/index', [
            'tickets' => $tickets,
            'stats' => $stats,
            'ticketTypes' => $this->ticketTypes,
            'statuses' => $this->statuses,
            'q' => $q,
            'type' => $type,
            'status' => $status,
        ], 'ticketing');
    }

    public function new()
    {
        $bookings = $this->db->table('bookings b')
            ->select('b.id, b.booking_no, c.name AS customer_name')
            ->join('customers c', 'c.id = b.customer_id')
            ->where('b.status !=', 'cancelled')
            ->orderBy('b.id', 'DESC')
            ->get()->getResultArray();

        $suppliers = $this->db->table('suppliers')
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->get()->getResultArray();

        return $this->renderTms('ticketing/form', [
            'ticket' => [
                'ticket_type'   => 'flight',
                'status'        => 'request',
                'cost_price'    => 0,
                'selling_price' => 0,
            ],
            'segments'    => [],
            'passengers'  => [],
            'bookings'    => $bookings,
            'suppliers'   => $suppliers,
            'ticketTypes' => $this->ticketTypes,
            'statuses'    => $this->statuses,
            'pageTitle'   => 'New Ticket',
            'isEdit'      => false,
        ], 'ticketing');
    }

    public function create()
    {
        $data = $this->ticketDataFromRequest();
        $segments = $this->segmentsFromRequest();

        if ($data['booking_id'] <= 0 || $data['passenger_id'] <= 0) {
            return redirect()->back()->withInput()->with('error', 'Booking and passenger are required.');
        }

        if (!$segments) {
            return redirect()->back()->withInput()->with('error', 'At least one ticket segment is required.');
        }

        $this->db->transStart();

        $this->db->table('ticket_bookings')->insert($data);
        $ticketId = (int) $this->db->insertID();

        foreach ($segments as $i => $segment) {
            $segment['ticket_booking_id'] = $ticketId;
            $segment['segment_no'] = $i + 1;
            $this->db->table('ticket_segments')->insert($segment);
        }

        $this->syncHeaderFromSegments($ticketId);

        $this->db->transComplete();

        if ($this->db->transStatus() === false || !$ticketId) {
            return redirect()->back()->withInput()->with('error', 'Ticket could not be saved.');
        }

        return redirect()->to(site_url('ticketing/show/' . $ticketId))
            ->with('success', 'Ticket created successfully.');
    }

    public function show(int $id)
    {
        $ticket = $this->ticketQuery()
            ->where('tb.id', $id)
            ->get()->getRowArray();

        if (!$ticket) {
            throw PageNotFoundException::forPageNotFound('Ticket not found.');
        }

        $segments = $this->db->table('ticket_segments')
            ->where('ticket_booking_id', $id)
            ->orderBy('segment_no', 'ASC')
            ->get()->getResultArray();

        return $this->renderTms('ticketing/show', [
            'ticket'     => $ticket,
            'segments'   => $segments,
            'ticketTypes' => $this->ticketTypes,
            'statuses'   => $this->statuses,
        ], 'ticketing');
    }

    public function edit(int $id)
    {
        $ticket = $this->db->table('ticket_bookings')
            ->where('id', $id)->get()->getRowArray();

        if (!$ticket) {
            throw PageNotFoundException::forPageNotFound('Ticket not found.');
        }

        $bookings = $this->db->table('bookings b')
            ->select('b.id, b.booking_no, c.name AS customer_name')
            ->join('customers c', 'c.id = b.customer_id')
            ->orderBy('b.id', 'DESC')
            ->get()->getResultArray();

        $suppliers = $this->db->table('suppliers')
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->get()->getResultArray();

        $segments = $this->db->table('ticket_segments')
            ->where('ticket_booking_id', $id)
            ->orderBy('segment_no', 'ASC')
            ->get()->getResultArray();

        $passengers = $this->passengersForBooking((int) $ticket['booking_id']);

        return $this->renderTms('ticketing/form', [
            'ticket'      => $ticket,
            'segments'    => $segments,
            'passengers'  => $passengers,
            'bookings'    => $bookings,
            'suppliers'   => $suppliers,
            'ticketTypes' => $this->ticketTypes,
            'statuses'    => $this->statuses,
            'pageTitle'   => 'Edit Ticket',
            'isEdit'      => true,
        ], 'ticketing');
    }

    public function update(int $id)
    {
        $exists = $this->db->table('ticket_bookings')->where('id', $id)->get()->getRowArray();

        if (!$exists) {
            throw PageNotFoundException::forPageNotFound('Ticket not found.');
        }

        $data = $this->ticketDataFromRequest();
        $segments = $this->segmentsFromRequest();

        if ($data['booking_id'] <= 0 || $data['passenger_id'] <= 0 || !$segments) {
            return redirect()->back()->withInput()->with('error', 'Booking, passenger and at least one segment are required.');
        }

        $this->db->transStart();

        $this->db->table('ticket_bookings')->where('id', $id)->update($data);
        $this->db->table('ticket_segments')->where('ticket_booking_id', $id)->delete();

        foreach ($segments as $i => $segment) {
            $segment['ticket_booking_id'] = $id;
            $segment['segment_no'] = $i + 1;
            $this->db->table('ticket_segments')->insert($segment);
        }

        $this->syncHeaderFromSegments($id);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Ticket update failed.');
        }

        return redirect()->to(site_url('ticketing/show/' . $id))
            ->with('success', 'Ticket updated successfully.');
    }

    public function delete(int $id)
    {
        $ticket = $this->db->table('ticket_bookings')->where('id', $id)->get()->getRowArray();

        if (!$ticket) {
            return redirect()->to(site_url('ticketing'))->with('error', 'Ticket not found.');
        }

        // Do not hard-delete financially/operationally significant tickets.
        if (in_array($ticket['status'], ['issued', 'paid', 'completed', 'refunded'], true)) {
            $this->db->table('ticket_bookings')->where('id', $id)->update([
                'status' => 'cancelled',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to(site_url('ticketing'))->with('success', 'Ticket cancelled.');
        }

        $this->db->transStart();
        $this->db->table('ticket_segments')->where('ticket_booking_id', $id)->delete();
        $this->db->table('ticket_bookings')->where('id', $id)->delete();
        $this->db->transComplete();

        return redirect()->to(site_url('ticketing'))->with('success', 'Ticket deleted.');
    }

    public function bookingPassengers(int $bookingId)
    {
        $rows = $this->passengersForBooking($bookingId);

        // When editing an existing ticket, the ticket passenger may exist in
        // ticket_bookings but not yet be linked in booking_passengers. Keep
        // that passenger available in the edit dropdown.
        $selectedPassengerId = (int) $this->request->getGet('selected');

        if ($selectedPassengerId > 0) {
            $exists = false;
            foreach ($rows as $row) {
                if ((int) $row['id'] === $selectedPassengerId) {
                    $exists = true;
                    break;
                }
            }

            if (!$exists) {
                $passenger = $this->db->table('passengers')
                    ->select('id, passenger_code, full_name, phone, email')
                    ->where('id', $selectedPassengerId)
                    ->get()->getRowArray();

                if ($passenger) {
                    $passenger['passenger_type'] = null;
                    $passenger['is_primary'] = 0;
                    $rows[] = $passenger;
                }
            }
        }

        return $this->response->setJSON($rows);
    }

    private function renderTms(string $view, array $data = [], string $activeMenu = 'ticketing')
    {
        return view('layouts/tms', [
            'contentView' => $view,
            'contentData' => $data,
            'activeMenu'  => $activeMenu,
            'pageTitle'   => $data['pageTitle'] ?? 'TMS',
        ]);
    }

    private function passengersForBooking(int $bookingId): array
    {
        return $this->db->table('booking_passengers bp')
            ->select('p.id, p.passenger_code, p.full_name, bp.passenger_type, bp.is_primary')
            ->join('passengers p', 'p.id = bp.passenger_id')
            ->where('bp.booking_id', $bookingId)
            ->orderBy('bp.is_primary', 'DESC')
            ->orderBy('p.full_name', 'ASC')
            ->get()->getResultArray();
    }

    private function ticketQuery()
    {
        return $this->db->table('ticket_bookings tb')
            ->select('tb.*, b.booking_no, b.booking_date, b.currency_code, c.name AS customer_name, p.full_name AS passenger_name, p.passenger_code, p.phone AS passenger_phone, p.email AS passenger_email, s.name AS supplier_name')
            ->join('bookings b', 'b.id = tb.booking_id')
            ->join('customers c', 'c.id = b.customer_id')
            ->join('passengers p', 'p.id = tb.passenger_id')
            ->join('suppliers s', 's.id = tb.supplier_id', 'left');
    }

    private function ticketDataFromRequest(): array
    {
        return [
            'booking_id'     => (int) $this->request->getPost('booking_id'),
            'passenger_id'   => (int) $this->request->getPost('passenger_id'),
            'ticket_type'    => $this->enumValue((string)$this->request->getPost('ticket_type'), array_keys($this->ticketTypes), 'other'),
            'supplier_id'    => $this->request->getPost('supplier_id') !== '' ? (int)$this->request->getPost('supplier_id') : null,
            'booking_code'   => trim((string)$this->request->getPost('booking_code')) ?: null,
            'ticket_number'  => trim((string)$this->request->getPost('ticket_number')) ?: null,
            'issue_date'     => $this->nullableDate($this->request->getPost('issue_date')),
            'departure_date' => $this->nullableDate($this->request->getPost('departure_date')),
            'departure_time' => $this->nullableTime($this->request->getPost('departure_time')),
            'arrival_date'   => $this->nullableDate($this->request->getPost('arrival_date')),
            'arrival_time'   => $this->nullableTime($this->request->getPost('arrival_time')),
            'origin'         => trim((string)$this->request->getPost('origin')) ?: null,
            'destination'    => trim((string)$this->request->getPost('destination')) ?: null,
            'carrier'        => trim((string)$this->request->getPost('carrier')) ?: null,
            'travel_class'   => trim((string)$this->request->getPost('travel_class')) ?: null,
            'seat'           => trim((string)$this->request->getPost('seat')) ?: null,
            'status'         => $this->enumValue((string)$this->request->getPost('status'), array_keys($this->statuses), 'request'),
            'cost_price'     => max(0, (float)$this->request->getPost('cost_price')),
            'selling_price'  => max(0, (float)$this->request->getPost('selling_price')),
            'updated_at'     => date('Y-m-d H:i:s'),
        ];
    }

    private function segmentsFromRequest(): array
    {
        $fields = [];
        foreach ([
            'origin','destination','carrier','service_no',
            'departure_date','departure_time','arrival_date','arrival_time',
            'travel_class','seat'
        ] as $name) {
            $fields[$name] = (array)$this->request->getPost('segment_' . $name);
        }

        $count = 0;
        foreach ($fields as $items) {
            $count = max($count, count($items));
        }

        $segments = [];

        for ($i = 0; $i < $count; $i++) {
            $origin = trim((string)($fields['origin'][$i] ?? ''));
            $destination = trim((string)($fields['destination'][$i] ?? ''));

            if ($origin === '' && $destination === '') {
                continue;
            }

            $segments[] = [
                'origin'         => $origin,
                'destination'    => $destination,
                'carrier'        => trim((string)($fields['carrier'][$i] ?? '')) ?: null,
                'service_no'     => trim((string)($fields['service_no'][$i] ?? '')) ?: null,
                'departure_date' => $this->nullableDate($fields['departure_date'][$i] ?? null),
                'departure_time' => $this->nullableTime($fields['departure_time'][$i] ?? null),
                'arrival_date'   => $this->nullableDate($fields['arrival_date'][$i] ?? null),
                'arrival_time'   => $this->nullableTime($fields['arrival_time'][$i] ?? null),
                'travel_class'   => trim((string)($fields['travel_class'][$i] ?? '')) ?: null,
                'seat'           => trim((string)($fields['seat'][$i] ?? '')) ?: null,
            ];
        }

        return $segments;
    }

    private function syncHeaderFromSegments(int $ticketId): void
    {
        $segments = $this->db->table('ticket_segments')
            ->where('ticket_booking_id', $ticketId)
            ->orderBy('segment_no', 'ASC')
            ->get()->getResultArray();

        if (!$segments) {
            return;
        }

        $first = $segments[0];
        $last = $segments[count($segments) - 1];

        $this->db->table('ticket_bookings')->where('id', $ticketId)->update([
            'origin'         => $first['origin'] ?? null,
            'destination'    => $last['destination'] ?? null,
            'departure_date' => $first['departure_date'] ?? null,
            'departure_time' => $first['departure_time'] ?? null,
            'arrival_date'   => $last['arrival_date'] ?? null,
            'arrival_time'   => $last['arrival_time'] ?? null,
            'carrier'        => $first['carrier'] ?? null,
            'travel_class'   => $first['travel_class'] ?? null,
            'seat'           => $first['seat'] ?? null,
            'updated_at'     => date('Y-m-d H:i:s'),
        ]);
    }

    private function enumValue(string $value, array $allowed, string $default): string
    {
        return in_array($value, $allowed, true) ? $value : $default;
    }

    private function nullableDate($value): ?string
    {
        $value = trim((string)$value);
        return ($value !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) ? $value : null;
    }

    private function nullableTime($value): ?string
    {
        $value = trim((string)$value);
        if ($value === '') return null;
        return preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $value) ? substr($value, 0, 8) : null;
    }
}
