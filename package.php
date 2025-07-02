<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include_once './app/_dbConnection.php';

// Set package_id and user_id with validation
$package_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

// Fetch package data
$packages = new Packages();
$res = $packages->getPackage($package_id);

// Initialize variables
$package_available = false;
$available_slots = 0;
$status_message = '';

if (!$res) {
    error_log("Database query failed for package_id: $package_id");
    $status_message = 'Terjadi kesalahan saat mengambil data paket.';
} elseif ($res->num_rows > 0) {
    $row = mysqli_fetch_assoc($res);
    
    // Use discount_percentage to calculate discounted_price dynamically
    $original_price = isset($row['package_price']) ? (float)$row['package_price'] : 0;
    $discount_percentage = isset($row['discount_percentage']) ? (float)$row['discount_percentage'] : 0;
    $discounted_price = $original_price - ($original_price * ($discount_percentage / 100));
    
    // Check package availability
    $curr_date = date("Y-m-d");
    $package_start = $row['package_start'];
    $datetime1 = strtotime($package_start);
    $datetime2 = strtotime($curr_date);
    $diff = $datetime1 - $datetime2;

    if ($diff > 0) {
        $available_slots = $row['package_capacity'] - $row['package_booked'];
        if ($available_slots > 0) {
            $package_available = true;
        } else {
            $status_message = 'Maaf, paket ini sudah penuh.';
        }
    } else {
        $status_message = 'Paket ini sudah berakhir.';
    }

    // Check user purchase status (only count transactions with status 'success')
    $ckUser = 0;
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
        $transactionInstance = new Transactions();
        $transaction = $transactionInstance->checkUserTransaction($user_id, $package_id);
        while ($trans = mysqli_fetch_assoc($transaction)) {
            $status = isset($trans['status']) ? $trans['status'] : null;
            if ($status === 'success') {
                $ckUser++;
                $status_message = 'Anda sudah membeli paket ini.';
                $package_available = false;
                break;
            }
        }
    }
} else {
    $status_message = 'Paket tidak ditemukan.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JDAR Travel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --white: #FFFFFF;
            --light-blue: #B3D4FC;
            --navy-blue: #2C3E50;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
            --border-radius: 15px;
            --orange: #FF6200;
            --yellow: #FFD700;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', 'Poppins', sans-serif;
        }

        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Poppins:wght@300;400;500;600;700&display=swap');

        .navbar {
            background-color: #fff;
            padding: 15px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            transition: var(--transition);
        }

        .navbar.hidden {
            transform: translateY(-100%);
        }

        .navbar-brand img {
            height: 60px;
            transition: height 0.3s ease;
        }

        .navbar-nav .nav-link {
            color: #333;
            font-size: 1.1rem;
            padding: 10px 20px;
            transition: color 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            color: #007bff;
        }

        .navbar-nav .nav-link.register-btn {
            background-color: #007bff;
            color: white !important;
            border-radius: 25px;
            padding: 10px 20px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
            transition: var(--transition);
        }

        .navbar-nav .nav-link.register-btn:hover {
            background-color: #0056b3;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 123, 255, 0.5);
        }

        body {
            padding-top: 120px; /* Increased padding to create space between navbar and content */
            background: #f5f5f5;
            color: var(--navy-blue);
            line-height: 1.6;
        }

        .package-container {
            max-width: 1200px;
            margin: 80px auto 60px; /* Added top margin for better spacing */
            padding: 0 20px;
            background: var(--white); /* Added background for a cleaner look */
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }

        .gallery {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 10px;
            margin-bottom: 20px;
            position: relative;
        }

        .gallery-img-1 img {
            width: 100%;
            height: 400px;
            aspect-ratio: 16 / 9;
            object-fit: cover;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .gallery-img-grp {
            display: grid;
            gap: 10px;
            grid-column: span 2;
        }

        .gallery-img-grp img {
            width: 100%;
            height: 195px;
            aspect-ratio: 16 / 9;
            object-fit: cover;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .gallery img:hover {
            transform: scale(1.03);
            filter: brightness(1.1);
        }

        .see-all-photos {
            position: absolute;
            bottom: 20px;
            right: 20px;
            background: rgba(0, 0, 0, 0.7);
            color: var(--white);
            padding: 8px 15px;
            border-radius: 5px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
        }

        .see-all-photos:hover {
            background: rgba(0, 0, 0, 0.9);
        }

        .package-details {
            border-radius: var(--border-radius);
            padding: 30px; /* Increased padding for a more spacious look */
        }

        .package-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .package-title h1 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--navy-blue);
        }

        .check-btn {
            background: var(--orange);
            color: var(--white);
            padding: 10px 25px;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .check-btn:hover:not(:disabled) {
            background: #e55a00;
            transform: translateY(-2px);
        }

        .check-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .rating-location {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .stars {
            color: var(--yellow);
            font-size: 1.2rem;
        }

        .stars span {
            font-size: 1rem;
            color: var(--navy-blue);
            margin-left: 5px;
        }

        .location {
            font-size: 0.9rem;
            color: #555;
        }

        .award {
            font-size: 0.9rem;
            color: #007bff;
            font-weight: 500;
        }

        .rating-summary {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .rating-score {
            background: #007bff;
            color: var(--white);
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 1.2rem;
            font-weight: 700;
        }

        .rating-score span {
            font-size: 0.9rem;
            font-weight: 400;
        }

        .rating-details {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            color: #555;
            font-size: 0.9rem;
        }

        .rating-details span {
            background: #e0e0e0;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .location-details {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .location-details a {
            color: #007bff;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .location-details a:hover {
            text-decoration: underline;
        }

        .facilities {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
        }

        .facility-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            color: #555;
        }

        .facility-item i {
            color: var(--navy-blue);
        }

        .read-more {
            color: #007bff;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .read-more:hover {
            text-decoration: underline;
        }

        .small-details {
            border-top: 1px solid #e0e0e0;
            padding-top: 20px;
        }

        .small-details h3 {
            font-size: 1.2rem;
            font-weight: 600;
            margin: 10px 0;
        }

        .package-desc {
            font-size: 0.95rem;
            color: #555;
            line-height: 1.8;
            margin-bottom: 20px;
        }

        .price-container {
            text-align: right;
            margin-top: 20px;
        }

        .original-price {
            font-size: 1rem;
            color: #888;
            text-decoration: line-through;
            margin-right: 10px;
        }

        .discounted-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: #e74c3c;
        }

        .discount-label {
            display: inline-block;
            background-color: #e74c3c;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.9rem;
            margin-right: 10px;
        }

        .status-msg {
            font-size: 0.9rem;
            color: #555;
            margin-top: 10px;
            text-align: right;
        }

        .status-msg a {
            color: #007bff;
            text-decoration: underline;
        }

        .status-msg a:hover {
            color: #0056b3;
        }

        .map {
            margin: 30px 0;
        }

        .map h3 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .map iframe {
            width: 100%;
            height: 300px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }

        .what-say {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }

        .what-say h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--navy-blue);
            margin-bottom: 20px;
        }

        .testimonial {
            background: var(--white);
            padding: 15px;
            border-radius: var(--border-radius);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            text-align: left;
            margin-bottom: 15px;
        }

        .testimonial-rating {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .testimonial-rating .stars {
            font-size: 1rem;
        }

        .testimonial-rating .score {
            background: #007bff;
            color: var(--white);
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .description {
            font-size: 0.9rem;
            color: #555;
            line-height: 1.6;
            margin-bottom: 10px;
        }

        .title {
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--navy-blue);
        }

        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 2000;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
        }

        .modal.show {
            opacity: 1;
            visibility: visible;
        }

        .modal img {
            max-width: 90%;
            max-height: 90%;
            border-radius: var(--border-radius);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            transform: scale(0.8);
            transition: var(--transition);
            cursor: zoom-in;
        }

        .modal.show img {
            transform: scale(1);
        }

        .modal img.zoomed {
            transform: scale(1.5);
            cursor: zoom-out;
        }

        .modal .close-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 2rem;
            color: var(--white);
            background: var(--navy-blue);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .modal .close-btn:hover {
            background: var(--light-blue);
            color: var(--navy-blue);
        }

        .prev-btn, .next-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.7);
            color: var(--white);
            border: none;
            padding: 10px;
            font-size: 1.5rem;
            cursor: pointer;
            border-radius: 5px;
            transition: var(--transition);
        }

        .prev-btn:hover, .next-btn:hover {
            background: rgba(0, 0, 0, 0.9);
        }

        .prev-btn {
            left: 20px;
        }

        .next-btn {
            right: 20px;
        }

        @media (max-width: 991px) {
            .navbar-nav {
                padding: 15px;
                background-color: #fff;
            }
            .navbar-brand img {
                height: 50px;
            }
            .navbar-nav .nav-link {
                padding: 10px 15px;
            }
            .package-container {
                margin: 60px auto 40px; /* Adjusted for smaller screens */
            }
            .gallery {
                grid-template-columns: 1fr;
            }
            .gallery-img-1 img, .gallery-img-grp img {
                height: 300px;
            }
            .gallery-img-grp {
                grid-column: span 1;
            }
            .package-title {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            .price-container {
                text-align: left;
            }
            .status-msg {
                text-align: left;
            }
        }

        @media (max-width: 576px) {
            .navbar-brand img {
                height: 40px;
            }
            .package-title h1 {
                font-size: 1.5rem;
            }
            .gallery-img-1 img, .gallery-img-grp img {
                height: 200px;
            }
            .rating-summary {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            .map iframe {
                height: 200px;
            }
            .what-say h3 {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <a href="./index.php" class="navbar-brand">
                    <img src="logo.png" alt="JDAR Logo">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="./index.php">Popular Places</a></li>
                        <li class="nav-item"><a class="nav-link" href="./listing.php">All Packages</a></li>
                        <li class="nav-item"><a class="nav-link" href="./mainpage.html">About JDAR</a></li>
                        <?php include "./components/_navBtns.php"; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <div class="package-container">
        <?php
        include("./utilities/countStars.php");

        if (!$res || $res->num_rows == 0) {
            echo "<div class='package-details'><h1 style='padding: 20px; text-align: center;'>Paket Tidak Ditemukan...</h1></div>";
        } else {
            $location = htmlspecialchars($row["map_loc"]);
            $stars = countStars($row['package_rating']);
            
            $features = "";
            if ($row["is_hotel"]) $features .= "<div class='facility-item'><i class='fa-solid fa-hotel'></i> Hotel</div>";
            if ($row["is_transport"]) $features .= "<div class='facility-item'><i class='fa-solid fa-bus-simple'></i> Transport</div>";
            if ($row["is_food"]) $features .= "<div class='facility-item'><i class='fa-solid fa-utensils'></i> Food</div>";
            if ($row["is_guide"]) $features .= "<div class='facility-item'><i class='fa-solid fa-person-hiking'></i> Tour Guide</div>";
            $features .= "<div class='facility-item'><i class='fa-solid fa-wifi'></i> WiFi</div>";
            $features .= "<div class='facility-item'><i class='fa-solid fa-clock'></i> 24-Hour Front Desk</div>";

            echo "<div class='gallery'>
                <div class='gallery-img-1'><img src='" . htmlspecialchars($row['master_image']) . "' alt='Master Image' loading='lazy'></div>
                <div class='gallery-img-grp'>
                    <img src='" . htmlspecialchars($row['extra_image_1']) . "' alt='Extra Image 1' loading='lazy'>
                    <img src='" . htmlspecialchars($row['extra_image_2']) . "' alt='Extra Image 2' loading='lazy'>
                </div>
                <div class='see-all-photos'>See All Photos</div>
            </div>
            <div class='package-details'>
                <div class='package-title'>
                    <h1>" . htmlspecialchars($row["package_name"]) . "</h1>";
            
            if (!isset($_SESSION['is_admin'])) {
                if ($ckUser > 0) {
                    echo "<button class='check-btn' disabled>Anda Sudah Membeli Paket Ini</button>";
                } elseif (!$package_available) {
                    echo "<button class='check-btn' disabled>Paket Tidak Tersedia</button>";
                } else {
                    echo "<a href='./services/_order_details.php?package=$package_id&user=$user_id&discounted_price=$discounted_price' class='check-btn'>Pesan Sekarang</a>";
                }
            }

            echo "</div>
                <div class='rating-location'>
                    <div class='stars'>" . $stars . " <span>" . number_format((float)$row["package_rating"], 1, '.', '') . "</span></div>
                    <div class='location'>" . htmlspecialchars($row["package_location"]) . ", Indonesia</div>
                    <div class='award'>JDAR Traveler Appreciation 2025: Exceptional Experience</div>
                </div>
                <div class='rating-summary'>
                    <div class='rating-score'>" . number_format((float)$row["package_rating"], 1, '.', '') . "/0.5 <span>Exceptional</span></div>
                    <div class='rating-details'>
                        <span>Experience (" . rand(150, 200) . ")</span>
                        <span>Location (" . rand(140, 180) . ")</span>
                        <span>Service (" . rand(130, 170) . ")</span>
                        <span>Value (" . rand(120, 160) . ")</span>
                    </div>
                </div>
                <div class='location-details'>
                    <i class='fa fa-map-marker-alt'></i>
                    <span>" . htmlspecialchars($row["package_location"]) . ", Indonesia</span>
                    <a href='#map'>See Map</a>
                </div>
                <div class='facilities'>
                    " . $features . "
                    <a href='#' class='read-more'>Read More</a>
                </div>
                <div class='small-details'>
                    <h3>Tour Starts: " . htmlspecialchars($row["package_start"]) . "</h3>
                    <h3>Tour Ends: " . htmlspecialchars($row["package_end"]) . "</h3>
                    <div class='package-desc'>" . htmlspecialchars($row['package_desc']) . "</div>
                    <div class='price-container'>";
            if ($discount_percentage > 0) {
                echo "<div class='discount-label'>Save " . number_format($discount_percentage, 0) . "%</div>";
                echo "<span class='discounted-price'>" . number_format($discounted_price, 0, ',', '.') . " Rp</span>";
                echo "<span class='original-price'>" . number_format($original_price, 0, ',', '.') . " Rp</span>";
            } else {
                echo "<span class='discounted-price'>" . number_format($original_price, 0, ',', '.') . " Rp</span>";
            }
            echo "</div>
                    <p class='status-msg'>" . htmlspecialchars($status_message) . "</p>
                </div>
                <div class='map' id='map'>
                    <h3>Location</h3>
                    <iframe src='" . $location . "' allowfullscreen='' loading='lazy' referrerpolicy='no-referrer-when-downgrade'></iframe>
                </div>
            </div>";
        }
        ?>

        <?php
        $testimonialInstance = new Testimonials();
        $testimonials = $testimonialInstance->getPackageTestimonials($package_id);
        $valid_testimonials = [];
        while ($row = mysqli_fetch_assoc($testimonials)) {
            $valid_testimonials[] = $row;
        }
        if (!empty($valid_testimonials)):
        ?>
        <div class="what-say">
            <h3>What Guests Say About Their Stay (<?php echo min(count($valid_testimonials), 2); ?>)</h3>
            <div class="row">
                <div class="col-md-12">
                    <?php
                    $count = 0;
                    foreach ($valid_testimonials as $row) {
                        if ($count >= 2) break;
                        $stars = countStars($row['rating']);
                        $display_name = !empty($row['full_name']) && trim($row['full_name']) !== '' ? htmlspecialchars($row['full_name']) : 'User';
                        echo "<div class='testimonial'>
                            <div class='testimonial-rating'>
                                <p class='stars'>" . $stars . "</p>
                                <span class='score'>" . number_format($row['rating'], 1) . "/5</span>
                            </div>
                            <p class='description'>" . htmlspecialchars($row['message']) . "</p>
                            <h3 class='title'>" . $display_name . "</h3>
                        </div>";
                        $count++;
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php include "./components/_footer.php"; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            let lastScrollTop = 0;
            const navbar = $('.navbar');
            $(window).scroll(function() {
                let scrollTop = $(this).scrollTop();
                navbar.toggleClass('hidden', scrollTop > lastScrollTop && scrollTop > 80);
                lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
            });

            $('.navbar-nav .nav-link').on('click', function() {
                if ($(window).width() <= 991) {
                    $('.navbar-collapse').collapse('hide');
                }
            });

            // Image gallery modal
            const images = [
                $('.gallery-img-1 img').attr('src'),
                $('.gallery-img-grp img').eq(0).attr('src'),
                $('.gallery-img-grp img').eq(1).attr('src')
            ].filter(src => src); // Filter out undefined sources
            let currentImageIndex = 0;

            function openModal(index) {
                if (images.length === 0) return;
                currentImageIndex = index % images.length;
                const modal = $(`
                    <div class="modal">
                        <img src="${images[currentImageIndex]}" alt="Package Image">
                        <button class="close-btn">×</button>
                        ${images.length > 1 ? '<button class="prev-btn"><i class="fa fa-chevron-left"></i></button><button class="next-btn"><i class="fa fa-chevron-right"></i></button>' : ''}
                    </div>
                `);
                $('body').append(modal);
                setTimeout(() => modal.addClass('show'), 10);

                modal.find('img').on('click', function(e) {
                    e.stopPropagation();
                    $(this).toggleClass('zoomed');
                });

                modal.find('.prev-btn').on('click', function(e) {
                    e.stopPropagation();
                    currentImageIndex = (currentImageIndex - 1 + images.length) % images.length;
                    modal.find('img').attr('src', images[currentImageIndex]);
                    modal.find('img').removeClass('zoomed');
                });

                modal.find('.next-btn').on('click', function(e) {
                    e.stopPropagation();
                    currentImageIndex = (currentImageIndex + 1) % images.length;
                    modal.find('img').attr('src', images[currentImageIndex]);
                    modal.find('img').removeClass('zoomed');
                });

                modal.on('click', '.close-btn, .modal', function(e) {
                    if (e.target === this || e.target.className === 'close-btn') {
                        modal.removeClass('show');
                        setTimeout(() => modal.remove(), 300);
                    }
                });
            }

            $('.gallery img').on('click', function() {
                const index = $(this).hasClass('gallery-img-1') ? 0 : $('.gallery-img-grp img').index(this) + 1;
                openModal(index);
            });

            $('.see-all-photos').on('click', function() {
                openModal(0);
            });
        });
    </script>
</body>
</html>