<?php

namespace Tests\Support;

use App\Models\ProductModel;
use App\Models\UserModel;
use CodeIgniter\Config\Services;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Filters;
use Config\Shop;

/**
 * Basis pengujian. Setiap test memakai salinan baru data demo di file JSON terpisah
 * (bukan writable/data/shopease.json milik aplikasi), sehingga perubahan stok atau status
 * pada satu test tidak memengaruhi test lain.
 */
abstract class ShopTestCase extends CIUnitTestCase
{
    use FeatureTestTrait;

    private const TEST_DIR = WRITEPATH . 'data/tests/';

    protected function setUp(): void
    {
        parent::setUp();

        // Data demo dibuat sekali (hash password cukup lambat), lalu disalin untuk setiap test.
        $template = self::TEST_DIR . 'template.json';

        if (! is_file($template)) {
            $this->useDataFile($template)->reset();
        }

        copy($template, self::TEST_DIR . 'current.json');
        $this->useDataFile(self::TEST_DIR . 'current.json');

        // Token CSRF tidak dapat dibuat dari dalam test; filter lain (auth, role) tetap aktif.
        $filters                    = config(Filters::class);
        $filters->globals['before'] = array_values(array_diff($filters->globals['before'], ['csrf']));
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        foreach (glob(self::TEST_DIR . '*') ?: [] as $file) {
            unlink($file);
        }
    }

    private function useDataFile(string $path): \App\Libraries\JsonStore
    {
        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        config(Shop::class)->dataFile = $path;
        Services::resetSingle('store');

        return service('store');
    }

    /**
     * Data session pengguna demo, misalnya `$this->actingAs('budi@example.test')`.
     */
    protected function actingAs(string $email, array $extraSession = []): static
    {
        $user = model(UserModel::class)->findByEmail($email);

        return $this->withSession([
            'user_id'   => $user['id'],
            'user_name' => $user['name'],
            'user_role' => $user['role'],
        ] + $extraSession);
    }

    protected function productBySlug(string $slug): array
    {
        return model(ProductModel::class)->firstWhere(['slug' => $slug]);
    }

    /**
     * Memastikan ada baris di tabel JSON yang cocok dengan semua kondisi (pengganti seeInDatabase).
     */
    protected function seeInStore(string $table, array $conditions): void
    {
        foreach (service('store')->all($table) as $row) {
            if (array_intersect_key($row, $conditions) == $conditions) {
                $this->addToAssertionCount(1);

                return;
            }
        }

        $this->fail("Tidak ada data {$table} yang cocok dengan " . json_encode($conditions));
    }
}
