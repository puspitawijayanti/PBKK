<?php

namespace App\Models;

class AddressModel extends JsonModel
{
    protected string $table = 'addresses';

    public const RULES = [
        'recipient_name' => ['label' => 'Nama penerima', 'rules' => 'required|min_length[3]|max_length[100]'],
        'phone'          => ['label' => 'Nomor HP', 'rules' => 'required|regex_match[/^(\+62|0)[0-9]{8,13}$/]', 'errors' => ['regex_match' => 'Nomor HP harus diawali 0 atau +62 dan berisi 9-14 digit.']],
        'address'        => ['label' => 'Alamat', 'rules' => 'required|min_length[10]|max_length[500]'],
        'city'           => ['label' => 'Kota/Kabupaten', 'rules' => 'required|max_length[80]'],
        'province'       => ['label' => 'Provinsi', 'rules' => 'required|max_length[80]'],
        'postal_code'    => ['label' => 'Kode pos', 'rules' => 'required|numeric|exact_length[5]'],
    ];

    public function forUser(int $userId): array
    {
        return self::sortBy($this->where(['user_id' => $userId]), [['is_default', 'desc'], ['id', 'desc']]);
    }

    public function findOwned(int $id, int $userId): ?array
    {
        $address = $this->find($id);

        return $address !== null && $address['user_id'] === $userId ? $address : null;
    }

    public static function format(array $address): string
    {
        return sprintf('%s, %s, %s %s', $address['address'], $address['city'], $address['province'], $address['postal_code']);
    }
}
