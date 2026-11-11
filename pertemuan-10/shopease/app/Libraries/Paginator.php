<?php

namespace App\Libraries;

/**
 * Pagination untuk data berbentuk array (FR-020), memakai Pager bawaan CodeIgniter untuk membuat tautan.
 */
class Paginator
{
    /**
     * @return array{items: array, links: string, total: int}
     */
    public static function paginate(array $rows, int $perPage): array
    {
        $total = count($rows);
        $pages = max(1, (int) ceil($total / $perPage));
        $page  = min($pages, max(1, (int) service('request')->getGet('page')));

        return [
            'items' => array_slice(array_values($rows), ($page - 1) * $perPage, $perPage),
            'links' => service('pager')->makeLinks($page, $perPage, $total, 'bootstrap'),
            'total' => $total,
        ];
    }
}
