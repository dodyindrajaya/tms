<?php

namespace App\Controllers;

use App\Services\InvoiceService;

class InvoicesController extends BaseController
{
    public function index()
    {
        $db = db_connect();
        $invoices = $db->table('invoices i')
            ->select('i.*, c.name AS customer_name, b.booking_no')
            ->join('customers c','c.id=i.customer_id','left')
            ->join('bookings b','b.id=i.booking_id','left')
            ->orderBy('i.id','DESC')->get()->getResultArray();

        return view('invoices/index', ['title'=>'Invoices','invoices'=>$invoices]);
    }

    public function show(int $id)
    {
        $db = db_connect();
        $invoice = $db->table('invoices i')
            ->select('i.*, c.name AS customer_name, b.booking_no')
            ->join('customers c','c.id=i.customer_id','left')
            ->join('bookings b','b.id=i.booking_id','left')
            ->where('i.id',$id)->get()->getRowArray();

        if (!$invoice) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $items = $db->table('invoice_items')->where('invoice_id',$id)->get()->getResultArray();

        return view('invoices/show', ['title'=>'Invoice '.$invoice['invoice_no'],'invoice'=>$invoice,'items'=>$items]);
    }

    public function post(int $id)
    {
        try {
            (new InvoiceService())->post($id);
            return redirect()->back()->with('success','Invoice posted and journal created.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error',$e->getMessage());
        }
    }
}
