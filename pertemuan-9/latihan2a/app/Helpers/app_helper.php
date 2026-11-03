<?php

if (! function_exists('role_label')) {
    function role_label(string $role): string
    {
        return [
            'admin'    => 'Administrator',
            'lecturer' => 'Dosen',
            'student'  => 'Mahasiswa',
        ][$role] ?? $role;
    }
}

if (! function_exists('term_label')) {
    function term_label(string $term): string
    {
        return [
            'odd'  => 'Ganjil',
            'even' => 'Genap',
        ][$term] ?? $term;
    }
}

if (! function_exists('period_label')) {
    function period_label(string $academicYear, string $term): string
    {
        return 'Semester ' . term_label($term) . ' ' . $academicYear;
    }
}

if (! function_exists('grade_badge')) {
    function grade_badge(?string $grade): string
    {
        if ($grade === null || $grade === '') {
            return '<span class="badge badge-light">Belum dinilai</span>';
        }

        $color = match ($grade) {
            'A', 'AB' => 'success',
            'B', 'BC' => 'primary',
            'C'       => 'warning',
            default   => 'danger',
        };

        return '<span class="badge badge-' . $color . '">' . esc($grade) . '</span>';
    }
}

if (! function_exists('format_gpa')) {
    function format_gpa(?float $gpa): string
    {
        return $gpa === null ? '-' : number_format($gpa, 2, ',', '.');
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
