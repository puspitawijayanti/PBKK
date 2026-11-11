<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Paginator;
use App\Models\CategoryModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use Config\Shop;

/**
 * CRUD kategori (FR-005).
 */
class CategoryController extends BaseController
{
    public function index(): string
    {
        $page = Paginator::paginate(model(CategoryModel::class)->withAllProductCount(), config(Shop::class)->adminPerPage);

        return view('admin/categories/index', [
            'title'      => 'Kategori',
            'categories' => $page['items'],
            'pagerLinks' => $page['links'],
        ]);
    }

    public function new(): string
    {
        return view('admin/categories/form', ['title' => 'Tambah Kategori', 'category' => null]);
    }

    public function create(): RedirectResponse
    {
        $this->prepareInput();

        if (! $this->validate(CategoryModel::rules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        model(CategoryModel::class)->insert($this->categoryData());

        return redirect()->to(url_to('admin.categories'))->with('success', 'Kategori ditambahkan.');
    }

    public function edit(int $id): string
    {
        return view('admin/categories/form', ['title' => 'Ubah Kategori', 'category' => $this->findCategory($id)]);
    }

    public function update(int $id): RedirectResponse
    {
        $this->findCategory($id);
        $this->prepareInput();

        if (! $this->validate(CategoryModel::rules($id))) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        model(CategoryModel::class)->update($id, $this->categoryData());

        return redirect()->to(url_to('admin.categories'))->with('success', 'Kategori diperbarui.');
    }

    /**
     * Kategori yang masih memiliki produk tidak boleh dihapus (aturan relasi FK); nonaktifkan saja.
     */
    public function delete(int $id): RedirectResponse
    {
        $category = $this->findCategory($id);

        if (model(CategoryModel::class)->hasProducts($id)) {
            return redirect()->back()->with('error', 'Kategori "' . $category['name'] . '" masih memiliki produk. Pindahkan produknya atau nonaktifkan kategori.');
        }

        model(CategoryModel::class)->delete($id);

        return redirect()->to(url_to('admin.categories'))->with('success', 'Kategori dihapus.');
    }

    private function prepareInput(): void
    {
        $post = $this->request->getPost();
        $post['slug'] = url_title((string) (($post['slug'] ?? '') !== '' ? $post['slug'] : ($post['name'] ?? '')), '-', true);
        $post['icon'] = ($post['icon'] ?? '') !== '' ? $post['icon'] : 'bi-tag';
        $this->request->setGlobal('post', $post);
    }

    private function categoryData(): array
    {
        $data              = $this->validator->getValidated();
        $data['is_active'] = $data['is_active'] === '1';

        return $data;
    }

    private function findCategory(int $id): array
    {
        return model(CategoryModel::class)->find($id) ?? throw PageNotFoundException::forPageNotFound('Kategori tidak ditemukan.');
    }
}
