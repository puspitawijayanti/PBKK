<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Paginator;
use App\Models\CategoryModel;
use App\Models\ProductModel;
use App\Services\UploadService;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use Config\Shop;

/**
 * CRUD produk dan upload gambar (FR-006, FR-007), dengan pencarian, filter, dan pagination (FR-020).
 */
class ProductController extends BaseController
{
    public function index(): string
    {
        $keyword    = trim((string) $this->request->getGet('q'));
        $categoryId = (int) $this->request->getGet('category');
        $status     = (string) $this->request->getGet('status');
        $page       = Paginator::paginate(model(ProductModel::class)->adminSearch($keyword, $categoryId, $status), config(Shop::class)->adminPerPage);

        return view('admin/products/index', [
            'title'      => 'Produk',
            'products'   => $page['items'],
            'pagerLinks' => $page['links'],
            'categories' => model(CategoryModel::class)->sorted(),
            'keyword'    => $keyword,
            'categoryId' => $categoryId,
            'status'     => $status,
        ]);
    }

    public function new(): string
    {
        return $this->form('Tambah Produk', null);
    }

    public function create(): RedirectResponse
    {
        $this->prepareInput();

        if (! $this->validate(ProductModel::rules() + $this->imageRule())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->productData();
        $file = $this->request->getFile('image');

        $data['image'] = $file !== null && $file->isValid() ? UploadService::storeProductImage($file) : null;

        model(ProductModel::class)->insert($data);

        return redirect()->to(url_to('admin.products'))->with('success', 'Produk "' . $data['name'] . '" ditambahkan.');
    }

    public function edit(int $id): string
    {
        return $this->form('Ubah Produk', $this->findProduct($id));
    }

    public function update(int $id): RedirectResponse
    {
        $product = $this->findProduct($id);
        $this->prepareInput();

        if (! $this->validate(ProductModel::rules($id) + $this->imageRule())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->productData();
        $file = $this->request->getFile('image');

        if ($file !== null && $file->isValid()) {
            $data['image'] = UploadService::storeProductImage($file);
            UploadService::deleteProductImage($product['image']);
        } elseif ($this->request->getPost('remove_image')) {
            $data['image'] = null;
            UploadService::deleteProductImage($product['image']);
        }

        model(ProductModel::class)->update($id, $data);

        return redirect()->to(url_to('admin.products'))->with('success', 'Produk "' . $data['name'] . '" diperbarui.');
    }

    /**
     * Produk yang sudah pernah dipesan hanya dinonaktifkan agar riwayat transaksi tetap utuh.
     */
    public function delete(int $id): RedirectResponse
    {
        $product  = $this->findProduct($id);
        $products = model(ProductModel::class);

        if ($products->isOrdered($id)) {
            $products->update($id, ['status' => 'inactive']);

            return redirect()->back()->with('info', 'Produk "' . $product['name'] . '" sudah pernah dipesan, sehingga dinonaktifkan (tidak dihapus).');
        }

        $products->delete($id);
        UploadService::deleteProductImage($product['image']);

        return redirect()->back()->with('success', 'Produk "' . $product['name'] . '" dihapus.');
    }

    private function form(string $title, ?array $product): string
    {
        return view('admin/products/form', [
            'title'      => $title,
            'product'    => $product,
            'categories' => model(CategoryModel::class)->sorted(),
        ]);
    }

    private function imageRule(): array
    {
        $maxKb = config(Shop::class)->maxProductSizeKb;

        return ['image' => [
            'label'  => 'Gambar produk',
            'rules'  => 'permit_empty|max_size[image,' . $maxKb . ']|is_image[image]|mime_in[image,image/jpeg,image/png,image/webp]|ext_in[image,jpg,jpeg,png,webp]',
            'errors' => ['max_size' => 'Ukuran gambar maksimal ' . ($maxKb / 1024) . ' MB.', 'is_image' => 'File harus berupa gambar.', 'mime_in' => 'Format gambar: JPG, PNG, WEBP.', 'ext_in' => 'Format gambar: JPG, PNG, WEBP.'],
        ]];
    }

    private function prepareInput(): void
    {
        $post = $this->request->getPost();
        $post['slug']        = url_title((string) (($post['slug'] ?? '') !== '' ? $post['slug'] : ($post['name'] ?? '')), '-', true);
        $post['icon']        = ($post['icon'] ?? '') !== '' ? $post['icon'] : 'bi-box';
        $post['is_featured'] = ! empty($post['is_featured']) ? '1' : '0';
        $this->request->setGlobal('post', $post);
    }

    private function productData(): array
    {
        $data = $this->validator->getValidated();
        unset($data['image']);

        return array_merge($data, [
            'category_id' => (int) $data['category_id'],
            'price'       => (float) $data['price'],
            'stock'       => (int) $data['stock'],
            'is_featured' => $data['is_featured'] === '1',
        ]);
    }

    private function findProduct(int $id): array
    {
        return model(ProductModel::class)->find($id) ?? throw PageNotFoundException::forPageNotFound('Produk tidak ditemukan.');
    }
}
