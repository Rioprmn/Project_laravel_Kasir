# 🛒 Kasir-Ku: Sistem Point of Sale (POS) Modern

[![Laravel Version](https://img.shields.io/badge/Laravel-v11-FF2D20?style=flat-square&logo=laravel)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-v8.2-777BB4?style=flat-square&logo=php)](https://www.php.net)

**Kasir-Ku** adalah aplikasi manajemen kasir berbasis web yang dibangun menggunakan **Laravel 11**. Aplikasi ini dirancang untuk mempermudah operasional toko dalam mencatat transaksi, mengelola stok produk, hingga menghasilkan laporan penjualan yang akurat.

---

## 🔥 Fitur Utama

- **Sistem Transaksi Dinamis**: Input penjualan cepat dengan fitur keranjang belanja (cart) yang otomatis menghitung total, bayar, dan kembalian.
- **Laporan Penjualan (Smart Report)**:
  - Monitoring riwayat transaksi dengan detail waktu yang sinkron (WIB).
  - **Bulk Action**: Fitur hapus massal menggunakan checkbox untuk mengelola laporan lebih efisien.
  - **Download Laporan**: Export seluruh data transaksi ke format CSV.
- **Manajemen Inventaris**: CRUD (Create, Read, Update, Delete) Produk dan Kategori yang terorganisir.
- **Pengaturan Toko**: Ubah nama toko, alamat, dan informasi lainnya secara fleksibel melalui Dashboard.
- **Sinkronisasi Waktu Lokal**: Penyesuaian otomatis jam transaksi agar sesuai dengan waktu operasional toko di Indonesia.
- * **Keamanan & Profil**: Sistem autentikasi yang aman untuk mengelola akses dan admin.

---

## 🛠️ Stack Teknologi

- **Backend**: Laravel 11 (PHP 8.2+)
- **Database**: MySQL
- **Frontend**: Blade Templating Engine & Custom CSS
- **Autentikasi**: Laravel Breeze (Secure Login & Profile Management)

---

## ⚙️ Cara Pasang Project

Ikuti instruksi di bawah ini untuk menginstal di lingkungan lokal Anda:


### Clone repository ke folder lokal:
```bash
git clone [https://github.com/Rioprmn/Project_laravel_Kasir.git](https://github.com/Rioprmn/Project_laravel_Kasir.git)
cd Project_laravel_Kasir
