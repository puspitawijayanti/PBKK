<?php

/**
 * Helper ShopEase: format uang, label status, info user login.
 * Dimuat otomatis lewat Config\Autoload::$helpers.
 */

if (! function_exists('rupiah')) {
    function rupiah($number): string
    {
        return 'Rp ' . number_format((float) $number, 0, ',', '.');
    }
}

if (! function_exists('tgl')) {
    /** Format tanggal-waktu: 18 Nov 2026 21:05 */
    function tgl(?string $datetime, bool $withTime = true): string
    {
        if (empty($datetime)) {
            return '-';
        }
        $ts = strtotime($datetime);

        return date($withTime ? 'd M Y H:i' : 'd M Y', $ts);
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

if (! function_exists('order_status_badge')) {
    function order_status_badge(string $status): string
    {
        $color = [
            'pending_payment'      => 'warning text-dark',
            'waiting_verification' => 'info text-dark',
            'processing'           => 'primary',
            'shipped'              => 'secondary',
            'completed'            => 'success',
            'cancelled'            => 'danger',
        ][$status] ?? 'light text-dark';

        return '<span class="badge bg-' . $color . '">' . esc(order_status_label($status)) . '</span>';
    }
}

if (! function_exists('payment_status_label')) {
    function payment_status_label(string $status): string
    {
        return [
            'unpaid'    => 'Belum Dibayar',
            'submitted' => 'Bukti Diunggah',
            'verified'  => 'Terverifikasi',
            'rejected'  => 'Ditolak',
        ][$status] ?? $status;
    }
}

if (! function_exists('payment_status_badge')) {
    function payment_status_badge(string $status): string
    {
        $color = [
            'unpaid'    => 'warning text-dark',
            'submitted' => 'info text-dark',
            'verified'  => 'success',
            'rejected'  => 'danger',
        ][$status] ?? 'light text-dark';

        return '<span class="badge bg-' . $color . '">' . esc(payment_status_label($status)) . '</span>';
    }
}

if (! function_exists('shipment_status_label')) {
    function shipment_status_label(?string $status): string
    {
        return [
            'pending'   => 'Belum Dikirim',
            'shipped'   => 'Dalam Pengiriman',
            'delivered' => 'Diterima',
        ][$status ?? 'pending'] ?? (string) $status;
    }
}

if (! function_exists('role_label')) {
    function role_label(string $role): string
    {
        return ['admin' => 'Administrator', 'operator' => 'Operator Toko', 'customer' => 'Customer'][$role] ?? $role;
    }
}

if (! function_exists('product_image_url')) {
    function product_image_url(?string $image): string
    {
        if ($image && is_file(FCPATH . 'uploads/products/' . $image)) {
            return base_url('uploads/products/' . rawurlencode($image));
        }

        return base_url('assets/img/placeholder.svg');
    }
}

if (! function_exists('is_logged_in')) {
    function is_logged_in(): bool
    {
        return (bool) session()->get('logged_in');
    }
}

if (! function_exists('current_user_id')) {
    function current_user_id(): ?int
    {
        return session()->get('user_id') ? (int) session()->get('user_id') : null;
    }
}

if (! function_exists('current_role')) {
    function current_role(): ?string
    {
        return session()->get('role');
    }
}

if (! function_exists('cart_count')) {
    /** Jumlah baris produk pada keranjang customer yang login (untuk badge navbar). */
    function cart_count(): int
    {
        if (! is_logged_in() || current_role() !== 'customer') {
            return 0;
        }

        return (new \App\Models\CartItemModel())->countForUser((int) current_user_id());
    }
}

if (! function_exists('unique_slug')) {
    /**
     * Buat slug unik untuk model apa pun yang punya kolom `slug`.
     * Jika sudah dipakai, ditambah akhiran -2, -3, dst.
     */
    function unique_slug(\CodeIgniter\Model $model, string $name, ?int $ignoreId = null): string
    {
        $base = url_title($name, '-', true);
        $base = $base !== '' ? $base : 'item';
        $slug = $base;
        $i    = 2;

        while (true) {
            $builder = $model->builder()->where('slug', $slug);
            if ($ignoreId !== null) {
                $builder->where($model->primaryKey . ' !=', $ignoreId);
            }
            if ($builder->countAllResults() === 0) {
                return $slug;
            }
            $slug = $base . '-' . $i++;
        }
    }
}

if (! function_exists('field_error')) {
    /** Pesan error validasi per field (dari flashdata 'errors'). */
    function field_error(string $field): string
    {
        $errors = session('errors');

        return isset($errors[$field])
            ? '<div class="invalid-feedback d-block">' . esc($errors[$field]) . '</div>'
            : '';
    }
}

if (! function_exists('invalid_class')) {
    function invalid_class(string $field): string
    {
        $errors = session('errors');

        return isset($errors[$field]) ? ' is-invalid' : '';
    }
}
