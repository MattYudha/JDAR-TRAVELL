# 🌍 JDAR Travel

**JDAR Travel** adalah aplikasi web full-stack ✨ yang dibangun dengan **PHP murni** untuk mengelola berbagai paket wisata dan perjalanan.
Pengguna dapat **menjelajah 🧭, mencari 🔍, membeli 🛒**, serta menulis ulasan dan menghasilkan laporan PDF 📄.
Admin dapat **mengelola pengguna 👥, paket wisata 🧳, penjualan 💰**, dan menghasilkan laporan 🗂️.
Terintegrasi dengan **Midtrans** untuk pemrosesan pembayaran yang **aman 🔐 dan cepat⚡**.

---

## 📸 Tampilan Aplikasi

Berikut adalah beberapa tampilan utama dari aplikasi JDAR Travel:

### Halaman Pengguna

<p align="center">
  <img src="screenshots/11.png" width="100%" alt="Landing Page">
  <br>
  <em>Landing Page</em>
</p>

<p align="center">
  <img src="screenshots/01.png" width="100%" alt="Beranda JDAR Travel">
  <br>
  <em>Beranda JDAR Travel</em>
</p>

<p align="center">
  <img src="screenshots/10.png" width="100%" alt="Pencarian Paket">
  <br>
  <em>Pencarian Paket</em>
</p>

<p align="center">
  <img src="screenshots/02.png" width="100%" alt="Daftar Paket">
  <br>
  <em>Daftar Paket Tersedia</em>
</p>

<p align="center">
  <img src="screenshots/03.png" width="100%" alt="Form Pemesanan">
  <br>
  <em>Form Pemesanan Paket</em>
</p>

<p align="center">
  <img src="screenshots/09.png" width="100%" alt="Form Ulasan">
  <br>
  <em>Form Pemberian Ulasan</em>
</p>

<p align="center">
  <img src="screenshots/08.png" width="100%" alt="Pembayaran Midtrans">
  <br>
  <em>Integrasi Pembayaran Midtrans</em>
</p>

### Halaman Admin

<p align="center">
  <img src="screenshots/04.png" width="100%" alt="Halaman Login Admin">
  <br>
  <em>Halaman Login Admin</em>
</p>

<p align="center">
  <img src="screenshots/05.png" width="100%" alt="Dashboard Admin">
  <br>
  <em>Dashboard Admin</em>
</p>

<p align="center">
  <img src="screenshots/06.png" width="100%" alt="Tabel Pengguna">
  <br>
  <em>Manajemen Data Pengguna</em>
</p>

<p align="center">
  <img src="screenshots/07.png" width="100%" alt="Laporan Penjualan">
  <br>
  <em>Laporan Penjualan</em>
</p>

---

## 🚀 Fitur Utama

### 👤 Pengguna
- ✅ **Validasi Pengguna**: Pastikan username dan email unik saat login & daftar. Email konfirmasi dikirim setelah pendaftaran (`api/register.php`, `services/phpmailer/`).
- 🛑 **Validasi Pembelian Paket**: Tidak bisa beli paket sama dua kali, atau paket yang sudah dimulai / penuh (`order_form.php`, `packageAvailability.php`).
- 📧 **Notifikasi Email**: Email otomatis dikirim setelah pembelian sukses (`success.php`).
- 📝 **Ulasan & Laporan**: Kirim ulasan dan unduh laporan PDF setelah pembelian (`user_review.php`, `sales_pdf.php`).
- 🔎 **Pencarian & Jelajah**: Cari paket via beranda atau jelajahi semua paket (`searchPackages.php`, `package.php`).
- ⭐ **Pembaruan Real-Time**: Rating paket diperbarui langsung setelah ulasan dikirim (`countStars.php`).

### 🛠️ Admin
- 🎒 **Manajemen Paket**: Tambah, ubah, dan lihat detail paket wisata (`new_package.php`, `packageAvailability.php`).
- 🧑‍💼 **Manajemen Pengguna**: Aktif/nonaktifkan akun pengguna (`user_update.php`).
- 📊 **Penjualan & Laporan**: Lihat data penjualan dan hasilkan PDF (`admin_dashboard.php`, `sales_pdf.php`, `generatePDF.php`).
- 📋 **Dashboard Admin**: Kontrol penuh aplikasi dari satu tempat (`admin_dashboard.php`).

---

## 🐞 Bug yang Diketahui

🔍 Periksa file `app/dbConnection.php` jika ada masalah koneksi atau query database.

---

## 🧱 Struktur Proyek

- `api/` – Endpoint API untuk login dan registrasi.
- `app/` – File inti aplikasi, termasuk koneksi database.
- `assets/` – Aset statis: CSS, JS, gambar.
- `auth/` – Komponen autentikasi.
- `components/` – Header, footer, navigasi, dll.
- `services/` – Layanan backend seperti checkout & transaksi.
- `utilities/` – Fungsi tambahan (ex: rating bintang).
- `logs/` – Log autentikasi & transaksi Midtrans.
- File penting:
  - `index.php`, `package.php`, `success.php`
  - `composer.json`, `tailwind.css`

---

## ⚙️ Prasyarat

- ✅ PHP 7.4+
- ✅ Database MySQL
- ✅ Composer
- ✅ Akun Midtrans Sandbox
- ✅ Web server (Apache/Nginx) dengan dukungan PHP
- ✅ Code editor (VSCode, PhpStorm, dll.)

💡 **Tips:** Gunakan XAMPP atau Laragon untuk development lokal.

---

## 📦 Instalasi

1. **Clone Repository:**
```bash
git clone [https://github.com/rahmatyudi/jdar-travel.git](https://github.com/rahmatyudi/jdar-travel.git)
cd jdar-travel
