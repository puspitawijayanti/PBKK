<?php

namespace App\Libraries;

/** Kesalahan bisnis yang aman ditampilkan ke pengguna (stok kurang, status tidak valid, dll). */
class OrderException extends \RuntimeException
{
}
