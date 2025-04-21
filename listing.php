<?php
if (!isset($_SESSION)) {
    session_start();
}
include_once './app/_dbConnection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JDAR - Listings</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Navbar Styling */
        .navbar {
            background-color: #fff;
            padding: 15px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: absolute;
            top: 0;
            width: 100%;
            z-index: 1000;
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
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .navbar-nav .nav-link.register-btn:hover {
            background-color: #0056b3;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 123, 255, 0.5);
        }

        .navbar-nav .nav-link.register-btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s ease, height 0.6s ease;
        }

        .navbar-nav .nav-link.register-btn:active::after {
            width: 200px;
            height: 200px;
            transition: 0s;
        }

        /* Body padding untuk navbar absolute */
        body {
            margin: 0;
        }

        /* Pastikan main container tidak tertutup */
        main.container {
            margin-top: 20px;
        }

        /* Hero Section */
        .hero {
            background: url('assets/bg.png') no-repeat center center/cover;
            height: 60vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            position: relative;
            z-index: 0;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            padding: 20px;
            max-width: 900px;
        }

        .hero h1 {
            font-size: 3.2rem;
            font-weight: 700;
            text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.6);
            margin: 0;
            line-height: 1.2;
            animation: fadeInDown 1s ease-out;
        }

        .hero p {
            font-size: 1.4rem;
            font-weight: 300;
            margin-top: 20px;
            text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.5);
            animation: fadeInUp 1s ease-out 0.3s;
            animation-fill-mode: both;
        }

        /* Modal style */
        .modal {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.8);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            max-width: 80%;
            max-height: 80%;
            border-radius: 15px;
            box-shadow: 0 0 20px #000;
        }

        .close-btn {
            position: absolute;
            top: 20px;
            right: 30px;
            color: white;
            font-size: 2rem;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .close-btn:hover {
            color: #ff4444;
        }

        /* Header Section */
        .header-section {
            background-color: #f8f9fa;
            padding: 100px 20px 20px 20px;
            text-align: center;
        }

        /* Responsivitas */
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
            .navbar-nav .nav-link.register-btn {
                margin: 10px auto;
                display: block;
                width: fit-content;
            }
        }

        @media (max-width: 576px) {
            .navbar-brand img {
                height: 40px;
            }
            .navbar-nav .nav-link {
                font-size: 1rem;
            }
        }

        /* Animasi Keyframes */
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* List Container */
        .list-container {
            display: flex;
            justify-content: space-between;
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            gap: 20px;
        }

        .left-col {
            flex-basis: 70%;
        }

        /* Package Styling */
        .package {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            padding: 30px 0;
            border-top: 2px solid #ccc;
        }

        .package:last-child {
            border-bottom: 2px solid #ccc;
        }

        .package-img {
            flex-basis: 57%;
            max-width: 57%;
            width: 100%;
            height: 300px;
            border-radius: 25px;
            overflow: hidden;
            transition: transform 0.3s ease;
            cursor: pointer;
            object-fit: cover;
        }

        .package-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .package-img:hover {
            transform: scale(1.1);
        }

        .package-info {
            flex-basis: 58%;
            padding: 15px 25px;
            background: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            color: #555;
            font-size: 1.1rem;
            line-height: 1.6;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .package-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }

        .package-price {
            margin-top: 20px;
            font-size: 1.5rem;
            font-weight: bold;
            color: #4682B4;
        }

        .package-price h4 {
            margin: 0;
        }

        .package-price span {
            color: #fff;
            font-size: 1rem;
            font-weight: 600;
            background: #87CEEB;
            padding: 4px 8px;
            border-radius: 5px;
        }

        .package .btn {
            padding: 10px 20px;
            background: #00bfae;
            color: #fff;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .package .btn:hover {
            background: linear-gradient(135deg, #cce5ff, #6495ed);
            transform: translateY(-2px);
        }

        /* Sidebar Styling */
        .right-col {
            flex-basis: 25%;
            max-width: 300px;
        }

        .sidebar {
            position: sticky;
            top: 20px;
            border-radius: 12px;
            padding: 25px;
            background: #ffffff;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
            border: 1px solid #e8eef3;
            transition: box-shadow 0.3s ease;
        }

        .sidebar:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }

        .sidebar .logo {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .sidebar h2 {
            font-size: 1.6rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 15px;
            text-align: center;
        }

        .sidebar p {
            font-size: 0.95rem;
            color: #7f8c8d;
            line-height: 1.5;
            margin-bottom: 20px;
            text-align: center;
        }

        .sidebar .brand {
            color: #3498db;
            font-weight: 700;
        }

        .search-listing {
            display: flex;
            position: relative;
            width: 100%;
            margin-bottom: 20px;
        }

        .search-listing input {
            width: 100%;
            padding: 12px 45px 12px 15px;
            border: 1px solid #e8eef3;
            border-radius: 8px;
            background: #f9fafb;
            font-size: 0.95rem;
            color: #34495e;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .search-listing input:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
            outline: none;
        }

        .search-listing button {
            position: absolute;
            top: 50%;
            right: 5px;
            transform: translateY(-50%);
            background: #3498db;
            border: none;
            width: 35px;
            height: 35px;
            border-radius: 6px;
            cursor: pointer;
            color: #fff;
            font-size: 1rem;
            transition: background 0.3s ease;
        }

        .search-listing button:hover {
            background: #2980b9;
        }

        .search-tips {
            padding-top: 15px;
            border-top: 1px solid #e8eef3;
        }

        .search-tips h3 {
            font-size: 1.2rem;
            color: #2c3e50;
            margin-bottom: 12px;
            text-align: center;
        }

        .popular-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: center;
        }

        .search-tag {
            padding: 6px 14px;
            background: #ecf0f1;
            color: #34495e;
            border: none;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s ease, color 0.3s ease;
        }

        .search-tag:hover {
            background: #3498db;
            color: #fff;
        }

        /* Pagination Styling */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            padding: 20px 0;
            margin: 20px 0;
            background: #fff;
            border-top: 1px solid #ddd;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .pagination .pagination-btns-container {
            display: flex;
            gap: 5px;
        }

        .pagination-btn {
            padding: 8px 12px;
            background: #f0f0f0;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .pagination-btn.current {
            background: #00bfae;
            color: #fff;
        }

        .pagination-btn:hover {
            background: #87CEEB;
        }

        .pagination-arrow {
            padding: 8px 12px;
            background: #f0f0f0;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .pagination-arrow:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .pagination-arrow:hover:not(:disabled) {
            background: #87CEEB;
        }

        /* Responsivitas */
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
            .navbar-nav .nav-link.register-btn {
                margin: 10px auto;
                display: block;
                width: fit-content;
            }
            .list-container {
                flex-direction: column;
            }
            .left-col, .right-col {
                flex-basis: 100%;
                max-width: 100%;
            }
            .right-col {
                margin-top: 25px;
            }
            .sidebar {
                position: relative;
                top: 0;
            }
        }

        @media (max-width: 576px) {
            .navbar-brand img {
                height: 40px;
            }
            .navbar-nav .nav-link {
                font-size: 1rem;
            }
            .package-img {
                flex-basis: 100%;
                max-width: 70%;
                margin: 0 auto;
            }
            .package-info {
                flex-basis: 100%;
                text-align: center;
            }
            .package-footer {
                flex-direction: column;
                align-items: center;
            }
            .package .btn {
                width: 100%;
                max-width: 200px;
            }
        }
    </style>
