<?php
ob_start(); // Start output buffering

// Prevent multiple inclusions of this file
if (defined('DB_CONNECTION_INCLUDED')) {
    return;
}
define('DB_CONNECTION_INCLUDED', true);

// Include Database.php for the Database class and its subclasses
require_once __DIR__ . '/Database.php';

// Packages class extending Database
if (!class_exists('Packages')) {
    class Packages extends Database
    {
        public function createPackage($package_name, $package_desc, $package_start, $package_end, $package_price, $discount_percentage, $package_location, $is_hotel, $is_transport, $is_food, $is_guide, $package_capacity, $map_loc, $master_image, $extra_image_1, $extra_image_2)
        {
            if (empty($package_name) || $package_price < 0 || $package_capacity < 0) {
                error_log("Invalid input: package_name, price, or capacity");
                return "400";
            }

            if (!is_numeric($discount_percentage) || $discount_percentage < 0 || $discount_percentage > 100) {
                error_log("Invalid discount percentage: $discount_percentage");
                return "400";
            }

            $discounted_price = $package_price * (1 - ($discount_percentage / 100));
            if ($discounted_price < 0) {
                error_log("Discounted price is negative: $discounted_price");
                return "400";
            }

            if (strtotime($package_start) >= strtotime($package_end)) {
                error_log("Invalid date range: start=$package_start, end=$package_end");
                return "400";
            }

            $stmt = $this->getConnection()->prepare(
                "INSERT INTO packages (package_name, package_desc, package_start, package_end, package_price, discount_percentage, package_location, is_hotel, is_transport, is_food, is_guide, package_capacity, map_loc, master_image, extra_image_1, extra_image_2) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );

            if (!$stmt) {
                error_log("Prepare failed: " . $this->getConnection()->error);
                return "500";
            }

            $stmt->bind_param(
                "ssssddiiiiisssss",
                $package_name,
                $package_desc,
                $package_start,
                $package_end,
                $package_price,
                $discount_percentage,
                $package_location,
                $is_hotel,
                $is_transport,
                $is_food,
                $is_guide,
                $package_capacity,
                $map_loc,
                $master_image,
                $extra_image_1,
                $extra_image_2
            );

            $success = $stmt->execute();
            if (!$success) {
                error_log("Execute failed: " . $stmt->error);
                $stmt->close();
                return "500";
            }

            $stmt->close();
            return "200";
        }

        public function updatePackage($package_id, $package_name, $package_desc, $package_start, $package_end, $package_price, $discount_percentage, $package_location, $is_hotel, $is_transport, $is_food, $is_guide, $package_capacity, $map_loc, $master_image, $extra_image_1, $extra_image_2)
        {
            if (empty($package_name) || $package_price < 0 || $package_capacity < 0) {
                error_log("Invalid input: package_name, price, or capacity");
                return "400";
            }

            if (!is_numeric($discount_percentage) || $discount_percentage < 0 || $discount_percentage > 100) {
                error_log("Invalid discount percentage: $discount_percentage");
                return "400";
            }

            $discounted_price = $package_price * (1 - ($discount_percentage / 100));
            if ($discounted_price < 0) {
                error_log("Discounted price is negative: $discounted_price");
                return "400";
            }

            if (strtotime($package_start) >= strtotime($package_end)) {
                error_log("Invalid date range: start=$package_start, end=$package_end");
                return "400";
            }

            $stmt = $this->getConnection()->prepare(
                "UPDATE packages SET package_name = ?, package_desc = ?, package_start = ?, package_end = ?, package_price = ?, discount_percentage = ?, package_location = ?, is_hotel = ?, is_transport = ?, is_food = ?, is_guide = ?, package_capacity = ?, map_loc = ?, master_image = ?, extra_image_1 = ?, extra_image_2 = ? WHERE package_id = ?"
            );

            if (!$stmt) {
                error_log("Prepare failed: " . $this->getConnection()->error);
                return "500";
            }

            $stmt->bind_param(
                "ssssddiiiiisssssi",
                $package_name,
                $package_desc,
                $package_start,
                $package_end,
                $package_price,
                $discount_percentage,
                $package_location,
                $is_hotel,
                $is_transport,
                $is_food,
                $is_guide,
                $package_capacity,
                $map_loc,
                $master_image,
                $extra_image_1,
                $extra_image_2,
                $package_id
            );

            $success = $stmt->execute();
            if (!$success) {
                error_log("Execute failed: " . $stmt->error);
                $stmt->close();
                return "500";
            }

            $stmt->close();
            return "200";
        }

        public function getPackages($location, $start = 0, $end = 1000)
        {
            if ($location == "All") {
                $stmt = $this->getConnection()->prepare("SELECT *, package_price * (1 - (discount_percentage / 100)) as discounted_price FROM packages ORDER BY package_id DESC LIMIT ?, ?");
                $stmt->bind_param("ii", $start, $end);
            } else {
                $location = "%$location%";
                $stmt = $this->getConnection()->prepare("SELECT *, package_price * (1 - (discount_percentage / 100)) as discounted_price FROM packages WHERE package_location LIKE ? ORDER BY package_id DESC LIMIT ?, ?");
                $stmt->bind_param("sii", $location, $start, $end);
            }

            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        }

        public function getPackage($id)
        {
            $stmt = $this->getConnection()->prepare("SELECT *, package_price * (1 - (discount_percentage / 100)) as discounted_price FROM packages WHERE package_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        }

        public function getPackagesCount()
        {
            $result = $this->getConnection()->query("SELECT COUNT(*) as count FROM packages");
            $row = $result->fetch_assoc();
            return $row['count'];
        }

        public function getPackagesWithQueryCount($location)
        {
            $location = "%$location%";
            $stmt = $this->getConnection()->prepare("SELECT COUNT(*) as count FROM packages WHERE package_location LIKE ?");
            $stmt->bind_param("s", $location);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            return $row['count'];
        }

        public function updatePackagePurchase($id, $count)
        {
            $stmt = $this->getConnection()->prepare("UPDATE packages SET package_booked = ? WHERE package_id = ?");
            $stmt->bind_param("ii", $count, $id);
            $success = $stmt->execute();
            if (!$success) {
                error_log("Execute failed: " . $stmt->error);
                $stmt->close();
                return "500";
            }
            $stmt->close();
            return "200";
        }

        public function updateRating($id, $rating)
        {
            $stmt = $this->getConnection()->prepare("UPDATE packages SET package_rating = ? WHERE package_id = ?");
            $stmt->bind_param("di", $rating, $id);
            $success = $stmt->execute();
            if (!$success) {
                error_log("Execute failed: " . $stmt->error);
                $stmt->close();
                return "500";
            }
            $stmt->close();
            return "200";
        }
    }
}

// Auth class extending Database
if (!class_exists('Auth')) {
    class Auth extends Database
    {
        public function checkUserName($username)
        {
            $stmt = $this->getConnection()->prepare("SELECT username FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            return $result->num_rows === 0;
        }

        public function checkEmail($email)
        {
            $stmt = $this->getConnection()->prepare("SELECT email FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            return $result->num_rows === 0;
        }

        public function createUser($username, $email, $pass)
        {
            $date_created = date("Y-m-d H:i:s");
            $hashed_pass = password_hash($pass, PASSWORD_BCRYPT);
            error_log("Creating user: $email with hashed password: $hashed_pass");
            $stmt = $this->getConnection()->prepare("INSERT INTO users (username, email, user_pass, date_created) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $hashed_pass, $date_created);
            $success = $stmt->execute();
            if (!$success) {
                error_log("Failed to insert user: " . $this->getConnection()->error);
                return "500";
            }
            $stmt->close();
            return "200";
        }

        public function checkAccountStatus($email)
        {
            $stmt = $this->getConnection()->prepare("SELECT account_status FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();
            if ($result->num_rows > 0 && $user['account_status'] == 0) {
                return false;
            }
            return true;
        }

        public function loginUser($email, $pass)
        {
            $stmt = $this->getConnection()->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                error_log("Password in database: " . $user['user_pass']);
                error_log("Password entered: " . $pass);
                if ($user['user_pass'] === $pass) {
                    $hashed_pass = password_hash($pass, PASSWORD_BCRYPT);
                    $update_stmt = $this->getConnection()->prepare("UPDATE users SET user_pass = ? WHERE email = ?");
                    $update_stmt->bind_param("ss", $hashed_pass, $email);
                    $update_stmt->execute();
                    $update_stmt->close();
                    error_log("Updating password for $email to hashed: $hashed_pass");
                } elseif (strlen($user['user_pass']) === 40 && ctype_xdigit($user['user_pass'])) {
                    if (sha1($pass) === $user['user_pass']) {
                        $hashed_pass = password_hash($pass, PASSWORD_BCRYPT);
                        $update_stmt = $this->getConnection()->prepare("UPDATE users SET user_pass = ? WHERE email = ?");
                        $update_stmt->bind_param("ss", $hashed_pass, $email);
                        $update_stmt->execute();
                        $update_stmt->close();
                        error_log("Updating password for $email to hashed: $hashed_pass");
                    } else {
                        $stmt->close();
                        return "404";
                    }
                } elseif (password_verify($pass, $user['user_pass'])) {
                    // Password is already BCRYPT
                } else {
                    $stmt->close();
                    return "404";
                }
                if (!isset($_SESSION)) {
                    session_start();
                }
                if ($user['is_admin'] == '1') {
                    $_SESSION["is_admin"] = true;
                }
                $_SESSION["user_id"] = $user['id'];
                $_SESSION["logged_in"] = true;
                $_SESSION["Email"] = $email;
                $stmt->close();
                return "200";
            } else {
                $stmt->close();
                return "404";
            }
        }
    }
}

// Users class extending Database
if (!class_exists('Users')) {
    class Users extends Database
    {
        public function getAllUsers($limit = 1000)
        {
            $stmt = $this->getConnection()->prepare("SELECT * FROM users WHERE is_admin = 0 ORDER BY date_created LIMIT ?");
            $stmt->bind_param("i", $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        }

        public function getUser($id)
        {
            $stmt = $this->getConnection()->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        }

        public function getUsersCount()
        {
            $result = $this->getConnection()->query("SELECT COUNT(*) FROM users WHERE is_admin = 0");
            $count = $result->fetch_row()[0];
            return $count;
        }

        public function updateUser($user_id, $email, $phone, $name, $address)
        {
            $stmt = $this->getConnection()->prepare("UPDATE users SET email = ?, phone = ?, address = ?, full_name = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $email, $phone, $address, $name, $user_id);
            $success = $stmt->execute();
            if (!$success) {
                error_log("Execute failed: " . $stmt->error);
                $stmt->close();
                return "500";
            }
            $stmt->close();
            return "200";
        }

        public function updateAccountStatus($user_id, $status)
        {
            $stmt = $this->getConnection()->prepare("UPDATE users SET account_status = ? WHERE id = ?");
            $stmt->bind_param("ii", $status, $user_id);
            $success = $stmt->execute();
            if (!$success) {
                error_log("Execute failed: " . $stmt->error);
                $stmt->close();
                return "500";
            }
            $stmt->close();
            return "200";
        }
    }
}

// Coupons class extending Database
if (!class_exists('Coupons')) {
    class Coupons extends Database
    {
        public function getActiveCoupons($limit = 1000)
        {
            $stmt = $this->getConnection()->prepare(
                "SELECT * FROM coupons WHERE is_active = 1 AND valid_until >= CURDATE() ORDER BY valid_until ASC LIMIT ?"
            );
            $stmt->bind_param("i", $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        }

        public function getCouponsCount()
        {
            $result = $this->getConnection()->query("SELECT COUNT(*) as count FROM coupons WHERE is_active = 1 AND valid_until >= CURDATE()");
            $row = $result->fetch_assoc();
            return $row['count'];
        }

        public function getCouponByCode($coupon_code)
        {
            $coupon_code = $this->getConnection()->real_escape_string($coupon_code);
            $stmt = $this->getConnection()->prepare("SELECT * FROM coupons WHERE coupon_code = ? AND is_active = 1 AND valid_until >= CURDATE()");
            $stmt->bind_param("s", $coupon_code);
            $stmt->execute();
            $result = $stmt->get_result();
            $coupon = $result->fetch_assoc();
            $stmt->close();
            return $coupon;
        }

        public function applyCoupon($coupon_code, $total_price)
        {
            $coupon = $this->getCouponByCode($coupon_code);
            if (!$coupon) {
                return ['success' => false, 'message' => 'Kode kupon tidak valid atau telah kedaluwarsa'];
            }

            $discount = $total_price * ($coupon['discount_percentage'] / 100);
            $new_price = $total_price - $discount;

            return [
                'success' => true,
                'discount_percentage' => $coupon['discount_percentage'],
                'discount_amount' => $discount,
                'new_price' => $new_price
            ];
        }
    }
}

// Testimonials class extending Database
if (!class_exists('Testimonials')) {
    class Testimonials extends Database
    {
        public function getAllTestimonials($limit = 1000)
        {
            $stmt = $this->getConnection()->prepare(
                "SELECT t.*, u.full_name, p.package_name 
                FROM testimonials t 
                INNER JOIN users u ON t.user_id = u.id 
                INNER JOIN packages p ON t.package_id = p.package_id 
                ORDER BY t.date_created DESC LIMIT ?"
            );
            $stmt->bind_param("i", $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        }

        public function getPackageTestimonials($package_id, $limit = 1000)
        {
            $stmt = $this->getConnection()->prepare(
                "SELECT t.*, u.full_name, p.package_name 
                FROM testimonials t 
                INNER JOIN users u ON t.user_id = u.id 
                INNER JOIN packages p ON t.package_id = p.package_id 
                WHERE t.package_id = ? 
                ORDER BY t.date_created DESC LIMIT ?"
            );
            $stmt->bind_param("ii", $package_id, $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        }

        public function checkUserTestimonialStatus($user_id)
        {
            $stmt = $this->getConnection()->prepare("SELECT DISTINCT package_id FROM testimonials WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        }

        public function addTestimonial($desc, $user_id, $package_id, $rating)
        {
            $desc = $this->getConnection()->real_escape_string($desc);
            $stmt = $this->getConnection()->prepare("INSERT INTO testimonials (message, user_id, package_id, rating) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("siii", $desc, $user_id, $package_id, $rating);
            $success = $stmt->execute();
            if (!$success) {
                error_log("Execute failed: " . $stmt->error);
                $stmt->close();
                return "500";
            }
            $stmt->close();
            return "200";
        }
    }
}

// Transactions class extending Database
if (!class_exists('Transactions')) {
    class Transactions extends Database
    {
        public function getUserTransaction($user_id, $package_id)
        {
            $stmt = $this->getConnection()->prepare("CALL getUserTransaction(?, ?)");
            if (!$stmt) {
                error_log("Prepare failed: " . $this->getConnection()->error);
                return false;
            }

            $stmt->bind_param("ii", $user_id, $package_id);
            $success = $stmt->execute();
            if (!$success) {
                error_log("Execute failed: " . $stmt->error);
                $stmt->close();
                return false;
            }

            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        }

        public function getAllTransactions($limit = 1000)
        {
            $stmt = $this->getConnection()->prepare(
                "SELECT t.trans_id, t.user_id, t.package_id, t.total_price, t.created_at, u.username, p.package_name 
                FROM transactions t 
                INNER JOIN users u ON t.user_id = u.id 
                INNER JOIN packages p ON t.package_id = p.package_id 
                ORDER BY t.created_at DESC LIMIT ?"
            );
            $stmt->bind_param("i", $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        }

        public function getTotalTransactionAmount()
        {
            $result = $this->getConnection()->query("SELECT SUM(total_price) as total FROM transactions");
            $row = $result->fetch_assoc();
            return $row['total'] ?? 0;
        }

        public function getRangedTransitions($days)
        {
            $stmt = $this->getConnection()->prepare(
                "SELECT t.trans_id, t.user_id, t.package_id, t.total_price, t.created_at, u.username, p.package_name 
                FROM transactions t 
                INNER JOIN users u ON t.user_id = u.id 
                INNER JOIN packages p ON t.package_id = p.package_id 
                WHERE t.created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY) 
                ORDER BY t.created_at DESC"
            );
            $stmt->bind_param("i", $days);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        }

        public function getRangedTransitionsTotal($days)
        {
            $stmt = $this->getConnection()->prepare(
                "SELECT SUM(t.total_price) as total 
                FROM transactions t 
                WHERE t.created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)"
            );
            $stmt->bind_param("i", $days);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            return $row['total'] ?? 0;
        }
    }
}
?>