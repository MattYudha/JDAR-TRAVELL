<?php
class Database
{
    private $db_host = "localhost";
    private $db_user = "root";
    private $db_password = "";
    private $db_name = "triptip";
    protected $conn;

    public function __construct()
    {
        $this->connect();
    }

    protected function connect()
    {
        try {
            $this->conn = new mysqli($this->db_host, $this->db_user, $this->db_password, $this->db_name);
            if ($this->conn->connect_error) {
                throw new Exception("Koneksi gagal: " . $this->conn->connect_error);
            }
        } catch (Exception $e) {
            error_log("Database connection failed: " . $e->getMessage());
            die(header("HTTP/1.0 503 Service Unavailable Error"));
        }
    }

    public function getConnection()
    {
        if (!$this->conn || !$this->conn->ping()) {
            $this->connect();
        }
        return $this->conn;
    }

    public function closeConnection()
    {
        if ($this->conn && $this->conn->ping()) {
            $this->conn->close();
            $this->conn = null;
        }
    }

    public function __destruct()
    {
        $this->closeConnection();
    }
}

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

    public function updatePackagePurchase($user_id, $package_id, $quantity)
    {
        $conn = $this->getConnection();
        $conn->begin_transaction();
        try {
            $sql = "SELECT COUNT(*) as count FROM purchases WHERE user_id = ? AND package_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $user_id, $package_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            if ($row['count'] > 0) {
                $conn->rollback();
                error_log("User $user_id already purchased package $package_id");
                return "409";
            }

            $sql = "SELECT package_capacity, COALESCE(package_booked, 0) as booked FROM packages WHERE package_id = ? FOR UPDATE";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $package_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $package = $result->fetch_assoc();

            if (!$package) {
                $conn->rollback();
                error_log("Package $package_id not found");
                return "404";
            }

            $available_slots = $package['package_capacity'] - $package['booked'];
            if ($quantity > $available_slots) {
                $conn->rollback();
                error_log("Not enough slots for package $package_id. Requested: $quantity, Available: $available_slots");
                return "400";
            }

            $new_booked = $package['booked'] + $quantity;
            $sql = "UPDATE packages SET package_booked = ? WHERE package_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $new_booked, $package_id);
            $stmt->execute();

            $sql = "INSERT INTO purchases (user_id, package_id, quantity) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iii", $user_id, $package_id, $quantity);
            $stmt->execute();

            $conn->commit();
            error_log("User $user_id successfully purchased package $package_id with $quantity units");
            return "200";
        } catch (Exception $e) {
            $conn->rollback();
            error_log("Error in updatePackagePurchase: " . $e->getMessage());
            return "500";
        }
    }

    public function updateRating($user_id, $package_id, $rating, $comment = null)
    {
        $conn = $this->getConnection();
        $conn->begin_transaction();
        try {
            $sql = "SELECT COUNT(*) as count FROM purchases WHERE user_id = ? AND package_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $user_id, $package_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            if ($row['count'] == 0) {
                $conn->rollback();
                error_log("User $user_id has not purchased package $package_id");
                return "403";
            }

            $sql = "SELECT package_end FROM packages WHERE package_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $package_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $package = $result->fetch_assoc();
            if (strtotime($package['package_end']) > time()) {
                $conn->rollback();
                error_log("Tour for package $package_id has not finished yet");
                return "403";
            }

            $sql = "INSERT INTO testimonials (package_id, user_id, rating, comment) VALUES (?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE rating = ?, comment = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iidsds", $package_id, $user_id, $rating, $comment, $rating, $comment);
            $stmt->execute();

            $sql = "SELECT AVG(rating) as avg_rating FROM testimonials WHERE package_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $package_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $avg_rating = $result->fetch_assoc()['avg_rating'];

            $sql = "UPDATE packages SET package_rating = ? WHERE package_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("di", $avg_rating, $package_id);
            $stmt->execute();

            $conn->commit();
            error_log("User $user_id rated package $package_id with rating $rating");
            return "200";
        } catch (Exception $e) {
            $conn->rollback();
            error_log("Error in updateRating: " . $e->getMessage());
            return "500";
        }
    }

    public function getDistinctDestinations()
    {
        $result = $this->getConnection()->query("SELECT DISTINCT package_location FROM packages");
        $destinations = [];
        while ($row = $result->fetch_assoc()) {
            $destinations[] = $row['package_location'];
        }
        return $destinations;
    }

    public function searchPackages($destination, $checkin, $checkout, $quantity, $accommodation, $budget, $facilities)
    {
        $sql = "SELECT *, package_price * (1 - (discount_percentage / 100)) as discounted_price FROM packages WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($destination)) {
            $destination = $this->getConnection()->real_escape_string($destination);
            $sql .= " AND package_location = ?";
            $params[] = $destination;
            $types .= "s";
        }
        if (!empty($checkin) && !empty($checkout)) {
            $sql .= " AND package_start <= ? AND package_end >= ?";
            $params[] = $checkout;
            $params[] = $checkin;
            $types .= "ss";
        }
        if (!empty($accommodation)) {
            $sql .= " AND is_hotel = ?";
            $params[] = $accommodation;
            $types .= "i";
        }
        if (!empty($budget)) {
            $sql .= " AND package_price * (1 - (discount_percentage / 100)) <= ?";
            $params[] = $budget;
            $types .= "d";
        }
        if (!empty($facilities) && is_array($facilities)) {
            foreach ($facilities as $facility) {
                $sql .= " AND is_$facility = 1";
            }
        }
        $sql .= " AND (package_capacity - COALESCE(package_booked, 0)) >= ?";
        $params[] = $quantity;
        $types .= "i";

        $stmt = $this->getConnection()->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $packages = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $packages;
    }

    public function getReviewCount($package_id)
    {
        $stmt = $this->getConnection()->prepare("SELECT COUNT(*) as review_count FROM testimonials WHERE package_id = ?");
        $stmt->bind_param("i", $package_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_assoc()['review_count'] ?? 0;
        $stmt->close();
        return $count;
    }
}

