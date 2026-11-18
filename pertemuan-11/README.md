# Pertemuan 11 · Toko Online ShopEase (CodeIgniter 4 + MySQL)

| Identitas | |
|---|---|
| Nama | Puspita Wijayanti Kusuma |
| NRP | 5025241059 |
| Kelas | PBKK C |

Lanjutan dari landing page pertemuan 10. Tugas kali ini membangun aplikasi toko online **ShopEase** secara utuh sesuai Studi Kasus, PRD, dan Hands-On Lab: katalog, keranjang, checkout, pembayaran manual dengan unggah bukti transfer, pengiriman, dashboard, dan laporan. Aplikasi punya tiga role: **admin**, **operator**, dan **customer**.

| Komponen | Teknologi |
|---|---|
| Framework | CodeIgniter 4.7 (PHP 8.2+) |
| Database | MySQL / MariaDB |
| Frontend | Bootstrap 5, Chart.js (aset lokal, tanpa CDN) |
| Arsitektur | MVC + lapisan *service* (`OrderService`) untuk logika transaksi |

---

## 1. Menjalankan Aplikasi

```bash
cd pertemuan-11
composer install
cp env .env        # lalu sesuaikan app.baseURL dan koneksi database
mysql -u root -e "CREATE DATABASE db_shopease CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
php spark migrate
php spark db:seed DatabaseSeeder
php spark serve --port 8085
```

Isi `.env` yang dipakai:

```ini
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost:8085/'

database.default.hostname = 127.0.0.1
database.default.database = db_shopease
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.port = 3306
```

Sebagai alternatif `migrate` dan `seed`, file `database/db_shopease.sql` (struktur + data awal) bisa diimpor lewat phpMyAdmin.

![Migrasi dan seeder](images/00a-migrate-seed.png)

*Tujuh migrasi membuat 11 tabel. Seeder mengisi akun demo, 4 kategori, 17 produk, alamat, dan 8 pesanan contoh dengan berbagai status supaya dashboard langsung terisi.*

![Server pengembangan](images/00b-serve.png)

*Server dijalankan di port 8085 karena port 8080 sudah dipakai layanan lain di laptop.*

### Akun demo (data fiktif)

| Role | Email | Password |
|---|---|---|
| Administrator | admin@shopease.test | `Admin123!` |
| Operator | operator@shopease.test | `Operator123!` |
| Customer 1 | customer1@shopease.test | `Customer123!` |
| Customer 2 | customer2@shopease.test | `Customer123!` |

## 2. Fitur dan Aturan Bisnis

| Role | Hak akses |
|---|---|
| Customer | Registrasi/login, katalog (cari, filter, urut, pagination), keranjang, alamat, checkout, unggah bukti bayar, riwayat pesanan, batalkan pesanan yang belum dibayar, konfirmasi pesanan diterima |
| Operator | Lihat pesanan, isi kurir dan nomor resi, tandai selesai, perbarui stok |
| Admin | Semua fitur operator, ditambah CRUD kategori dan produk, data pelanggan, verifikasi/tolak pembayaran, batalkan pesanan, dashboard, laporan + ekspor CSV |

Status pesanan dan status pembayaran disimpan sebagai dua atribut terpisah (BR-008). Setiap transisi divalidasi di backend (`OrderModel::TRANSITIONS`):

```
pending_payment --(unggah bukti)--> waiting_verification --(admin setujui)--> processing --(kurir+resi)--> shipped --> completed
      |  ^-------------(admin tolak bukti)--------------+                       |
      +--> cancelled <-------- (customer / admin) ------+-----------------------+
```

- **Stok (BR-009):** stok dikurangi saat pesanan dibuat, di dalam *database transaction* dengan `UPDATE ... WHERE stock >= qty`. Kalau pesanan dibatalkan, stok dikembalikan.
- **Total dihitung ulang di backend (BR-005):** harga dan total yang dikirim browser diabaikan.
- **Snapshot (BR-004):** `order_items` menyimpan nama dan harga saat transaksi, sedangkan alamat disalin ke `orders.shipping_address`.
- **Tanpa hapus fisik (BR-011):** produk yang pernah dipesan hanya dinonaktifkan, dan pesanan tidak bisa dihapus.
- **Log status (BR-012):** setiap perubahan status tercatat di `order_status_logs` (waktu, pengguna, catatan).

