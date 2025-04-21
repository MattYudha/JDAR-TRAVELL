JDAR Travel
JDAR Travel is a full-stack website built with raw PHP that handles the management of various tour and travel packages. Users can browse, search, and buy travel packages, generate PDF reports, write reviews, and manage bookings in real-time. Admins can manage users, tour packages, sales, and generate reports. The application integrates with Midtrans for secure payment processing.
Key Features
Users

User Validation: Validation on login and signup ensures unique usernames and emails. Users receive a confirmation email after registration (api/register.php, services/phpmailer/ - uncomment PHPMailer code to enable email).
Package Purchase Validation: Users cannot purchase the same package twice, cannot purchase after the package has started, and cannot purchase if the package capacity is full (order_form.php, packageAvailability.php).
Email Notifications: Users receive an email after completing a purchase (success.php - uncomment PHPMailer code in services/phpmailer/ to enable email).
Reviews and Reports: Users can write reviews and generate PDF reports after purchasing a package (user_review.php, sales_pdf.php).
Search and Browse: Search for desired packages directly from the landing page or browse all listed packages (searchPackages.php, package.php).
Real-Time Updates: Package ratings are updated in real-time after user reviews (countStars.php).

Admin

Package Management: View, add, and modify packages (new_package.php, packageAvailability.php).
User Management: Manage users, including activating or deactivating accounts (user_update.php).
Sales and Reports: View sales data and generate PDF reports (admin_dashboard.php, sales_pdf.php, generatePDF.php).
Admin Dashboard: Centralized dashboard to control the website (admin_dashboard.php).

Known Bugs

Check the app/dbConnection.php file for known bugs related to database connections or queries.

Project Structure

api/: API endpoints for login and registration (login.php, register.php).
app/: Core application files, including database connection (dbConnection.php, Database.php, db.sql).
assets/: Static assets like CSS, fonts, JavaScript, and images (css/, js/, logo/, favicon/).
auth/: Authentication-related components (auth/, components/).
components/: Reusable UI components like header, footer, and navigation (head.php, footer.php, navBtn.php).
services/: Backend services for checkout, transaction management, and more (checkOut.php, deleteTransaction.php).
utilities/: Helper utilities for star ratings and other functionalities (countStars.php).
logs/: Log files for authentication and Midtrans transactions (auth_log, midtrans_log).
Other Files:
index.php: Main entry point of the application.
package.php: Handles package-related logic.
success.php: Success page after booking.
composer.json: Dependency management with Composer.
tailwind.css: Styling using Tailwind CSS.



Prerequisites

PHP 7.4 or higher
MySQL database
Composer (for dependency management)
Midtrans Sandbox account (for payment integration)
Web server (e.g., Apache, Nginx) with PHP support
Code editor (e.g., VSCode, PhpStorm)

You can use:

XAMPP or Laragon for a local PHP and MySQL setup.

Installation

Clone the Repository:
git clone https://github.com/rahmatyudi/jdar-travel.git
cd jdar-travel


Install Dependencies:
composer install


Set Up the Database:

Create a new database named jdar_travel.
Import the database schema from app/db.sql into your MySQL database.
Update the database configuration in app/dbConnection.php with your database credentials.


Configure Midtrans:

Create a Midtrans Sandbox account at Midtrans Dashboard.

Get your Server Key and Client Key.

Update the Midtrans configuration in components/navBtn.php or create a .env file with your keys:
MIDTRANS_SERVER_KEY=your-server-key
MIDTRANS_CLIENT_KEY=your-client-key
MIDTRANS_IS_PRODUCTION=false




Set Up PHPMailer (Optional for Email Notifications):

Update the PHPMailer configuration in services/phpmailer/ with your email credentials (e.g., Gmail SMTP):
$mail->Username = 'your-email@gmail.com';
$mail->Password = 'your-email-password';


Uncomment the PHPMailer code in api/register.php and success.php to enable email notifications.

See PHPMailer for more details.



Set Up Environment:

Copy .env.example to .env and update the necessary variables (e.g., database credentials, Midtrans keys).
Ensure your web server is pointing to the project directory.


Run the Application:

Start your web server (e.g., using PHP's built-in server):
php -S localhost:8000


Access the application at http://localhost:8000.




Usage

User Flow:

Register or log in via /api/register.php or /api/login.php.
Search for travel packages using /searchPackages.php.
Book a package via /order_form.php and proceed to payment.
Complete payment using Midtrans (VA, GoPay, ShopeePay, etc.).
Write a review and download PDF receipts (user_review.php, sales_pdf.php).


Admin Flow:

Log in as an admin via /api/login.php.
To make a user an admin, go to your database -> jdar_travel -> users -> set is_admin to 1.
Access the admin dashboard (admin_dashboard.php).
Manage packages, view sales reports, and generate PDFs (new_package.php, generatePDF.php).



Screenshots

Search for Packages: Search for desired packages directly from the landing page (index.php).
Package Listings: Browse all listed packages or search based on destinations (searchPackages.php).
Package Ratings: Updated package ratings after user reviews (countStars.php).
User Reviews: View and write reviews for purchased packages (user_review.php).
Admin Dashboard: Control the website with the admin dashboard (admin_dashboard.php).
User Dashboard: Manage user info and bookings (user_dashboard.php).
Payment Gateway: Complete purchases using Midtrans (checkOut.php).

Built With

PHP: The scripting language used for the backend.
MySQL: The relational database management system used.
Tailwind CSS: For styling the frontend (tailwind.css).
PHPMailer: For sending email notifications (services/phpmailer/).
Midtrans: Payment gateway for secure transactions (navBtn.php).

Troubleshooting

Payment Issues:

Ensure your Midtrans Server Key is correct in the configuration.
Check the Midtrans logs (logs/midtrans_log) for error details.
Verify the transaction status in the Midtrans Dashboard.
Test transactions using endpoints like /settle, /approve, or /expire (e.g., POST https://api.sandbox.midtrans.com/v2/[transaction-id]/settle).


Database Errors:

Confirm that app/dbConnection.php has the correct database credentials.
Ensure the database schema (app/db.sql) is imported correctly.


Email Issues:

Verify your PHPMailer configuration in services/phpmailer/.
Ensure the PHPMailer code is uncommented in api/register.php and success.php.



Contact
For any inquiries or support, please contact:

Name: Rahmat Yudi Burhanudin
Email: dewarahmat12334@gmail.com

Authors

Rahmat Yudi Burhanudin - @rahmatyudi

License
This project is licensed under the MIT License - see the LICENSE file for details.
Acknowledgments

Design inspired by various travel booking platforms.

