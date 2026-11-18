<?php

namespace App\Libraries;

use CodeIgniter\HTTP\Files\UploadedFile;

/**
 * Penyimpanan file upload yang aman.
 * Validasi (ukuran, ekstensi, MIME) dilakukan oleh controller lewat rules validasi;
 * di sini file diberi NAMA ACAK buatan sistem dan dipindahkan.
 *
 *  - Gambar produk  : public/uploads/products/   (tampil langsung; eksekusi script dimatikan via .htaccess)
 *  - Bukti pembayaran: writable/uploads/payments/ (di luar public, hanya via PaymentController::proof)
 */
class FileUploader
{
    public static function saveProductImage(UploadedFile $file): string
    {
        $name = $file->getRandomName();
        $file->move(FCPATH . 'uploads/products', $name);

        return $name;
    }

    public static function deleteProductImage(?string $name): void
    {
        if ($name) {
            $path = FCPATH . 'uploads/products/' . basename($name);
            // file bawaan seeder (svg) dibiarkan agar data demo tidak hilang
            if (is_file($path) && ! str_starts_with(basename($name), 'seed-')) {
                @unlink($path);
            }
        }
    }

    public static function savePaymentProof(UploadedFile $file): string
    {
        $dir = WRITEPATH . 'uploads/payments';
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $name = $file->getRandomName();
        $file->move($dir, $name);

        return $name;
    }

    public static function paymentProofPath(string $name): string
    {
        return WRITEPATH . 'uploads/payments/' . basename($name);
    }

    public static function deletePaymentProof(?string $name): void
    {
        if ($name && is_file(self::paymentProofPath($name))) {
            @unlink(self::paymentProofPath($name));
        }
    }
}
