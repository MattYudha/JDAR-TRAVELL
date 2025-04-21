JDAR Travel
JDAR Travel adalah sebuah aplikasi web full-stack berbasis PHP murni yang dirancang untuk mengelola berbagai paket wisata dan perjalanan. Aplikasi ini memungkinkan pengguna untuk menjelajah, mencari, dan membeli paket wisata, serta menghasilkan laporan PDF, memberikan ulasan, dan mengelola pemesanan secara real-time. Administrator memiliki kontrol penuh atas manajemen pengguna, paket, dan laporan penjualan.

Aplikasi ini terintegrasi dengan Midtrans untuk proses pembayaran yang aman dan efisien.

✨ Fitur Utama
🔹 Untuk Pengguna
Validasi Pengguna: Validasi saat login dan pendaftaran, memastikan username dan email unik. Email konfirmasi dikirim setelah registrasi.

Validasi Pembelian: Pengguna tidak dapat membeli paket yang sama lebih dari sekali, paket yang telah dimulai, atau jika kuota penuh.

Notifikasi Email: Email otomatis dikirim setelah transaksi berhasil.

Ulasan & Laporan: Pengguna dapat memberikan ulasan dan mengunduh laporan PDF setelah pembelian.

Pencarian & Eksplorasi: Cari dan jelajahi semua paket dari halaman utama.

Rating Real-Time: Penilaian paket diperbarui secara langsung berdasarkan ulasan pengguna.

🔸 Untuk Admin
Manajemen Paket: Tambah, lihat, dan ubah paket wisata.

Manajemen Pengguna: Aktifkan/nonaktifkan akun pengguna.

Laporan & Penjualan: Lihat data penjualan dan cetak laporan PDF.

Dashboard Admin: Panel kontrol terpusat untuk mengelola situs.

🐞 Bug yang Diketahui
Beberapa bug mungkin terdapat di file app/dbConnection.php, terutama terkait koneksi dan query database.

├── api/               # Endpoint API (login & register)
├── app/               # File inti aplikasi (koneksi DB, SQL schema)
├── assets/            # Asset statis (CSS, JS, gambar, logo)
├── auth/              # Komponen terkait autentikasi
├── components/        # Komponen UI yang dapat digunakan kembali
├── services/          # Layanan backend (transaksi, checkout, dsb.)
├── utilities/         # Utilitas seperti perhitungan rating
├── logs/              # Log autentikasi dan transaksi Midtrans
├── index.php          # Halaman utama
├── package.php        # Logika terkait paket wisata
├── success.php        # Halaman sukses setelah transaksi
├── composer.json      # Konfigurasi dependency Composer
└── tailwind.css       # Styling menggunakan Tailwind CSS


📋 Prasyarat
Sebelum memulai, pastikan Anda telah menginstal:

PHP 7.4 atau lebih tinggi

MySQL

Composer

Akun Sandbox Midtrans

Web server (Apache, Nginx) dengan dukungan PHP

Code editor (VSCode, PhpStorm, dsb.)

Rekomendasi lokal development:
Gunakan XAMPP atau Laragon untuk pengaturan lokal PHP & MySQL.

⚙️ Instalasi
1. Clone Repositori
git clone https://github.com/rahmatyudi/jdar-travel.git
cd jdar-travel


2. Instal Dependensi
   composer install

3. Setup Database
Buat database baru bernama jdar_travel.

Import schema dari app/db.sql.

Ubah konfigurasi database di app/dbConnection.php.

4. Konfigurasi Midtrans
Buat akun Midtrans (Sandbox) di Midtrans Dashboard.

Dapatkan Server Key dan Client Key.

Tambahkan konfigurasi di components/navBtn.php atau file .env:

MIDTRANS_SERVER_KEY=your-server-key
MIDTRANS_CLIENT_KEY=your-client-key
MIDTRANS_IS_PRODUCTION=false


5. Konfigurasi PHPMailer (Opsional)
Sesuaikan file services/phpmailer/ dengan kredensial email Anda:

Contoh : 
$mail->Username = 'your-email@gmail.com';
$mail->Password = 'your-email-password';

Aktifkan kembali baris kode PHPMailer di:

api/register.php

success.php

6. Atur Environment
Salin .env.example menjadi .env, lalu sesuaikan isinya.

Pastikan web server Anda mengarah ke folder proyek ini.

7. Jalankan Aplikasi
   php -S localhost:8000
🚀 Alur Penggunaan
👤 Alur Pengguna
Daftar atau login via /api/register.php atau /api/login.php.

Telusuri atau cari paket wisata.

Lakukan pemesanan via /order_form.php.

Selesaikan pembayaran melalui Midtrans.

Tulis ulasan dan unduh bukti transaksi dalam PDF.

🛠️ Alur Admin
Login sebagai admin.

Jadikan user sebagai admin lewat database (users → is_admin = 1).

Kelola paket, lihat laporan penjualan, dan cetak laporan dari admin_dashboard.php.

🖼️ Cuplikan Layar
Pencarian Paket: Cari dari landing page (index.php).

Daftar Paket: Lihat semua destinasi (searchPackages.php).

Rating Real-Time: Diperbarui otomatis setelah review (countStars.php).

Dashboard Admin: Kontrol penuh atas aplikasi (admin_dashboard.php).

Gateway Pembayaran: Midtrans mendukung VA, GoPay, ShopeePay, dll (checkOut.php).


🛠️ Dibangun Dengan
PHP - Backend utama

MySQL - Database relasional

Tailwind CSS - Styling frontend

PHPMailer - Pengiriman email

Midtrans - Gateway pembayaran

❓ Troubleshooting
Masalah Pembayaran:
Pastikan Server Key Midtrans benar.

Cek file logs/midtrans_log.

Verifikasi transaksi di dashboard Midtrans.

Gunakan endpoint settle, approve, atau expire untuk simulasi status.

Masalah Database:
Pastikan kredensial di app/dbConnection.php benar.

Schema SQL (db.sql) sudah diimport?

Masalah Email:
Konfigurasi SMTP di PHPMailer sudah benar?

Pastikan kode tidak dikomentari di api/register.php dan success.php.

📞 Kontak
Untuk pertanyaan atau bantuan:

Nama: Rahmat Yudi Burhanudin
Email: dewarahmat12334@gmail.com

👥 Kontributor
Rahmat Yudi Burhanudin - @rahmatyudi

📄 Lisensi
Proyek ini dilisensikan di bawah lisensi MIT.

🙏 Penghargaan
Desain terinspirasi dari berbagai platform pemesanan wisata populer.







