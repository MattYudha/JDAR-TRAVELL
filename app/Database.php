<?php
/*
    Known bugs:
    * Package price can be negative
    * Two users purchase a product at the same time not handled.
    * User purchasing same package multiple times not handled.
    * One user can add multiple reviews on same package
    * Users can write reviews before finishing the tour
*/

class Database
{
    private $db_host = "localhost";
    private $db_user = "root";
    private $db_password = "";
    private $db_name = "triptip";
    protected $conn;

    protected function connect()
    {
        try {
            $this->conn = new mysqli($this->db_host, $this->db_user, $this->db_password, $this->db_name);
            if ($this->conn->connect_error) {
                throw new mysqli_sql_exception("Connection failed: " . $this->conn->connect_error);
            }
        } catch (mysqli_sql_exception $e) {
            http_response_code(503);
            die("Service Unavailable");
        }
    }
}

class Packages extends Database
{
    public function createPackage($package_name, $package_desc, $package_start, $package_end, $package_price, $package_location, $is_hotel, $is_transport, $is_food, $is_guide, $package_capacity, $map_loc, $master_image, $extra_image_1, $extra_image_2)
    {
        $this->connect();
        $package_name = mysqli_real_escape_string($this->conn, $package_name);
        $package_desc = mysqli_real_escape_string($this->conn, $package_desc);
        $package_location = mysqli_real_escape_string($this->conn, $package_location);
        $map_loc = mysqli_real_escape_string($this->conn, $map_loc);
        $master_image = mysqli_real_escape_string($this->conn, $master_image);
        $extra_image_1 = mysqli_real_escape_string($this->conn, $extra_image_1);
        $extra_image_2 = mysqli_real_escape_string($this->conn, $extra_image_2);

        $sql = "INSERT INTO packages (package_name, package_desc, package_start, package_end, package_price, package_location, is_hotel, is_transport, is_food, is_guide, package_capacity, map_loc, master_image, extra_image_1, extra_image_2)
                VALUES ('$package_name', '$package_desc', '$package_start', '$package_end', '$package_price', '$package_location', '$is_hotel', '$is_transport', '$is_food', '$is_guide', '$package_capacity', '$map_loc', '$master_image', '$extra_image_1', '$extra_image_2')";

        $result = $this->conn->query($sql);
        $this->conn->close();
        return $result ? "200" : "500";
    }

    public function updatePackage($package_id, $package_name, $package_desc, $package_start, $package_end, $package_price, $package_location, $is_hotel, $is_transport, $is_food, $is_guide, $package_capacity, $map_loc, $master_image, $extra_image_1, $extra_image_2)
    {
        $this->connect();
        $package_name = mysqli_real_escape_string($this->conn, $package_name);
        $package_desc = mysqli_real_escape_string($this->conn, $package_desc);
        $package_location = mysqli_real_escape_string($this->conn, $package_location);
        $map_loc = mysqli_real_escape_string($this->conn, $map_loc);
        $master_image = mysqli_real_escape_string($this->conn, $master_image);
        $extra_image_1 = mysqli_real_escape_string($this->conn, $extra_image_1);
        $extra_image_2 = mysqli_real_escape_string($this->conn, $extra_image_2);

        $sql = "UPDATE packages 
                SET package_name = '$package_name', package_desc = '$package_desc', package_start = '$package_start', package_end = '$package_end', 
                    package_price = $package_price, package_location = '$package_location', is_hotel = $is_hotel, is_transport = $is_transport, 
                    is_food = $is_food, is_guide = $is_guide, package_capacity = $package_capacity, map_loc = '$map_loc', 
                    master_image = '$master_image', extra_image_1 = '$extra_image_1', extra_image_2 = '$extra_image_2' 
                WHERE package_id = $package_id";

        $result = $this->conn->query($sql);
        $this->conn->close();
        return $result ? "200" : "500";
    }

    public function getPackages($location, $start = 0, $end = 1000)
    {
        $this->connect();
        $location = mysqli_real_escape_string($this->conn, $location);

        if ($location == "All") {
            $sql = "SELECT * FROM packages ORDER BY package_id DESC LIMIT ?, ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $start, $end);
        } else {
            $sql = "SELECT * FROM packages WHERE package_location LIKE ? ORDER BY package_id DESC LIMIT ?, ?";
            $loc = "%$location%";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sii", $loc, $start, $end);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $this->conn->close();
        return $result;
    }

    public function getPackage($id)
    {
        $this->connect();
        $sql = "SELECT * FROM packages WHERE package_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $this->conn->close();
        return $result;
    }

    public function getPackagesCount()
    {
        $this->connect();
        $sql = "SELECT COUNT(*) as count FROM packages";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        $this->conn->close();
        return $row['count'];
    }

    public function getPackagesWithQueryCount($location)
    {
        $this->connect();
        $location = mysqli_real_escape_string($this->conn, $location);
        $sql = "SELECT COUNT(*) as count FROM packages WHERE package_location LIKE ?";
        $loc = "%$location%";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $loc);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $this->conn->close();
        return $row['count'];
    }

    public function updatePackagePurchase($id, $count)
    {
        $this->connect();
        $sql = "UPDATE packages SET package_booked = ? WHERE package_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $count, $id);
        $result = $stmt->execute();
        $this->conn->close();
        return $result ? "200" : "500";
    }

    public function updateRating($id, $rating)
    {
        $this->connect();
        $sql = "UPDATE packages SET package_rating = ? WHERE package_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("di", $rating, $id); // 'd' untuk float/double
        $result = $stmt->execute();
        $this->conn->close();
        return $result ? "200" : "500";
    }

    public function getDistinctDestinations()
    {
        $this->connect();
        $sql = "SELECT DISTINCT package_location FROM packages";
        $result = $this->conn->query($sql);
        $destinations = [];
        while ($row = $result->fetch_assoc()) {
            $destinations[] = $row['package_location'];
        }
        $this->conn->close();
        return $destinations;
    }

    public function searchPackages($destination, $checkin, $checkout, $totalGuests, $accommodation, $budget, $facilities)
    {
        $this->connect();

        $sql = "SELECT * FROM packages WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($destination)) {
            $destination = mysqli_real_escape_string($this->conn, $destination);
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
            $sql .= " AND package_price <= ?";
            $params[] = $budget;
            $types .= "d";
        }
        if (!empty($facilities) && is_array($facilities)) {
            foreach ($facilities as $facility) {
                $sql .= " AND is_$facility = 1";
            }
        }
        $sql .= " AND (package_capacity - COALESCE(package_booked, 0)) >= ?";
        $params[] = $totalGuests;
        $types .= "i";

        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $packages = $result->fetch_all(MYSQLI_ASSOC);

        $this->conn->close();
        return $packages;
    }
}
?>