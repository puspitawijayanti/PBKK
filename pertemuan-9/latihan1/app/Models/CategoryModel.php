<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table         = 'categories';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['name'];
    protected $useTimestamps = true;

    protected $validationRules = [
        'id'   => 'permit_empty|is_natural_no_zero',
        'name' => 'required|min_length[3]|max_length[80]|is_unique[categories.name,id,{id}]',
    ];

    protected $validationMessages = [
        'name' => [
            'required'   => 'Nama kategori wajib diisi.',
            'min_length' => 'Nama kategori minimal 3 karakter.',
            'max_length' => 'Nama kategori maksimal 80 karakter.',
            'is_unique'  => 'Nama kategori sudah ada.',
        ],
    ];

    public function withToolCount(): static
    {
        return $this->select('categories.*, COUNT(tools.id) AS tool_count')
            ->join('tools', 'tools.category_id = categories.id', 'left')
            ->groupBy('categories.id');
    }

    public function options(): array
    {
        return array_column($this->orderBy('name')->findAll(), 'name', 'id');
    }
}
