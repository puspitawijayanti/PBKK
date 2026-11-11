<?php

namespace App\Models;

use RuntimeException;

class ProductModel extends JsonModel
{
    protected string $table = 'products';

    public const SORTS = [
        'newest'     => [['created_at', 'desc'], ['id', 'desc']],
        'price_asc'  => [['price', 'asc'], ['id', 'desc']],
        'price_desc' => [['price', 'desc'], ['id', 'desc']],
        'name'       => [['name', 'asc']],
    ];

    /**
     * Katalog: hanya produk aktif dari kategori aktif (BR-002), digabung dengan data kategorinya (FR-008, FR-009).
     */
    public function catalog(?string $keyword = null, ?string $categorySlug = null, string $sort = 'newest'): array
    {
        $rows = array_filter($this->withCategory(), static fn (array $p): bool => $p['status'] === 'active'
            && $p['category_is_active']
            && self::contains($p['name'], trim((string) $keyword))
            && ($categorySlug === null || $categorySlug === '' || $p['category_slug'] === $categorySlug));

        return self::sortBy(array_values($rows), self::SORTS[$sort] ?? self::SORTS['newest']);
    }

    public function featured(int $limit = 8): array
    {
        return array_slice(array_values(array_filter($this->catalog(), static fn (array $p): bool => $p['is_featured'])), 0, $limit);
    }

    public function newest(int $limit = 4): array
    {
        return array_slice($this->catalog(), 0, $limit);
    }

    public function findActiveBySlug(string $slug): ?array
    {
        foreach ($this->catalog() as $product) {
            if ($product['slug'] === $slug) {
                return $product;
            }
        }

        return null;
    }

    public function findActive(int $id): ?array
    {
        foreach ($this->catalog() as $product) {
            if ($product['id'] === $id) {
                return $product;
            }
        }

        return null;
    }

    public function related(array $product, int $limit = 4): array
    {
        $related = array_filter($this->catalog(null, $product['category_slug']), static fn (array $p): bool => $p['id'] !== $product['id']);

        return array_slice(array_values($related), 0, $limit);
    }

    /**
     * Daftar produk untuk admin, termasuk yang nonaktif.
     */
    public function adminSearch(?string $keyword, ?int $categoryId, ?string $status): array
    {
        $rows = array_filter($this->withCategory(), static fn (array $p): bool => self::contains($p['name'], trim((string) $keyword))
            && (! $categoryId || $p['category_id'] === $categoryId)
            && (! in_array($status, ['active', 'inactive'], true) || $p['status'] === $status));

        return self::sortBy(array_values($rows), [['id', 'desc']]);
    }

    /**
     * Mengubah stok secara aman; gagal jika stok tidak mencukupi (mencegah stok negatif).
     */
    public function adjustStock(int $id, int $delta): void
    {
        $this->store()->transaction(function () use ($id, $delta): void {
            $product = $this->find($id);

            if ($product === null) {
                return;
            }

            if ($product['stock'] + $delta < 0) {
                throw new RuntimeException('Stok ' . $product['name'] . ' tidak mencukupi. Sisa stok: ' . $product['stock'] . '.');
            }

            $this->update($id, ['stock' => $product['stock'] + $delta]);
        });
    }

    public function isOrdered(int $productId): bool
    {
        return model(OrderItemModel::class)->count(['product_id' => $productId]) > 0;
    }

    public function countLowStock(int $threshold = 10): int
    {
        return count(array_filter($this->where(['status' => 'active']), static fn (array $p): bool => $p['stock'] < $threshold));
    }

    /**
     * Aturan validasi form produk admin (PRD 13: harga tidak negatif, stok bilangan bulat, kategori valid).
     */
    public static function rules(?int $ignoreId = null): array
    {
        return [
            'category_id' => ['label' => 'Kategori', 'rules' => 'required|is_natural_no_zero|exists_in[categories]'],
            'name'        => ['label' => 'Nama produk', 'rules' => 'required|min_length[3]|max_length[150]'],
            'slug'        => ['label' => 'Slug', 'rules' => 'required|alpha_dash|max_length[170]|unique_in[products.slug,' . ($ignoreId ?? 0) . ']'],
            'description' => ['label' => 'Deskripsi', 'rules' => 'permit_empty|max_length[5000]'],
            'price'       => ['label' => 'Harga', 'rules' => 'required|decimal|greater_than_equal_to[0]'],
            'stock'       => ['label' => 'Stok', 'rules' => 'required|is_natural'],
            'icon'        => ['label' => 'Ikon', 'rules' => 'permit_empty|regex_match[/^bi-[a-z0-9-]+$/]'],
            'status'      => ['label' => 'Status', 'rules' => 'required|in_list[active,inactive]'],
            'is_featured' => ['label' => 'Unggulan', 'rules' => 'permit_empty|in_list[0,1]'],
        ];
    }

    /**
     * Produk beserta nama, slug, ikon, dan status kategorinya (setara JOIN products-categories).
     */
    private function withCategory(): array
    {
        $categories = model(CategoryModel::class);

        return array_map(static function (array $p) use ($categories): array {
            $category = $categories->find($p['category_id']);

            return $p + [
                'category_name'      => $category['name'] ?? '-',
                'category_slug'      => $category['slug'] ?? '',
                'category_icon'      => $category['icon'] ?? 'bi-tag',
                'category_is_active' => (bool) ($category['is_active'] ?? false),
            ];
        }, $this->all());
    }
}
