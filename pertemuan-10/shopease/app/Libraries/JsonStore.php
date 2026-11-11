<?php

namespace App\Libraries;

use App\Data\DemoData;
use RuntimeException;
use Throwable;

/**
 * Penyimpanan data aplikasi dalam satu file JSON (pengganti database MySQL).
 *
 * Struktur file: { "tables": { "<nama tabel>": { "<id>": {...baris...} } }, "auto_increment": { "<tabel>": n } }.
 * Setiap perubahan dijalankan di dalam transaction(): file dikunci (flock), data dibaca ulang,
 * diubah di memori, lalu ditulis sekaligus. Jika terjadi exception, perubahan dibuang (rollback),
 * sehingga pembuatan order, item, dan pengurangan stok tetap atomik (BR-010).
 */
class JsonStore
{
    private ?array $data = null;

    /** @var resource|null */
    private $lockHandle = null;

    private int $depth = 0;

    public function __construct(private readonly string $path)
    {
    }

    /**
     * Semua baris tabel, diindeks berdasarkan id.
     */
    public function all(string $table): array
    {
        return $this->load()['tables'][$table] ?? [];
    }

    public function find(string $table, int $id): ?array
    {
        return $this->load()['tables'][$table][$id] ?? null;
    }

    public function insert(string $table, array $row): int
    {
        return $this->transaction(function () use ($table, $row): int {
            $id  = ($this->data['auto_increment'][$table] ?? 0) + 1;
            $now = date('Y-m-d H:i:s');

            $this->data['auto_increment'][$table] = $id;
            $this->data['tables'][$table][$id]    = ['id' => $id] + $row + ['created_at' => $now, 'updated_at' => $now];

            return $id;
        });
    }

    public function update(string $table, int $id, array $changes): void
    {
        $this->transaction(function () use ($table, $id, $changes): void {
            if (! isset($this->data['tables'][$table][$id])) {
                throw new RuntimeException("Data {$table} #{$id} tidak ditemukan.");
            }

            $this->data['tables'][$table][$id] = array_merge(
                $this->data['tables'][$table][$id],
                $changes,
                ['id' => $id, 'updated_at' => $changes['updated_at'] ?? date('Y-m-d H:i:s')],
            );
        });
    }

    public function delete(string $table, int $id): void
    {
        $this->transaction(function () use ($table, $id): void {
            unset($this->data['tables'][$table][$id]);
        });
    }

    /**
     * Menjalankan beberapa perubahan sebagai satu kesatuan. Transaksi bersarang ikut transaksi terluar.
     */
    public function transaction(callable $callback): mixed
    {
        if ($this->depth > 0) {
            return $callback($this);
        }

        $this->load();
        $this->lock();
        $this->data = $this->readFile() ?? $this->data ?? $this->emptyData();
        $snapshot   = $this->data;
        $this->depth++;

        try {
            $result = $callback($this);
            $this->writeFile($this->data);

            return $result;
        } catch (Throwable $exception) {
            $this->data = $snapshot;

            throw $exception;
        } finally {
            $this->depth--;
            $this->unlock();
        }
    }

    /**
     * Menghapus semua data lalu memasang ulang data demo (kategori, produk, akun, dan pesanan contoh).
     */
    public function reset(bool $withOrders = true): void
    {
        if (is_file($this->path)) {
            unlink($this->path);
        }

        $this->data = $this->emptyData();
        DemoData::install($this, $withOrders);
    }

    public function path(): string
    {
        return $this->path;
    }

    private function load(): array
    {
        if ($this->data !== null) {
            return $this->data;
        }

        $this->data = $this->readFile();

        if ($this->data === null) {
            // File belum ada (pertama kali dijalankan): isi dengan data demo.
            $this->reset();
        }

        return $this->data;
    }

    private function readFile(): ?array
    {
        if (! is_file($this->path)) {
            return null;
        }

        $data = json_decode((string) file_get_contents($this->path), true);

        if (! is_array($data)) {
            throw new RuntimeException('File data rusak: ' . $this->path . '. Jalankan "php spark shop:reset".');
        }

        // json_decode mengubah kunci id menjadi int otomatis; pastikan setiap tabel tetap berupa array.
        $data['tables'] = array_map(static fn ($rows): array => (array) $rows, $data['tables'] ?? []);

        return $data;
    }

    private function writeFile(array $data): void
    {
        $directory = dirname($this->path);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $temp = $this->path . '.tmp';
        file_put_contents($temp, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION));
        rename($temp, $this->path);
    }

    private function lock(): void
    {
        $directory = dirname($this->path);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $this->lockHandle = fopen($this->path . '.lock', 'c');
        flock($this->lockHandle, LOCK_EX);
    }

    private function unlock(): void
    {
        if ($this->lockHandle !== null) {
            flock($this->lockHandle, LOCK_UN);
            fclose($this->lockHandle);
            $this->lockHandle = null;
        }
    }

    private function emptyData(): array
    {
        return ['tables' => [], 'auto_increment' => []];
    }
}
