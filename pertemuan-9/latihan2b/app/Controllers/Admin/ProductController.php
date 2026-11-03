<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\OrderItemModel;
use App\Models\ProductModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\HTTP\RedirectResponse;

class ProductController extends BaseController
{
    private const PHOTO_DIRECTORY = FCPATH . 'uploads/products/';

    private ProductModel $products;

    public function __construct()
    {
        $this->products = model(ProductModel::class);
    }

    public function index(): string
    {
        $keyword = trim((string) $this->request->getGet('q'));
        $categoryId = (int) $this->request->getGet('category');
        $sort = (string) $this->request->getGet('sort');
        $query = $this->products->catalog($keyword, $categoryId, array_key_exists($sort, ProductModel::SORTS) ? $sort : 'name', false);

        return view('admin/products/index', [
            'title'      => 'Produk',
            'products'   => $query->paginate(10),
            'pager'      => $query->pager,
            'categories' => model(CategoryModel::class)->options(),
            'keyword'    => $keyword,
            'categoryId' => $categoryId,
            'sort'       => $sort,
        ]);
    }

    public function new(): string
    {
        return view('admin/products/form', [
            'title'      => 'Tambah Produk',
            'product'    => null,
            'categories' => model(CategoryModel::class)->options(),
        ]);
    }

    public function create(): RedirectResponse
    {
        $data = $this->collectInput();
        $data['slug'] = $this->products->makeSlug($data['name']);
        $errors = $this->collectErrors($data);

        if ($errors !== []) {
            return $this->backWithErrors($errors);
        }

        $photo = $this->storePhoto($this->request->getFile('photo'));

        if ($photo !== null) {
            $data['photo'] = $photo;
        }

        if (! $this->products->insert($data)) {
            $this->deletePhoto($photo);

            return $this->backWithErrors($this->products->errors());
        }

        return redirect()->to('admin/products')->with('success', 'Produk ' . $data['name'] . ' ditambahkan.');
    }

    public function edit(int $id): string
    {
        return view('admin/products/form', [
            'title'      => 'Ubah Produk',
            'product'    => $this->findProduct($id),
            'categories' => model(CategoryModel::class)->options(),
        ]);
    }

    public function update(int $id): RedirectResponse
    {
        $product = $this->findProduct($id);
        $data = $this->collectInput() + ['id' => $id];
        $data['slug'] = $data['name'] === $product['name'] ? $product['slug'] : $this->products->makeSlug($data['name'], $id);
        $errors = $this->collectErrors($data);

        if ($errors !== []) {
            return $this->backWithErrors($errors);
        }

        $photo = $this->storePhoto($this->request->getFile('photo'));
        $removePhoto = $this->request->getPost('remove_photo') === '1';

        if ($photo !== null) {
            $data['photo'] = $photo;
        } elseif ($removePhoto) {
            $data['photo'] = null;
        }

        if (! $this->products->update($id, $data)) {
            $this->deletePhoto($photo);

            return $this->backWithErrors($this->products->errors());
        }

        if ($photo !== null || $removePhoto) {
            $this->deletePhoto($product['photo']);
        }

        return redirect()->to('admin/products')->with('success', 'Produk ' . $data['name'] . ' diperbarui.');
    }

    public function delete(int $id): RedirectResponse
    {
        $product = $this->findProduct($id);

        if (model(OrderItemModel::class)->where('product_id', $id)->countAllResults() > 0) {
            $this->products->update($id, ['is_active' => 0]);

            return redirect()->back()->with('info', 'Produk ' . $product['name'] . ' sudah pernah dipesan, sehingga hanya dinonaktifkan agar riwayat pesanan tetap utuh.');
        }

        $this->products->delete($id);
        $this->deletePhoto($product['photo']);

        return redirect()->to('admin/products')->with('success', 'Produk ' . $product['name'] . ' dihapus.');
    }

    private function findProduct(int $id): array
    {
        $product = $this->products->find($id);

        if ($product === null) {
            throw PageNotFoundException::forPageNotFound('Produk tidak ditemukan.');
        }

        return $product;
    }

    private function collectInput(): array
    {
        return [
            'category_id' => $this->request->getPost('category_id'),
            'name'        => trim((string) $this->request->getPost('name')),
            'origin'      => trim((string) $this->request->getPost('origin')),
            'process'     => $this->request->getPost('process'),
            'roast_level' => $this->request->getPost('roast_level'),
            'weight_gram' => $this->request->getPost('weight_gram'),
            'price'       => $this->request->getPost('price'),
            'stock'       => $this->request->getPost('stock'),
            'description' => trim((string) $this->request->getPost('description')),
            'is_active'   => $this->request->getPost('is_active') === '1' ? '1' : '0',
        ];
    }

    private function collectErrors(array $data): array
    {
        $errors = [];
        $photo = $this->request->getFile('photo');

        if ($photo !== null && $photo->getError() !== UPLOAD_ERR_NO_FILE) {
            $rules = [
                'photo' => [
                    'rules'  => 'max_size[photo,2048]|is_image[photo]|mime_in[photo,image/jpeg,image/png,image/webp]|ext_in[photo,jpg,jpeg,png,webp]',
                    'errors' => [
                        'max_size' => 'Ukuran foto maksimal 2 MB.',
                        'is_image' => 'File foto harus berupa gambar.',
                        'mime_in'  => 'Foto harus berformat JPG, PNG, atau WEBP.',
                        'ext_in'   => 'Ekstensi foto harus jpg, jpeg, png, atau webp.',
                    ],
                ],
            ];

            if (! $this->validate($rules)) {
                $errors = $this->validator->getErrors();
            }
        }

        if (! $this->products->validate($data)) {
            $errors += $this->products->errors();
        }

        return $errors;
    }

    private function storePhoto(?UploadedFile $file): ?string
    {
        if ($file === null || ! $file->isValid() || $file->hasMoved()) {
            return null;
        }

        $name = $file->getRandomName();
        $file->move(self::PHOTO_DIRECTORY, $name);

        return $name;
    }

    private function deletePhoto(?string $name): void
    {
        if ($name !== null && $name !== '' && is_file(self::PHOTO_DIRECTORY . $name)) {
            unlink(self::PHOTO_DIRECTORY . $name);
        }
    }
}
