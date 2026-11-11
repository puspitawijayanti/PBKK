<?php

if (! function_exists('rupiah')) {
    /**
     * Memformat angka menjadi mata uang Rupiah, misalnya 150000 menjadi "Rp150.000".
     */
    function rupiah(int|float|string|null $amount): string
    {
        return 'Rp' . number_format((float) $amount, 0, ',', '.');
    }
}

if (! function_exists('order_status_label')) {
    function order_status_label(string $status): string
    {
        return [
            'pending_payment'      => 'Menunggu Pembayaran',
            'waiting_verification' => 'Menunggu Verifikasi',
            'processing'           => 'Diproses',
            'shipped'              => 'Dikirim',
            'completed'            => 'Selesai',
            'cancelled'            => 'Dibatalkan',
        ][$status] ?? $status;
    }
}

if (! function_exists('payment_status_label')) {
    function payment_status_label(?string $status): string
    {
        return [
            'unpaid'    => 'Belum Dibayar',
            'submitted' => 'Bukti Dikirim',
            'verified'  => 'Terverifikasi',
            'rejected'  => 'Ditolak',
        ][$status] ?? '-';
    }
}

if (! function_exists('shipment_status_label')) {
    function shipment_status_label(?string $status): string
    {
        return [
            'preparing' => 'Disiapkan',
            'shipped'   => 'Dalam Pengiriman',
            'delivered' => 'Diterima',
        ][$status] ?? 'Belum Dikirim';
    }
}

if (! function_exists('status_badge')) {
    /**
     * Badge Bootstrap untuk status pesanan, pembayaran, atau pengiriman.
     */
    function status_badge(string $type, ?string $status): string
    {
        $classes = [
            'pending_payment' => 'warning', 'waiting_verification' => 'info', 'processing' => 'primary',
            'shipped' => 'primary', 'completed' => 'success', 'cancelled' => 'secondary',
            'unpaid' => 'warning', 'submitted' => 'info', 'verified' => 'success', 'rejected' => 'danger',
            'preparing' => 'info', 'delivered' => 'success',
        ];
        $label = match ($type) {
            'payment'  => payment_status_label($status),
            'shipment' => shipment_status_label($status),
            default    => order_status_label((string) $status),
        };
        $class = $classes[$status] ?? 'light';
        $text  = in_array($class, ['warning', 'info', 'light'], true) ? ' text-dark' : '';

        return '<span class="badge text-bg-' . $class . $text . '">' . esc($label) . '</span>';
    }
}

if (! function_exists('product_thumb')) {
    /**
     * Gambar produk hasil unggahan admin, atau ilustrasi ikon jika belum ada gambar.
     */
    function product_thumb(array $product, string $extraClass = ''): string
    {
        $class = trim('product-thumb cat-' . ($product['category_id'] ?? 0) . ' ' . $extraClass);

        if (! empty($product['image'])) {
            return '<div class="' . $class . ' has-image"><img src="' . esc(base_url('uploads/products/' . $product['image']), 'attr')
                . '" alt="' . esc($product['name'] ?? $product['product_name'] ?? '', 'attr') . '" loading="lazy"></div>';
        }

        return '<div class="' . $class . '"><i class="bi ' . esc($product['icon'] ?? 'bi-box', 'attr') . '" aria-hidden="true"></i></div>';
    }
}

if (! function_exists('current_user')) {
    /**
     * Data pengguna yang sedang login dari session, atau null untuk pengunjung.
     */
    function current_user(): ?array
    {
        return session('user_id') === null ? null : [
            'id'   => (int) session('user_id'),
            'name' => (string) session('user_name'),
            'role' => (string) session('user_role'),
        ];
    }
}

if (! function_exists('role_label')) {
    function role_label(string $role): string
    {
        return ['customer' => 'Customer', 'admin' => 'Administrator', 'operator' => 'Operator'][$role] ?? $role;
    }
}
