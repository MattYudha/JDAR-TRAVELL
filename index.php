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
    $result = $packagesObj->getPackages('All', 0, 6);
    $exclusivePackages = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

    $destinations = [
        'Tangerang' => $packagesObj->getPackagesWithQueryCount('Tangerang'),
        'Jawa Tengah' => $packagesObj->getPackagesWithQueryCount('Jawa Tengah'),
        'Bali' => $packagesObj->getPackagesWithQueryCount('Bali'),
        'Nusa Tenggara Barat' => $packagesObj->getPackagesWithQueryCount('Nusa Tenggara Barat'),
        'Jakarta' => $packagesObj->getPackagesWithQueryCount('Jakarta'),
        'Jawa Timur' => $packagesObj->getPackagesWithQueryCount('Tangerang'),
        'Yogyakarya' => $packagesObj->getPackagesWithQueryCount('Yogyakarta'),
        'Lombok' => $packagesObj->getPackagesWithQueryCount('Lombok'),
    ];
} catch (Exception $e) {
    $exclusivePackages = [];
    $destinations = [
        'Tangerang' => 0,
        'Jawa Tengah' => 0,
        'Bali' => 0,
        'Nusa Tenggara Barat' => 0,
        'Jakarta' => 0,
        'Yogyakarta' => 0,
       
    ];
    echo '<p class="error">Terjadi kesalahan: ' . htmlspecialchars($e->getMessage()) . '</p>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "./components/_head.php" ?>
    <title>Triptrip - Explore Your Dream Destinations</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2Lw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>JDAR Travel</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        /* Navbar Styling (Diselaraskan dengan listing.php dan package.php) */
        .navbar {
            background-color: #fff;
            padding: 15px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            position: fixed;
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

        /* Body padding untuk navbar fixed */
        body {
            padding-top: 90px;
        }

        /* Hero Section (Tidak diubah) */
        .hero {
            background: url('assets/bg.png') no-repeat center center/cover;
            height: 60vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            margin-top: 70px;
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

        /* Responsivitas (Hanya untuk navbar, diselaraskan) */
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

        /* Animasi Keyframes (Tidak diubah) */
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <!-- Header/Navbar (Diselaraskan dengan listing.php dan package.php) -->
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

    <!-- Bagian lain tetap sama persis seperti kode asli Anda -->
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

        <section id="search-results" class="search-results container">
            <div class="results-notification"></div>
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
                            echo '
                            <article class="slide">
                                <img src="' . $image . '" alt="' . htmlspecialchars($pkg['package_name'] ?? 'Paket') . '" class="slide-image" loading="lazy" />
                                <div class="content">
                                    <h2>Rp' . number_format($pkg['package_price'] ?? 0, 0, ',', '.') . '</h2>
                                    <h3>' . htmlspecialchars($pkg['package_name'] ?? 'Nama Paket') . '</h3>
                                    <p>' . htmlspecialchars($description) . '</p>
                                    <a href="package.php?id=' . ($pkg['package_id'] ?? 0) . '">
                                        <button>Lihat Detail</button>
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

        <section class="container destinations-section">
            <div class="destination-container">
                <div class="destination-grid">
                    <article class="destination-card wide-card">
                        <img src="https://media.istockphoto.com/id/2074779517/id/foto/young-couple-tourism-enjoying-the-tropical-pink-sandy-beach-with-clear-turquoise-water-at.webp?s=2048x2048&w=is&k=20&c=UEJy5VjIfMDKSyihOvs9MrtihHtAKonprP4uw2PBaSo=" alt="Tangerang" loading="lazy">
                        <div class="card-content">
                            <h3>Tangerang</h3>
                            <p><?php echo $destinations['Tangerang'] ?? 0; ?> Tours Available</p>
                        </div>
                    </article>
                    <article class="destination-card">
                        <img src="https://media.istockphoto.com/id/2074779517/id/foto/young-couple-tourism-enjoying-the-tropical-pink-sandy-beach-with-clear-turquoise-water-at.webp?s=2048x2048&w=is&k=20&c=UEJy5VjIfMDKSyihOvs9MrtihHtAKonprP4uw2PBaSo=" alt="Jawa Timur" loading="lazy">
                        <div class="card-content">
                            <h3>Jawa Timur</h3>
                            <p><?php echo $destinations['Jawa Timur'] ?? 0; ?> Tours Available</p>
                        </div>
                    </article>
                    <article class="destination-card">
                        <img src="https://media.istockphoto.com/id/1146250806/id/foto/matahari-terbenam-di-pantai-di-koh-chang.webp?s=2048x2048&w=is&k=20&c=9BY4LrSIyhr4AwsJkFSOCmZtC7T7rGpJoQhnN68a1pE=" alt="Jepang" loading="lazy">
                        <div class="card-content">
                            <h3>Bali</h3>
                            <p><?php echo $destinations['Bali'] ?? 0; ?> Tours Available</p>
                        </div>
                    </article>
                    <article class="destination-card">
                        <img src="https://media.istockphoto.com/id/998794190/id/foto/wanita-muda-bersantai-di-kolam-renang-saat-matahari-terbenam-di-atas-kuala-lumpur.webp?s=2048x2048&w=is&k=20&c=IdGGaSnKAzZjQj-4LZ8z9YFZ4aJYjMu9jQ4sKG0czow=" alt="America & Canada" loading="lazy">
                        <div class="card-content">
                            <h3>Nusa Tenggara Barat</h3>
                            <p><?php echo $destinations['Nusa Tenggara Barat'] ?? 0; ?> Tours Available</p>
                        </div>
                    </article>
                    <article class="destination-card">
                        <img src="https://media.istockphoto.com/id/2074779517/id/foto/young-couple-tourism-enjoying-the-tropical-pink-sandy-beach-with-clear-turquoise-water-at.webp?s=2048x2048&w=is&k=20&c=UEJy5VjIfMDKSyihOvs9MrtihHtAKonprP4uw2PBaSo=" alt="Jakarta" loading="lazy">
                        <div class="card-content">
                            <h3>Jakarta</h3>
                            <p><?php echo $destinations['Jakarta'] ?? 0; ?> Tours Available</p>
                        </div>
                    </article>
                    <article class="destination-card">
                        <img src="https://media.istockphoto.com/id/2074779517/id/foto/young-couple-tourism-enjoying-the-tropical-pink-sandy-beach-with-clear-turquoise-water-at.webp?s=2048x2048&w=is&k=20&c=UEJy5VjIfMDKSyihOvs9MrtihHtAKonprP4uw2PBaSo=" alt="Yogyakarta" loading="lazy">
                        <div class="card-content">
                            <h3>Yogyakarta</h3>
                            <p><?php echo $destinations['Yogyakarta'] ?? 0; ?> Tours Available</p>
                        </div>
                    </article>
                    <article class="destination-card">
                        <img src="https://media.istockphoto.com/id/2074779517/id/foto/young-couple-tourism-enjoying-the-tropical-pink-sandy-beach-with-clear-turquoise-water-at.webp?s=2048x2048&w=is&k=20&c=UEJy5VjIfMDKSyihOvs9MrtihHtAKonprP4uw2PBaSo=" alt="Lombok" loading="lazy">
                        <div class="card-content">
                            <h3>Lombok</h3>
                            <p><?php echo $destinations['Lombok'] ?? 0; ?> Tours Available</p>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section class="cta">
            <h3>Paket Menarik <br> Untuk Anda Dan Keluarga</h3>
            <p>Kombinasi luar biasa dengan harga yang tak terkalahkan <br> mencakup transportasi, akomodasi, dan makanan secara keseluruhan.</p>
            <a href="listing.php" class="cta-btn">Pesan Sekarang</a>
        </section>
    </main>

    <footer>
        <?php include "./components/_footer.php" ?>
    </footer>

    <div id="popup-notification" class="popup-notification"></div>

    <script>
        // Slider Functionality (Tidak diubah)
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

        function updateSlides() {
            if (!slides.length) {
                console.warn("No slides available.");
                return;
            }

            slides.forEach((slide, index) => {
                slide.classList.toggle("active", index === activeIndex);
            });

            const slideWidth = slides[0].offsetWidth;
            const containerWidth = slider.parentElement.clientWidth;
            const totalSlidesWidth = slideWidth * slides.length;
            const activeSlideCenter = (activeIndex + 0.5) * slideWidth;
            const containerCenter = containerWidth / 2;
            const offset = containerCenter - activeSlideCenter;
            const maxOffset = 0;
            const minOffset = -(totalSlidesWidth - containerWidth);
            const boundedOffset = Math.min(maxOffset, Math.max(minOffset, offset));

            slider.style.transition = "transform 0.5s ease";
            slider.style.transform = `translateX(${boundedOffset}px)`;
            previousTranslate = boundedOffset;
            updateDots();
        }

        function createDots() {
            if (!slides.length) return;
            slides.forEach((_, index) => {
                const dot = document.createElement("div");
                dot.classList.add("dot");
                if (index === activeIndex) dot.classList.add("active");
                dot.addEventListener("click", () => {
                    activeIndex = index;
                    updateSlides();
                });
                dotsContainer.appendChild(dot);
            });
        }

        function updateDots() {
            const dots = document.querySelectorAll(".dot");
            dots.forEach((dot, index) => {
                dot.classList.toggle("active", index === activeIndex);
            });
        }

        if (rightBtn) {
            rightBtn.addEventListener("click", (e) => {
                e.preventDefault();
                activeIndex = Math.min(activeIndex + 1, slides.length - 1);
                updateSlides();
            });
        }

        if (leftBtn) {
            leftBtn.addEventListener("click", (e) => {
                e.preventDefault();
                activeIndex = Math.max(activeIndex - 1, 0);
                updateSlides();
            });
        }

        slider.addEventListener("mousedown", (e) => {
            e.stopPropagation();
            isDragging = true;
            startX = e.clientX;
            slider.style.transition = "none";
        });

        window.addEventListener("mousemove", (e) => {
            if (isDragging) {
                const currentX = e.clientX;
                currentTranslate = previousTranslate + (currentX - startX);
                slider.style.transform = `translateX(${currentTranslate}px)`;
            }
        });

        window.addEventListener("mouseup", () => {
            if (isDragging) {
                isDragging = false;
                const movedBy = currentTranslate - previousTranslate;
                const slideWidth = slides[0].offsetWidth;

                if (movedBy < -slideWidth / 2 && activeIndex < slides.length - 1) {
                    activeIndex++;
                } else if (movedBy > slideWidth / 2 && activeIndex > 0) {
                    activeIndex--;
                }
                updateSlides();
            }
        });

        slider.addEventListener("touchstart", (e) => {
            e.stopPropagation();
            isDragging = true;
            startX = e.touches[0].clientX;
            slider.style.transition = "none";
        });

        slider.addEventListener("touchmove", (e) => {
            if (isDragging) {
                const currentX = e.touches[0].clientX;
                currentTranslate = previousTranslate + (currentX - startX);
                slider.style.transform = `translateX(${currentTranslate}px)`;
            }
        });

        slider.addEventListener("touchend", () => {
            if (isDragging) {
                isDragging = false;
                const movedBy = currentTranslate - previousTranslate;
                const slideWidth = slides[0].offsetWidth;

                if (movedBy < -slideWidth / 2 && activeIndex < slides.length - 1) {
                    activeIndex++;
                } else if (movedBy > slideWidth / 2 && activeIndex > 0) {
                    activeIndex--;
                }
                updateSlides();
            }
        });

        createDots();
        updateSlides();
        window.addEventListener("resize", updateSlides);

        // Navigation Functionality (Diselaraskan dengan listing.php dan package.php)
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
        });

        // Search Form (Tidak diubah)
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

                        data.forEach(pkg => {
                            const card = document.createElement('div');
                            card.className = 'package-card';
                            const image = pkg.master_image || './assets/default-image.jpg';
                            card.innerHTML = `
                                <div class="package-card-image">
                                    <img src="${image}" alt="${pkg.package_name || 'Paket'}" loading="lazy">
                                    <span class="stock">Stock: ${pkg.stock || 'N/A'}</span>
                                </div>
                                <div class="package-card-content">
                                    <h3 class="package-title">${pkg.package_name || 'Nama Paket'}</h3>
                                    <p class="package-price">Harga: Rp${Number(pkg.package_price || 0).toLocaleString('id-ID')}</p>
                                    <p class="package-location"><i class="fas fa-map-marker-alt"></i> ${pkg.package_location || 'Tidak diketahui'}</p>
                                    <p class="package-date"><i class="fas fa-calendar-alt"></i> ${pkg.package_start || 'N/A'} - ${pkg.package_end || 'N/A'}</p>
                                    <a href="package.php?id=${pkg.package_id || 0}" class="package-btn">Lihat Detail</a>
                                </div>
                            `;
                            searchResults.appendChild(card);
                        });
                        searchResults.style.display = 'grid';
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
                    }, 3000);
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
                    }, 3000);
                });
            });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
</body>
</html>