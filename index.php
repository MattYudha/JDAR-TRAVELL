<?php
try {
    $databasePath = './app/Database.php';
    if (!file_exists($databasePath)) {
        throw new Exception("File Database.php tidak ditemukan di path: $databasePath");
    }
    require_once $databasePath;

    if (!class_exists('Packages')) {
        throw new Exception("Kelas 'Packages' tidak ditemukan di file Database.php");
    }

    $packagesObj = new Packages();
    $result = $packagesObj->getPackages('All', 0, 999999);
    $exclusivePackages = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

    if (empty($exclusivePackages)) {
        error_log("Tidak ada paket yang ditemukan di database pada index.php");
    } else {
        error_log("Jumlah paket ditemukan: " . count($exclusivePackages));
        foreach ($exclusivePackages as $pkg) {
            error_log("Paket: " . ($pkg['package_name'] ?? 'Unknown') . " | Discount: " . ($pkg['discount_percentage'] ?? 0) . "% | Discounted Price: " . ($pkg['discounted_price'] ?? 'N/A'));
        }
    }

    $reviewCounts = [];
    foreach ($exclusivePackages as $pkg) {
        $package_id = $pkg['package_id'];
        $reviewCounts[$package_id] = $packagesObj->getReviewCount($package_id);
    }

    $destinations = [
        'Tangerang' => $packagesObj->getPackagesWithQueryCount('Tangerang'),
        'Jawa Tengah' => $packagesObj->getPackagesWithQueryCount('Jawa Tengah'),
        'Bali' => $packagesObj->getPackagesWithQueryCount('Bali'),
        'NTB' => $packagesObj->getPackagesWithQueryCount('NTB'),
        'NTT' => $packagesObj->getPackagesWithQueryCount('NTT'),
        'Jakarta' => $packagesObj->getPackagesWithQueryCount('Jakarta'),
        'Jawa Timur' => $packagesObj->getPackagesWithQueryCount('Jawa Timur'),
        'Yogyakarta' => $packagesObj->getPackagesWithQueryCount('Yogyakarta'),
        'Lombok' => $packagesObj->getPackagesWithQueryCount('Lombok'),
    ];
} catch (Exception $e) {
    $exclusivePackages = [];
    $reviewCounts = [];
    $destinations = [
        'Tangerang' => 0,
        'Jawa Tengah' => 0,
        'Bali' => 0,
        'NTB' => 0,
        'NTT' => 0,
        'Jakarta' => 0,
        'Jawa Timur' => 0,
        'Yogyakarta' => 0,
        'Lombok' => 0,
    ];
    error_log("Kesalahan di index.php: " . $e->getMessage());
    echo '<p class="error">Terjadi kesalahan: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

if (!isset($_SESSION)) {
    session_start();
}
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Triptrip - Explore Your Dream Destinations</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', 'Poppins', sans-serif;
        }

        body {
            background-color: rgb(227, 232, 240);
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
            padding-top: 90px;
        }

        .navbar {
            background-color: #fff;
            padding: 15px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            transition: transform 0.3s ease;
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
            transition: transform 0.3s ease, background-color 0.3s ease;
        }

        .navbar-nav .nav-link.register-btn:hover {
            background-color: #0056b3;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 123, 255, 0.5);
        }

        .hero {
            background: url('assets/bg.png') no-repeat center center/cover;
            height: 60vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            position: relative;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
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
        }

        .search-section {
    padding: 25px 0;
    background-color: #fff;
    box-shadow: 0 3px 15px rgba(0,0,0,0.06);
    position: relative;
    z-index: 10;
}

.search-section::after {
    content: "";
    position: absolute;
    bottom: -10px;
    left: 0;
    right: 0;
    height: 10px;
    background: linear-gradient(to bottom, rgba(0,0,0,0.03), transparent);
    z-index: -1;
}

.search-bar {
    max-width: 1160px;
    margin: 0 auto;
    padding: 0 20px;
}

.search-group {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    padding: 20px;
    background: #ffffff;
    border-radius: 12px;
    align-items: end;
    box-shadow: 0 0 0 1px rgba(0,0,0,0.08), 0 2px 4px rgba(0,0,0,0.03);
    border: 1px solid rgba(0, 123, 255, 0.1);
    position: relative;
}

.search-group::before {
    content: "";
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    background: linear-gradient(135deg, #007bff, #00c3ff);
    border-radius: 14px;
    z-index: -1;
    opacity: 0.08;
}

.location-input, .date-input, .accommodation-input, .budget-input, .facilities-input {
    display: flex;
    flex-direction: column;
    position: relative;
}

.search-group label {
    font-size: 0.85rem;
    color: #555;
    margin-bottom: 6px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 5px;
}

.search-group label i, 
.search-group label svg {
    color: #007bff;
    font-size: 14px;
}

.search-group select, 
.search-group input[type="date"],
.search-group input[type="text"] {
    width: 100%;
    padding: 11px 14px;
    border-radius: 8px;
    border: 1px solid #e0e0e0;
    background: #fff;
    font-size: 0.9rem;
    transition: all 0.25s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    color: #333;
}

.search-group select:hover, 
.search-group input[type="date"]:hover,
.search-group input[type="text"]:hover {
    border-color: #bbd9ff;
}

.search-group select:focus, 
.search-group input[type="date"]:focus,
.search-group input[type="text"]:focus {
    border-color: #007bff;
    outline: none;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.15);
}

.checkbox-group {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 2px;
}

.checkbox-item {
    display: flex;
    align-items: center;
    gap: 6px;
    position: relative;
}

.checkbox-item input[type="checkbox"] {
    appearance: none;
    -webkit-appearance: none;
    width: 18px;
    height: 18px;
    border: 1px solid #ddd;
    border-radius: 4px;
    outline: none;
    cursor: pointer;
    position: relative;
    background-color: white;
}

.checkbox-item input[type="checkbox"]:checked {
    background-color: #007bff;
    border-color: #007bff;
}

.checkbox-item input[type="checkbox"]:checked::after {
    content: "✓";
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 12px;
    font-weight: bold;
}

.checkbox-item label {
    font-size: 0.85rem;
    margin-bottom: 0;
    cursor: pointer;
    font-weight: 400;
    color: #555;
}

#search_button {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.25s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    box-shadow: 0 4px 10px rgba(0, 123, 255, 0.2);
    min-height: 44px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.9rem;
}

#search_button i,
#search_button svg {
    font-size: 16px;
}

#search_button:hover {
    background: linear-gradient(135deg, #0062cc, #004494);
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(0, 123, 255, 0.25);
}

#search_button:active {
    transform: translateY(0);
    box-shadow: 0 2px 8px rgba(0, 123, 255, 0.2);
}

@media (max-width: 768px) {
    .search-group {
        grid-template-columns: 1fr;
        gap: 12px;
        padding: 16px;
    }
    
    #search_button {
        width: 100%;
    }
}

