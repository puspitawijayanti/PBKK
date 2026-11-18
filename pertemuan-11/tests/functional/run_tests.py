#!/usr/bin/env python3
"""
Uji fungsional & keamanan ShopEase (black-box via HTTP).

Cara pakai (aplikasi harus sudah jalan, database sudah di-migrate + seed):
    php spark migrate:refresh && php spark db:seed DatabaseSeeder
    php spark serve --port 8080
    python3 tests/functional/run_tests.py [BASE_URL]

Membutuhkan: Python 3, paket `requests`, dan klien `mysql` (untuk memeriksa database).
Variabel lingkungan opsional: DB_USER (default root), DB_PASS (default kosong), DB_NAME (default db_shopease).
"""
import io
import os
import re
import struct
import subprocess
import sys
import threading
import zlib

import requests

BASE = (sys.argv[1] if len(sys.argv) > 1 else "http://localhost:8080").rstrip("/")
DB_USER = os.environ.get("DB_USER", "root")
DB_PASS = os.environ.get("DB_PASS", "")
DB_NAME = os.environ.get("DB_NAME", "db_shopease")

RESULTS = []  # (id, nama, lulus, catatan)


# --------------------------------------------------------------------------- util
def sql(query):
    cmd = ["mysql", f"-u{DB_USER}", "-N", "-B", DB_NAME, "-e", query]
    if DB_PASS:
        cmd.insert(2, f"-p{DB_PASS}")
    out = subprocess.run(cmd, capture_output=True, text=True, check=True).stdout.strip()
    return out


def sql_val(query):
    out = sql(query)
    return out.split("\n")[0] if out else ""


def png_bytes(w=40, h=30, noisy=False):
    """PNG valid. noisy=True memakai piksel acak sehingga ukuran file benar-benar besar."""
    if noisy:
        raw = b"".join(b"\x00" + os.urandom(3 * w) for _ in range(h))
    else:
        raw = b"".join(b"\x00" + b"\x4f\x46\xe5" * w for _ in range(h))

    def chunk(t, d):
        c = struct.pack(">I", len(d)) + t + d
        return c + struct.pack(">I", zlib.crc32(t + d) & 0xFFFFFFFF)

    return (b"\x89PNG\r\n\x1a\n" + chunk(b"IHDR", struct.pack(">IIBBBBB", w, h, 8, 2, 0, 0, 0))
            + chunk(b"IDAT", zlib.compress(raw)) + chunk(b"IEND", b""))


class Client:
    def __init__(self, name="anon"):
        self.s = requests.Session()
        self.name = name
        self.r = None

    def get(self, path, **kw):
        self.r = self.s.get(BASE + path, allow_redirects=True, **kw)
        return self.r

    def token(self, page=None):
        html = page if page is not None else (self.r.text if self.r is not None else "")
        m = re.search(r'name="csrf_test_name"\s+value="([^"]+)"', html)
        if not m:
            m = re.search(r'value="([^"]+)"\s+name="csrf_test_name"', html)
        return m.group(1) if m else None

    def post(self, path, data=None, files=None, form_page=None, csrf=True, allow_redirects=True):
        """POST dengan token CSRF yang diambil dari halaman `form_page` (atau halaman terakhir)."""
        data = dict(data or {})
        if csrf:
            if form_page:
                self.get(form_page)
            tok = self.token()
            if tok:
                data["csrf_test_name"] = tok
        self.r = self.s.post(BASE + path, data=data, files=files, allow_redirects=allow_redirects)
        return self.r

    def login(self, email, password):
        self.get("/login")
        return self.post("/login", {"email": email, "password": password})

    def logout(self):
        self.get("/")
        return self.post("/logout")


def check(id_, name, cond, note=""):
    RESULTS.append((id_, name, bool(cond), note))
    print(f"[{'PASS' if cond else 'FAIL'}] {id_} {name}" + (f"  -> {note}" if (note and not cond) else ""))
    return bool(cond)


def alert_text(r):
    return re.sub(r"\s+", " ", re.sub(r"<[^>]+>", " ", r.text))


