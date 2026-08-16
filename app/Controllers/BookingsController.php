<?php

namespace App\Controllers;

use App\Models\CustomerModel;
use App\Models\ProductModel;
use App\Services\BookingService;
use App\Services\InvoiceService;

class BookingsController extends BaseController
{
    public function index()
    {
        $db = db_connect();
        $bookings = $db->table('bookings b')
            ->select('b.*, c.name AS customer_name')
            ->join('customers c', 'c.id=b.customer_id', 'left')
            ->orderBy('b.id', 'DESC')
            ->get()->getResultArray();

        return view('bookings/index', [
            'title' => 'Bookings',
            'bookings' => $bookings,
        ]);
    }

    public function create()
    {
        return view('bookings/form', [
            'title' => 'New Booking',
            'customers' => (new CustomerModel())->where('is_active', 1)->orderBy('name')->findAll(),
            'products' => (new ProductModel())->where('is_active', 1)->orderBy('name')->findAll(),
        ]);
    }

    public function store()
    {
        $post = $this->request->getPost();

        $items = [[
            'product_id' => (int)($post['product_id'] ?? 0),
            'description' => $post['description'] ?? '',
            'quantity' => (float)($post['quantity'] ?? 1),
            'unit_sale_price' => (float)($post['unit_sale_price'] ?? 0),
            'discount_amount' => (float)($post['discount_amount'] ?? 0),
            'tax_amount' => (float)($post['tax_amount'] ?? 0),
        ]];

        try {
            $id = (new BookingService())->create([
                'customer_id' => (int)$post['customer_id'],
                'booking_date' => $post['booking_date'] ?? date('Y-m-d'),
                'travel_start_date' => $post['travel_start_date'] ?? null,
                'travel_end_date' => $post['travel_end_date'] ?? null,
                'source' => $post['source'] ?? 'office',
                'currency_code' => 'IDR',
                'notes' => $post['notes'] ?? null,
                'items' => $items,
            ]);

            return redirect()->to("/bookings/{$id}")->with('success', "Booking #{$id} created.");
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(int $id)
    {
        $db = db_connect();

        $booking = $db->table('bookings b')
            ->select('b.*, c.name AS customer_name, c.phone AS customer_phone')
            ->join('customers c', 'c.id=b.customer_id', 'left')
            ->where('b.id', $id)->get()->getRowArray();

        if (!$booking) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $items = $db->table('booking_items bi')
            ->select('bi.*, p.name AS product_name')
            ->join('products p', 'p.id=bi.product_id', 'left')
            ->where('booking_id', $id)->get()->getResultArray();

        $invoice = $db->table('invoices')->where('booking_id', $id)->orderBy('id','DESC')->get()->getRowArray();

        return view('bookings/show', [
            'title' => 'Booking '.$booking['booking_no'],
            'booking' => $booking,
            'items' => $items,
            'invoice' => $invoice,
        ]);
    }

    public function invoice(int $id)
    {
        try {
            $invoiceId = (new InvoiceService())->createFromBooking($id);
            return redirect()->to("/invoices/{$invoiceId}")->with('success', "Invoice #{$invoiceId} created.");
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