/* Custom date input styling */
input[type="date"]::-webkit-calendar-picker-indicator {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23007bff' viewBox='0 0 16 16'%3E%3Cpath d='M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z'/%3E%3C/svg%3E");
    cursor: pointer;
    padding: 5px;
}

/* Enhanced select styling */
select {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23007bff' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: calc(100% - 12px) center;
    padding-right: 35px !important;
}

/* Floating label animation (optional enhancement) */
.search-input-wrapper {
    position: relative;
}

.search-input-floating-label {
    position: absolute;
    top: 50%;
    left: 15px;
    transform: translateY(-50%);
    pointer-events: none;
    transition: all 0.2s ease;
    color: #777;
    font-size: 0.9rem;
}

.search-input:focus + .search-input-floating-label,
.search-input:not(:placeholder-shown) + .search-input-floating-label {
    top: 0;
    left: 10px;
    transform: translateY(-50%);
    font-size: 0.75rem;
    background: white;
    padding: 0 5px;
    color: #007bff;
}



.search-results {
            max-width: 1200px;
            width: 50%;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            padding: 20px;
        }

        .package-card {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0, 99, 209, 0.08);
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 123, 255, 0.1);
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .package-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 99, 209, 0.15);
            border-color: rgba(0, 123, 255, 0.2);
        }

        .package-card-image {
            position: relative;
            width: 100%;
            height: 200px;
            overflow: hidden;
        }

        .package-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .package-card:hover .package-card-image img {
            transform: scale(1.05);
        }

        .package-card-image::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.3));
            z-index: 1;
        }

        .stock {
            position: absolute;
            top: 12px;
            right: 12px;
            background: rgba(255, 255, 255, 0.9);
            color: #1a2b4e;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.85rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            z-index: 2;
            transition: all 0.3s ease;
        }

        .stock i {
            color: #007bff;
        }

        .package-card:hover .stock {
            background: rgba(255, 255, 255, 1);
        }

        .package-card-content {
            padding: 10px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .package-title {
            font-size: 1.2rem;
            color: #1a2b4e;
            font-weight: 700;
            margin-bottom: 10px;
            line-height: 1.3;
            transition: color 0.3s ease;
        }

        .package-card:hover .package-title {
            color: #007bff;
        }

        .package-price {
            font-size: 1.1rem;
            color: #ff5a5f;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .package-location, .package-date {
            font-size: 0.9rem;
            color: #667895;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .package-location i, .package-date i {
            color: #007bff;
            font-size: 1rem;
        }

        .package-btn {
            margin-top: auto;
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            text-align: center;
            padding: 12px;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: block;
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.2);
        }

        .package-btn:hover {
            background: linear-gradient(135deg, #0062cc, #004494);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 123, 255, 0.25);
        }

        .package-btn:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.2);
        }

        @media (max-width: 768px) {
            .search-results {
                grid-template-columns: 1fr;
                padding: 15px;
            }

            .package-card {
                border-radius: 12px;
            }

            .package-card-image {
                height: 180px;
            }

            .package-card-content {
                padding: 15px;
            }

            .package-title {
                font-size: 1.1rem;
            }

            .package-price {
                font-size: 1rem;
            }

            .package-location, .package-date {
                font-size: 0.85rem;
            }

            .package-btn {
                padding: 10px;
                font-size: 0.9rem;
            }

            .stock {
                padding: 5px 10px;
                font-size: 0.8rem;
            }
        }

        @media (max-width: 576px) {
            .package-card-image {
                height: 160px;
            }

            .package-title {
                font-size: 1rem;
            }

            .package-price {
                font-size: 0.95rem;
            }

            .package-btn {
                padding: 8px;
                font-size: 0.85rem;
            }
        }


        /* ========= Search Popup Section =========
   Styles for a dynamic pop-up that displays search result notifications
   with success (package found) or error (package not found) states.
   Includes modern UI aesthetics, animations, and responsive design.
*/
#popup-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    background-color: #ffffff;
    border-radius: 8px;
    padding: 16px 24px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    display: flex;
    align-items: center;
    gap: 12px;
    z-index: 1000;
    opacity: 0;
    transform: translateY(-20px);
    transition: opacity 0.3s ease, transform 0.3s ease;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    font-size: 16px;
    font-weight: 500;
    max-width: 90%;
    min-width: 240px;
}

/* Show state for pop-up animation */
#popup-notification.show {
    opacity: 1;
    transform: translateY(0);
}

/* Success state (package found) */
#popup-notification.available {
    background-color: #e6f4ea;
    color: #1a3c34;
    border-left: 4px solid #2e7d32;
}

/* Error state (package not found) */
#popup-notification.unavailable {
    background-color: #ffe6e6;
    color: #5f2120;
    border-left: 4px solid #d32f2f;
}

/* Icon styling for success and error states */
#popup-notification::before {
    content: '';
    display: inline-block;
    width: 24px;
    height: 24px;
    background-size: contain;
    background-repeat: no-repeat;
}

#popup-notification.available::before {
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="%232e7d32" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>');
}

#popup-notification.unavailable::before {
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="%23d32f2f" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18M6 6l12 12"/></svg>');
}

/* Responsive adjustments */
@media (max-width: 768px) {
    #popup-notification {
        top: 10px;
        right: 10px;
        padding: 12px 16px;
        font-size: 14px;
        min-width: 200px;
    }
}

@media (max-width: 480px) {
    #popup-notification {
        left: 50%;
        right: auto;
        transform: translateX(-50%) translateY(-20px);
        width: 90%;
        max-width: 320px;
    }

    #popup-notification.show {
        transform: translateX(-50%) translateY(0);
    }
}

        
.coupon-section {
    padding: 20px 0;
    background: #ffffff;
    position: relative;
}

.section-header {
    text-align: center;
    margin-bottom: 20px;
    position: relative;
}

.section-header h2 {
    font-size: 1.5rem;
    color: #0063D1;
    margin-bottom: 6px;
    font-weight: 700;
}

.section-header p {
    font-size: 0.9rem;
    color: #666;
    max-width: 550px;
    margin: 0 auto;
}

.coupon-container {
    background-color: white;
    max-width: 1100px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 16px;
    padding: 0 15px;
    position: relative;
}

.coupon-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    padding: 12px;
    overflow: hidden;
    position: relative;
    transition: all 0.25s ease;
    display: flex;
    flex-direction: column;
    border: 1px solid #f0f0f0;
    width: 100%;      /* Responsive width */
    max-width: 250px; /* Maximum width */
    height: 220px;    /* Reduced height */
    font-size: 12px;
    justify-content: space-between;
    margin: 0 auto;
}

.coupon-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 99, 209, 0.1);
    border-color: rgba(0, 99, 209, 0.15);
}

.coupon-top {
    padding: 10px;
    display: flex;
    align-items: center;
    position: relative;
    background: white;
}

