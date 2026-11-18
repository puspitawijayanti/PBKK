# Dokumentasi Pengujian ShopEase

Pengujian dijalankan otomatis oleh `tests/functional/run_tests.py` (black-box via HTTP terhadap aplikasi yang berjalan, dengan verifikasi langsung ke database MySQL/MariaDB).

**Ringkasan: 118 dari 118 pengujian LULUS.**

Lingkungan uji: PHP 8.3, MariaDB 10.11, CodeIgniter 4.7, server PHP multi-worker (untuk uji race condition).

## A. Skenario Pengujian pada Studi Kasus (Bagian 9)

| No. | Skenario | Hasil yang diharapkan | ID uji | Hasil |
|---|---|---|---|---|
| 1 | Registrasi customer dengan data valid | Akun dibuat, password tersimpan sebagai hash | T01 | LULUS |
| 2 | Login dengan kredensial valid | Masuk ke halaman sesuai role | T02a, T02b, T02c | LULUS |
| 3 | Login dengan password salah | Akses ditolak + pesan kesalahan | T03 | LULUS |
| 4 | Menambah produk melalui admin | Produk tersimpan & tampil di daftar | T07a, T07b | LULUS |
| 5 | Mengubah data produk | Perubahan tersimpan | T07c | LULUS |
| 6 | Menghapus produk yang tidak digunakan | Produk dihapus / dinonaktifkan sesuai aturan | T10a, T10b | LULUS |
| 7 | Upload gambar dengan format tidak diizinkan | Upload ditolak + pesan validasi | T08a, T08b, T08c, T08d, T08e, T08f | LULUS |
| 8 | Menambahkan produk ke keranjang | Item & kuantitas tersimpan | T12c, T12d | LULUS |
| 9 | Checkout dengan stok mencukupi | Order & detail order dibuat | T13a, T13b, T13c, T13d, T13e, T13f | LULUS |
| 10 | Checkout melebihi stok | Transaksi ditolak, stok tidak berubah | T14a, T14b | LULUS |
| 11 | Mengunggah bukti pembayaran | File tersimpan, status menunggu verifikasi | T16c, T16d | LULUS |
| 12 | Admin memverifikasi pembayaran | Status pembayaran & pesanan berubah sesuai aturan | T17e, T17b | LULUS |

## B. Pengujian Keamanan (HOL Praktikum 12, Langkah 2)

| Pengujian | ID uji | Hasil |
|---|---|---|
| Akses halaman admin tanpa login | T04a | LULUS |
| Customer mencoba membuka halaman admin | T04b, T04c | LULUS |
| Operator mengakses fitur khusus admin (verifikasi pembayaran) | T04d, T04e, T17a, T18b | LULUS |
| Upload file bukan gambar / skrip PHP / MIME palsu / terlalu besar | T08a, T08b, T08c, T08d, T08e, T16a, T16b | LULUS |
| Checkout dengan stok tidak mencukupi | T14a, T14b | LULUS |
| Manipulasi harga/total melalui browser | T13b, T13g | LULUS |
| Akses pesanan & bukti pembayaran milik customer lain | T15a, T15b, T15c, T15d, T16f, T16g | LULUS |
| CSRF protection aktif | T05, T04h | LULUS |
| Race condition: dua checkout bersamaan pada stok 1 | T23 | LULUS |
| XSS pada pencarian & injeksi SQL pada URL/pencarian | T11f, T22b, T22c | LULUS |
| File sensitif tidak terekspos (.env, writable, bukti bayar) | T22g, T22h, T22i | LULUS |

## C. Hasil Lengkap Seluruh Uji Otomatis

