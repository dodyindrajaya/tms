<?php
namespace App\Controllers;

use App\Models\CustomerModel;

class Customers extends BaseController
{
    protected CustomerModel $customerModel;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
    }

    public function index()
    {
        $q = trim((string)$this->request->getGet('q'));
        $type = trim((string)$this->request->getGet('customer_type'));
        $builder = $this->customerModel;

        if ($q !== '') {
            $builder = $builder->groupStart()
                ->like('customer_code', $q)
                ->orLike('name', $q)
                ->orLike('phone', $q)
                ->orLike('email', $q)
                ->groupEnd();
        }
        if ($type !== '') $builder = $builder->where('customer_type', $type);

        return view('customers/index', [
            'title'=>'Customers',
            'customers'=>$builder->orderBy('id','DESC')->paginate(15),
            'pager'=>$this->customerModel->pager,
            'q'=>$q, 'type'=>$type,
        ]);
    }

    public function create() { return view('customers/create', ['title'=>'New Customer','customer'=>[]]); }

    public function store()
    {
        if (!$this->validate($this->rules())) return redirect()->back()->withInput()->with('error','Please check the form.');
        if (!$this->customerModel->insert($this->inputData())) return redirect()->back()->withInput()->with('error','Customer could not be saved.');
        return redirect()->to(site_url('customers'))->with('success','Customer created successfully.');
    }

    public function edit(int $id)
    {
        $customer=$this->customerModel->find($id);
        if (!$customer) return redirect()->to(site_url('customers'))->with('error','Customer not found.');
        return view('customers/edit',['title'=>'Edit Customer','customer'=>$customer]);
    }

    public function update(int $id)
    {
        if (!$this->customerModel->find($id)) return redirect()->to(site_url('customers'))->with('error','Customer not found.');
        if (!$this->validate($this->rules($id))) return redirect()->back()->withInput()->with('error','Please check the form.');
        if (!$this->customerModel->update($id,$this->inputData())) return redirect()->back()->withInput()->with('error','Customer could not be updated.');
        return redirect()->to(site_url('customers'))->with('success','Customer updated successfully.');
    }

    public function delete(int $id)
    {
        if (!$this->customerModel->find($id)) return redirect()->to(site_url('customers'))->with('error','Customer not found.');
        $this->customerModel->delete($id);
        return redirect()->to(site_url('customers'))->with('success','Customer deleted successfully.');
    }

    private function inputData(): array
    {
        return [
            'customer_code'=>trim((string)$this->request->getPost('customer_code')),
            'name'=>trim((string)$this->request->getPost('name')),
            'customer_type'=>trim((string)$this->request->getPost('customer_type')),
            'phone'=>trim((string)$this->request->getPost('phone')),
            'email'=>trim((string)$this->request->getPost('email')),
            'address'=>trim((string)$this->request->getPost('address')),
            'city'=>trim((string)$this->request->getPost('city')),
            'province'=>trim((string)$this->request->getPost('province')),
            'postal_code'=>trim((string)$this->request->getPost('postal_code')),
            'is_active'=>$this->request->getPost('is_active') ? 1 : 0,
            'notes'=>trim((string)$this->request->getPost('notes')),
        ];
    }

    private function rules(?int $id=null): array
    {
        $unique='is_unique[customers.customer_code]';
        if ($id!==null) $unique='is_unique[customers.customer_code,id,'.$id.']';
        return [
            'customer_code'=>'required|max_length[30]|'.$unique,
            'name'=>'required|min_length[2]|max_length[150]',
            'customer_type'=>'required|in_list[individual,corporate]',
            'email'=>'permit_empty|valid_email|max_length[150]',
            'phone'=>'permit_empty|max_length[30]',
        ];
    }
}
