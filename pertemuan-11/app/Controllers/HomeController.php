<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductModel;

class HomeController extends BaseController
{
    public function index()
    {
        $productModel = new ProductModel();

        $data = [
            'title'      => 'ShopEase Online Store',
            'categories' => (new CategoryModel())->activeWithActiveProducts(),
            'latest'     => $productModel->catalog(['sort' => 'newest'])->limit(8)->findAll(),
            'bestSeller' => $productModel->bestSellers(4),
        ];

        return view('frontend/home', $data);
    }

    public function about()
    {
        return view('frontend/about', ['title' => 'Tentang Kami']);
    }

    public function contact()
    {
        return view('frontend/contact', ['title' => 'Kontak']);
    }
}