.coupon-brand {
    position: absolute;
    top: 8px;
    right: 8px;
    width: 30px;
    height: 30px;
    background: #fff;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.coupon-brand img {
    max-width: 24px;
    max-height: 24px;
}

.coupon-icon {
    width: 30px;
    height: 30px;
    margin-right: 8px;
    background: rgba(0, 99, 209, 0.08);
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.coupon-icon svg {
    width: 16px;
    height: 16px;
    color: #0063D1;
}

.coupon-info {
    flex: 1;
    padding-right: 30px;
}

.coupon-info h3 {
    font-size: 0.95rem;
    color: #0063D1;
    margin: 0;
    font-weight: 700;
    line-height: 1.2;
}

.coupon-info p {
    font-size: 0.75rem;
    color: #666;
    margin: 3px 0 0;
    line-height: 1.3;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.coupon-details {
    padding: 0 10px 8px;
}

.coupon-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
}

.coupon-discount {
    font-size: 1.1rem;
    font-weight: 800;
    color: #FF5A5F;
    background: rgba(255, 90, 95, 0.1);
    padding: 3px 8px;
    border-radius: 6px;
}

.coupon-validity {
    font-size: 0.7rem;
    color: #777;
    display: flex;
    align-items: center;
}

.coupon-validity svg {
    width: 12px;
    height: 12px;
    margin-right: 3px;
    color: #0063D1;
}

.coupon-divider {
    position: relative;
    height: 10px;
    margin: 0 -10px;
    background: transparent;
}

.coupon-divider::before {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    top: 50%;
    height: 1px;
    background: #f0f0f0;
    z-index: 1;
}

.coupon-divider::after {
    content: "";
    position: absolute;
    left: -5px;
    top: 0;
    width: 10px;
    height: 10px;
    background: white;
    border-radius: 0 5px 5px 0;
    box-shadow: inset -1px 0 0 #f0f0f0;
    z-index: 2;
}

.coupon-divider .right-circle {
    position: absolute;
    right: -5px;
    top: 0;
    width: 10px;
    height: 10px;
    background: white;
    border-radius: 5px 0 0 5px;
    box-shadow: inset 1px 0 0 #f0f0f0;
    z-index: 2;
}

.coupon-action {
    padding: 8px 10px;
    background: #f9fcff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: auto;
}

.coupon-code {
    display: flex;
    align-items: center;
    padding: 4px 7px;
    background: rgba(0, 99, 209, 0.06);
    border: 1px dashed #0063D1;
    border-radius: 4px;
    font-family: monospace;
    font-size: 0.75rem;
    font-weight: 600;
    color: #0063D1;
    letter-spacing: 0.5px;
}

.copy-btn {
    background: #0063D1;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 4px 10px;
    font-size: 0.7rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.25s ease;
    display: flex;
    align-items: center;
    gap: 3px;
}

.copy-btn svg {
    width: 12px;
    height: 12px;
}

.copy-btn:hover {
    background: #0052B4;
    transform: translateY(-1px);
    box-shadow: 0 2px 5px rgba(0, 99, 209, 0.15);
}

.coupon-tag {
    position: absolute;
    top: 0;
    left: 10px;
    background: #FF5A5F;
    color: white;
    font-size: 0.65rem;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 0 0 5px 5px;
    box-shadow: 0 1px 4px rgba(255, 90, 95, 0.2);
    z-index: 10;
}

.coupon-tag.popular {
    background: #0063D1;
    box-shadow: 0 1px 4px rgba(0, 99, 209, 0.2);
}

.coupon-tag.ending-soon {
    background: #FF9800;
    box-shadow: 0 1px 4px rgba(255, 152, 0, 0.2);
}

/* SEGERA BERAKHIR Buttons */
[class*="SEGERA BERAKHIR"] {
    background: #FF9800;
    color: white;
    font-size: 0.65rem;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 3px;
    display: inline-block;
    margin-bottom: 5px;
}

/* Salin Kode Buttons */
[class*="Salin Kode"] {
    background: #0063D1;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 5px 12px;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    text-align: center;
}

[class*="Salin Kode"]:hover {
    background: #0052B4;
    transform: translateY(-1px);
}

/* Media Queries */
@media (max-width: 768px) {
    .coupon-container {
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 12px;
    }
    
    .coupon-card {
        max-width: 200px;
        height: 200px;
    }
    
    .section-header h2 {
        font-size: 1.3rem;
    }
    
    .section-header p {
        font-size: 0.8rem;
    }
}

@media (max-width: 480px) {
    .coupon-container {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 10px;
    }
    
    .coupon-card {
        max-width: 180px;
        height: 190px;
    }
    
    .coupon-info h3 {
        font-size: 0.85rem;
    }
    
    .coupon-discount {
        font-size: 0.95rem;
    }
}

@keyframes subtle-pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.01); }
    100% { transform: scale(1); }
}

.coupon-card.featured {
    animation: subtle-pulse 3s infinite;
    border: 1px solid rgba(0, 99, 209, 0.15);
    box-shadow: 0 2px 8px rgba(0, 99, 209, 0.06);
}

.coupon-card.hotel {
    border-top: 2px solid #0063D1;
}

.coupon-card.flight {
    border-top: 2px solid #FF5A5F;
}

.coupon-card.activity {
    border-top: 2px solid #27AE60;
}

