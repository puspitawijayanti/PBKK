<?php

namespace App\Services;

use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Penyimpanan file unggahan (PRD 13: Upload).
 * Nama file diacak. Bukti pembayaran disimpan di writable/ (tidak dapat diakses langsung dari web)
 * dan hanya dikirim lewat controller yang sudah memeriksa hak akses.
 */
class UploadService
{
    private const PROOF_DIR   = WRITEPATH . 'uploads/payments/';
    private const PRODUCT_DIR = FCPATH . 'uploads/products/';

    public static function storePaymentProof(UploadedFile $file): string
    {
        return self::store($file, self::PROOF_DIR);
    }

    public static function deletePaymentProof(?string $fileName): void
    {
        self::delete(self::PROOF_DIR, $fileName);
    }

    public static function servePaymentProof(ResponseInterface $response, ?string $fileName): ResponseInterface
    {
        $path = self::PROOF_DIR . basename((string) $fileName);

        if ($fileName === null || $fileName === '' || ! is_file($path)) {
            throw PageNotFoundException::forPageNotFound('Bukti pembayaran tidak ditemukan.');
        }

        return $response
            ->setHeader('Content-Type', mime_content_type($path) ?: 'application/octet-stream')
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setHeader('Cache-Control', 'private, max-age=600')
            ->setBody(file_get_contents($path));
    }

    public static function storeProductImage(UploadedFile $file): string
    {
        return self::store($file, self::PRODUCT_DIR);
    }

    public static function deleteProductImage(?string $fileName): void
    {
        self::delete(self::PRODUCT_DIR, $fileName);
    }

    private static function store(UploadedFile $file, string $directory): string
    {
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $name = $file->getRandomName();
        $file->move($directory, $name);

        return $name;
    }

    private static function delete(string $directory, ?string $fileName): void
    {
        if ($fileName !== null && $fileName !== '' && is_file($directory . basename($fileName))) {
            unlink($directory . basename($fileName));
        }
    }
}
