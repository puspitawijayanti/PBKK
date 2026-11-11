<?php

namespace App\Validation;

/**
 * Aturan validasi yang membaca JsonStore, pengganti is_unique / is_not_unique berbasis database.
 */
class ShopRules
{
    /**
     * unique_in[tabel.kolom,idDiabaikan], misalnya unique_in[users.email] atau unique_in[products.slug,5].
     */
    public function unique_in(?string $value, string $params, array $data, ?string &$error = null): bool
    {
        [$target, $ignoreId] = array_pad(explode(',', $params), 2, '0');
        [$table, $field]     = explode('.', $target);

        foreach (service('store')->all($table) as $row) {
            if ((int) $row['id'] !== (int) $ignoreId && strcasecmp((string) ($row[$field] ?? ''), trim((string) $value)) === 0) {
                $error = '{field} sudah digunakan.';

                return false;
            }
        }

        return true;
    }

    /**
     * exists_in[tabel], misalnya exists_in[categories]: id harus ada di tabel tersebut.
     */
    public function exists_in(?string $value, string $table, array $data, ?string &$error = null): bool
    {
        if (service('store')->find($table, (int) $value) !== null) {
            return true;
        }

        $error = '{field} tidak valid.';

        return false;
    }
}
