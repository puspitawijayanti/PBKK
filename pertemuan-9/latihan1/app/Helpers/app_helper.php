<?php

if (! function_exists('loan_status_label')) {
    function loan_status_label(string $status): string
    {
        return [
            'pending'  => 'Menunggu Persetujuan',
            'approved' => 'Disetujui',
            'borrowed' => 'Sedang Dipinjam',
            'returned' => 'Dikembalikan',
            'rejected' => 'Ditolak',
        ][$status] ?? $status;
    }
}

if (! function_exists('loan_status_badge')) {
    function loan_status_badge(string $status): string
    {
        $color = [
            'pending'  => 'warning',
            'approved' => 'info',
            'borrowed' => 'primary',
            'returned' => 'success',
            'rejected' => 'danger',
        ][$status] ?? 'secondary';

        return '<span class="badge badge-' . $color . ' bg-' . $color . '">' . esc(loan_status_label($status)) . '</span>';
    }
}

if (! function_exists('condition_label')) {
    function condition_label(string $condition): string
    {
        return [
            'good'    => 'Baik',
            'fair'    => 'Cukup Baik',
            'damaged' => 'Rusak Ringan',
        ][$condition] ?? $condition;
    }
}

if (! function_exists('role_label')) {
    function role_label(string $role): string
    {
        return [
            'admin'    => 'Laboran',
            'borrower' => 'Mahasiswa',
        ][$role] ?? $role;
    }
}

if (! function_exists('tool_photo_url')) {
    function tool_photo_url(?string $photo): string
    {
        if ($photo !== null && $photo !== '' && is_file(FCPATH . 'uploads/tools/' . $photo)) {
            return base_url('uploads/tools/' . $photo);
        }

        return base_url('assets/img/no-photo.svg');
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

if (! function_exists('month_label')) {
    function month_label(string $period): string
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $timestamp = strtotime($period . '-01');

        return $months[(int) date('n', $timestamp) - 1] . ' ' . date('Y', $timestamp);
    }
}
