<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Services\CartService;
use CodeIgniter\HTTP\RedirectResponse;
use RuntimeException;

/**
 * Keranjang belanja customer (FR-011).
 */
class CartController extends BaseController
{
    public function index(): string
    {
        $cart  = new CartService(current_user()['id']);
        $items = $cart->items();

        return view('customer/cart', [
            'title'  => 'Keranjang Belanja',
            'items'  => $items,
            'totals' => $cart->totals($items),
        ]);
    }

    public function add(): RedirectResponse
    {
        if (! $this->validate(['product_id' => 'required|is_natural_no_zero', 'quantity' => 'required|is_natural_no_zero'])) {
            return redirect()->back()->with('error', 'Jumlah produk tidak valid (minimal 1).');
        }

        try {
            $message = (new CartService(current_user()['id']))->add(
                (int) $this->request->getPost('product_id'),
                (int) $this->request->getPost('quantity'),
            );
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return $this->request->getPost('buy_now')
            ? redirect()->to(url_to('cart'))->with('success', $message)
            : redirect()->back()->with('success', $message);
    }

    public function update(int $itemId): RedirectResponse
    {
        try {
            (new CartService(current_user()['id']))->update($itemId, (int) $this->request->getPost('quantity'));
        } catch (RuntimeException $e) {
            return redirect()->to(url_to('cart'))->with('error', $e->getMessage());
        }

        return redirect()->to(url_to('cart'))->with('success', 'Jumlah produk diperbarui.');
    }

    public function remove(int $itemId): RedirectResponse
    {
        try {
            (new CartService(current_user()['id']))->remove($itemId);
        } catch (RuntimeException $e) {
            return redirect()->to(url_to('cart'))->with('error', $e->getMessage());
        }

        return redirect()->to(url_to('cart'))->with('success', 'Produk dihapus dari keranjang.');
    }
}