| ID | Skenario | Hasil |
|---|---|---|
| T01 | Registrasi customer valid; password tersimpan sebagai hash | LULUS |
| T01b | Registrasi dengan email yang sudah dipakai ditolak | LULUS |
| T01c | Password tanpa angka ditolak (kebijakan password) | LULUS |
| T01d | Mass assignment role=admin saat registrasi diabaikan | LULUS |
| T02a | Login admin -> dashboard admin | LULUS |
| T02b | Login operator -> dashboard (menu terbatas) | LULUS |
| T02c | Login customer -> beranda toko | LULUS |
| T03 | Login password salah ditolak; pesan sama untuk email tak terdaftar | LULUS |
| T04a | Akses /admin tanpa login -> diarahkan ke login | LULUS |
| T04b | Customer membuka /admin -> 403 | LULUS |
| T04c | Customer membuka /admin/products -> 403 | LULUS |
| T04d | Operator membuka /admin/products (admin saja) -> 403 | LULUS |
| T04e | Operator membuka verifikasi pembayaran -> 403 (BR-007) | LULUS |
| T04f | Admin membuka /cart (khusus customer) -> 403 | LULUS |
| T04g | Operator boleh membuka daftar pesanan | LULUS |
| T04h | Logout lewat GET tidak diizinkan (hanya POST+CSRF) | LULUS |
| T05 | POST tanpa token CSRF ditolak | LULUS |
| T06a | Admin menambah kategori | LULUS |
| T06b | Validasi kategori: nama < 3 karakter ditolak | LULUS |
| T06c | Nama kategori duplikat ditolak | LULUS |
| T06d | Admin mengubah kategori | LULUS |
| T06e | Hapus kategori yang masih punya produk ditolak | LULUS |
| T06f | Hapus kategori kosong berhasil | LULUS |
| T07a | Admin menambah produk + upload gambar valid (nama file acak) | LULUS |
| T07b | Produk baru tampil pada daftar admin | LULUS |
| T07c | Admin mengubah data produk | LULUS |
| T08a | Upload .txt ditolak | LULUS |
| T08b | Upload skrip PHP (.php) ditolak | LULUS |
| T08c | File PHP menyamar jadi .png ditolak (cek MIME/isi file) | LULUS |
| T08d | Gambar GIF (format tidak diizinkan) ditolak | LULUS |
| T08e | Gambar > 2 MB ditolak | LULUS |
| T08f | Tidak ada produk tambahan tersimpan dari upload berbahaya | LULUS |
| T09a | Validasi: harga negatif ditolak | LULUS |
| T09b | Validasi: stok negatif ditolak | LULUS |
| T10a | Hapus produk yang belum dipakai -> terhapus | LULUS |
| T10b | Hapus produk yang sudah pernah dipesan -> dinonaktifkan (riwayat aman) | LULUS |
| T11a | Pencarian produk | LULUS |
| T11b | Filter kategori + urut harga naik | LULUS |
| T11c | Pagination tampil (8 produk/halaman) | LULUS |
| T11d | Produk nonaktif tidak tampil di katalog (BR-002) | LULUS |
| T11e | Detail produk nonaktif -> 404 | LULUS |
| T11f | Input pencarian di-escape (anti XSS) | LULUS |
| T12a | Tamu melihat detail produk, tombol aksi meminta login | LULUS |
| T12b | Tamu tidak bisa menambah ke keranjang (redirect/blokir) | LULUS |
| T12c | Customer menambah produk ke keranjang | LULUS |
| T12d | Menambah produk yang sama menggabungkan kuantitas | LULUS |
| T12e | Kuantitas 0 ditolak | LULUS |
| T12f | Kuantitas melebihi stok ditolak | LULUS |
| T12g | Ubah kuantitas di keranjang | LULUS |
| T12h | Ubah kuantitas > stok ditolak | LULUS |
| T12i | Subtotal = harga x kuantitas (5 x 299.000 = 1.495.000) | LULUS |
| T12j | Customer lain tidak bisa menghapus item keranjang orang lain (404) | LULUS |
| T13a | Checkout stok cukup -> order & order_items dibuat | LULUS |
| T13b | Total dihitung ulang di backend (abaikan total/harga dari browser) | LULUS |
| T13c | Snapshot nama & harga tersimpan di order_items | LULUS |
| T13d | Stok dikurangi saat pesanan dikonfirmasi | LULUS |
| T13e | Keranjang dikosongkan setelah checkout | LULUS |
| T13f | Pembayaran awal 'unpaid' & status pesanan 'pending_payment' | LULUS |
| T13g | Harga katalog berubah tetapi riwayat pesanan tetap (snapshot) | LULUS |
| T14a | Checkout melebihi stok ditolak dengan pesan jelas | LULUS |
| T14b | Stok tidak berubah & tidak ada order baru | LULUS |
| T14c | Checkout dengan keranjang kosong ditolak | LULUS |
| T14d | Checkout dengan alamat milik customer lain ditolak | LULUS |
| T14e | Checkout tanpa memilih alamat ditolak (validasi) | LULUS |
| T15a | Customer lain tidak dapat melihat pesanan milik orang lain (404) | LULUS |
| T15b | Pemilik dapat melihat pesanannya | LULUS |
| T15c | Customer lain tidak dapat membatalkan pesanan orang lain | LULUS |
| T15d | Customer lain tidak dapat mengunggah bukti ke pesanan orang lain | LULUS |
| T16a | Unggah bukti bukan gambar ditolak | LULUS |
| T16b | Unggah skrip PHP sebagai bukti ditolak | LULUS |
| T16c | Unggah bukti valid -> file tersimpan, status menunggu verifikasi | LULUS |
| T16d | Bukti pembayaran disimpan di luar folder public (writable/uploads/payments) | LULUS |
| T16e | Pemilik dapat melihat bukti pembayaran | LULUS |
| T16f | Customer lain tidak dapat melihat bukti pembayaran (404) | LULUS |
| T16g | Operator tidak dapat melihat bukti pembayaran (404) | LULUS |
| T16h | Admin dapat melihat bukti pembayaran | LULUS |
| T16i | Tamu tidak dapat melihat bukti pembayaran | LULUS |
| T16j | Unggah ulang saat menunggu verifikasi ditolak | LULUS |
| T17a | Operator tidak dapat memverifikasi pembayaran (403) | LULUS |
| T17b | Admin menolak bukti -> pembayaran 'rejected', pesanan kembali 'pending_payment' | LULUS |
| T17c | Customer melihat alasan penolakan & dapat unggah ulang | LULUS |
| T17d | Unggah ulang setelah ditolak berhasil | LULUS |
| T17e | Admin menyetujui -> pembayaran 'verified', pesanan 'processing'; verifier & waktu tercatat | LULUS |
| T17f | Verifikasi ulang pada pembayaran yang sudah diverifikasi ditolak | LULUS |
| T18a | Customer tidak dapat membatalkan sendiri pesanan yang sudah dibayar | LULUS |
| T18b | Operator tidak dapat membatalkan pesanan (403) | LULUS |
| T18c | Validasi nomor resi minimal 5 karakter | LULUS |
| T18d | Operator mengisi kurir & resi -> status 'shipped' | LULUS |
| T18e | Customer melihat info pengiriman (kurir & resi) | LULUS |
| T18f | Customer lain tidak dapat menyelesaikan pesanan orang lain | LULUS |
| T18g | Customer mengonfirmasi diterima -> 'completed' | LULUS |
| T18h | Pesanan 'completed' tidak dapat dibatalkan (transisi tidak valid) | LULUS |
| T18i | Setiap perubahan status tercatat di log (waktu + pengguna) | LULUS |
| T18j | Tidak ada rute hapus fisik pesanan (BR-011) | LULUS |
| T19a | Tidak bisa mengirim pesanan yang belum dibayar (state machine) | LULUS |
| T19b | Tidak bisa verifikasi tanpa bukti pembayaran | LULUS |
| T19c | Customer membatalkan pesanan 'pending_payment' -> stok dikembalikan | LULUS |
| T19d | Admin membatalkan pesanan 'processing' -> status 'cancelled' & stok kembali | LULUS |
| T20a | Operator dapat memperbarui stok | LULUS |
| T20b | Stok negatif ditolak | LULUS |
| T21a | Dashboard admin menampilkan statistik & grafik | LULUS |
| T21b | Laporan penjualan berdasarkan periode | LULUS |
| T21c | Laporan difilter berdasarkan status | LULUS |
| T21d | Ekspor laporan CSV | LULUS |
| T21e | Admin melihat daftar pelanggan | LULUS |
| T21f | Antrean pengiriman tampil | LULUS |
| T21g | Daftar pembayaran tampil | LULUS |
| T22a | Pesanan tidak ada -> 404 | LULUS |
| T22b | Injeksi SQL pada URL tidak berpengaruh (/orders/1' OR '1'='1) | LULUS |
| T22b | Injeksi SQL pada URL tidak berpengaruh (/product/' OR 1=1 --) | LULUS |
| T22c | Injeksi SQL pada pencarian aman (tidak error) | LULUS |
| T22d | Header keamanan hadir (X-Content-Type-Options / X-Frame-Options) | LULUS |
| T22e | Cookie session & CSRF diset HttpOnly | LULUS |
| T22f | Folder uploads tidak mengizinkan listing direktori | LULUS |
| T22g | Bukti pembayaran tidak dapat diakses langsung lewat URL publik | LULUS |
| T22h | Folder writable tidak terekspos | LULUS |
| T22i | .env tidak terekspos | LULUS |
| T23 | Dua checkout bersamaan untuk stok 1: tepat satu pesanan terbentuk, stok tidak negatif | LULUS |

## D. Pengujian Manual yang Disarankan

Hal berikut bersifat visual/perangkat dan perlu diperiksa manual sebelum presentasi:

- [ ] Tampilan responsif: homepage, katalog, keranjang, checkout, dan dashboard admin pada lebar 360 px (mobile), 768 px (tablet), dan desktop
- [ ] Kompatibilitas browser: Chrome/Edge (Chromium) dan Firefox
- [ ] Kemudahan penggunaan: alur belanja dari katalog hingga unggah bukti pembayaran oleh pengguna baru
- [ ] Pesan validasi terbaca dan kontras warna memadai (NFR-010)
- [ ] Backup & restore database: `mysqldump` lalu impor ulang `database/db_shopease.sql` (NFR-009)

## E. Cara Menjalankan Ulang

```bash
php spark migrate:refresh && php spark db:seed DatabaseSeeder
php spark serve
pip install requests && python3 tests/functional/run_tests.py
```
> Uji bersifat destruktif terhadap data (membuat/menghapus data uji). Jalankan pada database pengembangan, bukan produksi.
