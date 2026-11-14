# O'Clock — Watch E-Commerce Website

O'Clock merupakan aplikasi *e-commerce* berbasis web untuk penjualan jam tangan. Website ini dikembangkan menggunakan Laravel dan menyediakan fitur pengelolaan toko, katalog produk, keranjang belanja, pemesanan, pembayaran, serta pengelolaan pengiriman.

O'Clock menggunakan konsep *single-vendor*, yaitu seluruh produk dikelola oleh satu pihak administrator. Tampilan website mengusung konsep minimalis dan elegan dengan kombinasi warna *broken white* dan *champagne gold*.

## Teknologi yang Digunakan

- PHP 8.3 atau lebih baru
- Laravel 13
- MySQL
- Blade Template
- Tailwind CSS
- JavaScript
- Vite
- Composer
- Node.js dan npm
- Midtrans Snap Sandbox

## Fitur Aplikasi

### Administrator

Administrator bertugas mengelola operasional toko melalui halaman khusus Admin.

Fitur yang tersedia meliputi:

- Login dan pengelolaan akun administrator.
- Dashboard statistik toko.
- Pengelolaan kategori produk.
- Pengelolaan produk, harga, stok, dan gambar.
- Pengelolaan tarif pengiriman berdasarkan wilayah.
- Melihat data pelanggan.
- Melihat dan mengelola pesanan.
- Memproses pesanan yang sudah dibayar.
- Memasukkan informasi kurir dan nomor resi.
- Memperbarui status pengiriman hingga pesanan selesai.

### Customer

Customer dapat menggunakan website untuk berbelanja jam tangan secara daring.

Fitur yang tersedia meliputi:

- Registrasi dan login pelanggan.
- Melihat katalog dan detail produk.
- Pencarian, filter kategori, dan pengurutan produk.
- Menambahkan produk ke keranjang.
- Mengubah jumlah atau menghapus produk dari keranjang.
- Checkout dan pengisian alamat pengiriman.
- Perhitungan ongkos kirim.
- Pembayaran melalui Midtrans Snap Sandbox.
- Melihat riwayat dan status pesanan.
- Melihat informasi kurir dan nomor resi.
- Mencetak invoice pesanan yang sudah dibayar.
- Melihat dan memperbarui profil pelanggan.

## Instalasi Project

### 1. Clone Repository

```bash
git clone https://github.com/jenyferaprilino/oclock.git
cd oclock
```

### 2. Instal Dependensi PHP

```bash
composer install
```

### 3. Instal Dependensi Frontend

```bash
npm install
```

### 4. Konfigurasi Environment

Salin file `.env.example` menjadi `.env`.

Pada PowerShell Windows:

```powershell
Copy-Item .env.example .env
```

Kemudian sesuaikan konfigurasi aplikasi, database MySQL, dan Midtrans pada file `.env`.

Contoh konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=oclock_db
DB_USERNAME=root
DB_PASSWORD=
```

Buat database bernama `oclock_db` melalui MySQL sebelum menjalankan migrasi.

Contoh variabel Midtrans:

```env
MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_CHECKOUT_ENABLED=true
```

Isi kunci Midtrans menggunakan kredensial Sandbox milik pengembang. Jangan menggunakan kredensial produksi untuk pengujian.

File `.env` tidak boleh diunggah ke repository GitHub.

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Jalankan Migrasi Database

```bash
php artisan migrate
```

Untuk instalasi baru dengan data contoh, jalankan:

```bash
php artisan db:seed
```

**Perhatian:** Seeder dapat membuat atau mengubah data contoh. Periksa isinya terlebih dahulu sebelum menjalankannya pada database yang sudah memiliki data penting.

### 7. Buat Storage Link

```bash
php artisan storage:link
```

### 8. Jalankan Website

Jalankan server Laravel:

```bash
php artisan serve
```

Pada terminal lain, jalankan Vite:

```bash
npm run dev
```

Buka website melalui browser:

`http://127.0.0.1:8000`

Alternatif untuk menyiapkan aset frontend:

```bash
npm run build
```

## Konfigurasi Pembayaran

O'Clock menggunakan Midtrans Snap Sandbox sebagai lingkungan pengujian pembayaran.

Alur pembayaran:

1. Customer memilih produk dan melakukan checkout.
2. Sistem membuat pesanan berdasarkan harga, stok, dan ongkos kirim.
3. Customer diarahkan ke proses pembayaran Midtrans.
4. Sistem memeriksa status transaksi.
5. Setelah pembayaran terkonfirmasi, pesanan dapat diproses oleh Admin.

Pembayaran Sandbox hanya digunakan untuk simulasi transaksi dan bukan pembayaran uang sungguhan.

## Pengelolaan Pesanan

Proses pengelolaan pesanan dilakukan oleh Admin setelah pembayaran berhasil.

Alur utama:

**Processing → Shipped → Completed**

Informasi kurir dan nomor resi dapat dilihat pelanggan pada halaman riwayat pesanan.

Nomor resi simulasi O'Clock menggunakan awalan `OC-` dan tidak terhubung langsung dengan sistem pelacakan ekspedisi.

## Invoice

Customer dapat membuka invoice untuk pesanan dengan status pembayaran berhasil.

Invoice menampilkan informasi transaksi, penerima, alamat, produk, kuantitas, harga, ongkos kirim, total pembayaran, dan informasi pengiriman yang tersedia.

Invoice dapat dicetak menggunakan fitur cetak browser atau disimpan sebagai PDF.

## Pengujian

Pengujian otomatis dilakukan menggunakan PHPUnit melalui Laravel.

Jalankan:

```bash
php artisan test
```

Hasil pengujian terakhir pada lingkungan pengembangan:

**99 tests passed — 659 assertions**

Pengujian mencakup autentikasi, pengelolaan Admin, katalog, keranjang, checkout, pembayaran, pengiriman, dan akses invoice.

## Keamanan

- Autentikasi menggunakan sistem autentikasi Laravel.
- Hak akses Admin dan Customer dipisahkan.
- Akses invoice dibatasi berdasarkan pemilik pesanan dan status pembayaran.
- Validasi diterapkan pada input aplikasi.
- Status pembayaran diverifikasi melalui mekanisme integrasi Midtrans.
- Konfigurasi rahasia disimpan dalam file `.env` yang tidak disertakan dalam Git.

## Repository

GitHub: https://github.com/jenyferaprilino/oclock

**O'Clock — Watch E-Commerce Website**

Project pengembangan aplikasi *e-commerce* jam tangan berbasis Laravel.