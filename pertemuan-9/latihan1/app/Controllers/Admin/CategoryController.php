<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\ToolModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class CategoryController extends BaseController
{
    private CategoryModel $categories;

    public function __construct()
    {
        $this->categories = model(CategoryModel::class);
    }

    public function index(): string
    {
        $keyword = trim((string) $this->request->getGet('q'));
        $query = $this->categories->withToolCount()->orderBy('categories.name');

        if ($keyword !== '') {
            $query->like('categories.name', $keyword);
        }

        return view('admin/categories/index', [
            'title'      => 'Kategori Alat',
            'categories' => $query->paginate(10),
            'pager'      => $query->pager,
            'keyword'    => $keyword,
        ]);
    }

    public function new(): string
    {
        return view('admin/categories/form', ['title' => 'Tambah Kategori', 'category' => null]);
    }

    public function create(): RedirectResponse
    {
        $data = ['name' => trim((string) $this->request->getPost('name'))];

        if (! $this->categories->insert($data)) {
            return $this->backWithErrors($this->categories->errors());
        }

        return redirect()->to('admin/categories')->with('success', 'Kategori "' . $data['name'] . '" berhasil ditambahkan.');
    }

    public function edit(int $id): string
    {
        return view('admin/categories/form', ['title' => 'Ubah Kategori', 'category' => $this->findCategory($id)]);
    }

    public function update(int $id): RedirectResponse
    {
        $this->findCategory($id);
        $data = ['id' => $id, 'name' => trim((string) $this->request->getPost('name'))];

        if (! $this->categories->save($data)) {
            return $this->backWithErrors($this->categories->errors());
        }

        return redirect()->to('admin/categories')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function delete(int $id): RedirectResponse
    {
        $category = $this->findCategory($id);

        if (model(ToolModel::class)->where('category_id', $id)->countAllResults() > 0) {
            return redirect()->back()->with('error', 'Kategori "' . $category['name'] . '" masih dipakai oleh data alat sehingga tidak dapat dihapus.');
        }

        $this->categories->delete($id);

        return redirect()->to('admin/categories')->with('success', 'Kategori "' . $category['name'] . '" berhasil dihapus.');
    }

    private function findCategory(int $id): array
    {
        $category = $this->categories->find($id);

        if ($category === null) {
            throw PageNotFoundException::forPageNotFound('Kategori tidak ditemukan.');
        }

        return $category;
    }
}
