# ERD ShopEase (db_shopease)

Diagram di bawah ditulis dengan Mermaid (tampil otomatis di GitHub/VS Code dengan ekstensi Mermaid).
Dapat diekspor ke PNG/PDF melalui https://mermaid.live untuk dikumpulkan sebagai deliverable ERD.

```mermaid
erDiagram
    users ||--o{ orders : "membuat"
    users ||--o| carts : "memiliki"
    users ||--o{ addresses : "menyimpan"
    users ||--o{ payments : "memverifikasi (verified_by)"
    users ||--o{ order_status_logs : "mengubah status (changed_by)"
    categories ||--o{ products : "berisi"
    carts ||--o{ cart_items : "memuat"
    products ||--o{ cart_items : "ada di"
    products ||--o{ order_items : "ada di"
    addresses ||--o{ orders : "tujuan kirim"
    orders ||--|{ order_items : "terdiri dari"
    orders ||--|| payments : "dibayar dengan"
    orders ||--o| shipments : "dikirim lewat"
    orders ||--o{ order_status_logs : "riwayat status"

    users {
        int id PK
        varchar name
        varchar email UK
        varchar password "hash"
        varchar role "admin|operator|customer"
        varchar phone
        tinyint is_active
        datetime created_at
        datetime updated_at
    }
    categories {
        int id PK
        varchar name
        varchar slug UK
        text description
        tinyint is_active
    }
    products {
        int id PK
        int category_id FK
        varchar name
        varchar slug UK
        text description
        decimal price "12,2"
        int stock "tidak negatif"
        varchar image
        varchar status "active|inactive"
    }
    carts {
        int id PK
        int user_id FK, UK
    }
    cart_items {
        int id PK
        int cart_id FK
        int product_id FK
        int quantity
    }
    addresses {
        int id PK
        int user_id FK
        varchar recipient_name
        varchar phone
        text address
        varchar city
        varchar province
        varchar postal_code
        tinyint is_default
    }
    orders {
        int id PK
        int user_id FK
        int address_id FK
        varchar order_number UK
        datetime order_date
        decimal subtotal
        decimal shipping_cost
        decimal total
        varchar order_status
        text shipping_address "snapshot"
        text notes
    }
    order_items {
        int id PK
        int order_id FK
        int product_id FK
        varchar product_name "snapshot"
        decimal price "snapshot"
        int quantity
        decimal subtotal
    }
    payments {
        int id PK
        int order_id FK, UK
        varchar method
        decimal amount
        varchar proof_file
        varchar payment_status "unpaid|submitted|verified|rejected"
        varchar note
        int verified_by FK
        datetime verified_at
    }
    shipments {
        int id PK
        int order_id FK, UK
        varchar courier
        varchar tracking_number
        varchar shipment_status "pending|shipped|delivered"
        datetime shipped_at
        datetime delivered_at
    }
    order_status_logs {
        int id PK
        int order_id FK
        varchar old_status
        varchar new_status
        int changed_by FK
        varchar note
        datetime created_at
    }
```

Catatan desain:
- `order_items.product_name` dan `price` adalah **snapshot** agar riwayat tidak berubah saat katalog diperbarui.
- `orders.shipping_address` adalah salinan alamat saat transaksi (alamat master boleh diubah/dihapus).
- Status pesanan (`orders.order_status`) dan status pembayaran (`payments.payment_status`) dipisah.
- Foreign key `order_items.product_id → products` bersifat `RESTRICT`: produk yang sudah dipesan tidak bisa dihapus (dinonaktifkan saja).
