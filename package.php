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

if ($res && $res->num_rows > 0) {
    $row = mysqli_fetch_assoc($res);
    
    // Check package availability
    $curr_date = date("Y-m-d");
    $package_start = $row['package_start'];
    $datetime1 = strtotime($package_start);
    $datetime2 = strtotime($curr_date);
    $diff = $datetime1 - $datetime2;

    // Check if package is still in the future
    if ($diff > 0) {
        // Check capacity
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
            // Debug: Log the transaction data to check the keys
            error_log("Transaction data: " . print_r($trans, true));
            
            // Check if 'status' key exists, and if it is 'success'
            $status = isset($trans['status']) ? $trans['status'] : (isset($trans['tran_status']) ? $trans['tran_status'] : null);
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
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        /* Navbar Styling */
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
            padding-top: 90px;
            background: var(--white);
            color: var(--navy-blue);
            line-height: 1.6;
        }

        .package-container {
            max-width: 1200px;
            margin: 60px auto;
            padding: 0 20px;
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            border: 1px solid rgba(179, 212, 252, 0.3);
        }

        .package-details {
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .package-title {
            padding: 50px 30px;
            text-align: center;
            background: var(--white);
            color: var(--navy-blue);
            border-bottom: 2px solid var(--light-blue);
        }

        .package-title h1 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 25px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--navy-blue);
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
        }

        .package-title .rating-location {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 30px;
            font-size: 1.2rem;
            padding: 15px 25px;
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--light-blue);
        }

        .package-title .stars {
            color: #FFD700;
            font-size: 1.4rem;
        }

        .gallery {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 15px;
            padding: 25px;
            background: var(--white);
        }

        .gallery-img-1 img {
            width: 100%;
            height: 450px;
            object-fit: cover;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .gallery-img-grp {
            display: grid;
            gap: 15px;
        }

        .gallery-img-grp img {
            width: 100%;
            height: 217.5px;
            object-fit: cover;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .gallery img:hover {
            transform: scale(1.03);
            filter: brightness(1.1);
        }

        .small-details {
            padding: 30px;
            background: var(--white);
        }

        .small-details h3 {
            font-size: 1.6rem;
            font-weight: 600;
            margin: 15px 0;
            color: var(--navy-blue);
        }

        .small-details h4 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--navy-blue);
            margin: 20px 0;
            text-align: center;
            background: var(--light-blue);
            padding: 10px;
            border-radius: var(--border-radius);
        }

        .check-form {
            text-align: center;
            margin: 25px 0;
        }

        .check-btn {
            background: var(--navy-blue);
            color: var(--white);
            padding: 15px 35px;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: var(--shadow);
        }

        .check-btn:hover:not(:disabled) {
            background: var(--light-blue);
            color: var(--navy-blue);
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }

        .check-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            box-shadow: none;
        }

        .status-msg {
            margin-top: 15px;
            font-size: 1rem;
            color: var(--navy-blue);
            font-style: italic;
        }

        .status-msg a {
            color: var(--navy-blue);
            text-decoration: underline;
            transition: var(--transition);
        }

        .status-msg a:hover {
            color: var(--light-blue);
        }

        .details-list {
            list-style: none;
            padding: 0;
            margin: 25px 0;
            display: grid;
            gap: 20px;
        }

        .details-list li {
            display: flex;
            align-items: center;
            gap: 20px;
            background: var(--light-blue);
            padding: 15px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .details-list li:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }

        .details-list li i {
            color: var(--navy-blue);
            font-size: 1.8rem;
        }

        .details-list li div {
            font-size: 1rem;
        }

        .details-list li span {
            font-size: 0.9rem;
            color: #555;
        }

        .package-desc {
            font-size: 1.1rem;
            color: #555;
            line-height: 1.9;
            margin: 30px 0;
            padding: 25px;
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            border-left: 4px solid var(--light-blue);
        }

        .map {
            margin: 30px 0;
            text-align: center;
        }

        .map h3 {
            font-size: 1.6rem;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .map iframe {
            width: 100%;
            height: 450px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }

        .what-say {
            max-width: 1200px;
            margin: 60px auto;
            padding: 0 20px;
        }

        .what-say h3 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--navy-blue);
            text-align: center;
            margin-bottom: 40px;
        }

        .testimonial {
            background: var(--white);
            padding: 25px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            text-align: center;
            transition: var(--transition);
        }

        .testimonial:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .testimonial-icon {
            font-size: 2.5rem;
            color: var(--light-blue);
            margin-bottom: 15px;
        }

        .description {
            font-size: 1.1rem;
            color: var(--navy-blue);
            margin: 20px 0;
            font-style: italic;
            line-height: 1.8;
        }

        .testimonial-rating .stars {
            color: #FFD700;
            font-size: 1.2rem;
        }

        .title {
            font-size: 1.3rem;
            font-weight: 600;
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
        }

        .modal.show img {
            transform: scale(1);
        }

        .modal .close-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 2.5rem;
            color: var(--white);
            background: var(--navy-blue);
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .modal .close-btn:hover {
            background: var(--light-blue);
            color: var(--navy-blue);
            transform: rotate(90deg);
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
                margin: 40px auto;
            }
            .package-title {
                padding: 40px 20px;
            }
            .package-title h1 {
                font-size: 2.5rem;
            }
            .gallery {
                grid-template-columns: 1fr;
            }
            .gallery-img-1 img, .gallery-img-grp img {
                height: 350px;
            }
        }

        @media (max-width: 576px) {
            .navbar-brand img {
                height: 40px;
            }
            .package-title h1 {
                font-size: 2rem;
            }
            .package-title .rating-location {
                flex-direction: column;
                gap: 15px;
            }
            .gallery-img-1 img, .gallery-img-grp img {
                height: 250px;
            }
            .small-details h4 {
                font-size: 1.5rem;
            }
            .map iframe {
                height: 300px;
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
            echo "<div class='package-details'><div class='package-title'><h1>Paket Tidak Ditemukan...</h1></div></div>";
        } else {
            $location = htmlspecialchars($row["map_loc"]);
            $stars = countStars($row['package_rating']);
            
            $features = "<ul class='details-list'>";
            if ($row["is_hotel"]) $features .= "<li><i class='fa-solid fa-hotel'></i><div>Hotel<br><span>Hotel is <strong>JDAR</strong> verified with excellent customer service.</span></div></li>";
            if ($row["is_transport"]) $features .= "<li><i class='fa-solid fa-bus-simple'></i><div>Transport<br><span>Transportation includes bus tickets from and to " . htmlspecialchars($row['package_location']) . ".</span></div></li>";
            if ($row["is_food"]) $features .= "<li><i class='fa-solid fa-utensils'></i><div>Food<br><span>Breakfast and Dinner included in the package.</span></div></li>";
            if ($row["is_guide"]) $features .= "<li><i class='fa-solid fa-person-hiking'></i><div>Tour Guide<br><span>100% trusted local guide is assigned for sightseeing.</span></div></li>";
            $features .= "</ul>";

            echo "<div class='package-details'>
                <div class='package-title'>
                    <h1>" . htmlspecialchars($row["package_name"]) . "</h1>
                    <div class='rating-location'>
                        <div class='stars'>" . $stars . " <span>" . number_format((float)$row["package_rating"], 1, '.', '') . "</span></div>
                        <div>Location: <span>" . htmlspecialchars($row["package_location"]) . ", Indonesia</span></div>
                    </div>
                </div>
                <div class='gallery'>
                    <div class='gallery-img-1'><img src='" . htmlspecialchars($row['master_image']) . "' alt='Master Image'></div>
                    <div class='gallery-img-grp'>
                        <img src='" . htmlspecialchars($row['extra_image_1']) . "' alt='Extra Image 1'>
                        <img src='" . htmlspecialchars($row['extra_image_2']) . "' alt='Extra Image 2'>
                    </div>
                </div>
                <div class='small-details'>
                    <h3>Tour Mulai: " . htmlspecialchars($row["package_start"]) . "</h3>
                    <h3>Tour Selesai: " . htmlspecialchars($row["package_end"]) . "</h3>
                    <h4>" . number_format($row["package_price"], 0, ',', '.') . " Rp / All Inclusive</h4>
                    <div class='check-form'>";
            
            if (!isset($_SESSION['is_admin'])) {
                if ($ckUser > 0) {
                    echo "<button class='check-btn' disabled>Anda Sudah Membeli Paket Ini</button>";
                } elseif (!$package_available) {
                    echo "<button class='check-btn' disabled>Paket Tidak Tersedia</button>";
                } else {
                    echo "<a href='./services/_order_details.php?package=$package_id&user=$user_id' class='check-btn'>Pesan Sekarang</a>";
                }
            }

            echo "<p class='status-msg'>" . htmlspecialchars($status_message) . "</p>
                    </div>
                    <h3>Paket Termasuk</h3>
                    " . $features . "
                    <div class='package-desc'>" . htmlspecialchars($row['package_desc']) . "</div>
                    <div class='map'>
                        <h3>Lokasi</h3>
                        <iframe src='" . $location . "' allowfullscreen='' loading='lazy' referrerpolicy='no-referrer-when-downgrade'></iframe>
                        <b>" . htmlspecialchars($row['package_location']) . "</b>
                    </div>
                </div>
            </div>";
        }
        ?>
    </div>

    <div class="what-say">
        <?php
        $testimonialInstance = new Testimonials();
        $testimonials = $testimonialInstance->getPackageTestimonials($package_id);
        ?>
        <h3><?php echo ($testimonials->num_rows > 0) ? "Apa Kata Orang" : "Belum Ada Ulasan"; ?></h3>
        <div class="row">
            <div class="col-md-12">
                <div id="testimonial-slider" class="owl-carousel">
                    <?php
                    while ($row = mysqli_fetch_assoc($testimonials)) {
                        $stars = countStars($row['rating']);
                        echo "<div class='testimonial'>
                            <div class='testimonial-content'>
                                <div class='testimonial-icon'><i class='fa fa-quote-left'></i></div>
                                <p class='description'>" . htmlspecialchars($row['message']) . "</p>
                                <p class='testimonial-rating stars'>" . $stars . "</p>
                            </div>
                            <h3 class='title'>" . htmlspecialchars($row['full_name']) . "</h3>
                        </div>";
                    }
                    ?>
                </div>
            </div>
        </div>
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

            $("#testimonial-slider").owlCarousel({
                items: 3,
                margin: 20;
                loop: <?php echo ($testimonials->num_rows > 1) ? 'true' : 'false'; ?>,
                responsive: {
                    0: { items: 1 },
                    768: { items: 2 },
                    1000: { items: 3 }
                },
                pagination: true,
                nav: false,
                autoplay: true,
                autoplayTimeout: 5000,
                autoplayHoverPause: true,
                smartSpeed: 1000
            });

            $('.gallery img').on('click', function() {
                const modal = $('<div class="modal"><img src="' + this.src + '" alt="Package Image"><button class="close-btn">×</button></div>');
                $('body').append(modal);
                setTimeout(() => modal.addClass('show'), 10);
                modal.on('click', '.close-btn, .modal', function(e) {
                    if (e.target === this || e.target.className === 'close-btn') {
                        modal.removeClass('show');
                        setTimeout(() => modal.remove(), 300);
                    }
                });
            });
        });
    </script>
</body>
</html>