<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table         = 'categories';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['name', 'slug', 'description', 'is_active'];
    protected $useTimestamps = true;

    /** Kategori + jumlah produk yang terhubung. */
    public function withProductCount()
    {
        return $this->select('categories.*, COUNT(products.id) AS product_count')
            ->join('products', 'products.category_id = categories.id', 'left')
            ->groupBy('categories.id')
            ->orderBy('categories.name', 'ASC');
    }

    /** Kategori aktif yang punya produk aktif (untuk homepage / filter katalog). */
    public function activeWithActiveProducts(): array
    {
        return $this->select('categories.*, COUNT(products.id) AS product_count')
            ->join('products', "products.category_id = categories.id AND products.status = 'active'", 'left')
            ->where('categories.is_active', 1)
            ->groupBy('categories.id')
            ->orderBy('categories.name', 'ASC')
            ->findAll();
    }

    /** Buat slug unik dari nama. */
    public function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        return unique_slug($this, $name, $ignoreId);
    }
}
