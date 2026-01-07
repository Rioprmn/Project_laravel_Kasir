# 🐾 Kasir-Ku: Pet Information & POS System

[![Laravel Version](https://img.shields.io/badge/Laravel-v11-FF2D20?style=flat-square&logo=laravel)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-v8.2-777BB4?style=flat-square&logo=php)](https://www.php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg?style=flat-square)](https://opensource.org/licenses/MIT)

**Kasir-Ku** (Internal Project Name: `pet_information`) adalah solusi manajemen kasir (Point of Sale) modern yang ringan, cepat, dan responsif. Dibangun dengan **Laravel 11**, aplikasi ini dirancang khusus untuk membantu pemilik toko mengelola transaksi harian, stok produk, dan laporan penjualan secara efisien.

---

## ✨ Fitur Unggulan

Aplikasi ini hadir dengan berbagai fitur yang memudahkan operasional bisnis:

* **Sistem Kasir (Point of Sale)**: Transaksi cepat dengan fitur keranjang belanja dinamis.
* **Laporan Transaksi Cerdas**:
    * Monitoring riwayat penjualan secara real-time.
    * **Bulk Delete**: Hapus banyak data sekaligus menggunakan fitur Checkbox.
    * **Export Data**: Download laporan transaksi dalam format CSV untuk kebutuhan pembukuan.
* **Manajemen Produk & Stok**: Kelola data barang dan kategori dengan antarmuka yang bersih.
* **Timezone Synchronized**: Jam transaksi sudah disesuaikan otomatis dengan waktu lokal (WIB).
* **Keamanan & Profil**: Sistem autentikasi yang aman untuk mengelola akses kasir dan admin.
* **Pengaturan Toko**: Kustomisasi identitas toko (nama, alamat, dll) langsung dari dashboard.

---

## 🛠️ Teknologi yang Digunakan

* **Framework**: Laravel 11.x
* **Language**: PHP 8.2+
* **Database**: MySQL
* **Frontend**: Blade Templating & Custom CSS (Emerald-Slate Theme)
* **Security**: Laravel Breeze (Auth)

---

## 🚀 Panduan Instalasi

Ikuti langkah-langkah di bawah ini untuk menjalankan proyek di perangkat lokal Anda:

### 1. Persiapan Awal
Pastikan Anda sudah menginstal **PHP 8.2**, **Composer**, dan **Node.js**.

### 2. Clone & Install
```bash
# Clone repository
git clone [https://github.com/Rioprmn/Project_laravel_Kasir.git](https://github.com/Rioprmn/Project_laravel_Kasir.git)
cd Project_laravel_Kasir.

### 3. Konfigurasi Database
Salin file .env.example menjadi .env dan sesuaikan pengaturan database serta timezone Anda:

Cuplikan kode

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_anda
DB_USERNAME=root
DB_PASSWORD=

# Setting Waktu Indonesia
APP_TIMEZONE=Asia/Jakarta

# Install dependency PHP & JS
composer install
npm install && npm run build

### 4. Migrasi & Jalankan
Bash

# Generate key aplikasi
php artisan key:generate

# Migrasi tabel dan data awal
php artisan migrate --seed

# Jalankan server lokal
php artisan serve