class Coupons extends Database
{
    public function getActiveCoupons()
    {
        $sql = "SELECT * FROM coupons WHERE is_active = 1 AND valid_until >= CURDATE() ORDER BY valid_until ASC";
        $result = $this->getConnection()->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
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

class Users extends Database
{
    public function register($email, $password, $full_name, $phone)
    {
        $email = $this->getConnection()->real_escape_string($email);
        $password = password_hash($password, PASSWORD_DEFAULT);
        $full_name = $this->getConnection()->real_escape_string($full_name);
        $phone = $this->getConnection()->real_escape_string($phone);

        $stmt = $this->getConnection()->prepare("INSERT INTO users (email, password, full_name, phone) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $email, $password, $full_name, $phone);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function login($email, $password)
    {
        $email = $this->getConnection()->real_escape_string($email);
        $stmt = $this->getConnection()->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    public function getUser($user_id)
    {
        $stmt = $this->getConnection()->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        if (!$stmt) {
            error_log("Prepare failed in getUser: " . $this->getConnection()->error);
            return false;
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        if (!$user) {
            error_log("User not found for id: $user_id");
        }
        return $user;
    }

    public function getAllUsers($limit = 1000)
    {
        $stmt = $this->getConnection()->prepare("SELECT * FROM users WHERE is_admin = 0 ORDER BY date_created LIMIT ?");
        $stmt->bind_param("i", $limit);
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
        $stmt->close();
        return $success ? "200" : "500";
    }

    public function updateAccountStatus($user_id, $status)
    {
        $stmt = $this->getConnection()->prepare("UPDATE users SET account_status = ? WHERE id = ?");
        $stmt->bind_param("ii", $status, $user_id);
        $success = $stmt->execute();
        $stmt->close();
        return $success ? "200" : "500";
    }
}

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
        error_log("Membuat pengguna: $email dengan kata sandi terenkripsi: $hashed_pass");
        $stmt = $this->getConnection()->prepare("INSERT INTO users (username, email, user_pass, date_created) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $hashed_pass, $date_created);
        $success = $stmt->execute();
        $stmt->close();
        if ($success) {
            return "200";
        } else {
            error_log("Gagal memasukkan pengguna: " . $this->getConnection()->error);
            return "500";
        }
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
            error_log("Kata sandi di database: " . $user['user_pass']);
            error_log("Kata sandi yang dimasukkan: " . $pass);
            if ($user['user_pass'] === $pass) {
                $hashed_pass = password_hash($pass, PASSWORD_BCRYPT);
                $update_stmt = $this->getConnection()->prepare("UPDATE users SET user_pass = ? WHERE email = ?");
                $update_stmt->bind_param("ss", $hashed_pass, $email);
                $update_stmt->execute();
                $update_stmt->close();
                error_log("Memperbarui kata sandi untuk $email menjadi terenkripsi: $hashed_pass");
            } elseif (strlen($user['user_pass']) === 40 && ctype_xdigit($user['user_pass'])) {
                if (sha1($pass) === $user['user_pass']) {
                    $hashed_pass = password_hash($pass, PASSWORD_BCRYPT);
                    $update_stmt = $this->getConnection()->prepare("UPDATE users SET user_pass = ? WHERE email = ?");
                    $update_stmt->bind_param("ss", $hashed_pass, $email);
                    $update_stmt->execute();
                    $update_stmt->close();
                    error_log("Memperbarui kata sandi untuk $email menjadi terenkripsi: $hashed_pass");
                } else {
                    $stmt->close();
                    return "404";
                }
            } elseif (password_verify($pass, $user['user_pass'])) {
                // Kata sandi sudah menggunakan BCRYPT
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

class Transactions extends Database
{
    public function createTransaction($user_id, $package_id, $quantity, $visit_date, $total_price, $coupon_code = '')
    {
        $user_id = (int)$user_id;
        $package_id = (int)$package_id;
        $quantity = (int)$quantity;
        $visit_date = $this->getConnection()->real_escape_string($visit_date);
        $total_price = (float)$total_price;
        $coupon_code = $this->getConnection()->real_escape_string($coupon_code);
        $status = 'pending';

        $stmt = $this->getConnection()->prepare(
            "INSERT INTO transactions (user_id, package_id, quantity, visit_date, total_price, coupon_code, status) VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        if (!$stmt) {
            error_log("Prepare failed in createTransaction: " . $this->getConnection()->error);
            return false;
        }
        $stmt->bind_param("iissdss", $user_id, $package_id, $quantity, $visit_date, $total_price, $coupon_code, $status);
        $success = $stmt->execute();
        $insert_id = $this->getConnection()->insert_id;
        $stmt->close();
        if (!$success) {
            error_log("Execute failed in createTransaction: " . $stmt->error);
            return false;
        }
        return $insert_id;
    }

    public function getTransaction($trans_id)
    {
        $stmt = $this->getConnection()->prepare(
            "SELECT t.*, p.package_name, p.package_location 
            FROM transactions t 
            JOIN packages p ON t.package_id = p.package_id 
            WHERE t.trans_id = ?"
        );
        $stmt->bind_param("s", $trans_id); // trans_id adalah string (varchar)
        $stmt->execute();
        $result = $stmt->get_result();
        $transaction = $result->fetch_assoc();
        $stmt->close();
        return $transaction;
    }

    public function updateTransactionStatus($trans_id, $status)
    {
        $status = $this->getConnection()->real_escape_string($status);
        $stmt = $this->getConnection()->prepare("UPDATE transactions SET status = ? WHERE trans_id = ?");
        $stmt->bind_param("ss", $status, $trans_id); // trans_id adalah string (varchar)
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function getAllTransactions($limit = 1000)
    {
        $stmt = $this->getConnection()->prepare(
            "SELECT t.*, u.full_name, p.package_name 
            FROM transactions t 
            INNER JOIN users u ON t.user_id = u.id 
            INNER JOIN packages p ON t.package_id = p.package_id 
            ORDER BY t.trans_id DESC LIMIT ?"
        );
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function checkUserTransaction($user_id, $package_id)
    {
        $stmt = $this->getConnection()->prepare("SELECT * FROM transactions WHERE user_id = ? AND package_id = ?");
        $stmt->bind_param("ii", $user_id, $package_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function userAllTransactions($user_id)
    {
        $stmt = $this->getConnection()->prepare(
            "SELECT t.*, p.package_name 
            FROM transactions t 
            INNER JOIN packages p ON t.package_id = p.package_id 
            WHERE t.user_id = ? 
            ORDER BY t.trans_id DESC" // Mengganti transaction_id menjadi trans_id
        );
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function getTotalTransactionAmount()
    {
        $result = $this->getConnection()->query("SELECT SUM(total_price) FROM transactions");
        $total = $result->fetch_row()[0] ?? 0;
        return $total;
    }

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
}

class Reviews extends Database
{
    public function addReview($user_id, $package_id, $rating, $comment)
    {
        $comment = $this->getConnection()->real_escape_string($comment);
        $stmt = $this->getConnection()->prepare("INSERT INTO testimonials (user_id, package_id, rating, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iids", $user_id, $package_id, $rating, $comment);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function getReviews($package_id)
    {
        $stmt = $this->getConnection()->prepare(
            "SELECT r.*, u.full_name 
            FROM testimonials r 
            JOIN users u ON r.user_id = u.id 
            WHERE r.package_id = ? 
            ORDER BY r.date_created DESC"
        );
        $stmt->bind_param("i", $package_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $reviews = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $reviews;
    }

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

    public function checkUserTestimonialStatus($user_id)
    {
        $stmt = $this->getConnection()->prepare("SELECT DISTINCT package_id FROM testimonials WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }
}
?>