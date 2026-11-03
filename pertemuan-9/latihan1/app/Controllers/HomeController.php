<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\LoanModel;
use App\Models\ToolModel;

class HomeController extends BaseController
{
    public function index(): string
    {
        $tools = model(ToolModel::class);

        return view('home/index', [
            'title'       => 'Beranda',
            'latestTools' => $tools->withCategory()->orderBy('tools.created_at', 'DESC')->findAll(6),
            'categories'  => model(CategoryModel::class)->withToolCount()->orderBy('tool_count', 'DESC')->findAll(8),
            'stats'       => [
                'tools'      => $tools->countAllResults(),
                'units'      => (int) db_connect()->table('tools')->selectSum('stock')->get()->getRow()->stock,
                'categories' => model(CategoryModel::class)->countAllResults(),
                'loans'      => model(LoanModel::class)->whereIn('status', ['returned', 'borrowed'])->countAllResults(),
            ],
        ]);
    }
}