# --------------------------------------------------------------------------- tes
def main():
    admin, operator, c1, c2 = Client("admin"), Client("operator"), Client("c1"), Client("c2")

    # ---------------------------------------------------------------- Registrasi & login
    reg = Client("reg")
    reg.get("/register")
    r = reg.post("/register", {"name": "Tester Baru", "email": "tester.baru@shopease.test", "phone": "081234567890",
                               "password": "Rahasia123", "password_confirm": "Rahasia123"})
    pw = sql_val("SELECT password FROM users WHERE email='tester.baru@shopease.test'")
    check("T01", "Registrasi customer valid; password tersimpan sebagai hash",
          "Akun berhasil dibuat" in r.text and pw.startswith("$2y$") and "Rahasia123" not in pw, pw[:12])

    reg.get("/register")
    r = reg.post("/register", {"name": "Dobel", "email": "tester.baru@shopease.test", "password": "Rahasia123", "password_confirm": "Rahasia123"})
    check("T01b", "Registrasi dengan email yang sudah dipakai ditolak", "sudah digunakan" in r.text)

    reg.get("/register")
    r = reg.post("/register", {"name": "Lemah", "email": "lemah@shopease.test", "password": "abcdefgh", "password_confirm": "abcdefgh"})
    check("T01c", "Password tanpa angka ditolak (kebijakan password)", "huruf dan angka" in r.text
          and sql_val("SELECT COUNT(*) FROM users WHERE email='lemah@shopease.test'") == "0")

    reg.get("/register")
    r = reg.post("/register", {"name": "Hacker", "email": "hacker@shopease.test", "password": "Rahasia123",
                               "password_confirm": "Rahasia123", "role": "admin"})
    check("T01d", "Mass assignment role=admin saat registrasi diabaikan",
          sql_val("SELECT role FROM users WHERE email='hacker@shopease.test'") == "customer")

    r = admin.login("admin@shopease.test", "Admin123!")
    check("T02a", "Login admin -> dashboard admin", r.url.rstrip("/").endswith("/admin") and "Dashboard" in r.text, r.url)
    r = operator.login("operator@shopease.test", "Operator123!")
    check("T02b", "Login operator -> dashboard (menu terbatas)", r.url.rstrip("/").endswith("/admin") and "/admin/categories" not in r.text, r.url)
    r = c1.login("customer1@shopease.test", "Customer123!")
    check("T02c", "Login customer -> beranda toko", r.url.rstrip("/") == BASE and "Keranjang" in r.text, r.url)
    r = c2.login("customer2@shopease.test", "Customer123!")

    anon = Client("anon")
    anon.get("/login")
    r1 = anon.post("/login", {"email": "customer1@shopease.test", "password": "salah-banget"})
    m1 = "Email atau password salah" in r1.text
    anon.get("/login")
    r2 = anon.post("/login", {"email": "tidak.ada@shopease.test", "password": "salah-banget"})
    m2 = "Email atau password salah" in r2.text
    check("T03", "Login password salah ditolak; pesan sama untuk email tak terdaftar", m1 and m2)

    # ---------------------------------------------------------------- Otorisasi
    a = Client("anon2")
    r = a.get("/admin")
    check("T04a", "Akses /admin tanpa login -> diarahkan ke login", r.url.endswith("/login"), r.url)
    r = c1.get("/admin")
    check("T04b", "Customer membuka /admin -> 403", r.status_code == 403, r.status_code)
    r = c1.get("/admin/products")
    check("T04c", "Customer membuka /admin/products -> 403", r.status_code == 403)
    r = operator.get("/admin/products")
    check("T04d", "Operator membuka /admin/products (admin saja) -> 403", r.status_code == 403)
    r = operator.get("/admin/payments")
    check("T04e", "Operator membuka verifikasi pembayaran -> 403 (BR-007)", r.status_code == 403)
    r = admin.get("/cart")
    check("T04f", "Admin membuka /cart (khusus customer) -> 403", r.status_code == 403)
    r = operator.get("/admin/orders")
    check("T04g", "Operator boleh membuka daftar pesanan", r.status_code == 200)
    r = admin.get("/logout")
    check("T04h", "Logout lewat GET tidak diizinkan (hanya POST+CSRF)", r.status_code == 404, r.status_code)

    # ---------------------------------------------------------------- CSRF
    admin.get("/admin/categories/create")
    r = admin.post("/admin/categories", {"name": "Tanpa Token"}, csrf=False)
    check("T05", "POST tanpa token CSRF ditolak", r.status_code in (403, 400, 500)
          and sql_val("SELECT COUNT(*) FROM categories WHERE name='Tanpa Token'") == "0", r.status_code)

    # ---------------------------------------------------------------- CRUD kategori
    r = admin.post("/admin/categories", {"name": "Olahraga", "description": "Perlengkapan olahraga", "is_active": "1"},
                   form_page="/admin/categories/create")
    check("T06a", "Admin menambah kategori", "berhasil ditambahkan" in r.text
          and sql_val("SELECT slug FROM categories WHERE name='Olahraga'") == "olahraga")
    r = admin.post("/admin/categories", {"name": "Ol"}, form_page="/admin/categories/create")
    check("T06b", "Validasi kategori: nama < 3 karakter ditolak", "minimal 3 karakter" in r.text)
    r = admin.post("/admin/categories", {"name": "Olahraga"}, form_page="/admin/categories/create")
    check("T06c", "Nama kategori duplikat ditolak", "sudah digunakan" in r.text)
    cid = sql_val("SELECT id FROM categories WHERE name='Olahraga'")
    r = admin.post(f"/admin/categories/{cid}", {"name": "Olahraga & Outdoor", "description": "x", "is_active": "1"},
                   form_page=f"/admin/categories/{cid}/edit")
    check("T06d", "Admin mengubah kategori", sql_val(f"SELECT name FROM categories WHERE id={cid}") == "Olahraga & Outdoor")
    r = admin.post("/admin/categories/1/delete", form_page="/admin/categories")
    check("T06e", "Hapus kategori yang masih punya produk ditolak",
          "tidak dapat dihapus" in r.text and sql_val("SELECT COUNT(*) FROM categories WHERE id=1") == "1")
    r = admin.post(f"/admin/categories/{cid}/delete", form_page="/admin/categories")
    check("T06f", "Hapus kategori kosong berhasil", sql_val(f"SELECT COUNT(*) FROM categories WHERE id={cid}") == "0")

    # ---------------------------------------------------------------- CRUD produk + upload
    admin.get("/admin/products/create")
    r = admin.post("/admin/products",
                   {"name": "Produk Uji Upload", "category_id": "1", "description": "Deskripsi uji", "price": "99000", "stock": "7", "status": "active"},
                   files={"image": ("foto.png", png_bytes(), "image/png")}, form_page="/admin/products/create")
    row = sql("SELECT id,slug,image,stock,price FROM products WHERE name='Produk Uji Upload'")
    pid_new = row.split("\t")[0] if row else ""
    img = row.split("\t")[2] if row else ""
    check("T07a", "Admin menambah produk + upload gambar valid (nama file acak)",
          bool(row) and img.endswith(".png") and img != "foto.png" and os.path.exists(os.path.join(os.path.dirname(__file__), "..", "..", "public", "uploads", "products", img)), row)
    r = admin.get("/admin/products?q=Produk+Uji")
    check("T07b", "Produk baru tampil pada daftar admin", "Produk Uji Upload" in r.text)

    r = admin.post(f"/admin/products/{pid_new}",
                   {"name": "Produk Uji Upload", "category_id": "2", "description": "Diubah", "price": "125000", "stock": "9", "status": "active"},
                   form_page=f"/admin/products/{pid_new}/edit")
    check("T07c", "Admin mengubah data produk", sql_val(f"SELECT CONCAT(category_id,'|',price,'|',stock) FROM products WHERE id={pid_new}") == "2|125000.00|9")

    bad_cases = [
        ("T08a", "Upload .txt ditolak", ("catatan.txt", b"hello world", "text/plain")),
        ("T08b", "Upload skrip PHP (.php) ditolak", ("shell.php", b"<?php system($_GET['c']); ?>", "application/x-php")),
        ("T08c", "File PHP menyamar jadi .png ditolak (cek MIME/isi file)", ("shell.png", b"<?php system($_GET['c']); ?>", "image/png")),
        ("T08d", "Gambar GIF (format tidak diizinkan) ditolak", ("anim.gif", b"GIF89a\x01\x00\x01\x00\x80\x00\x00\x00\x00\x00\xff\xff\xff!\xf9\x04\x01\x00\x00\x00\x00,\x00\x00\x00\x00\x01\x00\x01\x00\x00\x02\x02D\x01\x00;", "image/gif")),
        ("T08e", "Gambar > 2 MB ditolak", ("besar.png", png_bytes(1100, 800, noisy=True), "image/png")),
    ]
    before = sql_val("SELECT COUNT(*) FROM products")
    for tid, tname, f in bad_cases:
        admin.get("/admin/products/create")
        r = admin.post("/admin/products", {"name": "Produk Jahat " + tid, "category_id": "1", "price": "1000", "stock": "1", "status": "active"},
                       files={"image": f}, form_page="/admin/products/create")
        check(tid, tname, sql_val(f"SELECT COUNT(*) FROM products WHERE name='Produk Jahat {tid}'") == "0"
              and ("Foto produk" in r.text or "Tipe file" in r.text or "Ekstensi" in r.text or "terlalu besar" in r.text or "gambar" in r.text), r.status_code)
    check("T08f", "Tidak ada produk tambahan tersimpan dari upload berbahaya", sql_val("SELECT COUNT(*) FROM products") == str(int(before)))

    r = admin.post("/admin/products", {"name": "Harga Negatif", "category_id": "1", "price": "-5", "stock": "1", "status": "active"}, form_page="/admin/products/create")
    check("T09a", "Validasi: harga negatif ditolak", sql_val("SELECT COUNT(*) FROM products WHERE name='Harga Negatif'") == "0")
    r = admin.post("/admin/products", {"name": "Stok Negatif", "category_id": "1", "price": "1000", "stock": "-3", "status": "active"}, form_page="/admin/products/create")
    check("T09b", "Validasi: stok negatif ditolak", sql_val("SELECT COUNT(*) FROM products WHERE name='Stok Negatif'") == "0")

    r = admin.post(f"/admin/products/{pid_new}/delete", form_page="/admin/products")
    check("T10a", "Hapus produk yang belum dipakai -> terhapus", sql_val(f"SELECT COUNT(*) FROM products WHERE id={pid_new}") == "0")
    used = sql_val("SELECT DISTINCT product_id FROM order_items LIMIT 1")
    r = admin.post(f"/admin/products/{used}/delete", form_page="/admin/products")
    check("T10b", "Hapus produk yang sudah pernah dipesan -> dinonaktifkan (riwayat aman)",
          sql_val(f"SELECT status FROM products WHERE id={used}") == "inactive" and "dinonaktifkan" in r.text)
    sql(f"UPDATE products SET status='active' WHERE id={used}")

    # ---------------------------------------------------------------- Katalog
    r = c1.get("/catalog?q=mug")
    check("T11a", "Pencarian produk", "Mug Keramik" in r.text and "Smart Watch" not in r.text)
    r = c1.get("/catalog?category=fashion&sort=price_asc")
    names = re.findall(r'class="name mb-1"[^>]*>([^<]+)<', r.text)
    check("T11b", "Filter kategori + urut harga naik", names[:1] == ["Kaos Polos Premium"] and "Smart Watch" not in r.text, names[:3])
    r = c1.get("/catalog")
    check("T11c", "Pagination tampil (8 produk/halaman)", "pagination" in r.text and len(re.findall(r'class="product-card', r.text)) == 8)
    r = c1.get("/catalog?q=Earphone")
    check("T11d", "Produk nonaktif tidak tampil di katalog (BR-002)", "Earphone Kabel Lama" not in r.text)
    r = c1.get("/product/earphone-kabel-lama")
    check("T11e", "Detail produk nonaktif -> 404", r.status_code == 404)
    r = c1.get("/catalog?q=%3Cscript%3Ealert(1)%3C%2Fscript%3E")
    check("T11f", "Input pencarian di-escape (anti XSS)", "<script>alert(1)</script>" not in r.text)

    # ---------------------------------------------------------------- Keranjang
    sql("DELETE FROM cart_items; ")
    pw_id = sql_val("SELECT id FROM products WHERE slug='wireless-headphone'")
    pw_stock0 = int(sql_val(f"SELECT stock FROM products WHERE id={pw_id}"))
    r = anon.get("/product/wireless-headphone")
    check("T12a", "Tamu melihat detail produk, tombol aksi meminta login", "Login untuk menambah" in r.text)
    r = anon.post("/cart/add", {"product_id": pw_id, "quantity": 1}, csrf=False)
    check("T12b", "Tamu tidak bisa menambah ke keranjang (redirect/blokir)", sql_val("SELECT COUNT(*) FROM cart_items") == "0")

    c1.get(f"/product/wireless-headphone")
    r = c1.post("/cart/add", {"product_id": pw_id, "quantity": 2})
    check("T12c", "Customer menambah produk ke keranjang", "ditambahkan ke keranjang" in r.text
          and sql_val(f"SELECT quantity FROM cart_items WHERE product_id={pw_id}") == "2")
    c1.get("/product/wireless-headphone")
    r = c1.post("/cart/add", {"product_id": pw_id, "quantity": 1})
    check("T12d", "Menambah produk yang sama menggabungkan kuantitas", sql_val(f"SELECT quantity FROM cart_items WHERE product_id={pw_id}") == "3")
    c1.get("/product/wireless-headphone")
    r = c1.post("/cart/add", {"product_id": pw_id, "quantity": 0})
    check("T12e", "Kuantitas 0 ditolak", sql_val(f"SELECT quantity FROM cart_items WHERE product_id={pw_id}") == "3")
    c1.get("/product/wireless-headphone")
    r = c1.post("/cart/add", {"product_id": pw_id, "quantity": 9999})
    check("T12f", "Kuantitas melebihi stok ditolak", sql_val(f"SELECT quantity FROM cart_items WHERE product_id={pw_id}") == "3")
    item_id = sql_val(f"SELECT id FROM cart_items WHERE product_id={pw_id}")
    r = c1.post(f"/cart/update/{item_id}", {"quantity": 5}, form_page="/cart")
    check("T12g", "Ubah kuantitas di keranjang", sql_val(f"SELECT quantity FROM cart_items WHERE id={item_id}") == "5")
    r = c1.post(f"/cart/update/{item_id}", {"quantity": 500}, form_page="/cart")
    check("T12h", "Ubah kuantitas > stok ditolak", sql_val(f"SELECT quantity FROM cart_items WHERE id={item_id}") == "5")
    r = c1.get("/cart")
    check("T12i", "Subtotal = harga x kuantitas (5 x 299.000 = 1.495.000)", "Rp 1.495.000" in r.text)
    r = c2.post(f"/cart/remove/{item_id}", form_page="/cart")
    check("T12j", "Customer lain tidak bisa menghapus item keranjang orang lain (404)",
          r.status_code == 404 and sql_val(f"SELECT COUNT(*) FROM cart_items WHERE id={item_id}") == "1", r.status_code)
    c1.post(f"/cart/update/{item_id}", {"quantity": 2}, form_page="/cart")

    # ---------------------------------------------------------------- Checkout
    addr1 = sql_val("SELECT id FROM addresses WHERE user_id=(SELECT id FROM users WHERE email='customer1@shopease.test') AND is_default=1")
    addr2 = sql_val("SELECT id FROM addresses WHERE user_id=(SELECT id FROM users WHERE email='customer2@shopease.test') LIMIT 1")
    stock_before = int(sql_val(f"SELECT stock FROM products WHERE id={pw_id}"))
    orders_before = int(sql_val("SELECT COUNT(*) FROM orders"))

    # manipulasi harga/total dari browser
    c1.get("/checkout")
    r = c1.post("/checkout", {"address_id": addr1, "notes": "Tes", "total": "1", "price": "1", "subtotal": "1", "shipping_cost": "0"})
    oid = sql_val("SELECT MAX(id) FROM orders")
    o = sql(f"SELECT subtotal,shipping_cost,total,order_status FROM orders WHERE id={oid}").split("\t")
    it = sql(f"SELECT product_name,price,quantity,subtotal FROM order_items WHERE order_id={oid}").split("\t")
    check("T13a", "Checkout stok cukup -> order & order_items dibuat", int(sql_val("SELECT COUNT(*) FROM orders")) == orders_before + 1 and it[0] == "Wireless Headphone", it)
    check("T13b", "Total dihitung ulang di backend (abaikan total/harga dari browser)", o[0] == "598000.00" and o[1] == "15000.00" and o[2] == "613000.00", o)
    check("T13c", "Snapshot nama & harga tersimpan di order_items", it[1] == "299000.00" and it[2] == "2" and it[3] == "598000.00", it)
    check("T13d", "Stok dikurangi saat pesanan dikonfirmasi", int(sql_val(f"SELECT stock FROM products WHERE id={pw_id}")) == stock_before - 2)
    check("T13e", "Keranjang dikosongkan setelah checkout", sql_val("SELECT COUNT(*) FROM cart_items") == "0")
    check("T13f", "Pembayaran awal 'unpaid' & status pesanan 'pending_payment'",
          sql_val(f"SELECT payment_status FROM payments WHERE order_id={oid}") == "unpaid" and o[3] == "pending_payment")

    # snapshot harga tidak berubah ketika harga katalog berubah
    sql(f"UPDATE products SET price=999999 WHERE id={pw_id}")
    r = c1.get(f"/orders/{oid}")
    check("T13g", "Harga katalog berubah tetapi riwayat pesanan tetap (snapshot)", "Rp 299.000" in r.text and "Rp 999.999" not in r.text)
    sql(f"UPDATE products SET price=299000 WHERE id={pw_id}")

    # checkout melebihi stok: tambah ke keranjang lalu stok dikurangi admin/operator
    sku = sql_val("SELECT id FROM products WHERE slug='dompet-kulit'")  # stok 3
    c1.get("/product/dompet-kulit")
    c1.post("/cart/add", {"product_id": sku, "quantity": 3})
    sql(f"UPDATE products SET stock=1 WHERE id={sku}")  # stok berubah setelah masuk keranjang
    orders_before = int(sql_val("SELECT COUNT(*) FROM orders"))
    c1.get("/checkout")
    r = c1.post("/checkout", {"address_id": addr1})
    check("T14a", "Checkout melebihi stok ditolak dengan pesan jelas", "tidak mencukupi" in r.text or "hanya tersisa" in r.text, alert_text(r)[:200])
    check("T14b", "Stok tidak berubah & tidak ada order baru", sql_val(f"SELECT stock FROM products WHERE id={sku}") == "1" and int(sql_val("SELECT COUNT(*) FROM orders")) == orders_before)
    sql("DELETE FROM cart_items")
    sql(f"UPDATE products SET stock=3 WHERE id={sku}")

    r = c1.post("/checkout", {"address_id": addr1}, form_page="/cart")
    check("T14c", "Checkout dengan keranjang kosong ditolak", "kosong" in r.text.lower())
    c1.get("/product/mug-keramik"); mug = sql_val("SELECT id FROM products WHERE slug='mug-keramik'")
    c1.post("/cart/add", {"product_id": mug, "quantity": 1})
    c1.get("/checkout")
    r = c1.post("/checkout", {"address_id": addr2})
    check("T14d", "Checkout dengan alamat milik customer lain ditolak", "Alamat pengiriman tidak valid" in r.text
          and sql_val("SELECT COUNT(*) FROM cart_items") == "1")
    r = c1.post("/checkout", {}, form_page="/checkout")
    check("T14e", "Checkout tanpa memilih alamat ditolak (validasi)", sql_val("SELECT COUNT(*) FROM cart_items") == "1")
    sql("DELETE FROM cart_items")

    # ---------------------------------------------------------------- Kepemilikan pesanan (BR-006)
    r = c2.get(f"/orders/{oid}")
    check("T15a", "Customer lain tidak dapat melihat pesanan milik orang lain (404)", r.status_code == 404, r.status_code)
    r = c1.get(f"/orders/{oid}")
    check("T15b", "Pemilik dapat melihat pesanannya", r.status_code == 200 and "Transfer sebesar" in r.text)
    r = c2.post(f"/orders/{oid}/cancel", form_page="/orders")
    check("T15c", "Customer lain tidak dapat membatalkan pesanan orang lain", sql_val(f"SELECT order_status FROM orders WHERE id={oid}") == "pending_payment")
    r = c2.post(f"/orders/{oid}/payment", files={"proof": ("b.png", png_bytes(), "image/png")}, form_page="/orders")
    check("T15d", "Customer lain tidak dapat mengunggah bukti ke pesanan orang lain", sql_val(f"SELECT payment_status FROM payments WHERE order_id={oid}") == "unpaid")

    # ---------------------------------------------------------------- Pembayaran
    r = c1.post(f"/orders/{oid}/payment", files={"proof": ("bukti.txt", b"bukan gambar", "text/plain")}, form_page=f"/orders/{oid}")
    check("T16a", "Unggah bukti bukan gambar ditolak", sql_val(f"SELECT payment_status FROM payments WHERE order_id={oid}") == "unpaid")
    r = c1.post(f"/orders/{oid}/payment", files={"proof": ("bukti.php", b"<?php echo 1;", "image/png")}, form_page=f"/orders/{oid}")
    check("T16b", "Unggah skrip PHP sebagai bukti ditolak", sql_val(f"SELECT payment_status FROM payments WHERE order_id={oid}") == "unpaid")
    r = c1.post(f"/orders/{oid}/payment", files={"proof": ("bukti.png", png_bytes(), "image/png")}, form_page=f"/orders/{oid}")
    pf = sql_val(f"SELECT proof_file FROM payments WHERE order_id={oid}")
    check("T16c", "Unggah bukti valid -> file tersimpan, status menunggu verifikasi",
          sql_val(f"SELECT payment_status FROM payments WHERE order_id={oid}") == "submitted"
          and sql_val(f"SELECT order_status FROM orders WHERE id={oid}") == "waiting_verification" and pf.endswith(".png"), pf)
    root = os.path.join(os.path.dirname(__file__), "..", "..")
    check("T16d", "Bukti pembayaran disimpan di luar folder public (writable/uploads/payments)",
          os.path.exists(os.path.join(root, "writable", "uploads", "payments", pf)) and not os.path.exists(os.path.join(root, "public", "uploads", pf)))
    r = c1.get(f"/payment/proof/{oid}")
    check("T16e", "Pemilik dapat melihat bukti pembayaran", r.status_code == 200 and r.headers.get("Content-Type", "").startswith("image/"))
    r = c2.get(f"/payment/proof/{oid}")
    check("T16f", "Customer lain tidak dapat melihat bukti pembayaran (404)", r.status_code == 404)
    r = operator.get(f"/payment/proof/{oid}")
    check("T16g", "Operator tidak dapat melihat bukti pembayaran (404)", r.status_code == 404)
    r = admin.get(f"/payment/proof/{oid}")
    check("T16h", "Admin dapat melihat bukti pembayaran", r.status_code == 200)
    r = anon.get(f"/payment/proof/{oid}")
    check("T16i", "Tamu tidak dapat melihat bukti pembayaran", r.url.endswith("/login"))
    r = c1.post(f"/orders/{oid}/payment", files={"proof": ("bukti2.png", png_bytes(), "image/png")}, form_page=f"/orders/{oid}")
    check("T16j", "Unggah ulang saat menunggu verifikasi ditolak", pf == sql_val(f"SELECT proof_file FROM payments WHERE order_id={oid}"))

    # ---------------------------------------------------------------- Verifikasi
    r = operator.post(f"/admin/payments/{oid}/verify", form_page=f"/admin/orders/{oid}")
    check("T17a", "Operator tidak dapat memverifikasi pembayaran (403)", r.status_code == 403
          and sql_val(f"SELECT payment_status FROM payments WHERE order_id={oid}") == "submitted")
    r = admin.post(f"/admin/payments/{oid}/reject", {"reason": "Nominal tidak sesuai"}, form_page=f"/admin/orders/{oid}")
    check("T17b", "Admin menolak bukti -> pembayaran 'rejected', pesanan kembali 'pending_payment'",
          sql_val(f"SELECT payment_status FROM payments WHERE order_id={oid}") == "rejected"
          and sql_val(f"SELECT order_status FROM orders WHERE id={oid}") == "pending_payment")
    r = c1.get(f"/orders/{oid}")
    check("T17c", "Customer melihat alasan penolakan & dapat unggah ulang", "Nominal tidak sesuai" in r.text and 'name="proof"' in r.text)
    r = c1.post(f"/orders/{oid}/payment", files={"proof": ("ulang.png", png_bytes(), "image/png")}, form_page=f"/orders/{oid}")
    check("T17d", "Unggah ulang setelah ditolak berhasil", sql_val(f"SELECT payment_status FROM payments WHERE order_id={oid}") == "submitted")
    r = admin.post(f"/admin/payments/{oid}/verify", form_page=f"/admin/orders/{oid}")
    check("T17e", "Admin menyetujui -> pembayaran 'verified', pesanan 'processing'; verifier & waktu tercatat",
          sql_val(f"SELECT payment_status FROM payments WHERE order_id={oid}") == "verified"
          and sql_val(f"SELECT order_status FROM orders WHERE id={oid}") == "processing"
          and sql_val(f"SELECT COUNT(*) FROM payments WHERE order_id={oid} AND verified_by IS NOT NULL AND verified_at IS NOT NULL") == "1")
    r = admin.post(f"/admin/payments/{oid}/verify", form_page=f"/admin/orders/{oid}")
    check("T17f", "Verifikasi ulang pada pembayaran yang sudah diverifikasi ditolak", "Tidak ada bukti pembayaran" in r.text)

    # ---------------------------------------------------------------- Pengiriman & status
    r = c1.post(f"/orders/{oid}/cancel", form_page=f"/orders/{oid}")
    check("T18a", "Customer tidak dapat membatalkan sendiri pesanan yang sudah dibayar", sql_val(f"SELECT order_status FROM orders WHERE id={oid}") == "processing")
    r = operator.post(f"/admin/orders/{oid}/cancel", form_page=f"/admin/orders/{oid}")
    check("T18b", "Operator tidak dapat membatalkan pesanan (403)", r.status_code == 403 and sql_val(f"SELECT order_status FROM orders WHERE id={oid}") == "processing")
    r = operator.post(f"/admin/orders/{oid}/ship", {"courier": "JNE", "tracking_number": "x"}, form_page=f"/admin/orders/{oid}")
    check("T18c", "Validasi nomor resi minimal 5 karakter", sql_val(f"SELECT order_status FROM orders WHERE id={oid}") == "processing")
    r = operator.post(f"/admin/orders/{oid}/ship", {"courier": "JNE", "tracking_number": "JNE1234567890"}, form_page=f"/admin/orders/{oid}")
    check("T18d", "Operator mengisi kurir & resi -> status 'shipped'",
          sql_val(f"SELECT order_status FROM orders WHERE id={oid}") == "shipped"
          and sql_val(f"SELECT CONCAT(courier,'|',tracking_number,'|',shipment_status) FROM shipments WHERE order_id={oid}") == "JNE|JNE1234567890|shipped")
    r = c1.get(f"/orders/{oid}")
    check("T18e", "Customer melihat info pengiriman (kurir & resi)", "JNE1234567890" in r.text and "Pesanan Sudah Diterima" in r.text)
    r = c2.post(f"/orders/{oid}/received", form_page="/orders")
    check("T18f", "Customer lain tidak dapat menyelesaikan pesanan orang lain", sql_val(f"SELECT order_status FROM orders WHERE id={oid}") == "shipped")
    r = c1.post(f"/orders/{oid}/received", form_page=f"/orders/{oid}")
    check("T18g", "Customer mengonfirmasi diterima -> 'completed'", sql_val(f"SELECT order_status FROM orders WHERE id={oid}") == "completed"
          and sql_val(f"SELECT shipment_status FROM shipments WHERE order_id={oid}") == "delivered")
    r = admin.post(f"/admin/orders/{oid}/cancel", form_page=f"/admin/orders/{oid}")
    check("T18h", "Pesanan 'completed' tidak dapat dibatalkan (transisi tidak valid)", sql_val(f"SELECT order_status FROM orders WHERE id={oid}") == "completed")
    check("T18i", "Setiap perubahan status tercatat di log (waktu + pengguna)",
          int(sql_val(f"SELECT COUNT(*) FROM order_status_logs WHERE order_id={oid} AND changed_by IS NOT NULL AND created_at IS NOT NULL")) >= 7)
    check("T18j", "Tidak ada rute hapus fisik pesanan (BR-011)", admin.post(f"/admin/orders/{oid}/delete", form_page="/admin/orders").status_code == 404)

    # transisi tidak valid: kirim pesanan yang masih pending
    c2.get("/product/mug-keramik")
    c2.post("/cart/add", {"product_id": mug, "quantity": 2})
    c2.get("/checkout")
    c2.post("/checkout", {"address_id": addr2})
    oid2 = sql_val("SELECT MAX(id) FROM orders")
    mug_stock_after_order = int(sql_val(f"SELECT stock FROM products WHERE id={mug}"))
    r = admin.post(f"/admin/orders/{oid2}/ship", {"courier": "JNE", "tracking_number": "JNE55555"}, form_page=f"/admin/orders/{oid2}")
    check("T19a", "Tidak bisa mengirim pesanan yang belum dibayar (state machine)", sql_val(f"SELECT order_status FROM orders WHERE id={oid2}") == "pending_payment"
          and sql_val(f"SELECT COUNT(*) FROM shipments WHERE order_id={oid2}") == "0")
    r = admin.post(f"/admin/payments/{oid2}/verify", form_page=f"/admin/orders/{oid2}")
    check("T19b", "Tidak bisa verifikasi tanpa bukti pembayaran", sql_val(f"SELECT payment_status FROM payments WHERE order_id={oid2}") == "unpaid")
    r = c2.post(f"/orders/{oid2}/cancel", form_page=f"/orders/{oid2}")
    check("T19c", "Customer membatalkan pesanan 'pending_payment' -> stok dikembalikan",
          sql_val(f"SELECT order_status FROM orders WHERE id={oid2}") == "cancelled"
          and int(sql_val(f"SELECT stock FROM products WHERE id={mug}")) == mug_stock_after_order + 2)

    # admin cancel pesanan yang sudah 'processing' -> stok kembali
    c1.get("/product/smart-watch"); sw = sql_val("SELECT id FROM products WHERE slug='smart-watch'")
    sw_before = int(sql_val(f"SELECT stock FROM products WHERE id={sw}"))
    c1.post("/cart/add", {"product_id": sw, "quantity": 1})
    c1.get("/checkout"); c1.post("/checkout", {"address_id": addr1})
    oid3 = sql_val("SELECT MAX(id) FROM orders")
    c1.post(f"/orders/{oid3}/payment", files={"proof": ("p.png", png_bytes(), "image/png")}, form_page=f"/orders/{oid3}")
    admin.post(f"/admin/payments/{oid3}/verify", form_page=f"/admin/orders/{oid3}")
    admin.post(f"/admin/orders/{oid3}/cancel", {"reason": "Stok rusak"}, form_page=f"/admin/orders/{oid3}")
    check("T19d", "Admin membatalkan pesanan 'processing' -> status 'cancelled' & stok kembali",
          sql_val(f"SELECT order_status FROM orders WHERE id={oid3}") == "cancelled" and int(sql_val(f"SELECT stock FROM products WHERE id={sw}")) == sw_before)

    # ---------------------------------------------------------------- Stok oleh operator
    r = operator.post(f"/admin/stock/{mug}", {"stock": "77"}, form_page="/admin/stock")
    check("T20a", "Operator dapat memperbarui stok", sql_val(f"SELECT stock FROM products WHERE id={mug}") == "77")
    r = operator.post(f"/admin/stock/{mug}", {"stock": "-5"}, form_page="/admin/stock")
    check("T20b", "Stok negatif ditolak", sql_val(f"SELECT stock FROM products WHERE id={mug}") == "77")

    # ---------------------------------------------------------------- Dashboard & laporan & pelanggan
    r = admin.get("/admin")
    check("T21a", "Dashboard admin menampilkan statistik & grafik", all(x in r.text for x in ["Total Produk", "Total Penjualan", "salesChart", "statusChart"]))
    r = admin.get("/admin/reports?from=2026-01-01&to=2026-12-31")
    check("T21b", "Laporan penjualan berdasarkan periode", "Laporan Penjualan" in r.text and "Rekap Harian" in r.text)
    r = admin.get("/admin/reports?from=2026-01-01&to=2026-12-31&status=completed")
    check("T21c", "Laporan difilter berdasarkan status", "Selesai" in r.text)
    r = admin.get("/admin/reports?from=2026-01-01&to=2026-12-31&export=csv")
    check("T21d", "Ekspor laporan CSV", r.headers.get("Content-Type", "").startswith("text/csv") and "No. Pesanan" in r.text)
    r = admin.get("/admin/customers")
    check("T21e", "Admin melihat daftar pelanggan", "Budi Santoso" in r.text and "admin@shopease.test" not in r.text)
    r = admin.get("/admin/shipments")
    check("T21f", "Antrean pengiriman tampil", r.status_code == 200)
    r = admin.get("/admin/payments?status=all")
    check("T21g", "Daftar pembayaran tampil", r.status_code == 200 and "Verifikasi Pembayaran" in r.text)

    # ---------------------------------------------------------------- Keamanan lain
    r = c1.get("/orders/999999")
    check("T22a", "Pesanan tidak ada -> 404", r.status_code == 404)
    for url in ["/orders/1' OR '1'='1", "/product/' OR 1=1 --"]:
        r = c1.get(url)
        check("T22b", f"Injeksi SQL pada URL tidak berpengaruh ({url[:20]})", r.status_code in (400, 404) and "SQL" not in r.text and "mysqli" not in r.text.lower(), r.status_code)
    r = c1.get("/catalog?q=" + "%27%20OR%201%3D1%20--")
    check("T22c", "Injeksi SQL pada pencarian aman (tidak error)", r.status_code == 200 and "Database" not in r.text)
    hdr = c1.get("/").headers
    check("T22d", "Header keamanan hadir (X-Content-Type-Options / X-Frame-Options)", "nosniff" in hdr.get("X-Content-Type-Options", "").lower() and hdr.get("X-Frame-Options"))
    raw = requests.get(BASE + "/login")
    cookies = raw.raw.headers.getlist("Set-Cookie")
    check("T22e", "Cookie session & CSRF diset HttpOnly", cookies and all("httponly" in c.lower() for c in cookies), str(cookies)[:200])
    r = requests.get(BASE + "/uploads/products/index.html")
    check("T22f", "Folder uploads tidak mengizinkan listing direktori", "Index of" not in r.text)
    r = requests.get(BASE + "/payment/proof/1")
    check("T22g", "Bukti pembayaran tidak dapat diakses langsung lewat URL publik", "image" not in r.headers.get("Content-Type", ""))
    r = requests.get(BASE + "/writable/uploads/payments/" + pf)
    check("T22h", "Folder writable tidak terekspos", r.status_code == 404)
    r = requests.get(BASE + "/.env")
    check("T22i", ".env tidak terekspos", r.status_code == 404)

    # ---------------------------------------------------------------- Race condition
    race_stock()

    # ---------------------------------------------------------------- Ringkasan
    passed = sum(1 for r in RESULTS if r[2])
    print(f"\n=== {passed}/{len(RESULTS)} pengujian lulus ===")
    for r in RESULTS:
        if not r[2]:
            print("GAGAL:", r[0], r[1], r[3])
    out = os.path.join(os.path.dirname(__file__), "hasil_pengujian.tsv")
    with open(out, "w", encoding="utf-8") as f:
        f.write("ID\tSkenario\tHasil\n")
        for r in RESULTS:
            f.write(f"{r[0]}\t{r[1]}\t{'LULUS' if r[2] else 'GAGAL'}\n")
    sys.exit(0 if passed == len(RESULTS) else 1)


