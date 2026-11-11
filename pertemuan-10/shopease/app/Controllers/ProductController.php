<?php

namespace App\Controllers;

use App\Libraries\Paginator;
use App\Models\CategoryModel;
use App\Models\ProductModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use Config\Shop;

/**
 * Katalog dan detail produk (FR-008, FR-009, FR-010, FR-020).
 */
class ProductController extends BaseController
{
    private const SORT_LABELS = [
        'newest'     => 'Terbaru',
        'price_asc'  => 'Harga terendah',
        'price_desc' => 'Harga tertinggi',
        'name'       => 'Nama A-Z',
    ];

    public function index(): string
    {
        $categories = model(CategoryModel::class);
        $products   = model(ProductModel::class);

        $keyword  = trim((string) $this->request->getGet('q'));
        $category = (string) $this->request->getGet('category');
        $sort     = (string) $this->request->getGet('sort');
        $sort     = array_key_exists($sort, self::SORT_LABELS) ? $sort : 'newest';

        $activeCategory = $category !== '' ? $categories->findActiveBySlug($category) : null;

        $page = Paginator::paginate(
            $products->catalog($keyword, $activeCategory['slug'] ?? null, $sort),
            config(Shop::class)->perPage,
        );

        return view('frontend/products/index', [
            'title'          => $activeCategory['name'] ?? 'Katalog Produk',
            'products'       => $page['items'],
            'total'          => $page['total'],
            'pagerLinks'     => $page['links'],
            'categories'     => $categories->activeWithProductCount(),
            'activeCategory' => $activeCategory,
            'keyword'        => $keyword,
            'sort'           => $sort,
            'sorts'          => self::SORT_LABELS,
        ]);
    }

    public function category(string $slug): RedirectResponse
    {
        if (model(CategoryModel::class)->findActiveBySlug($slug) === null) {
            throw PageNotFoundException::forPageNotFound('Kategori tidak ditemukan.');
        }

        return redirect()->to(url_to('products') . '?category=' . rawurlencode($slug));
    }

    public function show(string $slug): string
    {
        $products = model(ProductModel::class);
        $product  = $products->findActiveBySlug($slug);

        if ($product === null) {
            throw PageNotFoundException::forPageNotFound('Produk tidak ditemukan atau sudah tidak dijual.');
        }

        return view('frontend/products/show', [
            'title'   => $product['name'],
            'product' => $product,
            'related' => $products->related($product),
        ]);
    }
}
