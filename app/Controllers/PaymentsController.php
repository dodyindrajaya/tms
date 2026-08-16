<?php

namespace App\Controllers;

use App\Models\BookingModel;
use App\Services\PaymentService;

class PaymentsController extends BaseController
{
    public function index()
    {
        $db = db_connect();
        $payments = $db->table('payments p')
            ->select('p.*, c.name AS customer_name, b.booking_no')
            ->join('customers c','c.id=p.customer_id','left')
            ->join('bookings b','b.id=p.booking_id','left')
            ->orderBy('p.id','DESC')->get()->getResultArray();

        return view('payments/index', ['title'=>'Payments','payments'=>$payments]);
    }

    public function create()
    {
        return view('payments/form', [
            'title'=>'Receive Customer Payment',
            'bookings'=>(new BookingModel())->orderBy('id','DESC')->findAll(),
        ]);
    }

    public function store()
    {
        try {
            $id = (new PaymentService())->receiveCustomerPayment($this->request->getPost());
            return redirect()->to('/payments')->with('success',"Payment #{$id} recorded and journal posted.");
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error',$e->getMessage());
        }
    }
}