## 3. Dokumentasi Skenario

Semua skenario dijalankan lewat browser (Microsoft Edge, 1366×768) secara berurutan terhadap database hasil seed, menggunakan skrip Playwright. Hasil verifikasi database dicatat pada keterangan tiap gambar.

### 3.1 Halaman publik

![Beranda](images/01-home.png)

*Beranda untuk tamu: banner, daftar kategori, produk terbaru, dan produk unggulan. Produk dengan stok 0 diberi label "Stok habis".*

![Katalog dengan filter](images/02-katalog-filter.png)

*Katalog difilter kategori Elektronik dan diurutkan dari harga terendah.*

![Pencarian katalog](images/02b-katalog-cari.png)

*Pencarian dengan kata kunci "kaos".*

![Detail produk untuk tamu](images/03-detail-produk-tamu.png)

*Tamu bisa melihat detail produk, tetapi tombol aksinya berubah menjadi "Login untuk menambah ke keranjang".*

### 3.2 Skenario 1 – Registrasi customer

![Password lemah ditolak](images/04a-registrasi-password-lemah.png)

*Password `abcdefgh` ditolak karena kebijakan password mewajibkan minimal 8 karakter berisi huruf dan angka.*

![Form registrasi](images/04b-registrasi-form.png)

*Form registrasi diisi dengan data yang valid.*

![Registrasi berhasil](images/04c-registrasi-berhasil.png)

*Akun berhasil dibuat lalu diarahkan ke halaman login. Di database, password tersimpan sebagai hash bcrypt (`$2y$12$...`), bukan teks asli.*

### 3.3 Skenario 2 & 3 – Login

![Login password salah](images/05-login-password-salah.png)

*Skenario 3: login dengan password salah ditolak. Pesannya ("Email atau password salah") sama dengan pesan untuk email yang tidak terdaftar, sehingga tidak membocorkan email mana yang terdaftar.*

![Login customer](images/06a-login-customer.png)

*Skenario 2: customer yang berhasil login diarahkan ke beranda toko. Menu Keranjang dan nama pengguna muncul di navbar.*

![Login admin](images/06b-login-admin-dashboard.png)

*Admin diarahkan ke dashboard dengan menu lengkap (transaksi, katalog, pelanggan, laporan).*

![Login operator](images/06c-login-operator-dashboard.png)

*Operator masuk ke dashboard yang sama, tetapi menunya terbatas pada Pesanan, Pengiriman, dan Stok.*

### 3.4 Skenario 8 – Menambahkan produk ke keranjang

![Detail produk customer](images/07a-detail-produk-customer.png)

*Customer memilih 2 Wireless Headphone lalu menekan "Tambah ke Keranjang".*

![Melebihi stok](images/07b-tambah-keranjang-melebihi-stok.png)

*Menambahkan 10 Dompet Kulit padahal stoknya 3 ditolak oleh server. Validasi `max` di browser sengaja dilewati untuk menguji validasi di backend.*

![Keranjang](images/07c-keranjang.png)

*Keranjang berisi Wireless Headphone ×2 dan Mug Keramik ×1. Subtotal Rp 643.000 ditambah ongkos kirim tetap Rp 15.000.*

### 3.5 Skenario 9 – Checkout dengan stok mencukupi

![Halaman checkout](images/08a-checkout.png)

*Customer memilih alamat pengiriman dan melihat ringkasan pesanan.*

![Checkout berhasil](images/08b-checkout-berhasil.png)

*Pesanan dibuat dengan status "Menunggu Pembayaran" dan pembayaran "Belum Dibayar". Di database, total Rp 658.000, stok Wireless Headphone turun dari 19 menjadi 17 dan Mug Keramik dari 47 menjadi 46, lalu keranjang dikosongkan.*

### 3.6 Skenario 11 – Mengunggah bukti pembayaran

![Bukti bukan gambar](images/09a-upload-bukti-bukan-gambar.png)

*File `.txt` yang diunggah sebagai bukti ditolak.*

![Bukti berhasil diunggah](images/09b-upload-bukti-berhasil.png)

