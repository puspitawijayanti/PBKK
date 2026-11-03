<?php

if (! function_exists('rupiah')) {
    function rupiah(int|float|string|null $amount): string
    {
        return 'Rp' . number_format((float) $amount, 0, ',', '.');
    }
}

if (! function_exists('order_status_label')) {
    function order_status_label(string $status): string
    {
        return [
            'awaiting_payment'      => 'Menunggu Pembayaran',
            'awaiting_verification' => 'Menunggu Verifikasi',
            'processing'            => 'Diproses',
            'shipped'               => 'Dikirim',
            'completed'             => 'Selesai',
            'cancelled'             => 'Dibatalkan',
        ][$status] ?? $status;
    }
}

if (! function_exists('order_status_badge')) {
    function order_status_badge(string $status): string
    {
        $color = [
            'awaiting_payment'      => 'warning',
            'awaiting_verification' => 'info',
            'processing'            => 'primary',
            'shipped'               => 'secondary',
            'completed'             => 'success',
            'cancelled'             => 'danger',
        ][$status] ?? 'light';

        return '<span class="badge badge-' . $color . ' text-bg-' . $color . '">' . esc(order_status_label($status)) . '</span>';
    }
}

if (! function_exists('roast_label')) {
    function roast_label(string $roast): string
    {
        return [
            'light'  => 'Light Roast',
            'medium' => 'Medium Roast',
            'dark'   => 'Dark Roast',
        ][$roast] ?? $roast;
    }
}

if (! function_exists('process_label')) {
    function process_label(string $process): string
    {
        return [
            'washed'  => 'Full Washed',
            'natural' => 'Natural',
            'honey'   => 'Honey',
            'wet_hulled' => 'Giling Basah',
        ][$process] ?? $process;
    }
}

if (! function_exists('role_label')) {
    function role_label(string $role): string
    {
        return [
            'admin' => 'Admin Toko',
            'buyer' => 'Pembeli',
        ][$role] ?? $role;
    }
}

if (! function_exists('product_photo_url')) {
    function product_photo_url(?string $photo): string
    {
        if ($photo !== null && $photo !== '' && is_file(FCPATH . 'uploads/products/' . $photo)) {
            return base_url('uploads/products/' . $photo);
        }

        return base_url('assets/img/no-photo.svg');
    }
}

if (! function_exists('cart_count')) {
    function cart_count(): int
    {
        return array_sum(session('cart') ?? []);
    }
}

if (! function_exists('date_id')) {
    function date_id(?string $date, bool $withTime = false): string
    {
        if ($date === null || $date === '') {
            return '-';
        }

        $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $timestamp = strtotime($date);
        $text = date('j', $timestamp) . ' ' . $months[(int) date('n', $timestamp) - 1] . ' ' . date('Y', $timestamp);

        return $withTime ? $text . ', ' . date('H.i', $timestamp) : $text;
    }
}

if (! function_exists('month_label')) {
    function month_label(string $period): string
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $timestamp = strtotime($period . '-01');

        return $months[(int) date('n', $timestamp) - 1] . ' ' . date('Y', $timestamp);
    }
}

if (! function_exists('field_error')) {
    function field_error(string $field): string
    {
        $errors = session('errors') ?? [];

        if (! isset($errors[$field])) {
            return '';
        }

        return '<div class="invalid-feedback d-block">' . esc($errors[$field]) . '</div>';
    }
}

if (! function_exists('invalid_class')) {
    function invalid_class(string $field): string
    {
        return isset((session('errors') ?? [])[$field]) ? ' is-invalid' : '';
    }
}