def race_stock():
    """Dua customer checkout bersamaan untuk produk dengan stok tinggal 1 -> hanya satu yang berhasil."""
    pid = sql_val("SELECT id FROM products WHERE slug='kacamata-hitam'")
    sql(f"UPDATE products SET stock=1 WHERE id={pid}")
    sql("DELETE FROM cart_items")
    users = [("customer1@shopease.test", "Customer123!"), ("customer2@shopease.test", "Customer123!")]
    clients = []
    for email, pw in users:
        c = Client(email)
        c.login(email, pw)
        c.get(f"/product/kacamata-hitam")
        c.post("/cart/add", {"product_id": pid, "quantity": 1})
        c.get("/checkout")
        addr = re.search(r'name="address_id"[^>]*value="(\d+)"', c.r.text) or re.search(r'value="(\d+)"[^>]*name="address_id"', c.r.text)
        clients.append((c, addr.group(1), c.token()))
    orders_before = int(sql_val("SELECT COUNT(*) FROM orders"))
    barrier = threading.Barrier(2)
    res = []

    def go(c, addr, tok):
        barrier.wait()
        r = c.s.post(BASE + "/checkout", data={"address_id": addr, "csrf_test_name": tok}, allow_redirects=True)
        res.append(r)

    ts = [threading.Thread(target=go, args=a) for a in clients]
    [t.start() for t in ts]
    [t.join() for t in ts]
    created = int(sql_val("SELECT COUNT(*) FROM orders")) - orders_before
    stock = int(sql_val(f"SELECT stock FROM products WHERE id={pid}"))
    check("T23", "Dua checkout bersamaan untuk stok 1: tepat satu pesanan terbentuk, stok tidak negatif", created == 1 and stock == 0, f"orders+={created}, stock={stock}")


if __name__ == "__main__":
    main()