*Bukti transfer PNG diterima. Status pesanan menjadi "Menunggu Verifikasi". File disimpan dengan nama acak di `writable/uploads/payments` (di luar folder `public/`).*

### 3.7 Skenario 10 – Checkout melebihi stok

![Operator ubah stok](images/10a-operator-ubah-stok.png)

*Customer 2 sudah memasukkan 3 Dompet Kulit ke keranjang. Setelah itu operator mengubah stoknya menjadi 1.*

![Checkout stok kurang](images/10b-checkout-stok-kurang.png)

*Halaman checkout langsung memberi peringatan dan menonaktifkan tombol "Buat Pesanan".*

![Checkout ditolak](images/10c-checkout-stok-kurang-ditolak.png)

*Tombol diaktifkan paksa lewat DevTools lalu form dikirim, tetapi server tetap menolak. Jumlah pesanan tetap 9 dan stok Dompet Kulit tetap 1.*

### 3.8 Pembatalan oleh customer

![Customer batalkan pesanan](images/11-customer-batalkan-pesanan.png)

*Customer membatalkan pesanan yang masih "Menunggu Pembayaran". Status menjadi "Dibatalkan", stok dikembalikan, dan riwayatnya tercatat.*

### 3.9 Skenario 4–7 – Kelola kategori dan produk (admin)

![Tambah kategori](images/12a-admin-tambah-kategori.png)

*Admin menambah kategori "Olahraga". Slug `olahraga` dibuat otomatis.*

![Hapus kategori berisi produk](images/12b-hapus-kategori-berisi-produk.png)

*Kategori yang masih memiliki produk tidak bisa dihapus.*

![Form tambah produk](images/13a-admin-form-tambah-produk.png)

*Skenario 4: form tambah produk "Matras Yoga" beserta foto PNG.*

![Produk tersimpan](images/13b-admin-produk-tersimpan.png)

*Produk tersimpan dan muncul di urutan teratas daftar. Foto disimpan dengan nama file acak.*

![Ubah produk](images/14-admin-ubah-produk.png)

*Skenario 5: harga diubah dari Rp 185.000 menjadi Rp 175.000 dan stok dari 20 menjadi 25.*

![Produk tampil di toko](images/13c-produk-baru-di-toko.png)

*Produk baru langsung tampil di toko dengan data yang sudah diperbarui.*

![Upload PHP menyamar PNG](images/15a-upload-php-menyamar-png.png)

*Skenario 7: skrip PHP yang diberi nama `shell.png` ditolak karena tipe MIME aslinya diperiksa, bukan hanya ekstensinya.*

![Upload txt ditolak](images/15b-upload-txt-ditolak.png)

*File `.txt` sebagai foto produk juga ditolak, dan produk tidak tersimpan.*

![Hapus produk belum dipesan](images/16a-hapus-produk-belum-dipesan.png)

*Skenario 6: produk yang belum pernah dipesan (Matras Yoga) dihapus dari database.*

![Hapus produk sudah dipesan](images/16b-hapus-produk-sudah-dipesan.png)

*Produk yang sudah pernah dipesan (Botol Minum Stainless) tidak dihapus, melainkan dinonaktifkan agar riwayat transaksi tetap utuh.*

### 3.10 Skenario 12 – Verifikasi pembayaran (admin)

![Daftar pembayaran](images/17a-admin-daftar-pembayaran.png)

*Daftar pembayaran yang menunggu verifikasi.*

![Tolak pembayaran](images/17b-admin-tolak-pembayaran.png)

*Admin menolak bukti pembayaran beserta alasannya. Pembayaran berstatus "Ditolak" dan pesanan kembali ke "Menunggu Pembayaran".*

![Customer lihat penolakan](images/17c-customer-lihat-penolakan.png)

*Customer melihat alasan penolakan dan bisa mengunggah bukti ulang.*

![Detail pesanan dengan bukti](images/17d-admin-detail-pesanan-bukti.png)

*Detail pesanan di panel admin, lengkap dengan bukti transfer dan tombol "Setujui Pembayaran".*

![Lihat bukti](images/17e-admin-lihat-bukti.png)

*Bukti hanya bisa dibuka lewat controller yang memeriksa hak akses (`/payment/proof/{id}`).*

![Setujui pembayaran](images/17f-admin-setujui-pembayaran.png)

