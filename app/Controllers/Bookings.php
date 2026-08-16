<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BookingModel;
use App\Models\BookingItemModel;
use App\Models\CustomerModel;
use App\Models\ProductModel;
use App\Models\TicketBookingModel;

class Bookings extends BaseController
{
    protected BookingModel $bookingModel;
    protected BookingItemModel $itemModel;
    protected CustomerModel $customerModel;
    protected ProductModel $productModel;
    protected TicketBookingModel $ticketModel;

    public function __construct()
    {
        $this->bookingModel = new BookingModel();
        $this->itemModel = new BookingItemModel();
        $this->customerModel = new CustomerModel();
        $this->productModel = new ProductModel();
        $this->ticketModel = new TicketBookingModel();
    }

    public function index()
    {
        $q = trim((string)$this->request->getGet('q'));
        $status = trim((string)$this->request->getGet('status'));

        $builder = $this->bookingModel
            ->select('bookings.*, customers.name AS customer_name')
            ->join('customers', 'customers.id = bookings.customer_id', 'left');

        if ($q !== '') {
            $builder->groupStart()
                ->like('bookings.booking_no', $q)
                ->orLike('customers.name', $q)
                ->groupEnd();
        }

        if ($status !== '') {
            $builder->where('bookings.status', $status);
        }

        $bookings = $builder->orderBy('bookings.id', 'DESC')->paginate(15);

        return view('bookings/index', [
            'title' => 'Bookings',
            'bookings' => $bookings,
            'pager' => $this->bookingModel->pager,
            'q' => $q,
            'status' => $status,
            'statuses' => $this->statuses(),
        ]);
    }

    /**
     * Booking detail / read-only page.
     */
    public function show(int $id)
    {
        $booking = $this->bookingModel
            ->select('bookings.*, customers.name AS customer_name, customers.customer_code, customers.phone AS customer_phone, customers.email AS customer_email, customers.customer_type')
            ->join('customers', 'customers.id = bookings.customer_id', 'left')
            ->find($id);

        if (!$booking) {
            return redirect()->to(site_url('bookings'))
                ->with('error', 'Booking not found.');
        }

        $items = $this->itemModel
            ->select('booking_items.*, products.product_code, products.name AS product_name, products.category')
            ->join('products', 'products.id = booking_items.product_id', 'left')
            ->where('booking_items.booking_id', $id)
            ->orderBy('booking_items.id', 'ASC')
            ->findAll();

        $tickets = $this->ticketModel
            ->select('ticket_bookings.*, passengers.full_name AS passenger_name')
            ->join('passengers', 'passengers.id = ticket_bookings.passenger_id', 'left')
            ->where('ticket_bookings.booking_id', $id)
            ->orderBy('ticket_bookings.id', 'ASC')
            ->findAll();

        return view('bookings/show', [
            'title' => 'Booking Detail',
            'booking' => $booking,
            'items' => $items,
            'tickets' => $tickets,
            'statuses' => $this->statuses(),
        ]);
    }

    public function create()
    {
        return view('bookings/create', [
            'title' => 'New Booking',
            'customers' => $this->customerModel->where('is_active', 1)->orderBy('name')->findAll(),
            'products' => $this->productModel->where('is_active', 1)->orderBy('name')->findAll(),
            'sources' => $this->sources(),
            'statuses' => $this->statuses(),
        ]);
    }

    public function store()
    {
        $rules = [
            'customer_id' => 'required|integer',
            'booking_date' => 'required',
            'source' => 'required|in_list[walk_in,phone,whatsapp,website,agent,other]',
            'status' => 'required|in_list[draft,quotation,confirmed,partial_paid,paid,ready,traveling,completed,cancelled]',
            'currency_code' => 'required|exact_length[3]',
            'product_id' => 'required|integer',
            'quantity' => 'required|numeric|greater_than[0]',
            'unit_sale_price' => 'required|numeric|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Please check the booking form.');
        }

        $qty = (float)$this->request->getPost('quantity');
        $unit = (float)$this->request->getPost('unit_sale_price');
        $discount = (float)($this->request->getPost('discount_amount') ?: 0);
        $tax = (float)($this->request->getPost('tax_amount') ?: 0);

        $gross = max(0, $qty * $unit);
        $discount = min($discount, $gross);
        $subtotal = max(0, $gross - $discount);
        $lineTotal = max(0, $subtotal + $tax);

        $bookingNo = 'BK-' . date('Ymd-His') . '-' . random_int(10, 99);

        $this->bookingModel->db->transStart();

        $bookingId = $this->bookingModel->insert([
            'booking_no' => $bookingNo,
            'customer_id' => (int)$this->request->getPost('customer_id'),
            'booking_date' => $this->request->getPost('booking_date'),
            'travel_start_date' => $this->request->getPost('travel_start_date') ?: null,
            'travel_end_date' => $this->request->getPost('travel_end_date') ?: null,
            'source' => $this->request->getPost('source'),
            'status' => $this->request->getPost('status'),
            'currency_code' => strtoupper($this->request->getPost('currency_code')),
            'subtotal' => $subtotal,
            'discount_amount' => $discount,
            'tax_amount' => $tax,
            'total_amount' => $lineTotal,
            'paid_amount' => 0,
            'outstanding_amount' => $lineTotal,
            'notes' => trim((string)$this->request->getPost('notes')),
            'created_by' => (int)(session('user_id') ?: 1),
        ], true);

