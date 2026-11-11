<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Models\AddressModel;
use App\Services\CartService;
use App\Services\OrderService;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use RuntimeException;

/**
 * Checkout: pilih alamat, konfirmasi, dan buat pesanan (FR-012, FR-013).
 */
class CheckoutController extends BaseController
{
    public function index(): ResponseInterface|string
    {
        $userId = current_user()['id'];
        $cart   = new CartService($userId);
        $items  = $cart->items();

        if ($items === []) {
            return redirect()->to(url_to('cart'))->with('info', 'Keranjang masih kosong.');
        }

        // Token sekali pakai untuk mencegah submit ganda (PRD 13: Checkout).
        $token = bin2hex(random_bytes(16));
        session()->set('checkout_token', $token);

        return view('customer/checkout', [
            'title'     => 'Checkout',
            'items'     => $items,
            'totals'    => $cart->totals($items),
            'addresses' => model(AddressModel::class)->forUser($userId),
            'token'     => $token,
        ]);
    }

    public function store(): RedirectResponse
    {
        $token = (string) $this->request->getPost('checkout_token');

        if ($token === '' || ! hash_equals((string) session('checkout_token'), $token)) {
            return redirect()->to(url_to('orders'))->with('info', 'Pesanan ini sudah diproses. Cek daftar pesanan Anda.');
        }

        session()->remove('checkout_token');

        $rules = [
            'address_id' => ['label' => 'Alamat pengiriman', 'rules' => 'required|is_natural_no_zero', 'errors' => ['required' => 'Pilih alamat pengiriman.']],
            'notes'      => ['label' => 'Catatan', 'rules' => 'permit_empty|max_length[500]'],
        ];

        if (! $this->validate($rules)) {
            return redirect()->to(url_to('checkout'))->withInput()->with('errors', $this->validator->getErrors());
        }

        $userId = current_user()['id'];

        try {
            $order = (new OrderService())->checkout(
                $userId,
                (int) $this->request->getPost('address_id'),
                trim((string) $this->request->getPost('notes')),
                new CartService($userId),
            );
        } catch (RuntimeException $e) {
            return redirect()->to(url_to('checkout'))->withInput()->with('error', $e->getMessage());
        }

        return redirect()->to(url_to('order.show', $order['order_number']))
            ->with('success', 'Pesanan ' . $order['order_number'] . ' berhasil dibuat. Silakan lakukan pembayaran.');
    }
}
