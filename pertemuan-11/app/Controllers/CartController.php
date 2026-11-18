<?php

namespace App\Controllers;

use App\Models\CartItemModel;
use App\Models\CartModel;
use App\Models\ProductModel;

class CartController extends BaseController
{
    public function index()
    {
        $items    = (new CartItemModel())->itemsForUser($this->userId());
        $subtotal = array_sum(array_column($items, 'line_total'));

        return view('frontend/cart', [
            'title'    => 'Keranjang Belanja',
            'items'    => $items,
            'subtotal' => $subtotal,
        ]);
    }

    public function add()
    {
        $rules = [
            'product_id' => ['label' => 'Produk', 'rules' => 'required|is_natural_no_zero'],
            'quantity'   => ['label' => 'Jumlah', 'rules' => 'required|is_natural_no_zero|less_than_equal_to[999]'],
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $productId = (int) $this->request->getPost('product_id');
        $quantity  = (int) $this->request->getPost('quantity');

        // data produk selalu dicek ulang ke database (aktif + stok)
        $product = (new ProductModel())->where('status', 'active')->find($productId);
        if (! $product) {
            return redirect()->back()->with('error', 'Produk tidak tersedia.');
        }

        $cart      = (new CartModel())->getOrCreateForUser($this->userId());
        $itemModel = new CartItemModel();
        $existing  = $itemModel->where('cart_id', $cart['id'])->where('product_id', $productId)->first();
        $newQty    = $quantity + (int) ($existing['quantity'] ?? 0);

        if ($newQty > (int) $product['stock']) {
            return redirect()->back()->with('error', sprintf(
                'Stok "%s" tidak mencukupi (tersisa %d, di keranjang %d).',
                $product['name'],
                $product['stock'],
                (int) ($existing['quantity'] ?? 0)
            ));
        }

        if ($existing) {
            $itemModel->update($existing['id'], ['quantity' => $newQty]);
        } else {
            $itemModel->insert(['cart_id' => $cart['id'], 'product_id' => $productId, 'quantity' => $quantity]);
        }

        return redirect()->to('/cart')->with('success', '"' . $product['name'] . '" ditambahkan ke keranjang.');
    }

    public function update(int $itemId)
    {
        $itemModel = new CartItemModel();
        $item      = $itemModel->findOwned($itemId, $this->userId()); // hanya milik user login

        if (! $item) {
            return $this->notFound();
        }

        $rules = ['quantity' => ['label' => 'Jumlah', 'rules' => 'required|is_natural_no_zero|less_than_equal_to[999]']];
        if (! $this->validate($rules)) {
            return redirect()->to('/cart')->with('error', implode(' ', $this->validator->getErrors()));
        }

        $quantity = (int) $this->request->getPost('quantity');
        $product  = (new ProductModel())->find($item['product_id']);

        if (! $product || $product['status'] !== 'active') {
            return redirect()->to('/cart')->with('error', 'Produk sudah tidak tersedia.');
        }
        if ($quantity > (int) $product['stock']) {
            return redirect()->to('/cart')->with('error', 'Stok "' . $product['name'] . '" hanya tersisa ' . $product['stock'] . '.');
        }

        $itemModel->update($itemId, ['quantity' => $quantity]);

        return redirect()->to('/cart')->with('success', 'Jumlah diperbarui.');
    }

    public function remove(int $itemId)
    {
        $itemModel = new CartItemModel();

        if (! $itemModel->findOwned($itemId, $this->userId())) {
            return $this->notFound();
        }

        $itemModel->delete($itemId);

        return redirect()->to('/cart')->with('success', 'Item dihapus dari keranjang.');
    }
}
