<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;

class CategoryController extends BaseController
{
    private function rules(?int $ignoreId = null): array
    {
        return [
            'name'        => [
                'label' => 'Nama kategori',
                'rules' => 'required|min_length[3]|max_length[100]|is_unique[categories.name' . ($ignoreId ? ',id,' . $ignoreId : '') . ']',
            ],
            'description' => ['label' => 'Deskripsi', 'rules' => 'permit_empty|max_length[500]'],
        ];
    }

    public function index()
    {
        $model = new CategoryModel();

        return view('admin/categories/index', [
            'title'      => 'Kategori',
            'categories' => $model->withProductCount()->paginate(config('Shop')->adminPerPage),
            'pager'      => $model->pager,
        ]);
    }

    public function create()
    {
        return view('admin/categories/form', ['title' => 'Tambah Kategori', 'category' => null]);
    }

    public function store()
    {
        if (! $this->validate($this->rules())) {
            return $this->backWithErrors();
        }

        $model = new CategoryModel();
        $name  = trim((string) $this->request->getPost('name'));
        $model->insert([
            'name'        => $name,
            'slug'        => $model->uniqueSlug($name),
            'description' => trim((string) $this->request->getPost('description')) ?: null,
            'is_active'   => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to('/admin/categories')->with('success', 'Kategori "' . $name . '" berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $category = (new CategoryModel())->find($id);
        if (! $category) {
            return $this->notFound();
        }

        return view('admin/categories/form', ['title' => 'Ubah Kategori', 'category' => $category]);
    }

    public function update(int $id)
    {
        $model    = new CategoryModel();
        $category = $model->find($id);
        if (! $category) {
            return $this->notFound();
        }
        if (! $this->validate($this->rules($id))) {
            return $this->backWithErrors();
        }

        $name = trim((string) $this->request->getPost('name'));
        $model->update($id, [
            'name'        => $name,
            'slug'        => $name !== $category['name'] ? $model->uniqueSlug($name, $id) : $category['slug'],
            'description' => trim((string) $this->request->getPost('description')) ?: null,
            'is_active'   => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to('/admin/categories')->with('success', 'Kategori diperbarui.');
    }

    public function delete(int $id)
    {
        $model    = new CategoryModel();
        $category = $model->find($id);
        if (! $category) {
            return $this->notFound();
        }

        // aturan relasi: kategori yang masih memiliki produk tidak boleh dihapus
        $used = $model->db->table('products')->where('category_id', $id)->countAllResults();
        if ($used > 0) {
            return redirect()->to('/admin/categories')
                ->with('error', "Kategori \"{$category['name']}\" tidak dapat dihapus karena masih memiliki {$used} produk. Pindahkan/hapus produknya atau nonaktifkan kategori.");
        }

        $model->delete($id);

        return redirect()->to('/admin/categories')->with('success', 'Kategori dihapus.');
    }
}
