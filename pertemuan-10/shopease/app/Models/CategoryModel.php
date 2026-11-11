<?php

namespace App\Models;

class CategoryModel extends JsonModel
{
    protected string $table = 'categories';

    /**
     * Kategori aktif beserta jumlah produk aktif di dalamnya.
     */
    public function activeWithProductCount(): array
    {
        $counts = array_count_values(array_column(model(ProductModel::class)->where(['status' => 'active']), 'category_id'));
        $active = array_filter($this->all(), static fn (array $c): bool => $c['is_active']);

        return array_values(array_map(static fn (array $c): array => $c + ['product_count' => $counts[$c['id']] ?? 0], $active));
    }

    /**
     * Semua kategori (termasuk nonaktif) beserta jumlah seluruh produknya, untuk halaman admin.
     */
    public function withAllProductCount(): array
    {
        $counts = array_count_values(array_column(model(ProductModel::class)->all(), 'category_id'));

        return array_map(static fn (array $c): array => $c + ['product_count' => $counts[$c['id']] ?? 0], $this->sorted());
    }

    public function sorted(): array
    {
        return self::sortBy($this->all(), [['name', 'asc']]);
    }

    public function findActiveBySlug(string $slug): ?array
    {
        return $this->firstWhere(['slug' => $slug, 'is_active' => true]);
    }

    public function hasProducts(int $id): bool
    {
        return model(ProductModel::class)->count(['category_id' => $id]) > 0;
    }

    public static function rules(?int $ignoreId = null): array
    {
        return [
            'name'        => ['label' => 'Nama kategori', 'rules' => 'required|min_length[3]|max_length[100]'],
            'slug'        => ['label' => 'Slug', 'rules' => 'required|alpha_dash|max_length[120]|unique_in[categories.slug,' . ($ignoreId ?? 0) . ']'],
            'icon'        => ['label' => 'Ikon', 'rules' => 'permit_empty|regex_match[/^bi-[a-z0-9-]+$/]'],
            'description' => ['label' => 'Deskripsi', 'rules' => 'permit_empty|max_length[1000]'],
            'is_active'   => ['label' => 'Status', 'rules' => 'required|in_list[0,1]'],
        ];
    }
}
