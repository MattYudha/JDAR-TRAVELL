# JDAR Travel

JDAR Travel is a full-stack travel and tour booking website built with raw PHP. It allows users to explore, purchase, and manage travel packages, while providing administrators with tools to oversee users, bookings, and generate insightful reports. The platform integrates with Midtrans for secure payment processing and offers real-time updates and email notifications.

---

## ✨ Key Features

### 👤 Users
- **User Validation:** Unique username and email validation during registration. Confirmation email is sent post-registration *(see `api/register.php`, `services/phpmailer/` - uncomment PHPMailer code)*.
- **Purchase Restrictions:** Prevents double-booking, booking full packages, or booking after the package has started *(see `order_form.php`, `packageAvailability.php`).*
- **Email Notifications:** Automated email sent after purchase confirmation *(see `success.php`).*
- **Reviews & Reports:** Users can review purchased packages and generate PDF receipts *(see `user_review.php`, `sales_pdf.php`).*
- **Search & Browse:** Quick access to packages via search or full listings *(see `searchPackages.php`, `package.php`).*
- **Real-Time Ratings:** Package ratings update immediately after user reviews *(see `countStars.php`).*

### 🚀 Admin
- **Package Management:** Create, view, and modify tour packages *(see `new_package.php`, `packageAvailability.php`).*
- **User Management:** Activate or deactivate users *(see `user_update.php`).*
- **Sales & Reports:** View sales data and export reports as PDFs *(see `admin_dashboard.php`, `generatePDF.php`).*
- **Central Dashboard:** A streamlined interface to manage the platform *(see `admin_dashboard.php`).*

---

## 📃 Project Structure
```
api/            # API endpoints (login, registration)
app/            # Core application and DB logic
assets/         # Static files (CSS, JS, images)
auth/           # Authentication modules
components/     # UI components (header, footer, etc)
services/       # Payment and backend utilities
utilities/      # Helpers (e.g., star rating logic)
logs/           # Midtrans and auth logs
```

### Other Notable Files
- `index.php`: Main entry point
- `success.php`: Post-payment success page
- `composer.json`: Composer dependencies
- `tailwind.css`: Frontend styling

---

## 🚀 Prerequisites
- PHP 7.4 or higher
- MySQL database
- Composer (for PHP dependencies)
- Midtrans Sandbox account
- Web server with PHP support (e.g., Apache, Nginx)
- Code editor (e.g., VSCode, PhpStorm)

**Recommended Stack:** Use [XAMPP](https://www.apachefriends.org/) or [Laragon](https://laragon.org/) for local development.

---

## 📦 Installation

### 1. Clone the Repository
```bash
git clone https://github.com/rahmatyudi/jdar-travel.git
cd jdar-travel
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Set Up the Database
- Create a database named `jdar_travel`
- Import `app/db.sql` into MySQL
- Configure DB credentials in `app/dbConnection.php`

### 4. Configure Midtrans
- Sign up on [Midtrans Sandbox](https://dashboard.midtrans.com/)
- Get Server and Client Keys
- Update keys in `components/navBtn.php` or in a `.env` file:
```
MIDTRANS_SERVER_KEY=your-server-key
MIDTRANS_CLIENT_KEY=your-client-key
MIDTRANS_IS_PRODUCTION=false
```

### 5. Set Up PHPMailer (Optional)
- Edit credentials in `services/phpmailer/`
```php
$mail->Username = 'your-email@gmail.com';
$mail->Password = 'your-email-password';
```
- Uncomment PHPMailer code in `api/register.php` and `success.php`

### 6. Create `.env`
```bash
cp .env.example .env
```
Update necessary variables (e.g., DB, Midtrans keys).

### 7. Run the Application
```bash
php -S localhost:8000
```
Visit [http://localhost:8000](http://localhost:8000)

---

## 🚪 Usage

### User Flow
1. Register/login via `/api/register.php` or `/api/login.php`
2. Search or browse travel packages
3. Book and pay for a package using Midtrans
4. Receive confirmation email and download PDF receipt
5. Leave a review for the package

### Admin Flow
1. Login via `/api/login.php`
2. Set `is_admin` to `1` in `users` table via DB
3. Access `admin_dashboard.php`
4. Manage users, packages, and view reports

---

## 📷 Screenshots
- **Landing/Search:** `index.php`, `searchPackages.php`
- **Ratings/Reviews:** `countStars.php`, `user_review.php`
- **Admin Panel:** `admin_dashboard.php`
- **User Dashboard:** `user_dashboard.php`
- **Payments:** `checkOut.php`

---

## 💻 Built With
- **PHP**: Backend scripting
- **MySQL**: Database
- **Tailwind CSS**: Frontend styling
- **PHPMailer**: Email integration
- **Midtrans**: Secure payment gateway

---

## ⚡ Troubleshooting

### Payment Issues
- Confirm Midtrans credentials
- Review `logs/midtrans_log`
- Use Midtrans Dashboard to verify status
- Test transactions with endpoints like:
  - `POST https://api.sandbox.midtrans.com/v2/[transaction-id]/settle`

### Database Issues
- Ensure `app/dbConnection.php` has correct credentials
- Confirm database is properly imported

### Email Issues
- Verify credentials in `services/phpmailer/`
- Ensure PHPMailer code is uncommented in key files

---

## 📢 Contact
- **Name:** Rahmat Yudi Burhanudin
- **Email:** dewarahmat12334@gmail.com

---

## 👤 Authors
- **Rahmat Yudi Burhanudin** - [@rahmatyudi](https://github.com/rahmatyudi)

---

## 📄 License
This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## ✨ Acknowledgments
- Design and features inspired by modern travel booking platforms.

