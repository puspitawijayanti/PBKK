<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\LoanModel;
use App\Models\ToolModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class ToolController extends BaseController
{
    public function index(): string
    {
        $keyword = trim((string) $this->request->getGet('q'));
        $categoryId = (int) $this->request->getGet('category');
        $tools = model(ToolModel::class)->search($keyword, $categoryId);

        return view('tools/index', [
            'title'      => 'Katalog Alat',
            'tools'      => $tools->paginate(9),
            'pager'      => $tools->pager,
            'categories' => model(CategoryModel::class)->options(),
            'keyword'    => $keyword,
            'categoryId' => $categoryId,
        ]);
    }

    public function show(string $slug): string
    {
        $tool = model(ToolModel::class)->findBySlug($slug);

        if ($tool === null) {
            throw PageNotFoundException::forPageNotFound('Alat dengan alamat "' . $slug . '" tidak ditemukan.');
        }

        return view('tools/show', [
            'title'        => $tool['name'],
            'tool'         => $tool,
            'activeLoans'  => model(LoanModel::class)->where('tool_id', $tool['id'])->whereIn('status', ['approved', 'borrowed'])->countAllResults(),
            'relatedTools' => model(ToolModel::class)->withCategory()
                ->where('tools.category_id', $tool['category_id'])
                ->where('tools.id !=', $tool['id'])
                ->findAll(3),
        ]);
    }
}
