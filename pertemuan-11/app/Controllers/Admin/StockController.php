<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProductModel;

/** Pembaruan stok cepat; dapat diakses admin dan operator toko. */
class StockController extends BaseController
{
    public function index()
    {
        $q     = trim((string) $this->request->getGet('q'));
        $model = new ProductModel();

        return view('admin/stock', [
            'title'    => 'Manajemen Stok',
            'products' => $model->adminList(['q' => $q])->paginate(config('Shop')->adminPerPage),
            'pager'    => $model->pager,
            'q'        => $q,
        ]);
    }

    public function update(int $id)
    {
        $model   = new ProductModel();
        $product = $model->find($id);
        if (! $product) {
            return $this->notFound();
        }

        $rules = ['stock' => ['label' => 'Stok', 'rules' => 'required|is_natural|less_than_equal_to[1000000]']]; // tidak boleh negatif
        if (! $this->validate($rules)) {
            return redirect()->back()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $model->update($id, ['stock' => (int) $this->request->getPost('stock')]);

        return redirect()->back()->with('success', 'Stok "' . $product['name'] . '" diperbarui.');
    }
}
