<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TourPackageModel;
use App\Models\TourDepartureModel;
use App\Models\ProductModel;

class Tours extends BaseController
{
    protected TourPackageModel $model;
    protected TourDepartureModel $departureModel;
    protected ProductModel $productModel;

    public function __construct()
    {
        $this->model=new TourPackageModel();
        $this->departureModel=new TourDepartureModel();
        $this->productModel=new ProductModel();
    }

    public function index()
    {
        $q=trim((string)$this->request->getGet('q'));
        $builder=$this->model->select('tour_packages.*, products.name AS product_name')
            ->join('products','products.id=tour_packages.product_id','left');
        if($q!=='') $builder->groupStart()->like('tour_packages.package_code',$q)->orLike('tour_packages.name',$q)->orLike('tour_packages.destination',$q)->groupEnd();
        $tours=$builder->orderBy('tour_packages.id','DESC')->paginate(15);
        return view('tours/index',['title'=>'Tours','tours'=>$tours,'pager'=>$this->model->pager,'q'=>$q]);
    }

    public function create()
    {
        return view('tours/create',['title'=>'New Tour','products'=>$this->productModel->where('is_active',1)->orderBy('name')->findAll()]);
    }

    public function store()
    {
        if(!$this->validate([
            'product_id'=>'required|integer','package_code'=>'required|max_length[50]|is_unique[tour_packages.package_code]',
            'name'=>'required|min_length[2]|max_length[190]','destination'=>'required|max_length[255]',
            'duration_days'=>'required|integer|greater_than[0]','duration_nights'=>'required|integer|greater_than_equal_to[0]',
        ])) return redirect()->back()->withInput()->with('error','Please check the tour form.');

        $this->model->insert([
            'product_id'=>(int)$this->request->getPost('product_id'),
            'package_code'=>trim((string)$this->request->getPost('package_code')),
            'name'=>trim((string)$this->request->getPost('name')),
            'destination'=>trim((string)$this->request->getPost('destination')),
            'duration_days'=>(int)$this->request->getPost('duration_days'),
            'duration_nights'=>(int)$this->request->getPost('duration_nights'),
            'description'=>trim((string)$this->request->getPost('description')),
            'is_active'=>$this->request->getPost('is_active') ? 1 : 0,
        ]);
        return redirect()->to(site_url('tours'))->with('success','Tour package created successfully.');
    }

    public function edit(int $id)
    {
        $tour=$this->model->find($id);
        if(!$tour) return redirect()->to(site_url('tours'))->with('error','Tour not found.');
        return view('tours/edit',['title'=>'Edit Tour','tour'=>$tour,'products'=>$this->productModel->where('is_active',1)->orderBy('name')->findAll()]);
    }

    public function update(int $id)
    {
        if(!$this->model->find($id)) return redirect()->to(site_url('tours'))->with('error','Tour not found.');
        if(!$this->validate([
            'product_id'=>'required|integer','package_code'=>'required|max_length[50]|is_unique[tour_packages.package_code,id,'.$id.']',
            'name'=>'required|min_length[2]|max_length[190]','destination'=>'required|max_length[255]',
            'duration_days'=>'required|integer|greater_than[0]','duration_nights'=>'required|integer|greater_than_equal_to[0]',
        ])) return redirect()->back()->withInput()->with('error','Please check the tour form.');

        $this->model->update($id,[
            'product_id'=>(int)$this->request->getPost('product_id'),
            'package_code'=>trim((string)$this->request->getPost('package_code')),
            'name'=>trim((string)$this->request->getPost('name')),
            'destination'=>trim((string)$this->request->getPost('destination')),
            'duration_days'=>(int)$this->request->getPost('duration_days'),
            'duration_nights'=>(int)$this->request->getPost('duration_nights'),
            'description'=>trim((string)$this->request->getPost('description')),
            'is_active'=>$this->request->getPost('is_active') ? 1 : 0,
        ]);
        return redirect()->to(site_url('tours'))->with('success','Tour updated successfully.');
    }

    public function departures(int $tourId)
    {
        $tour=$this->model->find($tourId);
        if(!$tour) return redirect()->to(site_url('tours'))->with('error','Tour not found.');
        return view('tours/departures',[
            'title'=>'Tour Departures','tour'=>$tour,
            'departures'=>$this->departureModel->where('tour_package_id',$tourId)->orderBy('departure_date','DESC')->findAll()
        ]);
    }

    public function storeDeparture(int $tourId)
    {
        if(!$this->model->find($tourId)) return redirect()->to(site_url('tours'))->with('error','Tour not found.');
        if(!$this->validate([
            'departure_date'=>'required|valid_date[Y-m-d]',
            'return_date'=>'required|valid_date[Y-m-d]',
            'capacity'=>'required|integer|greater_than[0]',
            'status'=>'required|in_list[draft,open,full,departed,completed,cancelled]'
        ])) return redirect()->back()->withInput()->with('error','Please check departure data.');

        $this->departureModel->insert([
            'tour_package_id'=>$tourId,
            'departure_date'=>$this->request->getPost('departure_date'),
            'return_date'=>$this->request->getPost('return_date'),
            'capacity'=>(int)$this->request->getPost('capacity'),
            'status'=>$this->request->getPost('status'),
            'meeting_point'=>trim((string)$this->request->getPost('meeting_point')),
            'notes'=>trim((string)$this->request->getPost('notes')),
        ]);
        return redirect()->to(site_url('tours/departures/'.$tourId))->with('success','Departure added.');
    }

    public function cancelDeparture(int $id)
    {
        $row=$this->departureModel->find($id);
        if($row) {
            $this->departureModel->update($id,['status'=>'cancelled']);
            return redirect()->to(site_url('tours/departures/'.$row['tour_package_id']))->with('success','Departure cancelled.');
        }
        return redirect()->to(site_url('tours'))->with('error','Departure not found.');
    }
}