.coupon-card.bundle {
    border-top: 2px solid #9B59B6;
}
        .epic-offers-section {
            padding: 30px 0;
            background-color: #fff;
        }

        .epic-offers-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .epic-offers-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .epic-offers-header i {
            color: rgb(230, 160, 7);
            font-size: 1.2rem;
            margin-right: 10px;
        }

        .epic-offers-header h2 {
            font-size: 1.6rem;
            color: rgb(61, 78, 111);
            margin: 0;
            font-weight: 780;
        }

        .epic-offers-header a {
            margin-left: auto;
            font-size: 0.9rem;
            color: rgb(0, 48, 62);
            text-decoration: none;
        }

        .epic-offers-tabs {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .epic-offers-tabs button {
            background: rgb(100, 133, 182);
            border: none;
            border-radius: 20px;
            padding: 8px 16px;
            font-size: 0.9rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: background 0.3s ease, color 0.3s ease;
        }

        .epic-offers-tabs button.active {
            background: rgb(22, 94, 154);
            color: white;
        }

        .epic-offers-tabs button:hover {
            background: rgb(18, 40, 106);
        }

        .epic-offers-tabs button.active:hover {
            background: rgb(18, 40, 106);
        }


        
/* ========= Slider Section Styling =========

*/
/* ========= Slider Section Styling =========
   Defines the overall section styling with a gradient background and centering.
*/
.slider-section {
    padding: 40px 0;
    background: linear-gradient(to bottom,rgb(255, 255, 255), #f8faff);
    text-align: center;
    position: relative;
    overflow: hidden;
    width: 80%;
    margin: 0 auto; /* Center the section */
}

/* Background pattern */
.slider-section::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background : white;
    opacity: 0.4;
    pointer-events: none;
}

/* Slider title */
.slider-section h2 {
    font-size: 1.6rem;
    color: #1a2b4e;
    font-weight: 700;
    margin-bottom: 10px;
    position: relative;
    display: inline-block;
}

.slider-section h2::after {
    content: "";
    position: absolute;
    bottom: -5px;
    left: 50%;
    transform: translateX(-50%);
    width: 60px;
    height: 2px;
    background: linear-gradient(to right, #007bff, #00c3ff);
    border-radius: 30px;
}

/* Slider description */
.slider-section p {
    font-size: 0.95rem;
    color: #667895;
    max-width: 600px;
    margin: 0 auto 30px;
}

/* Slider container */
.slider-container {
    position: relative;
    width: 100%;
    overflow: hidden; /* Ensure no overflow of adjacent slides */
    padding: 0 15px;
    box-sizing: border-box;
}

/* Slider content using Flexbox for centering */
.slider {
    display: flex;
    transition: transform 0.6s cubic-bezier(0.25, 1, 0.5, 1);
}

/* Individual slide */
.slide {
    flex: 0 0 200px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.04);
    overflow: hidden;
    position: relative;
    transition: all 0.4s ease;
    transform-origin: center bottom;
    border: 1px solid rgba(255, 255, 255, 0.8);
    margin: 0 7.5px; /* Half of the gap to maintain spacing */
}

.slide:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 123, 255, 0.08);
    border-color: rgba(0, 123, 255, 0.1);
}

.slide::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(to right, #007bff, #00c3ff);
    transform: scaleX(0);
    transform-origin: center;
    transition: transform 0.4s ease;
    border-bottom-left-radius: 10px;
    border-bottom-right-radius: 10px;
}

.slide:hover::after {
    transform: scaleX(1);
}

/* Slide image */
.slide-image {
    width: 100%;
    height: 140px;
    object-fit: cover;
    transition: transform 0.5s ease;
    transform-origin: center;
}

.slide:hover .slide-image {
    transform: scale(1.05);
}

.slide-image-container {
    position: relative;
    overflow: hidden;
    height: 140px;
}

.slide-image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(to bottom, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0) 40%, rgba(0,0,0,0.2) 100%);
    z-index: 1;
}

/* Location pin */
.location-pin {
    position: absolute;
    top: 8px;
    left: 8px;
    background: rgba(255, 255, 255, 0.85);
    color: #1a2b4e;
    padding: 4px 8px;
    border-radius: 15px;
    font-size: 0.75rem;
    display: flex;
    align-items: center;
    gap: 4px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    backdrop-filter: blur(3px);
    -webkit-backdrop-filter: blur(3px);
    z-index: 2;
    font-weight: 600;
    transition: all 0.3s ease;
}

.location-pin svg,
.location-pin i {
    color: #007bff;
}

.slide:hover .location-pin {
    background: rgba(255, 255, 255, 0.95);
    transform: translateY(2px);
}

