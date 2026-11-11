<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Models\AddressModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Alamat pengiriman milik customer.
 */
class AddressController extends BaseController
{
    public function create(): RedirectResponse
    {
        if (! $this->validate(AddressModel::RULES)) {
            return redirect()->to(url_to('checkout'))->withInput()->with('errors', $this->validator->getErrors())->with('address_form_open', true);
        }

        $userId    = current_user()['id'];
        $addresses = model(AddressModel::class);
        $isFirst   = $addresses->count(['user_id' => $userId]) === 0;
        $id        = $addresses->insert($this->validator->getValidated() + ['user_id' => $userId, 'is_default' => $isFirst]);

        return redirect()->to(url_to('checkout'))->with('success', 'Alamat baru disimpan.')->with('selected_address', $id);
    }

    public function delete(int $id): RedirectResponse
    {
        $addresses = model(AddressModel::class);

        if ($addresses->findOwned($id, current_user()['id']) === null) {
            return redirect()->to(url_to('checkout'))->with('error', 'Alamat tidak ditemukan.');
        }

        // Pesanan lama menyimpan salinan alamat (shipping_address), sehingga riwayat tetap utuh.
        $addresses->delete($id);

        return redirect()->to(url_to('checkout'))->with('success', 'Alamat dihapus.');
    }
}
