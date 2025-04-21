<?php
ob_start(); // Mulai menahan output

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
            die(header("HTTP/1.0 503 Service Unavailable Error"));
        }
    }

    public function getConnection()
    {
        // Periksa apakah koneksi masih valid
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
    public function createPackage($package_name, $package_desc, $package_start, $package_end, $package_price, $package_location, $is_hotel, $is_transport, $is_food, $is_guide, $package_capacity, $map_loc, $master_image, $extra_image_1, $extra_image_2)
    {
        $stmt = $this->getConnection()->prepare(
            "INSERT INTO packages (package_name, package_desc, package_start, package_end, package_price, package_location, is_hotel, is_transport, is_food, is_guide, package_capacity, map_loc, master_image, extra_image_1, extra_image_2) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "ssssdssiiiissss",
            $package_name,
            $package_desc,
            $package_start,
            $package_end,
            $package_price,
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
        $stmt->execute();
        $stmt->close();
        return "200";
    }

    public function updatePackage($package_id, $package_name, $package_desc, $package_start, $package_end, $package_price, $package_location, $is_hotel, $is_transport, $is_food, $is_guide, $package_capacity, $map_loc, $master_image, $extra_image_1, $extra_image_2)
    {
        $stmt = $this->getConnection()->prepare(
            "UPDATE packages SET package_name = ?, package_desc = ?, package_start = ?, package_end = ?, package_price = ?, package_location = ?, is_hotel = ?, is_transport = ?, is_food = ?, is_guide = ?, package_capacity = ?, map_loc = ?, master_image = ?, extra_image_1 = ?, extra_image_2 = ? WHERE package_id = ?"
        );
        $stmt->bind_param(
            "ssssdssiiiissssi",
            $package_name,
            $package_desc,
            $package_start,
            $package_end,
            $package_price,
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
        $stmt->execute();
        $stmt->close();
        return "200";
    }

    public function getPackages($location, $start = 0, $end = 1000)
    {
        if ($location == "All") {
            $stmt = $this->getConnection()->prepare("SELECT * FROM packages ORDER BY package_id DESC LIMIT ?, ?");
            $stmt->bind_param("ii", $start, $end);
        } else {
            $location = "%$location%";
            $stmt = $this->getConnection()->prepare("SELECT * FROM packages WHERE package_location LIKE ? ORDER BY package_id DESC LIMIT ?, ?");
            $stmt->bind_param("sii", $location, $start, $end);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function getPackage($id)
    {
        $stmt = $this->getConnection()->prepare("SELECT * FROM packages WHERE package_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function getPackagesCount()
    {
        $result = $this->getConnection()->query("SELECT COUNT(*) FROM packages");
        $count = $result->fetch_row()[0];
        return $count;
    }

    public function getPackagesWithQueryCount($location)
    {
        $location = "%$location%";
        $stmt = $this->getConnection()->prepare("SELECT COUNT(*) FROM packages WHERE package_location LIKE ?");
        $stmt->bind_param("s", $location);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_row()[0];
        $stmt->close();
        return $count;
    }

    public function updatePackagePurchase($id, $count)
    {
        $stmt = $this->getConnection()->prepare("UPDATE packages SET package_booked = ? WHERE package_id = ?");
        $stmt->bind_param("ii", $count, $id);
        $stmt->execute();
        $stmt->close();
        return '200';
    }

    public function updateRating($id, $rating)
    {
        $stmt = $this->getConnection()->prepare("UPDATE packages SET package_rating = ? WHERE package_id = ?");
        $stmt->bind_param("di", $rating, $id);
        $stmt->execute();
        $stmt->close();
        return '200';
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
        if (!$success) {
            error_log("Gagal memasukkan pengguna: " . $this->getConnection()->error);
        }
        $stmt->close();
        if ($success) {
            return "200";
        } else {
            die(header("HTTP/1.0 500 Internal Server Error"));
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
            // Cek apakah kata sandi adalah teks biasa (untuk pengguna lama seperti mattyudha)
            if ($user['user_pass'] === $pass) {
                // Update kata sandi ke hash BCRYPT
                $hashed_pass = password_hash($pass, PASSWORD_BCRYPT);
                $update_stmt = $this->getConnection()->prepare("UPDATE users SET user_pass = ? WHERE email = ?");
                $update_stmt->bind_param("ss", $hashed_pass, $email);
                $update_stmt->execute();
                $update_stmt->close();
                error_log("Memperbarui kata sandi untuk $email menjadi terenkripsi: $hashed_pass");
            }
            // Cek apakah kata sandi adalah hash SHA-1 (panjang 40 karakter, semua hexadecimal)
            elseif (strlen($user['user_pass']) === 40 && ctype_xdigit($user['user_pass'])) {
                if (sha1($pass) === $user['user_pass']) {
                    // Update kata sandi ke hash BCRYPT
                    $hashed_pass = password_hash($pass, PASSWORD_BCRYPT);
                    $update_stmt = $this->getConnection()->prepare("UPDATE users SET user_pass = ? WHERE email = ?");
                    $update_stmt->bind_param("ss", $hashed_pass, $email);
                    $update_stmt->execute();
                    $update_stmt->close();
                    error_log("Memperbarui kata sandi untuk $email menjadi terenkripsi: $hashed_pass");
                } else {
                    $stmt->close();
                    die(header("HTTP/1.0 404 Kata Sandi Salah"));
                }
            }
            // Verifikasi kata sandi dengan BCRYPT
            elseif (password_verify($pass, $user['user_pass'])) {
                // Kata sandi sudah menggunakan BCRYPT, lanjutkan
            } else {
                $stmt->close();
                die(header("HTTP/1.0 404 Kata Sandi Salah"));
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
            die(header("HTTP/1.0 404 Email Tidak Ditemukan"));
        }
    }
}

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
        $stmt->execute();
        $stmt->close();
        return '200';
    }

    public function updateAccountStatus($user_id, $status)
    {
        $stmt = $this->getConnection()->prepare("UPDATE users SET account_status = ? WHERE id = ?");
        $stmt->bind_param("ii", $status, $user_id);
        $stmt->execute();
        $stmt->close();
        return '200';
    }
}

class Transactions extends Database
{
    public function getAllTransactions($limit = 1000)
    {
        $stmt = $this->getConnection()->prepare(
            "SELECT * FROM transactions 
            INNER JOIN users ON transactions.user_id = users.id 
            INNER JOIN packages ON transactions.package_id = packages.package_id 
            ORDER BY trans_date LIMIT ?"
        );
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function getTransaction($id)
    {
        $stmt = $this->getConnection()->prepare("SELECT * FROM transactions WHERE trans_id = ?");
        $stmt->bind_param("s", $id); // trans_id adalah string (e.g., JDAR_xxx)
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
            "SELECT * FROM transactions 
            INNER JOIN packages ON transactions.package_id = packages.package_id 
            WHERE user_id = ? ORDER BY trans_date"
        );
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function getUserTransaction($user_id, $package_id)
    {
        $stmt = $this->getConnection()->prepare(
            "SELECT * FROM transactions 
            INNER JOIN users ON transactions.user_id = users.id 
            INNER JOIN packages ON transactions.package_id = packages.package_id 
            WHERE user_id = ? AND transactions.package_id = ? 
            ORDER BY trans_date LIMIT 1"
        );
        $stmt->bind_param("ii", $user_id, $package_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function getTotalTransactionAmount()
    {
        $result = $this->getConnection()->query("SELECT SUM(trans_amount) FROM transactions");
        $total = $result->fetch_row()[0] ?? 0;
        return $total;
    }

    public function createNewTransaction($trans_id, $user_id, $package_id, $trans_amount, $trans_date, $card_no, $val_id, $card_type)
    {
        $stmt = $this->getConnection()->prepare(
            "INSERT INTO transactions (trans_id, user_id, package_id, trans_amount, trans_date, card_no, val_id, card_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "siiissss",
            $trans_id,
            $user_id,
            $package_id,
            $trans_amount,
            $trans_date,
            $card_no,
            $val_id,
            $card_type
        );
        $success = $stmt->execute();
        if (!$success) {
            error_log("Gagal membuat transaksi: " . $this->getConnection()->error);
        }
        $stmt->close();
        return $success ? "200" : "500";
    }

    public function updateTransaction($trans_id, $card_type, $val_id)
    {
        $stmt = $this->getConnection()->prepare(
            "UPDATE transactions SET card_type = ?, val_id = ? WHERE trans_id = ?"
        );
        $stmt->bind_param("sss", $card_type, $val_id, $trans_id);
        $success = $stmt->execute();
        if (!$success) {
            error_log("Gagal memperbarui transaksi: " . $this->getConnection()->error);
        }
        $stmt->close();
        return $success ? "200" : "500";
    }

    public function getRangedTransitions($days)
    {
        $stmt = $this->getConnection()->prepare(
            "SELECT * FROM transactions 
            INNER JOIN users ON transactions.user_id = users.id 
            INNER JOIN packages ON transactions.package_id = packages.package_id 
            WHERE trans_date > CURRENT_DATE - INTERVAL ? DAY"
        );
        $stmt->bind_param("i", $days);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function getRangedTransitionsTotal($days)
    {
        $stmt = $this->getConnection()->prepare("SELECT SUM(trans_amount) FROM transactions WHERE trans_date > CURRENT_DATE - INTERVAL ? DAY");
        $stmt->bind_param("i", $days);
        $stmt->execute();
        $result = $stmt->get_result();
        $total = $result->fetch_row()[0] ?? 0;
        $stmt->close();
        return $total;
    }
}

class Testimonials extends Database
{
    public function getAllTestimonials($limit = 1000)
    {
        $stmt = $this->getConnection()->prepare(
            "SELECT * FROM testimonials 
            INNER JOIN users ON testimonials.user_id = users.id 
            INNER JOIN packages ON testimonials.package_id = packages.package_id 
            ORDER BY testimonials.date_created DESC LIMIT ?"
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
            "SELECT * FROM testimonials 
            INNER JOIN users ON testimonials.user_id = users.id 
            INNER JOIN packages ON testimonials.package_id = packages.package_id 
            WHERE testimonials.package_id = ? 
            ORDER BY testimonials.date_created DESC LIMIT ?"
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
        $stmt = $this->getConnection()->prepare("INSERT INTO testimonials (message, user_id, package_id, rating) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siii", $desc, $user_id, $package_id, $rating);
        $stmt->execute();
        $stmt->close();
        return '200';
    }
}
ob_end_flush(); // Mengirim output setelah seluruh script selesai
?>