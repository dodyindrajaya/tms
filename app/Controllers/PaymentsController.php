<?php

namespace App\Controllers;

use App\Models\BookingModel;
use App\Models\PaymentMethodModel;
use App\Services\PaymentService;

class PaymentsController extends BaseController
{
    public function index()
    {
        $db = db_connect();
        $payments = $db->table('payments p')
            ->select('p.*, c.name AS customer_name, b.booking_no, pm.name AS payment_method_name, je.entry_no')
            ->join('customers c','c.id=p.customer_id','left')
            ->join('bookings b','b.id=p.booking_id','left')
            ->join('payment_methods pm','pm.id=p.payment_method_id','left')
            ->join('journal_entries je','je.id=p.journal_entry_id','left')
            ->orderBy('p.id','DESC')->get()->getResultArray();

        $stats = [
            'count' => count($payments),
            'total' => (float)$db->table('payments')->selectSum('amount','total')->get()->getRow()->total,
        ];

        return view('payments/index', ['title'=>'Payments','payments'=>$payments,'stats'=>$stats]);
    }

    public function create()
    {
        $bookings = (new BookingModel())
            ->where('outstanding_amount >', 0)
            ->where('status !=', 'cancelled')
            ->orderBy('id','DESC')
            ->findAll();

        $paymentMethods = (new PaymentMethodModel())
            ->where('is_active', 1)
            ->orderBy('name')
            ->findAll();

        return view('payments/form', [
            'title'=>'Receive Customer Payment',
            'bookings'=>$bookings,
            'paymentMethods'=>$paymentMethods,
        ]);
    }

    public function store()
    {
        $rules = [
            'booking_id' => 'required|integer|greater_than[0]',
            'payment_method_id' => 'required|integer|greater_than[0]',
            'payment_date' => 'required|valid_date[Y-m-d]',
            'amount' => 'required|numeric|greater_than[0]',
            'reference_no' => 'permit_empty|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        try {
            $userId = session('user_id') ? (int)session('user_id') : null;
            $id = (new PaymentService())->receiveCustomerPayment(array_merge(
                $this->request->getPost(),
                ['created_by' => $userId]
            ));
            return redirect()->to('/payments')->with('success',"Payment #{$id} recorded and journal posted.");
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error',$e->getMessage());
        }
    }

    public function show(int $id)
    {
        $db = db_connect();
        $payment = $db->table('payments p')
            ->select('p.*, c.name AS customer_name, b.booking_no, pm.name AS payment_method_name, pm.method_type, a.code AS account_code, a.name AS account_name, je.entry_no, je.status AS journal_status')
            ->join('customers c','c.id=p.customer_id','left')
            ->join('bookings b','b.id=p.booking_id','left')
            ->join('payment_methods pm','pm.id=p.payment_method_id','left')
            ->join('accounts a','a.id=p.account_id','left')
            ->join('journal_entries je','je.id=p.journal_entry_id','left')
            ->where('p.id',$id)->get()->getRowArray();

        if (!$payment) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $lines = $payment['journal_entry_id'] ? $db->table('journal_entry_lines l')
            ->select('l.*, a.code AS account_code, a.name AS account_name')
            ->join('accounts a','a.id=l.account_id','left')
            ->where('l.journal_entry_id',$payment['journal_entry_id'])
            ->orderBy('l.id')->get()->getResultArray() : [];

        return view('payments/show', ['title'=>'Payment '.$payment['payment_no'],'payment'=>$payment,'lines'=>$lines]);
    }
}
