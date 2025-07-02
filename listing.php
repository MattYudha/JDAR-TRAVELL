<?php
if (!isset($_SESSION)) {
    session_start();
}
// Use require_once to ensure _dbConnection.php is included only once
require_once './app/_dbConnection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JDAR - Listings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --white: #FFFFFF;
            --light-gray: #F5F6FA;
            --medium-gray: #E8ECEF;
            --dark-gray: #6B7280;
            --primary-blue: #0284C7;
            --secondary-blue: #3B82F6;
            --discount-red: #F05252;
            --rating-yellow: #FBBF24;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
            --border-radius: 12px;
            --deal-pink: #FECDD3;
            --deal-text: #BE123C;
            --search-border: #333333;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            background-color: var(--light-gray);
        }

        .navbar {
            background-color: var(--white);
            padding: 15px 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .navbar.hidden {
            transform: translateY(-100%);
        }

        .navbar-brand img {
            height: 50px;
            transition: height 0.3s ease;
        }

        .navbar-nav .nav-link {
            color: var(--dark-gray);
            font-size: 1rem;
            font-weight: 500;
            padding: 10px 15px;
            transition: color 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            color: var(--secondary-blue);
        }

        .navbar-nav .nav-link.register-btn {
            background-color: var(--secondary-blue);
            color: var(--white) !important;
            border-radius: 20px;
            padding: 8px 18px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 10px rgba(59, 130, 246, 0.3);
            transition: var(--transition);
        }

        .navbar-nav .nav-link.register-btn:hover {
            background-color: #2563EB;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.5);
        }

        main.container {
            margin-top: 20px;
            padding-bottom: 40px;
        }

        .header-section {
            background-color: var(--white);
            padding: 20px;
            text-align: center;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-bottom: 20px;
        }

        .available-package {
            font-size: 0.95rem;
            color: var(--dark-gray);
            font-weight: 400;
            background-color: var(--light-gray);
            padding: 10px 20px;
            border-radius: 8px;
            display: inline-block;
            margin: 0 auto;
        }

        .available-package span {
            font-weight: 600;
            color: #1F2937;
        }

        .intro-section .section-title {
            font-size: 2rem;
            font-weight: 600;
            color: #1F2937;
            text-align: left;
            margin: 20px 0;
        }

        .intro-section .brand {
            color: var(--primary-blue);
            font-weight: 700;
        }

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

        .package-container {
            padding: 20px 0;
        }

        .package-card {
            display: flex;
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-bottom: 20px;
            overflow: hidden;
            transition: var(--transition);
            position: relative;
        }

        .package-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
        }

        .package-image {
            flex: 0 0 30%;
            position: relative;
        }

        .package-image img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            border-radius: var(--border-radius) 0 0 var(--border-radius);
            cursor: pointer;
        }

        .location-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: var(--secondary-blue);
            color: var(--white);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .discount-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--discount-red);
            color: var(--white);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .package-info {
            flex: 1;
            padding: 15px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .package-info h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1F2937;
            margin-bottom: 8px;
        }

        .rating-location {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
        }

        .rating {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .rating .stars {
            color: var(--rating-yellow);
            font-size: 0.9rem;
        }

        .rating span {
            font-size: 0.9rem;
            color: #1F2937;
            font-weight: 500;
        }

        .rating .review-label {
            color: var(--primary-blue);
            font-weight: 600;
        }

        .deal {
            background: var(--deal-pink);
            color: var(--deal-text);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            margin-bottom: 8px;
            display: inline-block;
        }

        .review-snippet {
            font-size: 0.85rem;
            color: #4B5563;
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .review-author {
            font-size: 0.85rem;
            color: var(--primary-blue);
            font-weight: 500;
            margin-bottom: 8px;
        }

        .review-date {
            font-size: 0.85rem;
            color: #6B7280;
            margin-bottom: 8px;
        }

        .see-more {
            color: var(--secondary-blue);
            font-size: 0.85rem;
            text-decoration: none;
            font-weight: 500;
        }

        .see-more:hover {
            text-decoration: underline;
        }

        .price-section {
            flex: 0 0 20%;
            padding: 15px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-end;
            border-left: 1px solid var(--medium-gray);
        }

        .price-section .original-price {
            font-size: 0.9rem;
            color: var(--dark-gray);
            text-decoration: line-through;
            margin-bottom: 5px;
        }

        .price-section .discounted-price {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--discount-red);
            margin-bottom: 5px;
        }

        .price-section .price-note {
            font-size: 0.8rem;
            color: #6B7280;
            margin-bottom: 10px;
        }

        .price-section .check-btn {
            background: #F97316;
            color: var(--white);
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            transition: var(--transition);
            box-shadow: 0 2px 8px rgba(249, 115, 22, 0.3);
        }

        .price-section .check-btn:hover {
            background: #EA580C;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.5);
        }

        .right-col {
            flex-basis: 25%;
            max-width: 300px;
        }

        .sidebar {
            position: sticky;
            top: 20px;
            border-radius: var(--border-radius);
            padding: 20px;
            background: var(--white);
            box-shadow: var(--shadow);
            transition: box-shadow 0.3s ease;
        }

        .sidebar:hover {
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
        }

        .sidebar .logo {
            display: flex;
            justify-content: center;
            margin-bottom: 15px;
        }

        .sidebar h2 {
            font-size: 1.4rem;
            font-weight: 600;
            color: #1F2937;
            margin-bottom: 10px;
            text-align: center;
        }

        .sidebar p {
            font-size: 0.9rem;
            color: #6B7280;
            line-height: 1.5;
            margin-bottom: 15px;
            text-align: center;
        }

        .sidebar .brand {
            color: var(--primary-blue);
            font-weight: 700;
        }

        .search-listing {
            display: flex;
            position: relative;
            width: 100%;
            margin-bottom: 20px;
            background: var(--white);
            border: 1px solid var(--search-border);
            border-radius: 10px;
            overflow: hidden;
            transition: box-shadow 0.3s ease;
        }

        .search-listing:hover {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .search-listing input {
            width: 100%;
            padding: 12px 45px 12px 15px;
            border: none;
            background: transparent;
            font-size: 0.95rem;
            color: #1F2937;
            transition: background 0.3s ease;
        }

        .search-listing input::placeholder {
            color: #9CA3AF;
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        .search-listing input:focus {
            outline: none;
            background: var(--white);
        }

        .search-listing input:focus::placeholder {
            transform: translateX(10px);
            opacity: 0;
        }

        .search-listing button {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            background: var(--secondary-blue);
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            cursor: pointer;
            color: var(--white);
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s ease, transform 0.3s ease;
        }

        .search-listing button:hover {
            background: #2563EB;
            transform: translateY(-50%) scale(1.05);
        }

        .search-listing button:focus {
            outline: 2px solid var(--primary-blue);
            outline-offset: 2px;
        }

        .search-tips {
            padding-top: 20px;
            border-top: 1px solid var(--medium-gray);
        }

        .search-tips h3 {
            font-size: 1.15rem;
            color: #1F2937;
            margin-bottom: 15px;
            text-align: center;
            font-weight: 600;
        }

        .popular-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        .search-tag {
            padding: 8px 16px;
            background: var(--light-gray);
            color: #1F2937;
            border: 1px solid var(--search-border);
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s ease, color 0.3s ease, transform 0.3s ease;
        }

        .search-tag:hover {
            background: var(--secondary-blue);
            color: var(--white);
            border-color: var(--secondary-blue);
            transform: translateY(-2px);
        }

        .search-tag:focus {
            outline: 2px solid var(--primary-blue);
            outline-offset: 2px;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            padding: 20px 0;
            margin: 20px 0;
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }

        .pagination .pagination-btns-container {
            display: flex;
            gap: 5px;
        }

        .pagination-btn {
            padding: 8px 12px;
            background: var(--light-gray);
            border-radius: 5px;
            color: #1F2937;
            font-size: 0.9rem;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .pagination-btn.current {
            background: var(--secondary-blue);
            color: var(--white);
        }

        .pagination-btn:hover {
            background: #93C5FD;
        }

        .pagination-arrow {
            padding: 8px 12px;
            background: var(--light-gray);
            border: none;
            border-radius: 5px;
            color: #1F2937;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .pagination-arrow:disabled {
            background: #D1D5DB;
            color: #9CA3AF;
            cursor: not-allowed;
        }

        .pagination-arrow:hover:not(:disabled) {
            background: #93C5FD;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            max-width: 80%;
            max-height: 80%;
            border-radius: var(--border-radius);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
        }

        .close-btn {
            position: absolute;
            top: 20px;
            right: 30px;
            color: var(--white);
            font-size: 2rem;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .close-btn:hover {
            color: var(--discount-red);
        }

        @media (max-width: 991px) {
            .navbar-nav {
                padding: 15px;
                background-color: var(--white);
            }
            .navbar-brand img {
                height: 40px;
            }
            .list-container {
                flex-direction: column;
            }
            .left-col, .right-col {
                flex-basis: 100%;
                max-width: 100%;
            }
            .right-col {
                margin-top: 20px;
            }
            .sidebar {
                position: relative;
                top: 0;
            }
            .package-card {
                flex-direction: column;
            }
            .package-image {
                flex: 0 0 100%;
            }
            .package-image img {
                height: 250px;
            }
            .price-section {
                flex: 0 0 100%;
                align-items: center;
                padding: 15px;
                border-left: none;
                border-top: 1px solid var(--medium-gray);
            }
            .search-listing {
                padding: 5px;
            }
            .search-listing input {
                padding: 10px 40px 10px 12px;
                font-size: 0.9rem;
            }
            .search-listing button {
                width: 32px;
                height: 32px;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 576px) {
            .navbar-brand img {
                height: 35px;
            }
            .package-info h2 {
                font-size: 1.3rem;
            }
            .package-image img {
                height: 200px;
            }
            .price-section .discounted-price {
                font-size: 1.2rem;
            }
            .price-section .check-btn {
                padding: 8px 15px;
                font-size: 0.85rem;
            }
            .location-badge, .discount-badge {
                font-size: 0.75rem;
                padding: 4px 10px;
            }
            .deal {
                font-size: 0.75rem;
            }
            .review-snippet, .review-author, .review-date {
                font-size: 0.8rem;
            }
            .search-listing input {
                padding: 8px 35px 8px 10px;
                font-size: 0.85rem;
            }
            .search-listing button {
                width: 30px;
                height: 30px;
                font-size: 0.85rem;
            }
            .search-tag {
                padding: 6px 12px;
                font-size: 0.85rem;
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
                        <li class="nav-item">
                            <a class="nav-link" href="./index.php">Popular Places</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="./listing.php">All Packages</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="./mainpage.html">About JDAR</a>
                        </li>
                        <?php require_once "./components/_navBtns.php"; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

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

            <section class="intro-section">
                <div class="container">
                    <h1 class="section-title">
                        Find Your Suitable Package in <span class="brand">JDAR</span>
                    </h1>
                </div>
            </section>
        </div>
    </section>

    <div id="imageModal" class="modal">
        <span class="close-btn" onclick="closeZoom()">×</span>
        <img id="zoomedImage" class="modal-content" src="">
    </div>

    <main class="container">
        <div class="list-container">
            <div class="left-col">
                <section class="package-container">
                    <?php while ($row = mysqli_fetch_assoc($res)): ?>
                        <?php
                        $rating = floatval($row['package_rating']);
                        $reviewCount = rand(100, 1000);
                        $stars = '';
                        $fullStars = floor($rating);
                        $halfStar = ($rating - $fullStars) >= 0.5 ? 1 : 0;
                        $emptyStars = 5 - $fullStars - $halfStar;
                        for ($i = 0; $i < $fullStars; $i++) {
                            $stars .= "<i class='fa-solid fa-star'></i>";
                        }
                        if ($halfStar) {
                            $stars .= "<i class='fa-solid fa-star-half-stroke'></i>";
                        }
                        for ($i = 0; $i < $emptyStars; $i++) {
                            $stars .= "<i class='fa-regular fa-star'></i>";
                        }
                        $discountPercentage = isset($row['discount_percentage']) ? floatval($row['discount_percentage']) : 0;
                        $originalPrice = floatval(str_replace(',', '', $row['package_price']));
                        $discountedPrice = $discountPercentage > 0 ? $originalPrice * (1 - $discountPercentage / 100) : $originalPrice;
                        $formattedOriginalPrice = number_format($originalPrice, 0, ',', '.');
                        $formattedDiscountedPrice = number_format($discountedPrice, 0, ',', '.');
                        ?>
                        <article class="package-card">
                            <div class="package-image">
                                <img src="<?php echo htmlspecialchars($row['master_image']); ?>" alt="<?php echo htmlspecialchars($row['package_name']); ?>" onclick="zoomImage(this)">
                                <span class="location-badge"><?php echo htmlspecialchars($row['package_location']); ?></span>
                                <?php if ($discountPercentage > 0): ?>
                                    <span class="discount-badge"><?php echo $discountPercentage; ?>% OFF</span>
                                <?php endif; ?>
                            </div>
                            <div class="package-info">
                                <div>
                                    <h2><?php echo htmlspecialchars($row['package_name']); ?></h2>
                                    <div class="rating-location">
                                        <div class="rating">
                                            <span class="stars"><?php echo $stars; ?></span>
                                            <span><?php echo number_format($rating, 1); ?> (<?php echo $reviewCount; ?>)</span>
                                            <span class="review-label ?>"><?php echo $rating >= 4.5 ? 'Superb' : 'Impressive'; ?></span>
                                        </div>
                                    </div>
                                    <div class="deal">Hotel Deals</div>
                                    <div class="deal">Penawaran eksklusif s.d. <?php echo $discountPercentage; ?>% hanya di Aplikasi</div>
                                    <div class="review-snippet"><?php echo substr(htmlspecialchars($row['package_desc']), 0, 150); ?>... <a href="./package.php?id=<?php echo htmlspecialchars($row['package_id']); ?>" class="see-more">See more</a></div>
                                    <div class="review-author">JDAR Traveler</div>
                                    <div class="review-date">From <?php echo htmlspecialchars($row['package_start']); ?></div>
                                </div>
                            </div>
                            <div class="price-section">
                                <?php if ($discountPercentage > 0): ?>
                                    <div class="original-price">Rp <?php echo $formattedOriginalPrice; ?></div>
                                <?php endif; ?>
                                <div class="discounted-price">Rp <?php echo $formattedDiscountedPrice; ?></div>
                                <div class="price-note">Di luar pajak & biaya</div>
                                <a href="./package.php?id=<?php echo htmlspecialchars($row['package_id']); ?>" class="check-btn">Pilih</a>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </section>
            </div>

            <div class="right-col">
                <aside class="sidebar">
                    <div class="logo">
                        <img src="logo.png" alt="JDAR Logo" style="width: 50px; height: auto;">
                    </div>
                    <h2>Plan Your Adventure</h2>
                    <p>Explore exclusive travel packages with <span class="brand">JDAR</span>.</p>
                    <form class="search-listing" id="search-form" aria-label="Search travel destinations">
                        <input type="text" id="sidebar-search-input" name="search" value="<?php echo htmlspecialchars($location); ?>" placeholder="Search destinations..." aria-label="Enter destination city or region">
                        <button type="submit" id="search-button" aria-label="Search destinations"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </form>
                    <div class="search-tips">
                        <h3>Popular Destinations</h3>
                        <div class="popular-tags">
                            <button class="search-tag" data-location="Bali" aria-label="Search Bali packages">Bali</button>
                            <button class="search-tag" data-location="Jakarta" aria-label="Search Jakarta packages">Jakarta</button>
                            <button class="search-tag" data-location="Lombok" aria-label="Search Lombok packages">Lombok</button>
                            <button class="search-tag" data-location="Yogyakarta" aria-label="Search Yogyakarta packages">Yogyakarta</button>
                            <button class="search-tag" data-location="Jawa Timur" aria-label="Search Jawa Timur packages">Jawa Timur</button>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </main>

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

    <?php require_once "./components/_footer.php"; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
        $(document).ready(function() {
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

            $('.navbar-nav .nav-link').on('click', function() {
                if ($(window).width() <= 991) {
                    $('.navbar-collapse').collapse('hide');
                }
            });

            AOS.init({ duration: 800, once: true });

            window.zoomImage = function(img) {
                var modal = document.getElementById("imageModal");
                var zoomed = document.getElementById("zoomedImage");
                zoomed.src = img.src;
                modal.style.display = "flex";
            }

            window.closeZoom = function() {
                document.getElementById("imageModal").style.display = "none";
            }

            const searchForm = $('#search-form');
            const searchInput = $('#sidebar-search-input');
            const searchButton = $('#search-button');
            const packageContainer = $('.package-container');
            const paginationBtns = $('.pagination-btn');
            const prevPage = $('#prev-page');
            const nextPage = $('#next-page');
            const allPackagesCount = $('#all-packages-count');
            let currentPage = 1;
            let totalPackages = parseInt(allPackagesCount.text());
            let query = new URLSearchParams(location.search).get('loc') || '';

            const escapeHTML = (str) => {
                if (typeof str !== 'string') return str;
                return str.replace(/&/g, '&amp;')
                          .replace(/</g, '&lt;')
                          .replace(/>/g, '&gt;')
                          .replace(/"/g, '&quot;')
                          .replace(/'/g, '&#039;')
                          .replace(/`/g, '&#096;');
            };

            const addPackage = (pkg) => {
                try {
                    const rating = parseFloat(pkg.package_rating) || 0;
                    let stars = '';
                    for (let i = 1; i <= 5; i++) {
                        stars += i <= rating ? '<i class="fa-solid fa-star"></i>' :
                                 i - 0.5 <= rating ? '<i class="fa-solid fa-star-half-stroke"></i>' :
                                 '<i class="fa-regular fa-star"></i>';
                    }
                    const reviewCount = Math.floor(Math.random() * 900) + 100;
                    const discountPercentage = parseFloat(pkg.discount_percentage) || 0;
                    const priceAsString = typeof pkg.package_price === 'string' ? pkg.package_price : pkg.package_price.toString();
                    const originalPrice = parseFloat(priceAsString.replace(/,/g, '')) || 0;
                    const discountedPrice = discountPercentage > 0 ? originalPrice * (1 - discountPercentage / 100) : originalPrice;
                    const formattedOriginalPrice = originalPrice.toLocaleString('id-ID');
                    const formattedDiscountedPrice = discountedPrice.toLocaleString('id-ID');

                    const safePackageName = escapeHTML(pkg.package_name);
                    const safePackageLocation = escapeHTML(pkg.package_location);
                    const safeMasterImage = escapeHTML(pkg.master_image);
                    const safePackageDesc = escapeHTML(pkg.package_desc);
                    const safePackageId = escapeHTML(pkg.package_id);
                    const safePackageStart = escapeHTML(pkg.package_start);

                    packageContainer.append(`
                        <article class="package-card">
                            <div class="package-image">
                                <img src="${safeMasterImage}" alt="${safePackageName}" onclick="zoomImage(this)">
                                <span class="location-badge">${safePackageLocation}</span>
                                ${discountPercentage > 0 ? `<span class="discount-badge">${discountPercentage}% OFF</span>` : ''}
                            </div>
                            <div class="package-info">
                                <div>
                                    <h2>${safePackageName}</h2>
                                    <div class="rating-location">
                                        <div class="rating">
                                            <span class="stars">${stars}</span>
                                            <span>${rating.toFixed(1)} (${reviewCount})</span>
                                            <span class="review-label">${rating >= 4.5 ? 'Superb' : 'Impressive'}</span>
                                        </div>
                                    </div>
                                    <div class="deal">Hotel Deals</div>
                                    <div class="deal">Penawaran eksklusif s.d. ${discountPercentage}% hanya di Aplikasi</div>
                                    <div class="review-snippet">${safePackageDesc.substring(0, 150)}... <a href="./package.php?id=${safePackageId}" class="see-more">See more</a></div>
                                    <div class="review-author">JDAR Traveler</div>
                                    <div class="review-date">From ${safePackageStart}</div>
                                </div>
                            </div>
                            <div class="price-section">
                                ${discountPercentage > 0 ? `<div class="original-price">Rp ${formattedOriginalPrice}</div>` : ''}
                                <div class="discounted-price">Rp ${formattedDiscountedPrice}</div>
                                <div class="price-note">Di luar pajak & biaya</div>
                                <a href="./package.php?id=${safePackageId}" class="check-btn">Pilih</a>
                            </div>
                        </article>
                    `);
                } catch (error) {
                    console.error('Error adding package:', error, pkg);
                }
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
                    totalPackages = parseInt(data[0]) || 0;
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
                nextPage.prop('disabled', page === totalPages || totalPages === 0);
            };

            searchForm.on('submit', function(e) {
                e.preventDefault();
                query = searchInput.val().trim();
                currentPage = 1;
                fetchPackages(query, currentPage);
                history.pushState({}, '', query ? `?loc=${encodeURIComponent(query)}` : './listing.php');
            });

            searchButton.on('click', function(e) {
                e.preventDefault();
                searchForm.trigger('submit');
            });

            $('.search-tag').on('click', function(e) {
                e.preventDefault();
                const location = $(this).data('location');
                searchInput.val(location);
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