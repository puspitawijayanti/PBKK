<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table         = 'categories';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['name', 'slug', 'description'];
    protected $useTimestamps = true;

    protected $validationRules = [
        'id'          => 'permit_empty|is_natural_no_zero',
        'name'        => 'required|min_length[3]|max_length[80]|is_unique[categories.name,id,{id}]',
        'slug'        => 'required|max_length[100]|is_unique[categories.slug,id,{id}]',
        'description' => 'permit_empty|max_length[255]',
    ];

    protected $validationMessages = [
        'name' => [
            'required'   => 'Nama kategori wajib diisi.',
            'min_length' => 'Nama kategori minimal 3 karakter.',
            'max_length' => 'Nama kategori maksimal 80 karakter.',
            'is_unique'  => 'Nama kategori sudah ada.',
        ],
        'slug' => [
            'is_unique' => 'Slug kategori sudah dipakai.',
        ],
        'description' => [
            'max_length' => 'Deskripsi maksimal 255 karakter.',
        ],
    ];

    public function withProductCount(): static
    {
        return $this->select('categories.*, COUNT(products.id) AS product_count')
            ->join('products', 'products.category_id = categories.id AND products.is_active = 1', 'left')
            ->groupBy('categories.id');
    }

    public function options(): array
    {
        return array_column($this->orderBy('name')->findAll(), 'name', 'id');
    }
}
