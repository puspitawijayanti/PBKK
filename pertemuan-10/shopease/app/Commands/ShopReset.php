<?php

namespace App\Commands;

use App\Data\DemoData;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Mengembalikan data aplikasi ke data demo awal: `php spark shop:reset`.
 */
class ShopReset extends BaseCommand
{
    protected $group       = 'ShopEase';
    protected $name        = 'shop:reset';
    protected $description = 'Menghapus file data JSON dan mengisi ulang dengan data demo (produk, akun, pesanan contoh).';
    protected $usage       = 'shop:reset [--empty]';
    protected $options     = ['--empty' => 'Hanya katalog dan akun, tanpa pesanan contoh.'];

    public function run(array $params)
    {
        foreach (glob(WRITEPATH . 'uploads/payments/contoh-bukti-*.png') ?: [] as $file) {
            unlink($file);
        }

        $store = service('store');
        $store->reset(! CLI::getOption('empty'));

        CLI::write('Data demo dipasang di ' . $store->path(), 'green');
        CLI::newLine();
        CLI::table(array_map(static fn (array $a): array => [$a[3], $a[1], $a[2]], DemoData::ACCOUNTS), ['Role', 'Email', 'Password']);
    }
}
