<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AddressModel;
use App\Models\OrderModel;
use App\Models\UserModel;

class CustomerController extends BaseController
{
    public function index()
    {
        $q     = trim((string) $this->request->getGet('q'));
        $model = new UserModel();

        return view('admin/customers/index', [
            'title'     => 'Pelanggan',
            'customers' => $model->customersWithStats($q)->paginate(config('Shop')->adminPerPage),
            'pager'     => $model->pager,
            'q'         => $q,
        ]);
    }

    public function show(int $id)
    {
        $customer = (new UserModel())->where('role', 'customer')->find($id);
        if (! $customer) {
            return $this->notFound();
        }
        unset($customer['password']); // hash password tidak pernah dikirim ke view

        return view('admin/customers/show', [
            'title'     => 'Pelanggan: ' . $customer['name'],
            'customer'  => $customer,
            'addresses' => (new AddressModel())->forUser($id),
            'orders'    => (new OrderModel())->withRelations()->where('orders.user_id', $id)->orderBy('orders.id', 'DESC')->findAll(),
        ]);
    }
}