/* Discount badge */
.discount-badge {
  position: absolute;
  top: 7px;
  right: -7px; /* from right to left */
  background: linear-gradient(to right, #2193b0, #6dd5ed);
  color: white;
  padding: 6px 15px;
  font-size: 0.68rem;
  font-weight: bold;
  z-index: 10;
  clip-path: polygon(20% 0%, 100% 0%, 100% 100%, 20% 100%, 0% 50%); /* adjusted for more pronounced slant */
  box-shadow: 0 2px 4px rgba(0,0,0,0.15);
}

/* Slide content */
.content {
    padding: 12px;
    text-align: left;
}

.content h3 {
    font-size: 1rem;
    color: #1a2b4e;
    margin: 0 0 6px;
    font-weight: 700;
    transition: color 0.3s ease;
    line-height: 1.2;
}

.slide:hover .content h3 {
    color: #007bff;
}

/* Rating */
.rating {
    display: flex;
    align-items: center;
    margin-bottom: 8px;
}

.rating i,
.rating svg {
    color: #f9bc00;
    font-size: 0.8rem;
    margin-right: 2px;
}

.rating span {
    font-size: 0.8rem;
    color: #667895;
    margin-left: 4px;
    font-weight: 500;
}

/* Features */
.features {
    display: flex;
    gap: 10px;
    margin-bottom: 8px;
    flex-wrap: wrap;
}

.feature {
    display: flex;
    align-items: center;
    gap: 3px;
    font-size: 0.75rem;
    color: #667895;
}

.feature i,
.feature svg {
    color: #007bff;
    font-size: 10px;
}

/* Price section */
.price-section {
    display: flex;
    align-items: baseline;
    margin-bottom: 10px;
}

.price-section .current-price {
  font-size: 1.1rem;
  font-weight: 800;
  background: linear-gradient(90deg, #00b4db, #0083b0); /* biru gradasi */
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}


.price-section .original-price {
    font-size: 0.85rem;
    color: #ff5a5f;
    text-decoration: line-through;
    margin-left: 6px;
    font-weight: 500;
}

.price-section .per-night {
    font-size: 0.8rem;
    color: #667895;
    margin-left: 3px;
    font-weight: 400;
}

/* Detail button */
.detail-button {
    background: linear-gradient(to right, #007bff, #0075ea);
    color: white;
    border: none;
    border-radius: 8px;
    padding: 8px;
    width: 100%;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    box-shadow: 0 2px 8px rgba(0, 123, 255, 0.1);
}

.detail-button::before {
    content: "";
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: all 0.6s ease;
}

.detail-button:hover {
    background: linear-gradient(to right, #0056b3, #0064c7);
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0, 123, 255, 0.15);
}

.detail-button:hover::before {
    left: 100%;
}

/* Navigation buttons */
.nav-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255, 255, 255, 0.8);
    color: #1a2b4e;
    border: none;
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    cursor: pointer;
    border-radius: 50%;
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    z-index: 10;
    opacity: 0.9;
}

.nav-btn:hover {
    background: rgba(255, 255, 255, 0.95);
    color: #007bff;
    transform: translateY(-50%) scale(1.1);
    box-shadow: 0 3px 12px rgba(0, 123, 255, 0.1);
    opacity: 1;
}

.nav-btn:active {
    transform: translateY(-50%) scale(0.95);
}

.left-btn {
    left: 5px;
}

.right-btn {
    right: 5px;
}

/* Dots navigation */
.dots-container {
    display: flex;
    justify-content: center;
    margin-top: 15px;
    gap: 5px;
}

.dot {
    width: 6px;
    height: 6px;
    background: rgba(25, 43, 78, 0.1);
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
}

.dot::before {
    content: "";
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    border-radius: 50%;
    border: 1px solid rgba(0, 123, 255, 0);
    transition: all 0.3s ease;
}

.dot:hover {
    background: rgba(0, 123, 255, 0.5);
    transform: scale(1.15);
}

.dot.active {
    background: #007bff;
    transform: scale(1.2);
}

.dot.active::before {
    border-color: rgba(0, 123, 255, 0.3);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .slider-section {
        padding: 25px 0;
        width: 90%;
    }
    
    .slider-section h2 {
        font-size: 1.3rem;
    }
    
    .slider-section p {
        font-size: 0.85rem;
        margin-bottom: 20px;
    }
    
    .slider-container {
        padding: 10px 30px;
    }
    
    .slide {
        flex: 0 0 180px;
    }
    
    .slide-image, .slide-image-container {
        height: 120px;
    }
    
    .nav-btn {
        width: 30px;
        height: 30px;
        font-size: 0.9rem;
    }
}

@media (max-width: 576px) {
    .slider-section h2 {
        font-size: 1.1rem;
    }
    
    .slider {
        gap: 10px;
    }
    
    .slide {
        flex: 0 0 150px;
    }
    
    .slide-image, .slide-image-container {
        height: 100px;
    }
    
    .content {
        padding: 10px;
    }
    
    .content h3 {
        font-size: 0.9rem;
    }
    
    .price-section .current-price {
        font-size: 1rem;
    }
    
    .detail-button {
        padding: 6px;
        font-size: 0.8rem;
    }
    
    .nav-btn {
        width: 25px;
        height: 25px;
        font-size: 0.8rem;
    }
    
    .location-pin, .discount-badge {
        padding: 3px 6px;
        font-size: 0.7rem;
    }
}

/* Animation for slides */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.slide {
    animation: slideIn 0.6s ease forwards;
}

.slide:nth-child(1) { animation-delay: 0.1s; }
.slide:nth-child(2) { animation-delay: 0.2s; }
.slide:nth-child(3) { animation-delay: 0.3s; }
.slide:nth-child(4) { animation-delay: 0.4s; }
.slide:nth-child(5) { animation-delay: 0.5s; }

.slide:hover {
    z-index: 5;
}

=======================/*destination section*/============================


.destinations-section {
            padding: 40px 0;
            background-color: #fff;
        }

        .destination-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .destinations-header {
            margin-bottom: 30px;
        }

        .destinations-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #333;
            margin: 0;
        }

        .destination-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .destination-card {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.3s ease;
            height: 180px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .destination-card:hover {
            transform: scale(1.05);
        }

        .destination-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 12px;
        }

        .card-content {
            position: absolute;
            bottom: 15px;
            left: 15px;
            color: white;
            text-align: left;
        }

        .card-content h3 {
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0;
            text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.6);
        }

        .card-content p {
            font-size: 0.85rem;
            margin: 5px 0 0;
            text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.6);
        }

        @media (max-width: 991px) {
            .destination-grid {
                grid-template-columns: repeat(3, 1fr);
            }
            .destination-card {
                height: 160px;
            }
        }

        @media (max-width: 768px) {
            .destination-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .destination-card {
                height: 140px;
            }
            .destinations-header h2 {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .destination-grid {
                grid-template-columns: 1fr;
            }
            .destination-card {
                height: 180px;
            }
            .destinations-header h2 {
                font-size: 1.3rem;
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
                        <?php
                        if (!$isLoggedIn) {
                            echo '
                            <li class="nav-item">
                                <a class="nav-link register-btn" href="./registration.php">Register Now</a>
                            </li>';
                        } else {
                            echo '
                            <li class="nav-item">
                                <a class="nav-link" href="./services/_logout.php">Logout</a>
                            </li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h1>Travel With JDAR<br>Travel Like Never Before</h1>
        </div>
    </section>

    <main>
        <section class="search-section">
            <div class="search-bar">
                <form method="post" id="search_form">
                    <div class="search-group">
                        <div class="location-input">
                            <label for="destination">
                                <i class="fas fa-map-marker-alt"></i> Destinasi Pilihan
                            </label>
                            <select id="destination" name="destination" required>
                                <option value="">Pilih Destinasi</option>
                                <?php
                                foreach (array_keys($destinations) as $dest) {
                                    echo "<option value='" . htmlspecialchars($dest) . "'>" . htmlspecialchars($dest) . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="date-input">
                            <label for="checkin">
                                <i class="fas fa-calendar-alt"></i> Check - in
                            </label>
                            <input type="date" id="checkin" name="checkin" required>
                        </div>

                        <div class="date-input">
                            <label for="checkout">
                                <i class="fas fa-calendar-alt"></i> Check-out
                            </label>
                            <input type="date" id="checkout" name="checkout" required>
                        </div>

                        <div class="accommodation-input">
                            <label for="accommodation">
                                <i class="fas fa-hotel"></i>Akomodasi
                            </label>
                            <select id="accommodation" name="accommodation" required>
                                <option value="">Pilih Tipe</option>
                                <option value="1">Dengan Hotel</option>
                                <option value="0">Tanpa Hotel</option>
                            </select>
                        </div>

                        <div class="budget-input">
                            <label for="budget">
                                <i class="fas fa-money-bill-wave"></i> Anggaran
                            </label>
                            <select id="budget" name="budget" required>
                                <option value="">Pilih Anggaran</option>
                                <option value="2000000">Di Bawah 2 Juta</option>
                                <option value="5000000">2-5 Juta</option>
                                <option value="10000000">5-10 Juta</option>
                                <option value="30000000">Di Atas 10 Juta</option>
                            </select>
                        </div>

                        <div class="facilities-input">
                            <label>
                                <i class="fas fa-concierge-bell"></i> Fasilitas
                            </label>
                            <div class="checkbox-group">
                                <div class="checkbox-item">
                                    <input type="checkbox" id="transport" name="facilities[]" value="transport">
                                    <label for="transport">
                                        <i class="fas fa-car"></i> Transportasi
                                    </label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="food" name="facilities[]" value="food">
                                    <label for="food">
                                        <i class="fas fa-utensils"></i> Makanan
                                    </label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="guide" name="facilities[]" value="guide">
                                    <label for="guide">
                                        <i class="fas fa-user-tie"></i> Panduan
                                    </label>
                                </div>
                            </div>
                        </div>

                        <button type="submit" id="search_button">
                            <i class="fas fa-search"></i> Cari Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <section class="coupon-section">
            <?php include "./auth/components/_coupons.php"; ?>
        </section>

        <section id="search-results" class="search-results container">
            <div class="results-notification"></div>
        </section>

        <section class="epic-offers-section">
            <div class="epic-offers-container">
                <div class="epic-offers-header">
                    <i class="fas fa-star"></i>
                    <h2>Penawaran EPIC untukmu</h2>
                    <a href="listing.php">Lihat lebih banyak ></a>
                </div>
                <div class="epic-offers-tabs">
                    <button class="active"><i class="fas fa-hotel"></i> Hotel</button>
                    <button><i class="fas fa-plane"></i> Tiket Pesawat</button>
                    <button><i class="fas fa-ticket-alt"></i> Xperience</button>
                </div>
            </div>
        </section>

        <section class="slider-section">
            <div class="slider-container">
                <button class="nav-btn left-btn">‹</button>
                <div class="slider">
                    <?php
                    if (!empty($exclusivePackages)) {
                        foreach ($exclusivePackages as $pkg) {
                            $description = substr($pkg['package_desc'] ?? '', 0, 50) . '...';
                            $image = !empty($pkg['master_image']) ? htmlspecialchars($pkg['master_image']) : './assets/default-image.jpg';
                            $location = htmlspecialchars($pkg['package_location'] ?? 'Unknown Location');
                            $originalPrice = isset($pkg['package_price']) ? (float)$pkg['package_price'] : 0;
                            $discountedPrice = isset($pkg['discounted_price']) ? (float)$pkg['discounted_price'] : $originalPrice;
                            $discountPercentage = isset($pkg['discount_percentage']) ? (float)$pkg['discount_percentage'] : 0;
                            $rating = isset($pkg['package_rating']) ? (float)$pkg['package_rating'] : 0;
                            $reviewCount = $reviewCounts[$pkg['package_id']] ?? 0;

                            $fullStars = floor($rating);
                            $halfStar = ($rating - $fullStars) >= 0.5 ? 1 : 0;
                            $emptyStars = 5 - $fullStars - $halfStar;

                            echo '
                            <article class="slide">
                                <img src="' . $image . '" alt="' . htmlspecialchars($pkg['package_name'] ?? 'Paket') . '" class="slide-image" loading="lazy" />
                                <div class="location-pin"><i class="fas fa-map-marker-alt"></i> ' . $location . '</div>';
                                if ($discountPercentage > 0) {
                                    echo '<div class="discount-badge">Hemat ' . number_format($discountPercentage, 0) . '%</div>';
                                } else {
                                    echo '<div class="discount-badge" style="background: #27ae60;">No Discount</div>';
                                }
                                echo '
                                <div class="content">
                                    <h3>' . htmlspecialchars($pkg['package_name'] ?? 'Nama Paket') . '</h3>
                                    <div class="rating">';
                                    for ($i = 0; $i < $fullStars; $i++) {
                                        echo '<i class="fas fa-star"></i>';
                                    }
                                    if ($halfStar) {
                                        echo '<i class="fas fa-star-half-alt"></i>';
                                    }
                                    for ($i = 0; $i < $emptyStars; $i++) {
                                        echo '<i class="far fa-star"></i>';
                                    }
                                    echo '
                                        <span>' . number_format($rating, 1) . '/5.0</span>
                                        <span class="review-count">(' . $reviewCount . ')</span>
                                    </div>
                                    <div class="price-section">';
                                    if ($discountPercentage > 0) {
                                        echo '
                                        <span class="current-price">Rp' . number_format($discountedPrice, 0, ',', '.') . '</span>
                                        <span class="original-price">Rp' . number_format($originalPrice, 0, ',', '.') . '</span>';
                                    } else {
                                        echo '<span class="current-price">Rp' . number_format($originalPrice, 0, ',', '.') . '</span>';
                                    }
                                    echo '
                                    </div>
                                    <a href="package.php?id=' . ($pkg['package_id'] ?? 0) . '">
                                        <button class="detail-button">Lihat Detail</button>
                                    </a>
                                </div>
                            </article>';
                        }
                    } else {
                        echo '<p>Tidak ada paket eksklusif yang tersedia saat ini.</p>';
                    }
                    ?>
                </div>
                <button class="nav-btn right-btn">›</button>
            </div>
            <div class="dots-container"></div>
        </section>

        <section class="destinations-section">
            <div class="destination-container">
                <div class="destinations-header">
                    <h2>Temukan Kembali Diri Anda di Asia dan Sekitarnya</h2>
                </div>
                <div class="destination-grid">
                    <a href="listing.php?loc=Tangerang" style="text-decoration: none; color: inherit;">
                        <article class="destination-card">
                            <img src="https://pix8.agoda.net/hotelImages/3496/-1/53d2df16c0fee833223bed3d4869c82a.jpg?ca=9&ce=1&s=1024x" alt="Tangerang" loading="lazy">
                            <div class="card-content">
                                <h3>Tangerang</h3>
                                <p><?php echo $destinations['Tangerang'] ?? 0; ?> akomodasi</p>
                            </div>
                        </article>
                    </a>
                    <a href="listing.php?loc=Jawa Timur" style="text-decoration: none; color: inherit;">
                        <article class="destination-card">
                            <img src="https://media.istockphoto.com/id/177800254/id/foto/gunung-berapi-gunung-bromo-jawa-timur-surabuya-indonesia.webp?s=1024x1024&w=is&k=20&c=ATqyFJZ3ZIDFYq2kOOzx4DainHBPJ1XPgeOELpLA49Y=" alt="Jawa Timur" loading="lazy">
                            <div class="card-content">
                                <h3>Jawa Timur</h3>
                                <p><?php echo $destinations['Jawa Timur'] ?? 0; ?> akomodasi</p>
                            </div>
                        </article>
                    </a>
                    <a href="listing.php?loc=Bali" style="text-decoration: none; color: inherit;">
                        <article class="destination-card">
                            <img src="https://media.istockphoto.com/id/675172642/id/foto/pura-ulun-danu-bratan-temple-in-bali.webp?s=2048x2048&w=is&k=20&c=-l_yyplroyFc74ADDhHNx8g6_t5JZTho4cJrw7qfA7A=" alt="Bali" loading="lazy">
                            <div class="card-content">
                                <h3>Bali</h3>
                                <p><?php echo $destinations['Bali'] ?? 0; ?> akomodasi</p>
                            </div>
                        </article>
                    </a>
                    <a href="listing.php?loc=NTB" style="text-decoration: none; color: inherit;">
                        <article class="destination-card">
                            <img src="https://media.istockphoto.com/id/1269615561/id/foto/titik-sinar-manta-surga-tropis-nusa-penida-flores-labuan-bajo-underwater.webp?s=2048x2048&w=is&k=20&c=ZlEFlhQYVgjsk4OP1bFZcuGiH3LxpO5vSCSRwYHfRVQ=" alt="NTB" loading="lazy">
                            <div class="card-content">
                                <h3>NTB</h3>
                                <p><?php echo $destinations['NTB'] ?? 0; ?> akomodasi</p>
                            </div>
                        </article>
                    </a>
                    <a href="listing.php?loc=Jakarta" style="text-decoration: none; color: inherit;">
                        <article class="destination-card">
                            <img src="https://media.istockphoto.com/id/500798563/id/foto/city-skyline-at-sunset-jakarta-indonesia.webp?s=2048x2048&w=is&k=20&c=mL4oWUEqLj7CTt5WmETwWlPOe5QPeYmgH0Tj35V_tvc=" alt="Jakarta" loading="lazy">
                            <div class="card-content">
                                <h3>Jakarta</h3>
                                <p><?php echo $destinations['Jakarta'] ?? 0; ?> akomodasi</p>
                            </div>
                        </article>
                    </a>
                    <a href="listing.php?loc=Yogyakarta" style="text-decoration: none; color: inherit;">
                        <article class="destination-card">
                            <img src="https://media.istockphoto.com/id/2150759945/id/foto/woman-traveler-at-prambanan-temple-near-yogyakarta-city-central-java-indonesia.webp?s=2048x2048&w=is&k=20&c=397yDgRJBlHqv2PSicaZs3-Fb9CgyA41M8kBzbXXCHE=" alt="Yogyakarta" loading="lazy">
                            <div class="card-content">
                                <h3>Yogyakarta</h3>
                                <p><?php echo $destinations['Yogyakarta'] ?? 0; ?> akomodasi</p>
                            </div>
                        </article>
                    </a>
                    <a href="listing.php?loc=Jawa Tengah" style="text-decoration: none; color: inherit;">
                        <article class="destination-card">
                            <img src="https://images.pexels.com/photos/12400536/pexels-photo-12400536.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2" alt="Jawa Tengah" loading="lazy">
                            <div class="card-content">
                                <h3>Jawa Tengah</h3>
                                <p><?php echo $destinations['Jawa Tengah'] ?? 0; ?> akomodasi</p>
                            </div>
                        </article>
                    </a>
                    <a href="listing.php?loc=Lombok" style="text-decoration: none; color: inherit;">
                        <article class="destination-card">
                            <img src="https://media.istockphoto.com/id/2098338879/id/foto/woman-standing-on-viewpoint-after-hike-near-mount-rinjani-on-lombok.webp?s=2048x2048&w=is&k=20&c=Fw1Q6A_DTkXpWe0mownVGmTx5tmSaeEReQZmhG2UYOs=" alt="Lombok" loading="lazy">
                            <div class="card-content">
                                <h3>Lombok</h3>
                                <p><?php echo $destinations['Lombok'] ?? 0; ?> akomodasi</p>
                            </div>
                        </article>
                    </a>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <?php include "./components/_footer.php" ?>
    </footer>

    <div id="popup-notification" class="popup-notification"></div>

    <script>
       /* ========= Slider Section =========
   Handles the functionality of an improved carousel slider with enhanced navigation,
   smoother transitions, auto-play, better drag/swipe handling, dynamic dots, and ensures
   full slide visibility at the edges without cropping.
*/
const slides = document.querySelectorAll(".slide");
const slider = document.querySelector(".slider");
const leftBtn = document.querySelector(".left-btn");
const rightBtn = document.querySelector(".right-btn");
const dotsContainer = document.querySelector(".dots-container");

let activeIndex = 0;
let isDragging = false;
let startX = 0;
let currentTranslate = 0;
let previousTranslate = 0;
let autoPlayInterval;

function updateSlides() {
    if (!slides.length) {
        console.log("Tidak ada slide yang tersedia.");
        if (leftBtn) leftBtn.style.display = 'none';
        if (rightBtn) rightBtn.style.display = 'none';
        dotsContainer.style.display = 'none';
        slider.innerHTML = '<p>Tidak ada paket eksklusif yang tersedia saat ini.</p>';
        return;
    }

    if (leftBtn) leftBtn.style.display = 'block';
    if (rightBtn) rightBtn.style.display = 'block';
    dotsContainer.style.display = 'flex';

    slides.forEach((slide, index) => {
        slide.classList.toggle("active", index === activeIndex);
    });

    const slideWidth = slides[0].offsetWidth;
    const translateX = -activeIndex * slideWidth; // Exact slide width movement
    const containerWidth = slider.parentElement.clientWidth;
    const maxTranslate = 0;
    const minTranslate = -(slideWidth * (slides.length - 1));

    // Ensure the slide stays within bounds
    const boundedTranslate = Math.max(minTranslate, Math.min(maxTranslate, translateX));

    slider.style.transition = isDragging ? "none" : "transform 0.5s ease-in-out";
    slider.style.transform = `translateX(${boundedTranslate}px)`;
    previousTranslate = boundedTranslate;
    updateDots();
}

function createDots() {
    if (!slides.length) return;
    dotsContainer.innerHTML = ''; // Clear existing dots
    for (let index = 0; index < slides.length; index++) {
        const dot = document.createElement("div");
        dot.classList.add("dot");
        if (index === activeIndex) dot.classList.add("active");
        dot.addEventListener("click", () => {
            activeIndex = index;
            updateSlides();
            startAutoPlay();
        });
        dotsContainer.appendChild(dot);
    }
}

function updateDots() {
    if (!slides.length) return;
    const dots = document.querySelectorAll(".dot");
    dots.forEach((dot, index) => {
        dot.classList.toggle("active", index === activeIndex);
    });
}

function startAutoPlay() {
    stopAutoPlay();
    autoPlayInterval = setInterval(() => {
        activeIndex = (activeIndex + 1) % slides.length;
        updateSlides();
    }, 5000);
}

function stopAutoPlay() {
    clearInterval(autoPlayInterval);
}

if (rightBtn && slides.length) {
    rightBtn.addEventListener("click", (e) => {
        e.preventDefault();
        activeIndex = Math.min(activeIndex + 1, slides.length - 1);
        updateSlides();
        startAutoPlay();
    });
}

if (leftBtn && slides.length) {
    leftBtn.addEventListener("click", (e) => {
        e.preventDefault();
        activeIndex = Math.max(activeIndex - 1, 0);
        updateSlides();
        startAutoPlay();
    });
}

if (slider && slides.length) {
    slider.addEventListener("mousedown", (e) => {
        e.stopPropagation();
        isDragging = true;
        startX = e.clientX;
        stopAutoPlay();
        slider.style.transition = "none";
    });

    window.addEventListener("mousemove", (e) => {
        if (isDragging) {
            const currentX = e.clientX;
            const moveDistance = currentX - startX;
            currentTranslate = previousTranslate + moveDistance;
            slider.style.transform = `translateX(${currentTranslate}px)`;
        }
    });

    window.addEventListener("mouseup", () => {
        if (isDragging) {
            isDragging = false;
            const movedBy = currentTranslate - previousTranslate;
            const slideWidth = slides[0].offsetWidth;
            if (Math.abs(movedBy) > slideWidth / 3) {
                activeIndex = Math.max(0, Math.min(slides.length - 1, activeIndex - Math.round(movedBy / slideWidth)));
            }
            updateSlides();
            startAutoPlay();
        }
    });

    slider.addEventListener("touchstart", (e) => {
        e.stopPropagation();
        isDragging = true;
        startX = e.touches[0].clientX;
        stopAutoPlay();
        slider.style.transition = "none";
    });

    slider.addEventListener("touchmove", (e) => {
        if (isDragging) {
            const currentX = e.touches[0].clientX;
            const moveDistance = currentX - startX;
            currentTranslate = previousTranslate + moveDistance;
            slider.style.transform = `translateX(${currentTranslate}px)`;
        }
    });

    slider.addEventListener("touchend", () => {
        if (isDragging) {
            isDragging = false;
            const movedBy = currentTranslate - previousTranslate;
            const slideWidth = slides[0].offsetWidth;
            if (Math.abs(movedBy) > slideWidth / 3) {
                activeIndex = Math.max(0, Math.min(slides.length - 1, activeIndex - Math.round(movedBy / slideWidth)));
            }
            updateSlides();
            startAutoPlay();
        }
    });

    slider.addEventListener("touchcancel", () => {
        isDragging = false;
        updateSlides();
        startAutoPlay();
    });
}

createDots();
updateSlides();
startAutoPlay();

slider.addEventListener("mouseenter", stopAutoPlay);
slider.addEventListener("mouseleave", startAutoPlay);

window.addEventListener("resize", () => {
    updateSlides();
});

        createDots();
        updateSlides();
        window.addEventListener("resize", updateSlides);

        document.addEventListener('DOMContentLoaded', function () {
            const navbarToggler = document.querySelector('.navbar-toggler');
            const navbarCollapse = document.querySelector('#navbarNav');
            const navbar = document.querySelector('.navbar');
            let lastScrollTop = 0;

            if (navbarCollapse && navbarToggler) {
                navbarCollapse.querySelectorAll('.nav-link').forEach(link => {
                    link.addEventListener('click', () => {
                        if (navbarCollapse.classList.contains('show')) {
                            navbarToggler.click();
                        }
                    });
                });

                window.addEventListener('resize', () => {
                    if (window.innerWidth >= 991 && navbarCollapse.classList.contains('show')) {
                        navbarToggler.click();
                    }
                });
            }

            if (navbar) {
                window.addEventListener('scroll', () => {
                    let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                    if (scrollTop > lastScrollTop && scrollTop > 80) {
                        navbar.classList.add('hidden');
                    } else {
                        navbar.classList.remove('hidden');
                    }
                    lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
                });
            }

            const copyButtons = document.querySelectorAll('.copy-btn');
            copyButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const code = button.previousElementSibling.textContent;
                    navigator.clipboard.writeText(code).then(() => {
                        alert('Kode kupon ' + code + ' telah disalin!');
                    });
                });
            });

            const tabButtons = document.querySelectorAll('.epic-offers-tabs button');
            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');
                });
            });
        });

       /* ========= Search Section =========
   Handles the search form submission, fetches package data from the server,
   and displays results or error messages with notifications. Modifies layout
   to display two available packages side by side (horizontally) instead of vertically.
*/
const searchForm = document.getElementById('search_form');
if (searchForm) {
    searchForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        const searchResults = document.getElementById('search-results');
        const resultsNotification = document.querySelector('.results-notification');
        const popupNotification = document.getElementById('popup-notification');

        if (!searchResults || !resultsNotification || !popupNotification) {
            console.error("Search elements not found.");
            return;
        }

        fetch('services/search_packages.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Gagal mencari paket');
            }
            return response.json();
        })
        .then(data => {
            searchResults.innerHTML = '';
            searchResults.appendChild(resultsNotification);

            if (data && Array.isArray(data) && data.length > 0) {
                resultsNotification.textContent = 'Berikut adalah package yang tersedia';
                resultsNotification.classList.remove('unavailable');
                resultsNotification.classList.add('available');
                resultsNotification.style.display = 'flex';

                popupNotification.textContent = 'Package Tersedia';
                popupNotification.classList.remove('unavailable');
                popupNotification.classList.add('available', 'show');

                searchResults.style.display = 'grid';
                searchResults.style.gridTemplateColumns = '1fr 1fr'; // Two columns for horizontal layout
                searchResults.style.gap = '20px'; // Space between cards

                data.forEach(pkg => {
                    const card = document.createElement('div');
                    card.className = 'package-card';
                    const image = pkg.master_image || './assets/default-image.jpg';
                    const priceToShow = pkg.discount_percentage > 0 ? pkg.discounted_price : pkg.package_price;
                    card.innerHTML = `
                        <div class="package-card-image">
                            <img src="${image}" alt="${pkg.package_name || 'Paket'}" loading="lazy">
                            <span class="stock">Stock: ${pkg.package_capacity - (pkg.package_booked ?? 0)}</span>
                        </div>
                        <div class="package-card-content">
                            <h3 class="package-title">${pkg.package_name || 'Nama Paket'}</h3>
                            <p class="package-price">Harga: Rp${Number(priceToShow || 0).toLocaleString('id-ID')}</p>
                            <p class="package-location"><i class="fas fa-map-marker-alt"></i> ${pkg.package_location || 'Tidak diketahui'}</p>
                            <p class="package-date"><i class="fas fa-calendar-alt"></i> ${pkg.package_start || 'N/A'} - ${pkg.package_end || 'N/A'}</p>
                            <a href="package.php?id=${pkg.package_id || 0}" class="package-btn">Lihat Detail</a>
                        </div>
                    `;
                    searchResults.appendChild(card);
                });
            } else {
                resultsNotification.textContent = 'Package Tidak Tersedia';
                resultsNotification.classList.remove('available');
                resultsNotification.classList.add('unavailable');
                resultsNotification.style.display = 'flex';

                popupNotification.textContent = 'Package Tidak Tersedia';
                popupNotification.classList.remove('available');
                popupNotification.classList.add('unavailable', 'show');

                searchResults.innerHTML = '<p class="no-results">Tidak ada paket yang ditemukan.</p>';
                searchResults.appendChild(resultsNotification);
                searchResults.style.display = 'block';
            }

            setTimeout(() => {
                resultsNotification.style.display = 'none';
                popupNotification.classList.remove('show');
            }, 500);
        })
        .catch(error => {
            console.error('Error fetching packages:', error);
            resultsNotification.textContent = 'Terjadi Kesalahan';
            resultsNotification.classList.remove('available');
            resultsNotification.classList.add('unavailable');
            resultsNotification.style.display = 'flex';

            popupNotification.textContent = 'Terjadi Kesalahan';
            popupNotification.classList.remove('available');
            popupNotification.classList.add('unavailable', 'show');

            searchResults.innerHTML = '<p class="error">Terjadi kesalahan saat mencari paket. Silakan coba lagi nanti.</p>';
            searchResults.appendChild(resultsNotification);
            searchResults.style.display = 'block';

            setTimeout(() => {
                resultsNotification.style.display = 'none';
                popupNotification.classList.remove('show');
            }, 500);
                });
            });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
</body>
</html>