        if (!$bookingId) {
            $this->bookingModel->db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Booking could not be saved.');
        }

        $product = $this->productModel->find((int)$this->request->getPost('product_id'));

        $this->itemModel->insert([
            'booking_id' => $bookingId,
            'product_id' => (int)$this->request->getPost('product_id'),
            'description' => trim((string)($this->request->getPost('description') ?: ($product['name'] ?? ''))),
            'quantity' => $qty,
            'unit_sale_price' => $unit,
            'discount_amount' => $discount,
            'tax_amount' => $tax,
            'line_total' => $lineTotal,
            'revenue_account_id' => null,
            'cost_account_id' => null,
        ]);

        $this->bookingModel->db->transComplete();

        if ($this->bookingModel->db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Booking transaction failed.');
        }

        return redirect()->to(site_url('bookings/show/' . $bookingId))
            ->with('success', "Booking {$bookingNo} created.");
    }

    public function edit(int $id)
    {
        $booking = $this->bookingModel->find($id);

        if (!$booking) {
            return redirect()->to(site_url('bookings'))
                ->with('error', 'Booking not found.');
        }

        $item = $this->itemModel->where('booking_id', $id)->first();

        return view('bookings/edit', [
            'title' => 'Edit Booking',
            'booking' => $booking,
            'item' => $item,
            'customers' => $this->customerModel->orderBy('name')->findAll(),
            'products' => $this->productModel->where('is_active', 1)->orderBy('name')->findAll(),
            'sources' => $this->sources(),
            'statuses' => $this->statuses(),
        ]);
    }

    public function update(int $id)
    {
        $booking = $this->bookingModel->find($id);

        if (!$booking) {
            return redirect()->to(site_url('bookings'))
                ->with('error', 'Booking not found.');
        }

        if (!$this->validate([
            'customer_id' => 'required|integer',
            'booking_date' => 'required',
            'source' => 'required',
            'status' => 'required',
            'currency_code' => 'required|exact_length[3]',
            'product_id' => 'required|integer',
            'quantity' => 'required|numeric|greater_than[0]',
            'unit_sale_price' => 'required|numeric|greater_than_equal_to[0]',
        ])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please check the booking form.');
        }

        $qty = (float)$this->request->getPost('quantity');
        $unit = (float)$this->request->getPost('unit_sale_price');
        $discount = (float)($this->request->getPost('discount_amount') ?: 0);
        $tax = (float)($this->request->getPost('tax_amount') ?: 0);

        $gross = max(0, $qty * $unit);
        $discount = min($discount, $gross);
        $subtotal = max(0, $gross - $discount);
        $total = max(0, $subtotal + $tax);

        $oldPaid = (float)$booking['paid_amount'];

        $this->bookingModel->db->transStart();

        $this->bookingModel->update($id, [
            'customer_id' => (int)$this->request->getPost('customer_id'),
            'booking_date' => $this->request->getPost('booking_date'),
            'travel_start_date' => $this->request->getPost('travel_start_date') ?: null,
            'travel_end_date' => $this->request->getPost('travel_end_date') ?: null,
            'source' => $this->request->getPost('source'),
            'status' => $this->request->getPost('status'),
            'currency_code' => strtoupper($this->request->getPost('currency_code')),
            'subtotal' => $subtotal,
            'discount_amount' => $discount,
            'tax_amount' => $tax,
            'total_amount' => $total,
            'outstanding_amount' => max(0, $total - $oldPaid),
            'notes' => trim((string)$this->request->getPost('notes')),
        ]);

        $item = $this->itemModel->where('booking_id', $id)->first();

        $itemData = [
            'booking_id' => $id,
            'product_id' => (int)$this->request->getPost('product_id'),
            'description' => trim((string)$this->request->getPost('description')),
            'quantity' => $qty,
            'unit_sale_price' => $unit,
            'discount_amount' => $discount,
            'tax_amount' => $tax,
            'line_total' => $total,
        ];

        if ($item) {
            $this->itemModel->update($item['id'], $itemData);
        } else {
            $this->itemModel->insert($itemData);
        }

        $this->bookingModel->db->transComplete();

        if ($this->bookingModel->db->transStatus() === false) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Booking update transaction failed.');
        }

        return redirect()->to(site_url('bookings/show/' . $id))
            ->with('success', 'Booking updated successfully.');
    }

    public function cancel(int $id)
    {
        if (!$this->bookingModel->find($id)) {
            return redirect()->to(site_url('bookings'))
                ->with('error', 'Booking not found.');
        }

        $this->bookingModel->update($id, ['status' => 'cancelled']);

        return redirect()->to(site_url('bookings/show/' . $id))
            ->with('success', 'Booking cancelled.');
    }

    private function sources(): array
    {
        return [
            'walk_in' => 'Walk-in',
            'phone' => 'Phone',
            'whatsapp' => 'WhatsApp',
            'website' => 'Website',
            'agent' => 'Agent',
            'other' => 'Other',
        ];
    }

    private function statuses(): array
    {
        return [
            'draft' => 'Draft',
            'quotation' => 'Quotation',
            'confirmed' => 'Confirmed',
            'partial_paid' => 'Partial Paid',
            'paid' => 'Paid',
            'ready' => 'Ready',
            'traveling' => 'Traveling',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
    }
}
