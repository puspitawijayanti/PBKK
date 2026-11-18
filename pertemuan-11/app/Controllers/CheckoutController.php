<?php

namespace App\Controllers;

use App\Libraries\OrderException;
use App\Libraries\OrderService;
use App\Models\AddressModel;
use App\Models\CartItemModel;

class CheckoutController extends BaseController
{
    public function index()
    {
        $items = (new CartItemModel())->itemsForUser($this->userId());

        if (! $items) {
            return redirect()->to('/cart')->with('error', 'Keranjang masih kosong.');
        }

        // tandai item bermasalah (nonaktif / stok kurang) agar customer bisa memperbaiki sebelum checkout
        $problems = [];
        foreach ($items as $it) {
            if ($it['status'] !== 'active') {
                $problems[] = '"' . $it['name'] . '" sudah tidak tersedia.';
            } elseif ((int) $it['quantity'] > (int) $it['stock']) {
                $problems[] = 'Stok "' . $it['name'] . '" hanya tersisa ' . $it['stock'] . '.';
            }
        }

        $subtotal = array_sum(array_column($items, 'line_total'));
        $shipping = config('Shop')->shippingCost;

        return view('frontend/checkout', [
            'title'     => 'Checkout',
            'items'     => $items,
            'subtotal'  => $subtotal,
            'shipping'  => $shipping,
            'total'     => $subtotal + $shipping, // hanya tampilan; total final dihitung ulang di backend
            'addresses' => (new AddressModel())->forUser($this->userId()),
            'problems'  => $problems,
        ]);
    }

    public function place()
    {
        $rules = [
            'address_id' => ['label' => 'Alamat pengiriman', 'rules' => 'required|is_natural_no_zero', 'errors' => ['required' => 'Pilih alamat pengiriman terlebih dahulu.']],
            'notes'      => ['label' => 'Catatan', 'rules' => 'permit_empty|max_length[500]'],
        ];
        if (! $this->validate($rules)) {
            return $this->backWithErrors();
        }

        try {
            // CATATAN: tidak ada harga/total dari browser yang dipakai di sini
            $orderId = (new OrderService())->placeOrder(
                $this->userId(),
                (int) $this->request->getPost('address_id'),
                trim((string) $this->request->getPost('notes')) ?: null
            );
        } catch (OrderException $e) {
            return redirect()->to('/cart')->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            log_message('error', 'Checkout gagal: ' . $e->getMessage());

            return redirect()->to('/cart')->with('error', 'Terjadi kesalahan saat membuat pesanan. Silakan coba lagi.');
        }

        return redirect()->to('/orders/' . $orderId)
            ->with('success', 'Pesanan berhasil dibuat. Silakan lakukan pembayaran dan unggah bukti transfer.');
    }
}
