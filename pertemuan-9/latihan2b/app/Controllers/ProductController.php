<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Config\Shop;

class ProductController extends BaseController
{
    public function index(): string
    {
        $keyword = trim((string) $this->request->getGet('q'));
        $categoryId = (int) $this->request->getGet('category');
        $sort = (string) $this->request->getGet('sort');
        $sort = array_key_exists($sort, ProductModel::SORTS) ? $sort : 'newest';
        $products = model(ProductModel::class)->catalog($keyword, $categoryId, $sort);

        return view('products/index', [
            'title'      => 'Katalog Kopi',
            'products'   => $products->paginate(12),
            'pager'      => $products->pager,
            'categories' => model(CategoryModel::class)->withProductCount()->orderBy('categories.name')->findAll(),
            'keyword'    => $keyword,
            'categoryId' => $categoryId,
            'sort'       => $sort,
        ]);
    }

    public function show(string $slug): string
    {
        $product = model(ProductModel::class)->findActiveBySlug($slug);

        if ($product === null) {
            throw PageNotFoundException::forPageNotFound('Produk "' . $slug . '" tidak ditemukan.');
        }

        return view('products/show', [
            'title'   => $product['name'],
            'product' => $product,
            'shop'    => config(Shop::class),
            'inCart'  => (int) ((session('cart') ?? [])[$product['id']] ?? 0),
            'related' => model(ProductModel::class)->catalog(null, (int) $product['category_id'], 'newest')
                ->where('products.id !=', $product['id'])
                ->findAll(4),
        ]);
    }
}
