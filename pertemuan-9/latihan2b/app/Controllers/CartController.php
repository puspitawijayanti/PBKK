<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Services\CartService;
use CodeIgniter\HTTP\RedirectResponse;
use Config\Shop;

class CartController extends BaseController
{
    private CartService $cart;

    public function __construct()
    {
        $this->cart = new CartService();
    }

    public function index(): string
    {
        $items = $this->cart->items();

        return view('cart/index', [
            'title'  => 'Keranjang Belanja',
            'items'  => $items,
            'totals' => $this->cart->totals($items),
            'shop'   => config(Shop::class),
        ]);
    }

    public function add(): RedirectResponse
    {
        $productId = (int) $this->request->getPost('product_id');
        $quantity = max(1, (int) $this->request->getPost('quantity'));
        $product = model(ProductModel::class)->where('is_active', 1)->find($productId);

        if ($product === null) {
            return redirect()->back()->with('error', 'Produk tidak ditemukan.');
        }

        $alreadyInCart = (int) ($this->cart->raw()[$productId] ?? 0);

        if ($alreadyInCart + $quantity > (int) $product['stock']) {
            return redirect()->back()->with('error', 'Stok ' . $product['name'] . ' hanya tersisa ' . $product['stock'] . ' bungkus' . ($alreadyInCart > 0 ? ' dan ' . $alreadyInCart . ' sudah ada di keranjang.' : '.'));
        }

        $this->cart->add($productId, $quantity);

        return redirect()->back()->with('success', $product['name'] . ' ditambahkan ke keranjang.');
    }

    public function update(): RedirectResponse
    {
        $quantities = (array) $this->request->getPost('quantities');
        $warnings = [];

        foreach ($this->cart->items() as $item) {
            $productId = (int) $item['product']['id'];

            if (! array_key_exists($productId, $quantities)) {
                continue;
            }

            $requested = (int) $quantities[$productId];
            $allowed = min($requested, (int) $item['product']['stock']);

            if ($requested > $allowed) {
                $warnings[] = $item['product']['name'] . ' disesuaikan menjadi ' . $allowed . ' (stok tersisa).';
            }

            $this->cart->set($productId, $allowed);
        }

        $redirect = redirect()->to('cart')->with('success', 'Keranjang diperbarui.');

        return $warnings === [] ? $redirect : $redirect->with('info', implode(' ', $warnings));
    }

    public function remove(int $productId): RedirectResponse
    {
        $this->cart->remove($productId);

        return redirect()->to('cart')->with('success', 'Produk dihapus dari keranjang.');
    }
}
