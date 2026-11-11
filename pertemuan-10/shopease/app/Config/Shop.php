<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Identitas dan aturan toko ShopEase.
 */
class Shop extends BaseConfig
{
    public string $name    = 'ShopEase';
    public string $tagline = 'Belanja mudah, pesanan terpantau.';
    public string $email   = 'halo@shopease.test';
    public string $phone   = '0812-0000-0000';
    public string $address = 'Jl. Contoh No. 1, Surabaya';

    /** Jumlah produk per halaman katalog dan baris per halaman tabel admin (FR-020). */
    public int $perPage      = 8;
    public int $adminPerPage = 10;

    /** Ongkos kirim tetap (PRD 20: ongkir nilai tetap). */
    public int $flatShippingCost = 15000;

    /** Rekening tujuan pembayaran manual (data contoh). */
    public array $bankAccounts = [
        ['bank' => 'BCA', 'number' => '123 456 7890', 'holder' => 'PT ShopEase Contoh'],
        ['bank' => 'Mandiri', 'number' => '137 000 123 4567', 'holder' => 'PT ShopEase Contoh'],
    ];

    /** Lokasi file data JSON (pengganti database). Dapat diubah lewat .env: shop.dataFile */
    public string $dataFile = WRITEPATH . 'data/shopease.json';

    /** Batas ukuran file unggahan dalam kilobyte. */
    public int $maxProofSizeKb   = 2048;
    public int $maxProductSizeKb = 2048;
}
