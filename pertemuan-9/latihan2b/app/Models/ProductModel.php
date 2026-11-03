<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    public const PROCESSES = ['washed', 'natural', 'honey', 'wet_hulled'];
    public const ROAST_LEVELS = ['light', 'medium', 'dark'];
    public const SORTS = [
        'newest'     => 'Terbaru',
        'price_asc'  => 'Harga terendah',
        'price_desc' => 'Harga tertinggi',
        'name'       => 'Nama A-Z',
    ];

    protected $table         = 'products';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['category_id', 'name', 'slug', 'origin', 'process', 'roast_level', 'weight_gram', 'price', 'stock', 'photo', 'description', 'is_active'];
    protected $useTimestamps = true;

    protected $validationRules = [
        'id'          => 'permit_empty|is_natural_no_zero',
        'category_id' => 'required|is_natural_no_zero|is_not_unique[categories.id]',
        'name'        => 'required|min_length[3]|max_length[120]',
        'slug'        => 'required|max_length[140]|is_unique[products.slug,id,{id}]',
        'origin'      => 'required|max_length[100]',
        'process'     => 'required|in_list[washed,natural,honey,wet_hulled]',
        'roast_level' => 'required|in_list[light,medium,dark]',
        'weight_gram' => 'required|is_natural_no_zero|less_than_equal_to[5000]',
        'price'       => 'required|is_natural_no_zero|less_than_equal_to[10000000]',
        'stock'       => 'required|is_natural|less_than_equal_to[100000]',
        'description' => 'permit_empty|max_length[3000]',
        'is_active'   => 'required|in_list[0,1]',
    ];

    protected $validationMessages = [
        'category_id' => [
            'required'           => 'Kategori wajib dipilih.',
            'is_natural_no_zero' => 'Kategori wajib dipilih.',
            'is_not_unique'      => 'Kategori tidak ditemukan.',
        ],
        'name' => [
            'required'   => 'Nama produk wajib diisi.',
            'min_length' => 'Nama produk minimal 3 karakter.',
            'max_length' => 'Nama produk maksimal 120 karakter.',
        ],
        'slug' => [
            'is_unique' => 'Slug produk sudah dipakai.',
        ],
        'origin' => [
            'required' => 'Asal daerah wajib diisi.',
        ],
        'process' => [
            'in_list' => 'Proses pascapanen tidak valid.',
        ],
        'roast_level' => [
            'in_list' => 'Tingkat sangrai tidak valid.',
        ],
        'weight_gram' => [
            'required'           => 'Berat wajib diisi.',
            'is_natural_no_zero' => 'Berat minimal 1 gram.',
            'less_than_equal_to' => 'Berat maksimal 5000 gram.',
        ],
        'price' => [
            'required'           => 'Harga wajib diisi.',
            'is_natural_no_zero' => 'Harga harus lebih dari 0.',
            'less_than_equal_to' => 'Harga terlalu besar.',
        ],
        'stock' => [
            'required'   => 'Stok wajib diisi.',
            'is_natural' => 'Stok harus berupa bilangan bulat tidak negatif.',
        ],
        'description' => [
            'max_length' => 'Deskripsi maksimal 3000 karakter.',
        ],
    ];

    public function withCategory(): static
    {
        return $this->select('products.*, categories.name AS category_name, categories.slug AS category_slug')
            ->join('categories', 'categories.id = products.category_id');
    }

    public function catalog(?string $keyword, ?int $categoryId, string $sort, bool $activeOnly = true): static
    {
        $this->withCategory();

        if ($activeOnly) {
            $this->where('products.is_active', 1);
        }

        if ($keyword !== null && $keyword !== '') {
            $this->groupStart()
                ->like('products.name', $keyword)
                ->orLike('products.origin', $keyword)
                ->orLike('products.description', $keyword)
                ->groupEnd();
        }

        if ($categoryId !== null && $categoryId > 0) {
            $this->where('products.category_id', $categoryId);
        }

        return match ($sort) {
            'price_asc'  => $this->orderBy('products.price', 'ASC')->orderBy('products.name'),
            'price_desc' => $this->orderBy('products.price', 'DESC')->orderBy('products.name'),
            'name'       => $this->orderBy('products.name'),
            default      => $this->orderBy('products.created_at', 'DESC')->orderBy('products.id', 'DESC'),
        };
    }

    public function findActiveBySlug(string $slug): ?array
    {
        return $this->withCategory()->where('products.slug', $slug)->where('products.is_active', 1)->first();
    }

    public function makeSlug(string $name, ?int $ignoreId = null): string
    {
        $base = url_title($name, '-', true) ?: 'produk';
        $slug = $base;
        $suffix = 2;

        while ($this->slugTaken($slug, $ignoreId)) {
            $slug = $base . '-' . $suffix++;
        }

        return $slug;
    }

    private function slugTaken(string $slug, ?int $ignoreId): bool
    {
        $builder = $this->db->table($this->table)->where('slug', $slug);

        if ($ignoreId !== null) {
            $builder->where('id !=', $ignoreId);
        }

        return $builder->countAllResults() > 0;
    }
}
