# JDAR Travel

JDAR Travel adalah situs web full-stack yang dibangun dengan PHP murni untuk mengelola berbagai paket wisata dan perjalanan. Pengguna dapat menjelajah, mencari, dan membeli paket perjalanan, menghasilkan laporan PDF, menulis ulasan, dan mengelola pemesanan secara real-time. Admin dapat mengelola pengguna, paket wisata, penjualan, serta menghasilkan laporan. Aplikasi ini terintegrasi dengan Midtrans untuk pemrosesan pembayaran yang aman.

## Fitur Utama

### Pengguna
- **Validasi Pengguna**: Validasi saat login dan pendaftaran memastikan nama pengguna dan email unik. Pengguna menerima email konfirmasi setelah mendaftar (`api/register.php`, `services/phpmailer/` - hapus komentar kode PHPMailer untuk mengaktifkan email).
- **Validasi Pembelian Paket**: Pengguna tidak dapat membeli paket yang sama dua kali, tidak dapat membeli setelah paket dimulai, dan tidak dapat membeli jika kapasitas penuh (`order_form.php`, `packageAvailability.php`).
- **Notifikasi Email**: Pengguna menerima email setelah menyelesaikan pembelian (`success.php` - hapus komentar kode PHPMailer di `services/phpmailer/`).
- **Ulasan dan Laporan**: Pengguna dapat menulis ulasan dan menghasilkan laporan PDF setelah membeli paket (`user_review.php`, `sales_pdf.php`).
- **Pencarian dan Jelajah**: Cari paket langsung dari halaman utama atau jelajahi semua paket yang tersedia (`searchPackages.php`, `package.php`).
- **Pembaruan Real-Time**: Rating paket diperbarui secara real-time setelah pengguna mengirim ulasan (`countStars.php`).

### Admin
- **Manajemen Paket**: Melihat, menambah, dan mengubah paket (`new_package.php`, `packageAvailability.php`).
- **Manajemen Pengguna**: Mengelola akun pengguna, termasuk mengaktifkan atau menonaktifkan akun (`user_update.php`).
- **Penjualan dan Laporan**: Melihat data penjualan dan menghasilkan laporan PDF (`admin_dashboard.php`, `sales_pdf.php`, `generatePDF.php`).
- **Dashboard Admin**: Dasbor pusat untuk mengontrol seluruh situs (`admin_dashboard.php`).

## Bug yang Diketahui

Periksa file `app/dbConnection.php` untuk bug yang berkaitan dengan koneksi atau query database.

## Struktur Proyek

- `api/`: Endpoint API untuk login dan registrasi.
- `app/`: File inti aplikasi, termasuk koneksi database.
- `assets/`: Aset statis seperti CSS, font, JavaScript, dan gambar.
- `auth/`: Komponen terkait autentikasi.
- `components/`: Komponen UI yang dapat digunakan ulang seperti header, footer, dan navigasi.
- `services/`: Layanan backend untuk checkout, manajemen transaksi, dan lainnya.
- `utilities/`: Utilitas bantu untuk rating bintang dan fungsi lainnya.
- `logs/`: File log untuk autentikasi dan transaksi Midtrans.
- File lainnya:
  - `index.php`: Titik masuk utama aplikasi.
  - `package.php`: Logika terkait paket wisata.
  - `success.php`: Halaman setelah pemesanan berhasil.
  - `composer.json`: Manajemen dependensi dengan Composer.
  - `tailwind.css`: Styling menggunakan Tailwind CSS.

## Prasyarat

- PHP 7.4 atau lebih tinggi
- Database MySQL
- Composer
- Akun Midtrans Sandbox
- Web server (Apache, Nginx, dll.) dengan dukungan PHP
- Code editor (VSCode, PhpStorm, dll.)

**Disarankan:** Gunakan XAMPP atau Laragon untuk setup lokal.

## Instalasi

1. **Clone Repository:**
```bash
git clone https://github.com/rahmatyudi/jdar-travel.git
cd jdar-travel
```

2. **Pasang Dependensi:**
```bash
composer install
```

