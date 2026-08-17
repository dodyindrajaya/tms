<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TicketBookingModel;
use App\Models\BookingModel;
use App\Models\PassengerModel;
use App\Models\SupplierModel;

class Tickets extends BaseController
{
    protected TicketBookingModel $model;
    public function __construct() { $this->model = new TicketBookingModel(); }

    public function index()
    {
        $q=trim((string)$this->request->getGet('q'));
        $type=trim((string)$this->request->getGet('ticket_type'));
        $builder=$this->model
            ->select('ticket_bookings.*, bookings.booking_no, passengers.full_name AS passenger_name')
            ->join('bookings','bookings.id=ticket_bookings.booking_id','left')
            ->join('passengers','passengers.id=ticket_bookings.passenger_id','left');

        if($q!=='') $builder->groupStart()->like('ticket_bookings.booking_code',$q)->orLike('ticket_bookings.ticket_number',$q)->orLike('passengers.full_name',$q)->groupEnd();
        if($type!=='') $builder->where('ticket_bookings.ticket_type',$type);

        $tickets=$builder->orderBy('ticket_bookings.id','DESC')->paginate(15);
        return view('tickets/index',['title'=>'Ticketing','tickets'=>$tickets,'pager'=>$this->model->pager,'q'=>$q,'type'=>$type,'types'=>$this->types()]);
    }

    public function create()
    {
        return view('tickets/create',[
            'title'=>'New Ticket',
            'bookings'=>(new BookingModel())->orderBy('id','DESC')->findAll(100),
            'passengers'=>(new PassengerModel())->orderBy('full_name')->findAll(),
            'suppliers'=>(new SupplierModel())->where('is_active',1)->orderBy('name')->findAll(),
            'types'=>$this->types(),'statuses'=>$this->statuses()
        ]);
    }

    public function store()
    {
        if(!$this->validate([
            'booking_id'=>'required|integer','passenger_id'=>'required|integer',
            'ticket_type'=>'required|in_list[flight,train,bus,ferry,other]',
            'status'=>'required','cost_price'=>'required|numeric|greater_than_equal_to[0]',
            'selling_price'=>'required|numeric|greater_than_equal_to[0]',
        ])) return redirect()->back()->withInput()->with('error','Please check the ticket form.');

        $data=$this->input();
        if(!$this->model->insert($data)) return redirect()->back()->withInput()->with('error','Ticket could not be saved.');
        return redirect()->to(site_url('tickets'))->with('success','Ticket created successfully.');
    }

    public function edit(int $id)
    {
        $ticket=$this->model->find($id);
        if(!$ticket) return redirect()->to(site_url('tickets'))->with('error','Ticket not found.');
        return view('tickets/edit',[
            'title'=>'Edit Ticket','ticket'=>$ticket,
            'bookings'=>(new BookingModel())->orderBy('id','DESC')->findAll(100),
            'passengers'=>(new PassengerModel())->orderBy('full_name')->findAll(),
            'suppliers'=>(new SupplierModel())->where('is_active',1)->orderBy('name')->findAll(),
            'types'=>$this->types(),'statuses'=>$this->statuses()
        ]);
    }

    public function update(int $id)
    {
        if(!$this->model->find($id)) return redirect()->to(site_url('tickets'))->with('error','Ticket not found.');
        if(!$this->validate([
            'booking_id'=>'required|integer','passenger_id'=>'required|integer',
            'ticket_type'=>'required','status'=>'required',
            'cost_price'=>'required|numeric|greater_than_equal_to[0]',
            'selling_price'=>'required|numeric|greater_than_equal_to[0]',
        ])) return redirect()->back()->withInput()->with('error','Please check the ticket form.');
        $this->model->update($id,$this->input());
        return redirect()->to(site_url('tickets'))->with('success','Ticket updated successfully.');
    }

    public function cancel(int $id)
    {
        if($this->model->find($id)) $this->model->update($id,['status'=>'cancelled']);
        return redirect()->to(site_url('tickets'))->with('success','Ticket cancelled.');
    }

    private function input():array
    {
        return [
            'booking_id'=>(int)$this->request->getPost('booking_id'),
            'passenger_id'=>(int)$this->request->getPost('passenger_id'),
            'ticket_type'=>$this->request->getPost('ticket_type'),
            'supplier_id'=>$this->request->getPost('supplier_id') ?: null,
            'booking_code'=>trim((string)$this->request->getPost('booking_code')),
            'ticket_number'=>trim((string)$this->request->getPost('ticket_number')),
            'issue_date'=>$this->request->getPost('issue_date') ?: null,
            'departure_date'=>$this->request->getPost('departure_date') ?: null,
            'departure_time'=>$this->request->getPost('departure_time') ?: null,
            'arrival_date'=>$this->request->getPost('arrival_date') ?: null,
            'arrival_time'=>$this->request->getPost('arrival_time') ?: null,
            'origin'=>trim((string)$this->request->getPost('origin')),
            'destination'=>trim((string)$this->request->getPost('destination')),
            'carrier'=>trim((string)$this->request->getPost('carrier')),
            'travel_class'=>trim((string)$this->request->getPost('travel_class')),
            'seat'=>trim((string)$this->request->getPost('seat')),
            'status'=>$this->request->getPost('status'),
            'cost_price'=>(float)$this->request->getPost('cost_price'),
            'selling_price'=>(float)$this->request->getPost('selling_price'),
        ];
    }

    private function types():array{return ['flight'=>'Flight','train'=>'Train','bus'=>'Bus','ferry'=>'Ferry','other'=>'Other'];}
    private function statuses():array{return ['request'=>'Request','quoted'=>'Quoted','booked'=>'Booked','paid'=>'Paid','issued'=>'Issued','completed'=>'Completed','cancelled'=>'Cancelled','void'=>'Void','refunded'=>'Refunded','rescheduled'=>'Rescheduled'];}
}