</head>
<body>
    <!-- Header/Navbar -->
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
                        <li class="nav-item">
                            <a class="nav-link" href="./index.php">Popular Places</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="./listing.php">All Packages</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="./mainpage.html">About JDAR</a>
                        </li>
                        <?php include "./components/_navBtns.php"; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Header Section with Title -->
    <section class="header-section">
        <div class="packages-header">
            <?php
            $location = isset($_GET["loc"]) ? $_GET["loc"] : "";
            $guest = isset($_GET["g"]) ? $_GET["g"] : 0;
            $packages = new Packages();
            $allPackages = $location ? $packages->getPackagesWithQueryCount($location) : $packages->getPackagesCount();
            $res = $location ? $packages->getPackages($location, 0, 5) : $packages->getPackages("All", 0, 5);
            ?>
            <p class="available-package">
                Total <span id="all-packages-count"><?php echo $allPackages; ?></span> Package(s) Available
            </p>

            <style>
                @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap');

                .available-package {
                    font-family: 'Inter', sans-serif;
                    font-size: 1rem;
                    color: #5e5e5e;
                    font-weight: 400;
                    background-color: #ffffff;
                    padding: 12px 24px;
                    border-radius: 12px;
                    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
                    border: 1px solid #e1e1e1;
                    display: inline-block;
                    margin: 20px auto;
                    text-align: center;
                }

                .available-package span {
                    font-weight: 600;
                    color: #333;
                }
            </style>

            <style>
                @import url('https://fonts.googleapis.com/css2?family=Inter:wght@600;700&display=swap');

                .intro-section {
                    background-color: #f5f5f5;
                    padding: 60px 5% 40px;
                    border-top: 1px solid #d3cbc4;
                }

                .intro-section .container {
                    max-width: 1200px;
                    margin: 0 auto;
                }

                .intro-section .section-title {
                    font-family: 'Inter', sans-serif;
                    font-size: 2.8rem;
                    font-weight: 700;
                    color: #2e2e2e;
                    text-align: left;
                    margin-top: 10px;
                    position: relative;
                }

                .intro-section .brand {
                    color: #7f7f7f;
                    font-weight: 700;
                }
            </style>

            <section class="intro-section">
                <div class="container">
                    <h1 class="section-title">
                        Find Your Suitable Package in <span class="brand">JDAR</span>
                    </h1>
                </div>
            </section>
        </div>
    </section>

    <!-- Modal for Zoom -->
    <div id="imageModal" class="modal">
        <span class="close-btn" onclick="closeZoom()">&times;</span>
        <img id="zoomedImage" class="modal-content" src="">
    </div>

    <!-- Main Content -->
    <main class="container">
        <div class="list-container">
            <!-- Packages Column -->
            <div class="left-col">
                <section class="package-container">
                    <?php while ($row = mysqli_fetch_assoc($res)): ?>
                        <article class="package">
                            <div class="package-img" onclick="zoomImage(this.querySelector('img'))">
                                <img src="<?php echo htmlspecialchars($row['master_image']); ?>" alt="<?php echo htmlspecialchars($row['package_name']); ?>" loading="lazy">
                            </div>
                            <div class="package-info">
                                <div class="package-header">
                                    <p class="location"><?php echo htmlspecialchars($row['package_location']); ?></p>
                                    <h3><?php echo htmlspecialchars($row['package_name']); ?></h3>
                                </div>
                                <div class="package-features">
                                    <?php
                                    $features = [];
                                    if ($row["is_hotel"]) $features[] = "Hotel";
                                    if ($row["is_transport"]) $features[] = "Transport";
                                    if ($row["is_food"]) $features[] = "Food";
                                    if ($row["is_guide"]) $features[] = "Tour Guide";
                                    echo implode(" / ", $features);
                                    ?>
                                </div>
                                <div class="package-rating">
                                    <?php
                                    $rating = floatval($row['package_rating']);
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $rating) echo "<i class='fa-solid fa-star'></i>";
                                        elseif ($i - 0.5 <= $rating) echo "<i class='fa-solid fa-star-half-stroke'></i>";
                                        else echo "<i class='fa-regular fa-star'></i>";
                                    }
                                    ?>
                                </div>
                                <p class="package-description"><?php echo htmlspecialchars($row['package_desc']); ?></p>
                                <div class="tour-dates">
                                    <div class="date-item">
                                        <span>Tour Start:</span>
                                        <h4><?php echo htmlspecialchars($row['package_start']); ?></h4>
                                    </div>
                                    <div class="date-item">
                                        <span>Tour End:</span>
                                        <h4><?php echo htmlspecialchars($row['package_end']); ?></h4>
                                    </div>
                                </div>
                                <div class="package-footer">
                                    <div class="package-price">
                                        <h4><?php echo htmlspecialchars($row['package_price']); ?> Rp <span>All Inclusive</span></h4>
                                    </div>
                                    <div class="package-action">
                                        <a href="./package.php?id=<?php echo htmlspecialchars($row['package_id']); ?>" class="btn">View Details</a>
                                    </div>
                                </div>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </section>
            </div>

            <!-- Sidebar Column -->
            <div class="right-col">
                <aside class="sidebar">
                    <div class="logo">
                        <img src="logo.png" alt="JDAR Logo" style="width: 50px; height: auto;">
                    </div>
                    <h2>Explore Your Next Journey</h2>
                    <p>Discover curated travel packages with <span class="brand">JDAR</span>.</p>
                    <form class="search-listing" id="search-form">
                        <input type="text" id="sidebar-search-input" name="search" value="<?php echo htmlspecialchars($location); ?>" placeholder="Where to next?" aria-label="Search destination">
                        <button type="submit" aria-label="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </form>
                    <div class="search-tips">
                        <h3>Top Destinations</h3>
                        <div class="popular-tags">
                            <button class="search-tag" data-location="Bali">Bali</button>
                            <button class="search-tag" data-location="Jakarta">Jakarta</button>
                            <button class="search-tag" data-location="Lombok">Lombok</button>
                            <button class="search-tag" data-location="Yogyakarta">Yogyakarta</button>
                            <button class="search-tag" data-location="Jawa Timur">Jawa Timur</button>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </main>

    <!-- Pagination -->
    <nav class="pagination" aria-label="Packages pagination">
        <button class="pagination-arrow" id="prev-page" aria-label="Previous page" disabled>
            <i class="fa-solid fa-chevron-left"></i>
        </button>
        <div class="pagination-btns-container">
            <span class="pagination-btn current" data-page="1">1</span>
            <?php for ($i = 2; $i <= ceil($allPackages / 5); $i++): ?>
                <span class="pagination-btn" data-page="<?php echo $i; ?>"><?php echo $i; ?></span>
            <?php endfor; ?>
        </div>
        <button class="pagination-arrow" id="next-page" aria-label="Next page" <?php echo $allPackages <= 5 ? 'disabled' : ''; ?>>
            <i class="fa-solid fa-chevron-right"></i>
        </button>
    </nav>

    <!-- Footer -->
    <?php include "./components/_footer.php"; ?>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
        $(document).ready(function() {
            // Navbar scroll effect
            let lastScrollTop = 0;
            const navbar = $('.navbar');
            $(window).scroll(function() {
                let scrollTop = $(this).scrollTop();
                if (scrollTop > lastScrollTop && scrollTop > 80) {
                    navbar.addClass('hidden');
                } else {
                    navbar.removeClass('hidden');
                }
                lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
            });

            // Close navbar on link click in mobile
            $('.navbar-nav .nav-link').on('click', function() {
                if ($(window).width() <= 991) {
                    $('.navbar-collapse').collapse('hide');
                }
            });

            // AOS Initialization
            AOS.init({ duration: 800, once: true });

            // Zoom Image Functionality
            window.zoomImage = function(img) {
                var modal = document.getElementById("imageModal");
                var zoomed = document.getElementById("zoomedImage");
                zoomed.src = img.src;
                modal.style.display = "flex";
            }

            window.closeZoom = function() {
                document.getElementById("imageModal").style.display = "none";
            }

            // Pagination and Search Logic
            const searchForm = $('#search-form');
            const searchInput = $('#sidebar-search-input');
            const packageContainer = $('.package-container');
            const paginationBtns = $('.pagination-btn');
            const prevPage = $('#prev-page');
            const nextPage = $('#next-page');
            const allPackagesCount = $('#all-packages-count');
            let currentPage = 1;
            let totalPackages = parseInt(allPackagesCount.text());
            let query = new URLSearchParams(location.search).get('loc') || '';

            const addPackage = (pkg) => {
                const features = [];
                if (parseInt(pkg.is_hotel)) features.push('Hotel');
                if (parseInt(pkg.is_transport)) features.push('Transport');
                if (parseInt(pkg.is_food)) features.push('Food');
                if (parseInt(pkg.is_guide)) features.push('Tour Guide');

                let stars = '';
                const rating = parseFloat(pkg.package_rating);
                for (let i = 1; i <= 5; i++) {
                    stars += i <= rating ? '<i class="fa-solid fa-star"></i>' :
                           i - 0.5 <= rating ? '<i class="fa-solid fa-star-half-stroke"></i>' :
                           '<i class="fa-regular fa-star"></i>';
                }

                packageContainer.append(`
                    <article class="package">
                        <div class="package-img" onclick="zoomImage(this.querySelector('img'))">
                            <img src="${pkg.master_image}" alt="${pkg.package_name}" loading="lazy">
                        </div>
                        <div class="package-info">
                            <div class="package-header">
                                <p class="location">${pkg.package_location}</p>
                                <h3>${pkg.package_name}</h3>
                            </div>
                            <div class="package-features">${features.join(' / ')}</div>
                            <div class="package-rating">${stars}</div>
                            <p class="package-description">${pkg.package_desc}</p>
                            <div class="tour-dates">
                                <div class="date-item">
                                    <span>Tour Start:</span>
                                    <h4>${pkg.package_start}</h4>
                                </div>
                                <div class="date-item">
                                    <span>Tour End:</span>
                                    <h4>${pkg.package_end}</h4>
                                </div>
                            </div>
                            <div class="package-footer">
                                <div class="package-price">
                                    <h4>${pkg.package_price} Rp <span>All Inclusive</span></h4>
                                </div>
                                <div class="package-action">
                                    <a href="./package.php?id=${pkg.package_id}" class="btn">View Details</a>
                                </div>
                            </div>
                        </div>
                    </article>`);
            };

            const fetchPackages = async (query, page) => {
                const start = (page - 1) * 5;
                try {
                    const response = await fetch('./services/_packagePagination.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `query=${encodeURIComponent(query)}&start=${start}&end=5`
                    });
                    const data = await response.json();
                    totalPackages = data[0];
                    allPackagesCount.text(totalPackages);
                    packageContainer.empty();
                    data.slice(1).forEach(addPackage);
                    updatePagination(page);
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } catch (error) {
                    console.error('Error fetching packages:', error);
                }
            };

            const updatePagination = (page) => {
                const totalPages = Math.ceil(totalPackages / 5);
                paginationBtns.each(function() {
                    $(this).toggleClass('current', parseInt($(this).data('page')) === page);
                });
                prevPage.prop('disabled', page === 1);
                nextPage.prop('disabled', page === totalPages);
            };

            searchForm.on('submit', (e) => {
                e.preventDefault();
                query = searchInput.val().trim();
                currentPage = 1;
                fetchPackages(query, currentPage);
                history.pushState({}, '', query ? `?loc=${encodeURIComponent(query)}` : './listing.php');
            });

            $('.search-tag').on('click', function() {
                searchInput.val($(this).data('location'));
                searchForm.trigger('submit');
            });

            paginationBtns.on('click', function() {
                currentPage = parseInt($(this).data('page'));
                fetchPackages(query, currentPage);
            });

            prevPage.on('click', () => {
                if (currentPage > 1) {
                    currentPage--;
                    fetchPackages(query, currentPage);
                }
            });

            nextPage.on('click', () => {
                if (currentPage < Math.ceil(totalPackages / 5)) {
                    currentPage++;
                    fetchPackages(query, currentPage);
                }
            });
        });
    </script>
</body>
</html>