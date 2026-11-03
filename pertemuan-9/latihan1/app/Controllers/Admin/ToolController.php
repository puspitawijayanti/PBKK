<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\LoanModel;
use App\Models\ToolModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\HTTP\RedirectResponse;

class ToolController extends BaseController
{
    private const PHOTO_DIRECTORY = FCPATH . 'uploads/tools/';

    private ToolModel $tools;

    public function __construct()
    {
        $this->tools = model(ToolModel::class);
    }

    public function index(): string
    {
        $keyword = trim((string) $this->request->getGet('q'));
        $categoryId = (int) $this->request->getGet('category');
        $query = $this->tools->search($keyword, $categoryId);

        return view('admin/tools/index', [
            'title'      => 'Data Alat',
            'tools'      => $query->paginate(10),
            'pager'      => $query->pager,
            'categories' => model(CategoryModel::class)->options(),
            'keyword'    => $keyword,
            'categoryId' => $categoryId,
        ]);
    }

    public function new(): string
    {
        return view('admin/tools/form', [
            'title'      => 'Tambah Alat',
            'tool'       => null,
            'categories' => model(CategoryModel::class)->options(),
        ]);
    }

    public function create(): RedirectResponse
    {
        $data = $this->collectInput();
        $data['slug'] = $this->tools->makeSlug($data['name']);

        $errors = $this->collectErrors($data);

        if ($errors !== []) {
            return $this->backWithErrors($errors);
        }

        $photoName = $this->storePhoto($this->request->getFile('photo'));

        if ($photoName !== null) {
            $data['photo'] = $photoName;
        }

        if (! $this->tools->insert($data)) {
            $this->deletePhoto($photoName);

            return $this->backWithErrors($this->tools->errors());
        }

        return redirect()->to('admin/tools')->with('success', 'Alat "' . $data['name'] . '" berhasil ditambahkan.');
    }

    public function edit(int $id): string
    {
        return view('admin/tools/form', [
            'title'      => 'Ubah Alat',
            'tool'       => $this->findTool($id),
            'categories' => model(CategoryModel::class)->options(),
        ]);
    }

    public function update(int $id): RedirectResponse
    {
        $tool = $this->findTool($id);
        $data = $this->collectInput();
        $data['id'] = $id;
        $data['slug'] = $data['name'] === $tool['name'] ? $tool['slug'] : $this->tools->makeSlug($data['name'], $id);

        $errors = $this->collectErrors($data);

        if ($errors !== []) {
            return $this->backWithErrors($errors);
        }

        $photoName = $this->storePhoto($this->request->getFile('photo'));
        $removePhoto = $this->request->getPost('remove_photo') === '1';

        if ($photoName !== null) {
            $data['photo'] = $photoName;
        } elseif ($removePhoto) {
            $data['photo'] = null;
        }

        if (! $this->tools->update($id, $data)) {
            $this->deletePhoto($photoName);

            return $this->backWithErrors($this->tools->errors());
        }

        if ($photoName !== null || $removePhoto) {
            $this->deletePhoto($tool['photo']);
        }

        return redirect()->to('admin/tools')->with('success', 'Alat "' . $data['name'] . '" berhasil diperbarui.');
    }

    public function delete(int $id): RedirectResponse
    {
        $tool = $this->findTool($id);

        if (model(LoanModel::class)->where('tool_id', $id)->countAllResults() > 0) {
            return redirect()->back()->with('error', 'Alat "' . $tool['name'] . '" tidak dapat dihapus karena memiliki riwayat peminjaman.');
        }

        $this->tools->delete($id);
        $this->deletePhoto($tool['photo']);

        return redirect()->to('admin/tools')->with('success', 'Alat "' . $tool['name'] . '" berhasil dihapus.');
    }

    private function findTool(int $id): array
    {
        $tool = $this->tools->find($id);

        if ($tool === null) {
            throw PageNotFoundException::forPageNotFound('Alat tidak ditemukan.');
        }

        return $tool;
    }

    private function collectInput(): array
    {
        return [
            'name'        => trim((string) $this->request->getPost('name')),
            'category_id' => $this->request->getPost('category_id'),
            'stock'       => $this->request->getPost('stock'),
            'condition'   => $this->request->getPost('condition'),
            'description' => trim((string) $this->request->getPost('description')),
        ];
    }

    private function collectErrors(array $data): array
    {
        $errors = [];

        $photoRules = [
            'photo' => [
                'rules'  => 'max_size[photo,2048]|is_image[photo]|mime_in[photo,image/jpeg,image/png,image/webp]|ext_in[photo,jpg,jpeg,png,webp]',
                'errors' => [
                    'is_image' => 'File foto harus berupa gambar.',
                    'mime_in'  => 'Foto harus berformat JPG, PNG, atau WEBP.',
                    'ext_in'   => 'Ekstensi foto harus jpg, jpeg, png, atau webp.',
                    'max_size' => 'Ukuran foto maksimal 2 MB.',
                ],
            ],
        ];

        $photo = $this->request->getFile('photo');
        $hasPhoto = $photo !== null && $photo->getError() !== UPLOAD_ERR_NO_FILE;

        if ($hasPhoto && ! $this->validate($photoRules)) {
            $errors = $this->validator->getErrors();
        }

        if (! $this->tools->validate($data)) {
            $errors += $this->tools->errors();
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
