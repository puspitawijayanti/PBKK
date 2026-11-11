<?php

namespace Tests\Feature;

use App\Models\ProductModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Tests\Support\ShopTestCase;

/**
 * Halaman publik: landing page, katalog, detail produk (FR-008 s.d. FR-010).
 *
 * @internal
 */
final class LandingPageTest extends ShopTestCase
{
    public function testHomepageShowsLandingSections(): void
    {
        $result = $this->get('/');

        $result->assertOK();
        $result->assertSee('Belanja per kategori');
        $result->assertSee('Produk unggulan');
        $result->assertSee('Cara belanja di ShopEase');
    }

    public function testCatalogSearchFiltersByName(): void
    {
        $result = $this->get('products?q=botol');

        $result->assertOK();
        $result->assertSee('Botol Minum Stainless');
        $result->assertDontSee('Matras Yoga');
    }

    public function testCatalogFiltersByCategory(): void
    {
        $result = $this->get('products?category=olahraga');

        $result->assertOK();
        $result->assertSee('Matras Yoga');
        $result->assertDontSee('Tote Bag Kanvas');
    }

    public function testCategoryLinkRedirectsToFilteredCatalog(): void
    {
        $this->get('category/fashion')->assertRedirectTo(url_to('products') . '?category=fashion');
    }

    public function testInactiveProductIsHidden(): void
    {
        $this->get('products')->assertDontSee('Topi Baseball Polos');

        $this->expectException(PageNotFoundException::class);
        $this->get('products/topi-baseball-polos');
    }

    public function testProductDetailShowsPriceAndStock(): void
    {
        $result = $this->get('products/matras-yoga-6mm');

        $result->assertOK();
        $result->assertSee('Rp159.000');
        $result->assertSee('Stok tersedia');
    }

    public function testCatalogReadsProductsFromDataStore(): void
    {
        model(ProductModel::class)->insert([
            'category_id' => 1,
            'name'        => 'Speaker Mini Uji',
            'slug'        => 'speaker-mini-uji',
            'description' => null,
            'price'       => 120000.0,
            'stock'       => 5,
            'image'       => null,
            'icon'        => 'bi-speaker',
            'status'      => 'active',
            'is_featured' => false,
        ]);

        $this->get('products?q=speaker')->assertSee('Speaker Mini Uji');
        $this->get('products/speaker-mini-uji')->assertSee('Rp120.000');
    }
}
