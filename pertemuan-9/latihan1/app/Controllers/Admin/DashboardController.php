<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\LoanModel;
use App\Models\MessageModel;
use App\Models\ToolModel;
use App\Models\UserModel;

class DashboardController extends BaseController
{
    public function index(): string
    {
        $loans = model(LoanModel::class);
        $counts = $loans->countByStatus();
        $monthly = $loans->monthlyTotals(6);
        $categories = model(CategoryModel::class)->withToolCount()->orderBy('tool_count', 'DESC')->findAll();

        return view('admin/dashboard', [
            'title'  => 'Dashboard',
            'stats'  => [
                'tools'     => model(ToolModel::class)->countAllResults(),
                'units'     => (int) db_connect()->table('tools')->selectSum('stock')->get()->getRow()->stock,
                'borrowers' => model(UserModel::class)->where('role', 'borrower')->countAllResults(),
                'pending'   => $counts['pending'],
                'active'    => $counts['approved'] + $counts['borrowed'],
                'messages'  => model(MessageModel::class)->countAllResults(),
            ],
            'counts'        => $counts,
            'monthlyLabels' => array_map('month_label', array_keys($monthly)),
            'monthlyTotals' => array_values($monthly),
            'categoryNames' => array_column($categories, 'name'),
            'categoryTools' => array_map('intval', array_column($categories, 'tool_count')),
            'pendingLoans'  => $loans->withRelations()->where('loans.status', 'pending')->orderBy('loans.created_at')->findAll(5),
            'lowStockTools' => model(ToolModel::class)->withCategory()->where('tools.stock <=', 2)->orderBy('tools.stock')->findAll(5),
        ]);
    }
}
