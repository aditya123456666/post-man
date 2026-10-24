# TechStore

Aplikasi toko berbasis Laravel dengan katalog produk responsif, dashboard admin, autentikasi pelanggan, keranjang, checkout COD, dan REST API dengan Laravel Sanctum.

## Kebutuhan

- PHP 8.3+
- Composer
- MySQL 8+ atau MariaDB
- Node.js dan npm

## Menjalankan secara lokal

1. Install dependency dan buat file environment:

   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   npm install
   ```

2. Buat database MySQL khusus aplikasi, lalu isi konfigurasi `DB_*` di `.env`. Jangan commit `.env` atau membagikan password database.

3. Isi `ADMIN_EMAIL` dan `ADMIN_PASSWORD` pada `.env` untuk membuat akun admin. Kata sandi admin dibuat dengan hash Laravel ketika seeder dijalankan.

4. Buat tabel, isi katalog awal, buat akun admin, hubungkan storage publik, lalu build assets:

   ```bash
   php artisan migrate --seed
   php artisan storage:link
   npm run build
   ```

   Seeder produk dapat dijalankan ulang tanpa menghapus produk atau mengatur ulang stok. Bila ingin memperbarui gambar ilustrasi lokal:

   ```bash
   php artisan db:seed --class=ProductSeeder
   ```

5. Jalankan server:

   ```bash
   php artisan serve
   ```

Buka `http://127.0.0.1:8000`. Halaman admin tersedia di `/admin`; pendaftaran pelanggan tersedia di `/register`.

## Alur pelanggan

Pelanggan dapat menambahkan produk ke keranjang tanpa login, lalu mendaftar atau login untuk checkout. Checkout memerlukan alamat pengiriman, membuat pesanan COD dengan status `pending`, dan mengurangi stok secara atomik.

## REST API

- `POST /api/register` dan `POST /api/login` — mendapatkan token Sanctum
- `POST /api/logout` — mencabut token aktif
- `GET /api/products` — mencari, menyaring, dan mengurutkan produk
- `GET /api/products/{product}` — melihat detail produk
- `POST`, `PUT/PATCH`, `DELETE /api/products` — mutasi produk khusus admin
- `POST /api/orders/checkout` — checkout dengan `items` dan `shipping_address`
- `GET /api/orders/history` — riwayat pesanan milik pengguna

Kirim token API melalui header `Authorization: Bearer <token>`. Pendaftaran publik selalu membuat akun pelanggan; hak admin hanya diberikan melalui konfigurasi/seeder admin.

## Pengujian

```bash
php artisan test
```

Suite pengujian menggunakan SQLite in-memory; aktifkan ekstensi `pdo_sqlite` pada PHP CLI. Pengujian tidak ditujukan untuk database produksi.
