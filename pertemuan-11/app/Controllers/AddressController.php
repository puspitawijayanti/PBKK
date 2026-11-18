<?php

namespace App\Controllers;

use App\Models\AddressModel;

class AddressController extends BaseController
{
    private function rules(): array
    {
        return [
            'recipient_name' => ['label' => 'Nama penerima', 'rules' => 'required|min_length[3]|max_length[100]'],
            'phone'          => ['label' => 'No. telepon', 'rules' => 'required|regex_match[/^[0-9+\- ]{8,20}$/]', 'errors' => ['regex_match' => 'No. telepon tidak valid.']],
            'address'        => ['label' => 'Alamat lengkap', 'rules' => 'required|min_length[10]|max_length[500]'],
            'city'           => ['label' => 'Kota', 'rules' => 'required|max_length[100]'],
            'province'       => ['label' => 'Provinsi', 'rules' => 'required|max_length[100]'],
            'postal_code'    => ['label' => 'Kode pos', 'rules' => 'required|regex_match[/^[0-9]{5}$/]', 'errors' => ['regex_match' => 'Kode pos harus 5 digit angka.']],
        ];
    }

    public function index()
    {
        return view('frontend/addresses/index', [
            'title'     => 'Alamat Pengiriman',
            'addresses' => (new AddressModel())->forUser($this->userId()),
        ]);
    }

    public function create()
    {
        return view('frontend/addresses/form', [
            'title'   => 'Tambah Alamat',
            'address' => null,
            'return'  => (string) $this->request->getGet('return'),
        ]);
    }

    public function store()
    {
        if (! $this->validate($this->rules())) {
            return $this->backWithErrors();
        }

        $model  = new AddressModel();
        $isFirst = $model->where('user_id', $this->userId())->countAllResults() === 0;

        $id = $model->insert($this->payload() + ['user_id' => $this->userId(), 'is_default' => 0], true);
        if ($isFirst || $this->request->getPost('is_default')) {
            $model->setDefault((int) $id, $this->userId());
        }

        $to = $this->request->getPost('return') === 'checkout' ? '/checkout' : '/account/addresses';

        return redirect()->to($to)->with('success', 'Alamat disimpan.');
    }

    public function edit(int $id)
    {
        $address = (new AddressModel())->findOwned($id, $this->userId());
        if (! $address) {
            return $this->notFound();
        }

        return view('frontend/addresses/form', ['title' => 'Ubah Alamat', 'address' => $address, 'return' => '']);
    }

    public function update(int $id)
    {
        $model = new AddressModel();
        if (! $model->findOwned($id, $this->userId())) {
            return $this->notFound();
        }
        if (! $this->validate($this->rules())) {
            return $this->backWithErrors();
        }

        $model->update($id, $this->payload());
        if ($this->request->getPost('is_default')) {
            $model->setDefault($id, $this->userId());
        }

        return redirect()->to('/account/addresses')->with('success', 'Alamat diperbarui.');
    }

    public function delete(int $id)
    {
        $model = new AddressModel();
        if (! $model->findOwned($id, $this->userId())) {
            return $this->notFound();
        }

        // pesanan lama aman: alamat disimpan sebagai snapshot pada orders.shipping_address
        $model->delete($id);

        return redirect()->to('/account/addresses')->with('success', 'Alamat dihapus.');
    }

    public function makeDefault(int $id)
    {
        $model = new AddressModel();
        if (! $model->findOwned($id, $this->userId())) {
            return $this->notFound();
        }
        $model->setDefault($id, $this->userId());

        return redirect()->back()->with('success', 'Alamat utama diperbarui.');
    }

    private function payload(): array
    {
        return [
            'recipient_name' => trim((string) $this->request->getPost('recipient_name')),
            'phone'          => trim((string) $this->request->getPost('phone')),
            'address'        => trim((string) $this->request->getPost('address')),
            'city'           => trim((string) $this->request->getPost('city')),
            'province'       => trim((string) $this->request->getPost('province')),
            'postal_code'    => trim((string) $this->request->getPost('postal_code')),
        ];
    }
}
