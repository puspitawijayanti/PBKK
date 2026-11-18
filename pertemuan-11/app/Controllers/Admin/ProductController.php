<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\FileUploader;
use App\Models\CategoryModel;
use App\Models\ProductModel;

class ProductController extends BaseController
{
    private function rules(bool $hasFile): array
    {
        $cfg   = config('Shop');
        $rules = [
            'name'        => ['label' => 'Nama produk', 'rules' => 'required|min_length[3]|max_length[150]'],
            'category_id' => ['label' => 'Kategori', 'rules' => 'required|is_natural_no_zero|is_not_unique[categories.id]'],
            'description' => ['label' => 'Deskripsi', 'rules' => 'permit_empty|max_length[5000]'],
            'price'       => ['label' => 'Harga', 'rules' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[9999999999]'],
            'stock'       => ['label' => 'Stok', 'rules' => 'required|is_natural'], // bilangan bulat >= 0
            'status'      => ['label' => 'Status', 'rules' => 'required|in_list[active,inactive]'],
        ];

        if ($hasFile) {
            $rules['image'] = [
                'label' => 'Foto produk',
                'rules' => 'uploaded[image]|is_image[image]'
                    . '|mime_in[image,' . implode(',', $cfg->imageMimes) . ']'
                    . '|ext_in[image,' . implode(',', $cfg->imageExts) . ']'
                    . '|max_size[image,' . $cfg->maxUploadKb . ']',
            ];
        }

        return $rules;
    }

    private function hasUpload(): bool
    {
        $file = $this->request->getFile('image');

        return $file !== null && $file->getError() !== UPLOAD_ERR_NO_FILE;
    }

    public function index()
    {
        $filters = [
            'q'           => trim((string) $this->request->getGet('q')),
            'category_id' => (int) $this->request->getGet('category_id'),
            'status'      => (string) $this->request->getGet('status'),
        ];

        $model = new ProductModel();

        return view('admin/products/index', [
            'title'      => 'Produk',
            'products'   => $model->adminList($filters)->paginate(config('Shop')->adminPerPage),
            'pager'      => $model->pager,
            'categories' => (new CategoryModel())->orderBy('name')->findAll(),
            'filters'    => $filters,
        ]);
    }

    public function create()
    {
        return view('admin/products/form', [
            'title'      => 'Tambah Produk',
            'product'    => null,
            'categories' => (new CategoryModel())->orderBy('name')->findAll(),
        ]);
    }

    public function store()
    {
        if (! $this->validate($this->rules($this->hasUpload()))) {
            return $this->backWithErrors();
        }

        $model = new ProductModel();
        $name  = trim((string) $this->request->getPost('name'));
        $image = $this->hasUpload() ? FileUploader::saveProductImage($this->request->getFile('image')) : null;

        $model->insert($this->payload($name, $model->uniqueSlug($name)) + ['image' => $image]);

        return redirect()->to('/admin/products')->with('success', 'Produk "' . $name . '" berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $product = (new ProductModel())->find($id);
        if (! $product) {
            return $this->notFound();
        }

        return view('admin/products/form', [
            'title'      => 'Ubah Produk',
            'product'    => $product,
            'categories' => (new CategoryModel())->orderBy('name')->findAll(),
        ]);
    }

    public function update(int $id)
    {
        $model   = new ProductModel();
        $product = $model->find($id);
        if (! $product) {
            return $this->notFound();
        }
        if (! $this->validate($this->rules($this->hasUpload()))) {
            return $this->backWithErrors();
        }

        $name = trim((string) $this->request->getPost('name'));
        $slug = $name !== $product['name'] ? $model->uniqueSlug($name, $id) : $product['slug'];
        $data = $this->payload($name, $slug);

        if ($this->hasUpload()) {
            $data['image'] = FileUploader::saveProductImage($this->request->getFile('image'));
            FileUploader::deleteProductImage($product['image']);
        }

        $model->update($id, $data);

        return redirect()->to('/admin/products')->with('success', 'Produk diperbarui.');
    }

    /**
     * Hapus produk. Jika produk sudah pernah dipakai pada pesanan, produk TIDAK dihapus fisik
     * melainkan dinonaktifkan agar riwayat transaksi tetap utuh (BR-011 / aturan relasi).
     */
    public function delete(int $id)
    {
        $model   = new ProductModel();
        $product = $model->find($id);
        if (! $product) {
            return $this->notFound();
        }

        if ($model->isUsedInOrders($id)) {
            $model->update($id, ['status' => 'inactive']);

            return redirect()->to('/admin/products')
                ->with('success', 'Produk sudah pernah dipesan sehingga tidak dihapus, melainkan dinonaktifkan.');
        }

        $model->delete($id);
        FileUploader::deleteProductImage($product['image']);

        return redirect()->to('/admin/products')->with('success', 'Produk dihapus.');
    }

    private function payload(string $name, string $slug): array
    {
        return [
            'category_id' => (int) $this->request->getPost('category_id'),
            'name'        => $name,
            'slug'        => $slug,
            'description' => trim((string) $this->request->getPost('description')) ?: null,
            'price'       => round((float) $this->request->getPost('price'), 2),
            'stock'       => (int) $this->request->getPost('stock'),
            'status'      => $this->request->getPost('status'),
        ];
    }
}
