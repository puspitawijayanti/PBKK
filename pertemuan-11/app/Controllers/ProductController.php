<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductModel;

class ProductController extends BaseController
{
    /** Katalog: pencarian, filter kategori, urut harga, pagination. */
    public function catalog()
    {
        $filters = [
            'q'        => trim((string) $this->request->getGet('q')),
            'category' => trim((string) $this->request->getGet('category')),
            'sort'     => (string) $this->request->getGet('sort'),
        ];

        $model    = new ProductModel();
        $products = $model->catalog($filters)->paginate(config('Shop')->catalogPerPage);

        return view('frontend/catalog', [
            'title'      => 'Katalog Produk',
            'products'   => $products,
            'pager'      => $model->pager,
            'categories' => (new CategoryModel())->activeWithActiveProducts(),
            'filters'    => $filters,
            'total'      => $model->pager->getTotal(),
        ]);
    }

    public function detail(string $key)
    {
        $model   = new ProductModel();
        $product = $model->findActiveBySlugOrId($key);

        if (! $product) {
            return $this->notFound('Produk tidak ditemukan atau tidak aktif.');
        }

        $related = (new ProductModel())->catalog(['category' => $product['category_slug']])
            ->where('products.id !=', $product['id'])
            ->limit(4)->findAll();

        return view('frontend/product_detail', [
            'title'   => $product['name'],
            'product' => $product,
            'related' => $related,
        ]);
    }
}
