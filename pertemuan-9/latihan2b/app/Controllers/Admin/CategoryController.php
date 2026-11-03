<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\ProductModel;
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
        $query = $this->categories
            ->select('categories.*, (SELECT COUNT(*) FROM products WHERE products.category_id = categories.id) AS product_count', false)
            ->orderBy('categories.name');

        return view('admin/categories/index', [
            'title'      => 'Kategori Produk',
            'categories' => $query->paginate(10),
            'pager'      => $query->pager,
        ]);
    }

    public function new(): string
    {
        return view('admin/categories/form', ['title' => 'Tambah Kategori', 'category' => null]);
    }

    public function create(): RedirectResponse
    {
        $data = $this->collectInput();

        if (! $this->categories->insert($data)) {
            return $this->backWithErrors($this->categories->errors());
        }

        return redirect()->to('admin/categories')->with('success', 'Kategori ' . $data['name'] . ' ditambahkan.');
    }

    public function edit(int $id): string
    {
        return view('admin/categories/form', ['title' => 'Ubah Kategori', 'category' => $this->findCategory($id)]);
    }

    public function update(int $id): RedirectResponse
    {
        $this->findCategory($id);
        $data = $this->collectInput() + ['id' => $id];

        if (! $this->categories->save($data)) {
            return $this->backWithErrors($this->categories->errors());
        }

        return redirect()->to('admin/categories')->with('success', 'Kategori ' . $data['name'] . ' diperbarui.');
    }

    public function delete(int $id): RedirectResponse
    {
        $category = $this->findCategory($id);

        if (model(ProductModel::class)->where('category_id', $id)->countAllResults() > 0) {
            return redirect()->back()->with('error', 'Kategori ' . $category['name'] . ' masih memiliki produk.');
        }

        $this->categories->delete($id);

        return redirect()->to('admin/categories')->with('success', 'Kategori ' . $category['name'] . ' dihapus.');
    }

    private function findCategory(int $id): array
    {
        $category = $this->categories->find($id);

        if ($category === null) {
            throw PageNotFoundException::forPageNotFound('Kategori tidak ditemukan.');
        }

        return $category;
    }

    private function collectInput(): array
    {
        $name = trim((string) $this->request->getPost('name'));

        return [
            'name'        => $name,
            'slug'        => url_title($name, '-', true),
            'description' => trim((string) $this->request->getPost('description')),
        ];
    }
}
