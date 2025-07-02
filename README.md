# 🌍 JDAR Travel
### Full-Stack Travel Package Management System

<div align="center">

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![Midtrans](https://img.shields.io/badge/Midtrans-02BFDB?style=for-the-badge&logo=midtrans&logoColor=white)

**Aplikasi web full-stack untuk mengelola paket wisata dengan sistem pembayaran terintegrasi**

[📸 Screenshots](#-screenshots) • [🚀 Features](#-fitur-utama) • [⚡ Quick Start](#-quick-start) • [📖 Documentation](#-dokumentasi)

</div>

---

## 📸 Screenshots

<div align="center">
  <table>
    <tr>
      <td align="center">
        <img src="screenshots/01.png" width="300" alt="Beranda JDAR Travel">
        <br><sub><b>🏠 Beranda</b></sub>
      </td>
      <td align="center">
        <img src="screenshots/02.png" width="300" alt="Daftar Paket">
        <br><sub><b>📋 Daftar Paket</b></sub>
      </td>
      <td align="center">
        <img src="screenshots/03.png" width="300" alt="Form Pemesanan">
        <br><sub><b>🛒 Form Pemesanan</b></sub>
      </td>
    </tr>
    <tr>
      <td align="center">
        <img src="screenshots/04.png" width="300" alt="Halaman Login">
        <br><sub><b>🔐 Login</b></sub>
      </td>
      <td align="center">
        <img src="screenshots/05.png" width="300" alt="Dashboard Admin">
        <br><sub><b>📊 Dashboard Admin</b></sub>
      </td>
      <td align="center">
        <img src="screenshots/06.png" width="300" alt="Tabel Pengguna">
        <br><sub><b>👥 Manajemen User</b></sub>
      </td>
    </tr>
  </table>
</div>

---

## 🚀 Fitur Utama

<table>
<tr>
<td width="50%">

### 👤 **Fitur Pengguna**
- ✅ **Autentikasi Lengkap** - Registrasi, login dengan validasi email
- 🔍 **Pencarian & Filter** - Cari paket berdasarkan kategori & harga
- 🛒 **Sistem Pemesanan** - Booking paket dengan validasi ketersediaan
- 💳 **Pembayaran Digital** - Integrasi Midtrans (VA, QRIS, E-wallet)
- ⭐ **Review & Rating** - Tulis ulasan dan berikan rating
- 📄 **Laporan PDF** - Generate laporan pembelian pribadi
- 📱 **Responsive Design** - Mobile-friendly interface

</td>
<td width="50%">

### 🛠️ **Fitur Admin**
- 📊 **Dashboard Analytics** - Overview penjualan & statistik
- 🎒 **Manajemen Paket** - CRUD paket wisata lengkap
- 👥 **User Management** - Kelola akun pengguna
- 💰 **Sales Tracking** - Monitor penjualan real-time
- 📈 **Report Generation** - Export laporan ke PDF
- 🔧 **System Settings** - Konfigurasi aplikasi
- 🔐 **Role-based Access** - Kontrol akses bertingkat

</td>
</tr>
</table>

---

## ⚡ Quick Start

### 📋 Prerequisites

```bash
# Requirements
PHP >= 7.4
MySQL >= 5.7
Composer
Node.js (optional, for asset building)
```

### 🔧 Installation

```bash
# 1. Clone repository
git clone https://github.com/rahmatyudi/jdar-travel.git
cd jdar-travel

# 2. Install dependencies
composer install

# 3. Setup environment
cp .env.example .env
# Edit .env dengan konfigurasi Anda

# 4. Setup database
mysql -u root -p
CREATE DATABASE triptip;
mysql -u root -p triptip < app/db.sql

# 5. Start development server
php -S localhost:8000
```

### ⚙️ Configuration

<details>
<summary><b>🔐 Environment Setup</b></summary>

```env
# Database Configuration
DB_HOST=localhost
DB_NAME=triptip
DB_USER=root
DB_PASS=

# Midtrans Configuration
MIDTRANS_SERVER_KEY=your-server-key
MIDTRANS_CLIENT_KEY=your-client-key
MIDTRANS_IS_PRODUCTION=false

# SMTP Configuration (Optional)
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=your-email@gmail.com
SMTP_PASS=your-app-password
```

</details>

<details>
<summary><b>🗃️ Database Schema</b></summary>

```sql
-- Import provided schema
mysql -u root -p triptip < app/db.sql

-- Or create manually
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- ... (additional tables)
```

</details>

---

## 🏗️ Arsitektur Sistem

```
┌─────────────────────────────────────────────────────────────┐
│                        JDAR Travel                          │
├─────────────────────────────────────────────────────────────┤
│  Frontend (Tailwind CSS + Vanilla JS)                      │
│  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐          │
│  │   Landing   │ │   Package   │ │    Admin    │          │
│  │    Page     │ │   Catalog   │ │  Dashboard  │          │
│  └─────────────┘ └─────────────┘ └─────────────┘          │
├─────────────────────────────────────────────────────────────┤
│  Backend (Pure PHP)                                        │
│  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐          │
│  │     API     │ │   Services  │ │  Utilities  │          │
│  │ Endpoints   │ │   Layer     │ │   & Helpers │          │
│  └─────────────┘ └─────────────┘ └─────────────┘          │
├─────────────────────────────────────────────────────────────┤
│  External Services                                          │
│  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐          │
│  │   Midtrans  │ │  PHPMailer  │ │    MySQL    │          │
│  │   Payment   │ │    SMTP     │ │  Database   │          │
│  └─────────────┘ └─────────────┘ └─────────────┘          │
└─────────────────────────────────────────────────────────────┘
```

---

## 📁 Struktur Proyek

```
jdar-travel/
├── 📁 api/                    # API Endpoints
│   ├── 🔐 login.php
│   ├── 📝 register.php
│   └── 🔍 searchPackages.php
├── 📁 app/                    # Core Application
│   ├── 🗄️ dbConnection.php
│   └── 📊 db.sql
├── 📁 assets/                 # Static Assets
│   ├── 🎨 css/
│   ├── ⚡ js/
│   └── 🖼️ images/
├── 📁 auth/                   # Authentication
│   └── 🛡️ middleware.php
├── 📁 components/             # Reusable Components
│   ├── 📄 header.php
│   ├── 📄 footer.php
│   └── 🧭 navigation.php
├── 📁 services/               # Business Logic
│   ├── 💳 checkout/
│   ├── 📧 phpmailer/
│   └── 🔄 transactions/
├── 📁 utilities/              # Helper Functions
│   ├── ⭐ countStars.php
│   └── 📄 generatePDF.php
├── 📁 logs/                   # Application Logs
├── 🏠 index.php              # Landing Page
├── 📦 package.php            # Package Listing
├── ✅ success.php            # Payment Success
└── 📋 composer.json          # Dependencies
```

---

## 📖 Dokumentasi

### 🔌 API Endpoints

<details>
<summary><b>Authentication Endpoints</b></summary>

| Method | Endpoint | Description | Parameters |
|--------|----------|-------------|------------|
| `POST` | `/api/login.php` | User login | `username`, `password` |
| `POST` | `/api/register.php` | User registration | `username`, `email`, `password` |
| `POST` | `/api/logout.php` | User logout | `session_token` |

</details>

<details>
<summary><b>Package Endpoints</b></summary>

| Method | Endpoint | Description | Parameters |
|--------|----------|-------------|------------|
| `GET` | `/api/searchPackages.php` | Search packages | `query`, `category`, `price_range` |
| `GET` | `/packageAvailability.php` | Check availability | `package_id`, `date` |
| `POST` | `/order_form.php` | Create booking | `package_id`, `user_id`, `booking_date` |

</details>

### 🎯 Usage Examples

<details>
<summary><b>👤 User Flow</b></summary>

```php
// 1. User Registration
POST /api/register.php
{
    "username": "john_doe",
    "email": "john@email.com",
    "password": "secure_password"
}

// 2. Search Packages
GET /api/searchPackages.php?query=bali&category=adventure

// 3. Book Package
POST /order_form.php
{
    "package_id": 1,
    "booking_date": "2024-03-15",
    "participants": 2
}
```

</details>

<details>
<summary><b>🛠️ Admin Operations</b></summary>

```php
// 1. Create New Package
POST /new_package.php
{
    "name": "Bali Adventure",
    "description": "3D2N Bali Trip",
    "price": 1500000,
    "max_participants": 20
}

// 2. Generate Sales Report
GET /sales_pdf.php?start_date=2024-01-01&end_date=2024-03-31

// 3. Update User Status
POST /user_update.php
{
    "user_id": 123,
    "status": "active"
}
```

</details>

---

## 🐛 Troubleshooting

<details>
<summary><b>Common Issues</b></summary>

### Database Connection Error
```bash
# Check database configuration
php -r "include 'app/dbConnection.php'; echo 'Connection: OK';"
```

### Midtrans Payment Issues
```bash
# Verify Midtrans credentials
curl -X POST https://api.sandbox.midtrans.com/v2/charge \
  -H "Authorization: Basic $(echo -n 'YOUR_SERVER_KEY:' | base64)"
```

### Email Sending Problems
```php
// Test SMTP configuration
php -r "
require 'services/phpmailer/test_email.php';
testEmailConfiguration();
"
```

</details>

---

## 🤝 Contributing

<div align="center">

### We welcome contributions! 🎉

</div>

```bash
# 1. Fork the repository
# 2. Create feature branch
git checkout -b feature/amazing-feature

# 3. Commit changes
git commit -m 'Add amazing feature'

# 4. Push to branch
git push origin feature/amazing-feature

# 5. Open Pull Request
```

### 📝 Contribution Guidelines

- Follow PSR-12 coding standards
- Write clear commit messages
- Add tests for new features
- Update documentation
- Ensure backward compatibility

---

## 📊 Performance & Security

<table>
<tr>
<td width="50%">

### ⚡ **Performance**
- Optimized MySQL queries
- Image compression & lazy loading
- Minified CSS/JS assets
- Browser caching headers
- Database connection pooling

</td>
<td width="50%">

### 🔐 **Security**
- SQL injection protection
- XSS prevention
- CSRF token validation
- Password hashing (bcrypt)
- Input sanitization

</td>
</tr>
</table>

---

## 📄 License

<div align="center">

**MIT License** - feel free to use this project for educational or commercial purposes.

See [LICENSE](LICENSE) for more details.

</div>

---

## 👨‍💻 Developer

<div align="center">

### **Rahmat Yudi Burhanudin**

[![GitHub](https://img.shields.io/badge/GitHub-100000?style=for-the-badge&logo=github&logoColor=white)](https://github.com/rahmatyudi)
[![Email](https://img.shields.io/badge/Email-D14836?style=for-the-badge&logo=gmail&logoColor=white)](mailto:dewarahmat12334@gmail.com)

*Passionate Full-Stack Developer crafting digital travel experiences* ✈️

</div>

---

<div align="center">

### 🌟 **Star this repository if you found it helpful!** 🌟

**Thank you for using JDAR Travel!** 🎒🌴

*Built with ❤️ in Indonesia* 🇮🇩
