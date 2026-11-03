<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Shop extends BaseConfig
{
    public string $storeName = 'Kopi Nusantara';
    public string $tagline = 'Biji kopi lokal pilihan dari petani Indonesia';
    public string $email = 'halo@kopinusantara.id';
    public string $phone = '0812-3456-7890';
    public string $address = 'Jl. Kopi Tubruk No. 12, Surabaya';
    public int $shippingCost = 15000;
    public int $freeShippingThreshold = 200000;
    public int $maxQuantityPerItem = 20;
    public int $paymentDeadlineHours = 24;

    public array $bankAccounts = [
        ['bank' => 'BCA', 'number' => '1234567890', 'holder' => 'PT Kopi Nusantara Sejahtera'],
        ['bank' => 'Mandiri', 'number' => '1400012345678', 'holder' => 'PT Kopi Nusantara Sejahtera'],
    ];

    public array $couriers = ['JNE', 'J&T Express', 'SiCepat', 'AnterAja', 'Pos Indonesia'];
}
