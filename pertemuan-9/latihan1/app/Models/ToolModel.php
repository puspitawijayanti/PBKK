<?php

namespace App\Models;

use CodeIgniter\Model;

class ToolModel extends Model
{
    public const CONDITIONS = ['good', 'fair', 'damaged'];

    protected $table         = 'tools';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['name', 'slug', 'category_id', 'stock', 'condition', 'photo', 'description'];
    protected $useTimestamps = true;

    protected $validationRules = [
        'id'          => 'permit_empty|is_natural_no_zero',
        'name'        => 'required|min_length[3]|max_length[120]',
        'slug'        => 'required|max_length[140]|is_unique[tools.slug,id,{id}]',
        'category_id' => 'required|is_natural_no_zero|is_not_unique[categories.id]',
        'stock'       => 'required|is_natural|less_than_equal_to[1000]',
        'condition'   => 'required|in_list[good,fair,damaged]',
        'description' => 'permit_empty|max_length[2000]',
    ];

    protected $validationMessages = [
        'name' => [
            'required'   => 'Nama alat wajib diisi.',
            'min_length' => 'Nama alat minimal 3 karakter.',
            'max_length' => 'Nama alat maksimal 120 karakter.',
        ],
        'slug' => [
            'is_unique' => 'Slug alat sudah dipakai.',
        ],
        'category_id' => [
            'required'           => 'Kategori wajib dipilih.',
            'is_natural_no_zero' => 'Kategori wajib dipilih.',
            'is_not_unique'      => 'Kategori tidak ditemukan.',
        ],
        'stock' => [
            'required'              => 'Stok wajib diisi.',
            'is_natural'            => 'Stok harus berupa bilangan bulat tidak negatif.',
            'less_than_equal_to'    => 'Stok maksimal 1000 unit.',
        ],
        'condition' => [
            'required' => 'Kondisi wajib dipilih.',
            'in_list'  => 'Kondisi tidak valid.',
        ],
        'description' => [
            'max_length' => 'Deskripsi maksimal 2000 karakter.',
        ],
    ];

    public function withCategory(): static
    {
        return $this->select('tools.*, categories.name AS category_name')
            ->join('categories', 'categories.id = tools.category_id');
    }

    public function search(?string $keyword, ?int $categoryId): static
    {
        $this->withCategory();

        if ($keyword !== null && $keyword !== '') {
            $this->groupStart()
                ->like('tools.name', $keyword)
                ->orLike('tools.description', $keyword)
                ->groupEnd();
        }

        if ($categoryId !== null && $categoryId > 0) {
            $this->where('tools.category_id', $categoryId);
        }

        return $this->orderBy('tools.name');
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->withCategory()->where('tools.slug', $slug)->first();
    }

    public function makeSlug(string $name, ?int $ignoreId = null): string
    {
        $base = url_title($name, '-', true) ?: 'alat';
        $slug = $base;
        $suffix = 2;

        while ($this->slugExists($slug, $ignoreId)) {
            $slug = $base . '-' . $suffix++;
        }

        return $slug;
    }

    private function slugExists(string $slug, ?int $ignoreId): bool
    {
        $builder = $this->db->table($this->table)->where('slug', $slug);

        if ($ignoreId !== null) {
            $builder->where('id !=', $ignoreId);
        }

        return $builder->countAllResults() > 0;
    }

    public function decreaseStock(int $toolId, int $quantity): bool
    {
        $this->db->table($this->table)
            ->set('stock', 'stock - ' . $quantity, false)
            ->set('updated_at', date('Y-m-d H:i:s'))
            ->where('id', $toolId)
            ->where('stock >=', $quantity)
            ->update();

        return $this->db->affectedRows() === 1;
    }

    public function increaseStock(int $toolId, int $quantity): bool
    {
        $this->db->table($this->table)
            ->set('stock', 'stock + ' . $quantity, false)
            ->set('updated_at', date('Y-m-d H:i:s'))
            ->where('id', $toolId)
            ->update();

        return $this->db->affectedRows() === 1;
    }
}
