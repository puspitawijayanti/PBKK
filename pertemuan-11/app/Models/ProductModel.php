<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table         = 'products';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'category_id', 'name', 'slug', 'description',
        'price', 'stock', 'image', 'status',
    ];
    protected $useTimestamps = true;

    public const SORTS = [
        'newest'     => ['products.created_at', 'DESC'],
        'price_asc'  => ['products.price', 'ASC'],
        'price_desc' => ['products.price', 'DESC'],
        'name'       => ['products.name', 'ASC'],
    ];

    /**
     * Query katalog untuk customer: hanya produk aktif (BR-002) dan kategori aktif.
     * Mengembalikan model agar bisa langsung dipanggil ->paginate().
     *
     * @param array{q?:string,category?:string,sort?:string} $filters
     */
    public function catalog(array $filters = []): self
    {
        $this->select('products.*, categories.name AS category_name, categories.slug AS category_slug')
            ->join('categories', 'categories.id = products.category_id')
            ->where('products.status', 'active')
            ->where('categories.is_active', 1);

        if (! empty($filters['q'])) {
            $this->groupStart()
                ->like('products.name', $filters['q'])
                ->orLike('products.description', $filters['q'])
                ->groupEnd();
        }

        if (! empty($filters['category'])) {
            $this->where('categories.slug', $filters['category']);
        }

        [$col, $dir] = self::SORTS[$filters['sort'] ?? 'newest'] ?? self::SORTS['newest'];

        return $this->orderBy($col, $dir)->orderBy('products.id', 'DESC');
    }

    /** Query daftar produk untuk admin (semua status). */
    public function adminList(array $filters = []): self
    {
        $this->select('products.*, categories.name AS category_name')
            ->join('categories', 'categories.id = products.category_id');

        if (! empty($filters['q'])) {
            $this->like('products.name', $filters['q']);
        }
        if (! empty($filters['category_id'])) {
            $this->where('products.category_id', (int) $filters['category_id']);
        }
        if (! empty($filters['status']) && in_array($filters['status'], ['active', 'inactive'], true)) {
            $this->where('products.status', $filters['status']);
        }

        return $this->orderBy('products.id', 'DESC');
    }

    /** Detail produk aktif berdasarkan slug atau id. */
    public function findActiveBySlugOrId(string $key): ?array
    {
        $this->select('products.*, categories.name AS category_name, categories.slug AS category_slug')
            ->join('categories', 'categories.id = products.category_id')
            ->where('products.status', 'active')
            ->where('categories.is_active', 1);

        if (ctype_digit($key)) {
            $this->where('products.id', (int) $key);
        } else {
            $this->where('products.slug', $key);
        }

        return $this->first();
    }

    /** Produk terlaris: berdasarkan jumlah terjual pada pesanan yang tidak dibatalkan. */
    public function bestSellers(int $limit = 4): array
    {
        return $this->select('products.*, categories.name AS category_name, SUM(order_items.quantity) AS sold')
            ->join('categories', 'categories.id = products.category_id')
            ->join('order_items', 'order_items.product_id = products.id')
            ->join('orders', "orders.id = order_items.order_id AND orders.order_status <> 'cancelled'")
            ->where('products.status', 'active')
            ->groupBy('products.id')
            ->orderBy('sold', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /** Apakah produk sudah pernah dipakai pada transaksi? (menentukan hapus vs nonaktifkan) */
    public function isUsedInOrders(int $id): bool
    {
        return $this->db->table('order_items')->where('product_id', $id)->countAllResults() > 0;
    }

    public function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        return unique_slug($this, $name, $ignoreId);
    }
}
