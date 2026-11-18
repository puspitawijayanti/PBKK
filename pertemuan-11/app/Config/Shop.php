<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Konfigurasi khusus aplikasi ShopEase.
 * Ambil di mana saja dengan: config('Shop')
 */
class Shop extends BaseConfig
{
    public string $storeName = 'ShopEase';

    /** Ongkos kirim tetap (Rupiah). PRD: boleh nilai tetap, integrasi API ekspedisi ditunda. */
    public int $shippingCost = 15000;

    /** Jumlah item per halaman. */
    public int $catalogPerPage = 8;
    public int $adminPerPage   = 10;

    /** Instruksi pembayaran manual yang ditampilkan ke customer. */
    public array $bankAccount = [
        'bank'   => 'Bank Demo Nusantara',
        'number' => '1234-5678-90',
        'holder' => 'ShopEase Demo Store',
    ];

    /** Status pesanan yang dihitung sebagai penjualan (pembayaran sudah diverifikasi). */
    public array $salesStatuses = ['processing', 'shipped', 'completed'];

    /** Aturan upload gambar (produk & bukti pembayaran). */
    public int $maxUploadKb    = 2048;
    public array $imageMimes   = ['image/jpeg', 'image/png', 'image/webp'];
    public array $imageExts    = ['jpg', 'jpeg', 'png', 'webp'];

    /** Kurir yang tersedia pada form pengiriman. */
    public array $couriers = ['JNE', 'J&T Express', 'SiCepat', 'AnterAja', 'Pos Indonesia'];
}