3. **Setup Database:**
- Buat database baru dengan nama `jdar_travel`.
- Import schema dari `app/db.sql`.
- Update konfigurasi database di `app/dbConnection.php`.

4. **Konfigurasi Midtrans:**
- Buat akun di Midtrans Sandbox.
- Dapatkan Server Key dan Client Key.
- Update konfigurasi Midtrans di `components/navBtn.php` atau `env`:
```env
MIDTRANS_SERVER_KEY=server-key-anda
MIDTRANS_CLIENT_KEY=client-key-anda
MIDTRANS_IS_PRODUCTION=false
```

5. **Konfigurasi PHPMailer (Opsional):**
- Update konfigurasi PHPMailer di `services/phpmailer/`:
```php
$mail->Username = 'email-anda@gmail.com';
$mail->Password = 'password-email-anda';
```
- Hapus komentar kode PHPMailer di `api/register.php` dan `success.php`.

6. **Set Up Environment:**
- Salin `.env.example` ke `.env` dan isi variabel yang diperlukan.
- Pastikan server web mengarah ke direktori proyek.

7. **Jalankan Aplikasi:**
```bash
php -S localhost:8000
```
- Akses aplikasi di [http://localhost:8000](http://localhost:8000).

## Penggunaan

### Alur Pengguna:
- Daftar atau login melalui `/api/register.php` atau `/api/login.php`.
- Cari paket wisata melalui `/searchPackages.php`.
- Pesan paket melalui `/order_form.php` dan lanjutkan ke pembayaran.
- Selesaikan pembayaran dengan Midtrans (VA, GoPay, ShopeePay, dll.).
- Tulis ulasan dan unduh struk PDF (`user_review.php`, `sales_pdf.php`).

### Alur Admin:
- Login sebagai admin melalui `/api/login.php`.
- Jadikan pengguna sebagai admin di database (`users -> is_admin = 1`).
- Akses dashboard admin (`admin_dashboard.php`).
- Kelola paket, lihat laporan penjualan, dan cetak PDF (`generatePDF.php`).

## Cuplikan

- **Pencarian Paket**: Cari langsung dari halaman utama (`index.php`).
- **Daftar Paket**: Jelajahi semua paket (`searchPackages.php`).
- **Rating Paket**: Diperbarui berdasarkan ulasan (`countStars.php`).
- **Ulasan Pengguna**: Tampilkan dan tulis ulasan (`user_review.php`).
- **Dashboard Admin**: Kontrol penuh untuk admin (`admin_dashboard.php`).
- **Dashboard Pengguna**: Kelola info dan pemesanan (`user_dashboard.php`).
- **Pembayaran**: Proses pembelian menggunakan Midtrans (`checkOut.php`).

## Dibuat Dengan

- **PHP**: Bahasa backend utama.
- **MySQL**: Sistem manajemen database relasional.
- **Tailwind CSS**: Untuk styling frontend.
- **PHPMailer**: Untuk notifikasi email.
- **Midtrans**: Gateway pembayaran.

## Troubleshooting

### Masalah Pembayaran:
- Pastikan Server Key Midtrans benar.
- Periksa log Midtrans (`logs/midtrans_log`).
- Verifikasi status transaksi di dashboard Midtrans.
- Gunakan endpoint uji coba seperti `/settle`, `/approve`, atau `/expire`.

### Masalah Database:
- Pastikan kredensial database benar di `app/dbConnection.php`.
- Pastikan schema `db.sql` telah diimpor.

### Masalah Email:
- Verifikasi konfigurasi PHPMailer.
- Pastikan kode PHPMailer tidak dikomentari di `api/register.php` dan `success.php`.

## Kontak

Untuk pertanyaan atau bantuan:
- **Nama**: Rahmat Yudi Burhanudin
- **Email**: dewarahmat12334@gmail.com

## Penulis

- Rahmat Yudi Burhanudin - [@rahmatyudi](https://github.com/rahmatyudi)

## Lisensi

Proyek ini dilisensikan di bawah MIT License - lihat file LICENSE untuk detailnya.

## Penghargaan

- Desain terinspirasi dari berbagai platform pemesanan perjalanan.

