<?php

namespace App\Controllers\Buyer;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Services\CartService;
use App\Services\OrderService;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Shop;
use RuntimeException;

class CheckoutController extends BaseController
{
    public function index(): ResponseInterface|string
    {
        $cart = new CartService();
        $items = $cart->items();

        if ($items === []) {
            return redirect()->to('products')->with('info', 'Keranjang masih kosong. Pilih kopi favorit Anda terlebih dahulu.');
        }

        return view('buyer/checkout', [
            'title'  => 'Checkout',
            'items'  => $items,
            'totals' => $cart->totals($items),
            'user'   => model(UserModel::class)->find($this->currentUserId()),
            'shop'   => config(Shop::class),
        ]);
    }

    public function store(): RedirectResponse
    {
        $rules = [
            'recipient_name' => ['rules' => 'required|min_length[3]|max_length[100]', 'errors' => ['required' => 'Nama penerima wajib diisi.', 'min_length' => 'Nama penerima minimal 3 karakter.']],
            'phone'          => ['rules' => 'required|regex_match[/^(\+62|0)[0-9]{8,13}$/]', 'errors' => ['required' => 'Nomor HP wajib diisi.', 'regex_match' => 'Nomor HP tidak valid.']],
            'address'        => ['rules' => 'required|min_length[10]|max_length[500]', 'errors' => ['required' => 'Alamat wajib diisi.', 'min_length' => 'Alamat terlalu singkat.']],
            'city'           => ['rules' => 'required|max_length[60]', 'errors' => ['required' => 'Kota wajib diisi.']],
            'postal_code'    => ['rules' => 'required|regex_match[/^[0-9]{5}$/]', 'errors' => ['required' => 'Kode pos wajib diisi.', 'regex_match' => 'Kode pos harus 5 digit angka.']],
            'note'           => ['rules' => 'permit_empty|max_length[255]', 'errors' => ['max_length' => 'Catatan maksimal 255 karakter.']],
        ];

        if (! $this->validate($rules)) {
            return $this->backWithErrors($this->validator->getErrors());
        }

        try {
            $order = (new OrderService())->checkout($this->currentUserId(), $this->validator->getValidated(), new CartService());
        } catch (RuntimeException $exception) {
            return redirect()->to('cart')->with('error', $exception->getMessage());
        }

        if ($this->request->getPost('save_address') === '1') {
            model(UserModel::class)->update($this->currentUserId(), [
                'phone'   => $order['phone'],
                'address' => $order['address'] . ', ' . $order['city'] . ' ' . $order['postal_code'],
            ]);
        }

        return redirect()->to('orders/' . $order['code'])->with('success', 'Pesanan ' . $order['code'] . ' berhasil dibuat. Silakan transfer ' . rupiah($order['total']) . ' dan unggah bukti pembayaran.');
    }
}
