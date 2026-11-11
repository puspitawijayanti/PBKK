<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductModel;
use Config\Shop;

/**
 * Landing page ShopEase (PRD bagian 10: Homepage).
 */
class HomeController extends BaseController
{
    public function index(): string
    {
        $products = model(ProductModel::class);

        return view('frontend/home', [
            'title'      => 'Beranda',
            'shop'       => config(Shop::class),
            'categories' => model(CategoryModel::class)->activeWithProductCount(),
            'featured'   => $products->featured(8),
            'newest'     => $products->newest(4),
        ]);
    }
}