*Setelah disetujui, pembayaran menjadi "Terverifikasi" dan pesanan "Diproses". ID admin yang memverifikasi dan waktunya tercatat di tabel `payments`.*

### 3.11 Pengiriman sampai pesanan selesai

![Operator kirim pesanan](images/18a-operator-kirim-pesanan.png)

*Operator mengisi kurir JNE dan nomor resi, lalu status pesanan berubah menjadi "Dikirim".*

![Antrean pengiriman](images/18b-antrean-pengiriman.png)

*Halaman antrean pengiriman untuk operator.*

![Customer lihat resi](images/18c-customer-lihat-resi.png)

*Customer melihat kurir dan nomor resi.*

![Pesanan diterima](images/18d-customer-pesanan-diterima.png)

*Customer menekan "Pesanan Sudah Diterima" sehingga status menjadi "Selesai". Tabel `order_status_logs` mencatat lima entri: pesanan dibuat (`pending_payment`), lalu `waiting_verification → processing → shipped → completed`. Pelakunya berturut-turut customer, customer, admin, operator, dan customer.*

![Riwayat pesanan](images/18e-riwayat-pesanan.png)

*Riwayat pesanan customer.*

![Admin batalkan pesanan](images/19-admin-batalkan-pesanan.png)

*Admin membatalkan pesanan yang sedang diproses, dan stok dikembalikan.*

### 3.12 Pengujian keamanan

| Gambar | Uji | Hasil |
|---|---|---|
| <img src="images/20a-tamu-akses-admin.png" width="360"> | Tamu membuka `/admin` | Diarahkan ke halaman login |
| <img src="images/20b-customer-akses-admin-403.png" width="360"> | Customer membuka `/admin` | 403 |
| <img src="images/20c-operator-akses-pembayaran-403.png" width="360"> | Operator membuka verifikasi pembayaran (BR-007) | 403 |
| <img src="images/20d-customer-lain-buka-pesanan-404.png" width="360"> | Customer 2 membuka pesanan milik Customer 1 | 404 |
| <img src="images/20e-customer-lain-buka-bukti-404.png" width="360"> | Customer 2 membuka bukti bayar milik Customer 1 | 404 |
| <img src="images/20f-xss-pencarian-di-escape.png" width="360"> | Pencarian `<script>alert(1)</script>` | Ditampilkan sebagai teks (di-escape) |
| <img src="images/20g-env-tidak-terekspos.png" width="360"> | Membuka `/.env` | 404, karena *document root* adalah `public/` |

### 3.13 Halaman admin lainnya

![Dashboard admin](images/21a-dashboard-admin.png)

*Dashboard: statistik, grafik penjualan 14 hari, distribusi status pesanan (Chart.js), pesanan terbaru, dan produk terlaris.*

![Laporan penjualan](images/21b-laporan-penjualan.png)

*Laporan penjualan per periode, dengan rekap harian dan tombol "Unduh CSV".*

![Laporan filter status](images/21c-laporan-filter-status.png)

*Laporan yang difilter status "Selesai".*

![Data pelanggan](images/21d-data-pelanggan.png)

*Data pelanggan dengan jumlah pesanan dan total belanja.*

![Detail pelanggan](images/21e-detail-pelanggan.png)

*Detail pelanggan: informasi akun, alamat, dan riwayat pesanan.*

![Daftar pesanan](images/21f-daftar-pesanan.png)

*Daftar seluruh pesanan beserta status pembayaran dan status pesanan.*

![Kategori](images/21g-kategori.png)

*Daftar kategori.*

![Produk](images/21h-produk.png)

*Daftar produk dengan filter kategori dan status.*

![Stok](images/21i-stok.png)

*Manajemen stok oleh operator. Stok 0 ditandai merah, stok menipis ditandai kuning.*

![Alamat](images/22a-alamat.png)

*Daftar alamat pengiriman milik customer.*

![Alamat ditambahkan](images/22b-alamat-ditambahkan.png)

*Customer menambah alamat baru.*

## 4. Pengujian Otomatis

`tests/functional/run_tests.py` menjalankan 118 uji *black-box* lewat HTTP dan memverifikasinya langsung ke database. Cakupannya 12 skenario studi kasus, uji keamanan, dan uji *race condition* (dua checkout bersamaan untuk stok 1). Daftar lengkapnya ada di [`docs/PENGUJIAN.md`](docs/PENGUJIAN.md).

