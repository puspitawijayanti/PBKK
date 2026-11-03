<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\OrderItemModel;
use App\Models\ProductModel;
use Config\Shop;

class HomeController extends BaseController
{
    public function index(): string
    {
        return view('home/index', [
            'title'       => 'Beranda',
            'shop'        => config(Shop::class),
            'newProducts' => model(ProductModel::class)->catalog(null, null, 'newest')->findAll(8),
            'categories'  => model(CategoryModel::class)->withProductCount()->orderBy('product_count', 'DESC')->findAll(6),
            'bestSellers' => model(OrderItemModel::class)->bestSellers(3),
        ]);
    }
}