```bash
php spark migrate:refresh -f && php spark db:seed DatabaseSeeder
pip install requests
DB_NAME=db_shopease python tests/functional/run_tests.py http://localhost:8085
```

![Hasil uji otomatis](images/00c-uji-otomatis.png)

*Hasil di laptop (mode development): **116/118 lulus**.*

Kedua uji T22b yang gagal sebenarnya sudah ditolak dengan benar. CodeIgniter mengembalikan `400 Bad Request` karena karakter `'` tidak diizinkan di URI. Uji tetap dianggap gagal karena halaman debug di mode development menampilkan isi variabel environment, termasuk teks `MySQLi`, yang dicari oleh skrip uji. Di mode production, URL yang sama menghasilkan `400` dengan body kosong.

![Hasil uji mode production](images/00d-uji-otomatis-production.png)

*Hasil di mode production: **115/118 lulus**. T22b lulus, tetapi tiga uji lain gagal karena faktor lingkungan:*

- **T05 (CSRF):** di production, `Config\Security::$redirect` bernilai `true`, sehingga request tanpa token CSRF di-*redirect* kembali, bukan dijawab 403. Skrip uji mengikuti redirect dan menerima 200. Proteksinya tetap bekerja: kategori "Tanpa Token" tidak tersimpan di database.
- **T08b/T08c (upload skrip PHP):** Windows Defender mengkarantina file temp upload yang berisi `<?php system($_GET['c']); ?>` sebelum CodeIgniter sempat memeriksanya, sehingga `finfo_file()` gagal dan server mengembalikan 500. Hal ini terlihat dari log aplikasi dan riwayat deteksi Defender, yang menunjuk file temp yang sama pada detik yang sama. Penolakan file PHP menyamar sebagai PNG tetap terbukti di skenario 3.9 (gambar 15a), yang menggunakan isi PHP yang tidak dianggap berbahaya oleh antivirus.

## 5. Struktur Proyek

```
app/
├── Config/            Routes.php, Filters.php, Shop.php (ongkir, batas upload), ...
├── Controllers/       Home, Auth, Product, Cart, Checkout, Order, Address, Payment
│   └── Admin/         Dashboard, Category, Product, Stock, Customer, Order, Payment, Shipment, Report
├── Database/
│   ├── Migrations/    users, categories, products, carts, cart_items, addresses,
│   │                  orders, order_items, order_status_logs, payments, shipments
│   └── Seeds/         User, Category, Product, Address, SampleOrder
├── Filters/AuthFilter.php        otorisasi berbasis role, mis. auth:admin,operator
├── Helpers/shopease_helper.php   rupiah(), badge status, field_error()
├── Libraries/         OrderService (transaksi), FileUploader, OrderException
├── Models/            11 model sesuai tabel
└── Views/             layouts/, partials/, frontend/, auth/, admin/, errors/
public/uploads/products/          foto produk
writable/uploads/payments/        bukti pembayaran (privat)
database/db_shopease.sql          struktur + data awal
docs/                             ERD.md, PENGUJIAN.md
tests/functional/run_tests.py     uji fungsional & keamanan (118 uji)
```

ERD dalam format Mermaid tersedia di [`docs/ERD.md`](docs/ERD.md).

## 6. Keamanan yang Diterapkan

- Password di-hash dengan `password_hash`, dan percobaan login dibatasi (*throttling*). Session dibuat ulang saat login, dan logout hanya lewat POST + CSRF.
- CSRF aktif global, ada validasi di server, `esc()` pada semua output, dan Query Builder untuk mencegah SQL injection.
- Role diperiksa ulang ke database pada setiap request (`AuthFilter`). Customer hanya bisa mengakses data miliknya sendiri; data milik orang lain menghasilkan 404.
- Upload divalidasi ukurannya (≤ 2 MB), ekstensinya, dan tipe MIME aslinya, lalu disimpan dengan nama acak. Bukti pembayaran disimpan di luar `public/`, dan eksekusi skrip di folder upload dimatikan.
- Header keamanan (`secureheaders`) aktif, dan ekspor CSV dilindungi dari *formula injection*.
