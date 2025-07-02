-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 22, 2025
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `triptip`
--
CREATE DATABASE IF NOT EXISTS `triptip` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `triptip`;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--
CREATE TABLE `users` (
    `id` INT(10) NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50) NULL DEFAULT NULL,
    `user_pass` VARCHAR(255) NULL DEFAULT NULL,
    `email` VARCHAR(100) NULL DEFAULT NULL,
    `date_created` TIMESTAMP NULL DEFAULT NULL,
    `is_admin` INT(10) NULL DEFAULT '0',
    `phone` VARCHAR(15) NULL DEFAULT NULL,
    `address` TEXT NULL DEFAULT NULL,
    `full_name` VARCHAR(255) NULL DEFAULT NULL,
    `account_status` INT(10) NULL DEFAULT '1',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--
CREATE TABLE `packages` (
    `package_id` INT(10) NOT NULL AUTO_INCREMENT,
    `package_name` VARCHAR(255) NULL DEFAULT NULL,
    `package_rating` FLOAT NULL DEFAULT NULL,
    `package_desc` TEXT NULL DEFAULT NULL,
    `package_start` DATE NULL DEFAULT NULL,
    `package_end` DATE NULL DEFAULT NULL,
    `package_price` INT(10) NULL DEFAULT NULL,
    `package_location` VARCHAR(255) NULL DEFAULT NULL,
    `is_hotel` INT(10) NULL DEFAULT '0',
    `is_transport` INT(10) NULL DEFAULT '0',
    `is_food` INT(10) NULL DEFAULT '0',
    `is_guide` INT(10) NULL DEFAULT '0',
    `package_capacity` INT(10) NULL DEFAULT '0',
    `package_booked` INT(10) UNSIGNED NULL DEFAULT '0',
    `map_loc` TEXT NULL DEFAULT NULL,
    `master_image` TEXT NULL DEFAULT NULL,
    `extra_image_1` TEXT NULL DEFAULT NULL,
    `extra_image_2` TEXT NULL DEFAULT NULL,
    `discount_percentage` FLOAT NULL DEFAULT '0',
    PRIMARY KEY (`package_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `packages`
--
INSERT INTO `packages` (`package_id`, `package_name`, `package_rating`, `package_desc`, `package_start`, `package_end`, `package_price`, `package_location`, `is_hotel`, `is_transport`, `is_food`, `is_guide`, `package_capacity`, `package_booked`, `map_loc`, `master_image`, `extra_image_1`, `extra_image_2`, `discount_percentage`) VALUES
(1, 'Paket Tour Bromo', 4.5, 'Gunung Bromo adalah salah satu gunung berapi paling terkenal di Indonesia, terletak di Provinsi Jawa Timur, dalam kawasan Taman Nasional Bromo Tengger Semeru. Dengan ketinggian sekitar 2.329 meter di atas permukaan laut, Gunung Bromo menawarkan pemandangan yang menakjubkan, terutama saat matahari terbit. Banyak wisatawan datang untuk menyaksikan keindahan alam ini, yang dikelilingi oleh lautan pasir seluas sekitar 10 kilometer persegi.', '2024-12-24', '2024-12-30', 5000000, 'Jawa Timur', 0, 1, 0, 1, 10, 0, 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15806.18004244916!2d112.95301219999999!3d-7.94249345!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd637aaab794a41%3A0xada40d36ecd2a5dd!2sMt%20Bromo!5e0!3m2!1sen!2sid!4v1734756452782!5m2!1sen!2sid', 'https://media.istockphoto.com/id/474625072/id/foto/matahari-terbit-di-gunung-berapi-bromo-di-indonesia.webp?s=2048x2048&w=is&k=20&c=GAZ5HTrXYmTQDfmJhJVhuOYAcgXh5L0Sr29AEg1ibVU=', 'https://media.istockphoto.com/id/501598384/id/foto/gunung-berapi-bromo-dengan-kabut-dan-kabut-saat-matahari-terbit.webp?s=2048x2048&w=is&k=20&c=ZEUQ52S2Dl9VI0gNG6k5AgYBEk2OxinSvqvltsGP5D8=', 'https://media.istockphoto.com/id/1452522983/id/foto/bali.webp?s=2048x2048&w=is&k=20&c=Y1PJKFnHp1TQehpRpeWs0JfiDUmCluAEMD-aqe6Ru2Q=', 20),
(2, 'Paket Tour Bali', 5, 'Bali adalah sebuah pulau yang terletak di Indonesia, terkenal sebagai destinasi wisata internasional yang menawarkan keindahan alam, budaya yang kaya, dan keramahan penduduknya. Dikenal sebagai \"Pulau Dewata,\" Bali memiliki pantai-pantai yang menakjubkan, seperti Kuta, Seminyak, dan Nusa Dua, yang menarik pengunjung untuk bersantai dan menikmati aktivitas air.\r\n\r\nSelain pantai, Bali juga terkenal dengan sawah terasering yang hijau, terutama di daerah Ubud, yang menjadi pusat seni dan budaya. Di sini, pengunjung dapat menemukan berbagai galeri seni, pasar tradisional, dan pertunjukan tari yang memukau.', '2024-12-22', '2024-12-22', 2000000, 'Bali', 0, 0, 0, 0, 5, 0, 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d31568.269029832263!2d115.26603944999998!3d-8.49611115!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd23d739f22c9c3%3A0x54a38afd6b773d1c!2sUbud%2C%20Gianyar%20Regency%2C%20Bali!5e0!3m2!1sen!2sid!4v1734756709901!5m2!1sen!2sid', 'https://media.istockphoto.com/id/675172642/id/foto/pura-ulun-danu-bratan-temple-in-bali.webp?s=2048x2048&w=is&k=20&c=-l_yyplroyFc74ADDhHNx8g6_t5JZTho4cJrw7qfA7A=', 'https://media.istockphoto.com/id/1262883486/id/foto/pura-ulun-danu-bratan-temple-in-bali-indonesia.webp?s=2048x2048&w=is&k=20&c=EJ6qIB3K9ecewarXOSeRRzcgWOrcgOl3fKij5S3YgLY=', 'https://media.istockphoto.com/id/953295782/id/foto/pura-ulun-danu-bratan-temple.webp?s=2048x2048&w=is&k=20&c=_wj2xN7kOXlIymiUlQ9_tnbXZq4_IS132zbblTDJyVE=', 0),
(3, 'Paket Tour Jogja', 4, 'Yogyakarta, sering disebut Jogja, adalah sebuah kota yang terletak di pulau Jawa, Indonesia. Dikenal sebagai pusat budaya dan pendidikan, Jogja memiliki pesona yang unik dengan kombinasi tradisi dan modernitas. Kota ini terkenal dengan warisan budayanya yang kaya, termasuk keraton (istana) Sultan Yogyakarta yang megah, di mana pengunjung dapat menyaksikan berbagai pertunjukan seni dan budaya.', '2024-12-22', '2024-12-23', 5000000, 'Yogyakarta', 1, 1, 1, 0, 10, 0, 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d63245.97055414075!2d110.37484495!3d-7.803250449999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a5787bd5b6bc5%3A0x21723fd4d3684f71!2sYogyakarta%2C%20Yogyakarta%20City%2C%20Special%20Region%20of%20Yogyakarta!5e0!3m2!1sen!2sid!4v1734756942933!5m2!1sen!2sid', 'https://media.istockphoto.com/id/2150759955/id/foto/wisatawan-wanita-di-candi-prambanan-dekat-kota-yogyakarta-jawa-tengah-indonesia.webp?s=2048x2048&w=is&k=20&c=CzFHH2FTU8z2VuStpB1qPjcnhg52WA0r8KAhM1bIb1c=', 'https://media.istockphoto.com/id/2150759945/id/foto/woman-traveler-at-prambanan-temple-near-yogyakarta-city-central-java-indonesia.webp?s=2048x2048&w=is&k=20&c=397yDgRJBlHqv2PSicaZs3-Fb9CgyA41M8kBzbXXCHE=', 'https://media.istockphoto.com/id/2150759955/id/foto/wisatawan-wanita-di-candi-prambanan-dekat-kota-yogyakarta-jawa-tengah-indonesia.webp?s=2048x2048&w=is&k=20&c=CzFHH2FTU8z2VuStpB1qPjcnhg52WA0r8KAhM1bIb1c=', 0),
(4, 'Gili Trawangan', 5, 'Gili Trawangan adalah salah satu dari tiga pulau Gili yang terletak di lepas pantai barat laut Lombok, Indonesia. Pulau ini terkenal dengan pantai berpasir putihnya, air laut yang jernih, dan suasana yang santai, menjadikannya destinasi populer bagi wisatawan yang mencari ketenangan dan keindahan alam. Gili Trawangan juga dikenal sebagai spot yang ideal untuk snorkeling dan menyelam, dengan terumbu karang yang indah dan kehidupan laut yang beragam.', '2024-12-22', '2025-01-11', 3000000, 'Lombok', 1, 1, 1, 0, 20, 0, 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d63104.20841810985!2d116.01528518907718!3d-8.349386344854705!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dcdb7e5e3a0d8f5%3A0x3e5d5f5c5d5e5f5d!2sGili%20Trawangan%2C%20Gili%20Indah%2C%20Pemenang%2C%20North%20Lombok%20Regency%2C%20West%20Nusa%20Tenggara!5e0!3m2!1sen!2sid!4v1734757329180!5m2!1sen!2sid', 'https://media.istockphoto.com/id/1317276731/id/foto/situs-glamping-romantis-di-malam-hari.webp?s=2048x2048&w=is&k=20&c=9IfD6BYTaWmbEseiiTHLEVRfJV3JyKfkQz-X4q50M2M=', 'https://media.istockphoto.com/id/1385899877/id/foto/tenda-turis-saat-senja-saat-langit-bersinar-oranye-sebelum-matahari-terbenam.webp?s=2048x2048&w=is&k=20&c=AFiZGCoIn4tzbRxCG5ea2NZIovYlnBvmHMK9XfukU5M=', 'https://media.istockphoto.com/id/1385899828/id/foto/sekelompok-wisatawan-berkemah-menikmati-malam-melawan-langit-malam-yang-indah.jpg?s=612x612&w=0&k=20&c=rqB2t1tPVAHIw0jyEf28bPpmUT1C_dApvkaBasho0zY=', 0),
(5, 'Tour Komodo', 4.5, 'Pulau Komodo adalah salah satu pulau yang terletak di Nusa Tenggara Timur, Indonesia, dan terkenal sebagai habitat asli komodo, kadal raksasa yang merupakan hewan purba. Pulau ini merupakan bagian dari Taman Nasional Komodo, yang diakui sebagai Situs Warisan Dunia UNESCO, dan menawarkan keindahan alam yang luar biasa, termasuk pantai-pantai berpasir putih, perairan jernih, serta pemandangan bukit yang menakjubkan.', '2024-12-22', '2025-01-11', 2500000, 'Nusa Tenggara Timur', 1, 1, 1, 0, 20, 0, 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d252484.5273573213!2d119.30782184436586!3d-8.589197444198112!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2db4545ba8b2bbbb%3A0x62b9b2c3feba412!2sKomodo!5e0!3m2!1sen!2sid!4v1734757501290!5m2!1sen!2sid', 'https://media.istockphoto.com/id/472831484/id/foto/pegunungan-di-taman-nasional-komodo-di-indonesia.webp?s=2048x2048&w=is&k=20&c=VrSmkSXTzEDW5zpn7lVueuyeZjdZOyqucs_sn2QLlvk=', 'https://media.istockphoto.com/id/2098186298/id/foto/komodo-dragon.webp?s=2048x2048&w=is&k=20&c=YMwnUJUwoCSvtpv_JdPqlUmwOdPigoZpPrgHEZBlFWM=', 'https://media.istockphoto.com/id/537641836/id/foto/gili-lawa-darat-in-komodo-national-park.webp?s=2048x2048&w=is&k=20&c=n_0RChKG7yN89PHy-Dq5tMO0msXt7VvLofGJiwynpso=', 0),
(6, 'Tour Padar', 4.5, 'Pulau Padar adalah salah satu pulau yang terletak di Taman Nasional Komodo, Indonesia, dan terkenal dengan pemandangan alamnya yang spektakuler. Pulau ini memiliki bentuk yang unik dengan tiga teluk yang berbeda, masing-masing menawarkan panorama yang menakjubkan. Dikenal sebagai salah satu tempat terbaik untuk trekking, pengunjung dapat mendaki ke puncak pulau untuk menyaksikan pemandangan laut biru yang memukau dan garis pantai yang indah.', '2024-12-23', '2025-01-10', 2000000, 'Nusa Tenggara Timur', 1, 1, 0, 0, 15, 0, 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d63108.854998173665!2d119.52852654654664!3d-8.66266484438308!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2db4f84ff6cd01ab%3A0xf7e6fd33b692a898!2sPadar%20Island!5e0!3m2!1sen!2sid!4v1734757908565!5m2!1sen!2sid', 'https://media.istockphoto.com/id/1181651984/id/foto/pagi-hari-di-puncak-gunung-wanita-melihat-ke-atas-pemandangan-pulau.jpg?s=612x612&w=0&k=20&c=6fBYkJblg3_LKj2cG14fwsRTGCwP1Y98R8yUZE20aGI=', 'https://media.istockphoto.com/id/1181651984/id/foto/pagi-hari-di-puncak-gunung-wanita-melihat-ke-atas-pemandangan-pulau.jpg?s=612x612&w=0&k=20&c=6fBYkJblg3_LKj2cG14fwsRTGCwP1Y98R8yUZE20aGI=', 'https://media.istockphoto.com/id/954047948/id/foto/pulau-padar-taman-nasional-komodo-indonesia.jpg?s=612x612&w=0&k=20&c=ZvUXHe79H5RWwDo1jzhFC_byUN12zbRLtZK9LPSOAjM=', 0),
(7, 'Tour Labuan Bajo', 5, 'Labuan Bajo adalah sebuah kota kecil yang terletak di ujung barat Pulau Flores, Indonesia. Dikenal sebagai pintu gerbang menuju Taman Nasional Komodo, Labuan Bajo menawarkan pemandangan laut yang indah dan suasana yang tenang. Kota ini menjadi tempat yang populer bagi para wisatawan yang ingin menjelajahi keindahan alam, termasuk pulau-pulau eksotis dan keanekaragaman hayati yang kaya.', '2024-12-23', '2025-01-09', 7000000, 'Nusa Tenggara Timur', 1, 1, 1, 0, 20, 0, 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d63144.20841810985!2d119.78628518907718!3d-8.449386344854705!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2db468a6d47ed641%3A0x87f524333c6a6e8d!2sLabuan%20Bajo%2C%20Komodo%2C%20West%20Manggarai%20Regency%2C%20East%20Nusa%20Tenggara!5e0!3m2!1sen!2sid!4v1734758073876!HARGA5m2!1sen!2sid', 'https://media.istockphoto.com/id/2176719573/id/foto/labuan-bajo-sunset-flores-island-indonesia.jpg?s=612x612&w=0&k=20&c=Fb2ngKRgvbhIb1v0V2zDpvtKJ4gFpbOx-FqtlriPEqU=', 'https://media.istockphoto.com/id/1247852913/id/foto/pemandangan-udara-pink-beach-pulau-komodo-indonesia.jpg?s=612x612&w=0&k=20&c=CDPCos7SgM5Fmm1Q7_4Tc5gOcY6HOXj7GezSJ2_o8cs=', 'https://media.istockphoto.com/id/1200113602/id/foto/pelabuhan-labuan-bajo-warnai-sunset-twilight-indonesia.jpg?s=612x612&w=0&k=20&c=nKLkVXp1GszQWGjOqJuziD1UAYa6ZJUFZ0Whmv69WDU=', 0),
(8, 'Tour Lombok', 4.875, 'Lombok adalah sebuah pulau yang terletak di sebelah timur Bali, Indonesia, terkenal dengan keindahan alamnya yang memukau. Pulau ini menawarkan pantai-pantai berpasir putih yang menawan, air terjun yang menakjubkan, serta pegunungan yang hijau. Gunung Rinjani, gunung tertinggi kedua di Indonesia, menjadi daya tarik utama bagi para pendaki yang ingin menikmati pemandangan spektakuler dari puncaknya.', '2024-12-28', '2025-01-09', 4000000, 'Nusa Tenggara Barat', 1, 1, 1, 1, 15, 0, 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d504977.3342538657!2d115.94333110342826!3d-8.582975590871232!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dcdb7d23e8cc745%3A0x446689c4ab50d8c9!2sLombok!5e0!3m2!1sen!2sid!4v1734758204649!5m2!1sen!2sid', 'https://media.istockphoto.com/id/2098338879/id/foto/woman-standing-on-viewpoint-after-hike-near-mount-rinjani-on-lombok.webp?s=2048x2048&w=is&k=20&c=Fw1Q6A_DTkXpWe0mownVGmTx5tmSaeEReQZmhG2UYOs=', 'https://media.istockphoto.com/id/966705750/id/foto/pantai-berpasir-tropis-dan-ombak-untuk-berselancar-di-lautan-dengan-langit-biru.webp?s=2048x2048&w=is&k=20&c=Fnx8Ic1mk_QyZFtGD1bax_knvR3Y2FZ-pcYBmV08Jt4=', 'https://media.istockphoto.com/id/1736605264/id/foto/pemandangan-udara-pantai-senggigi-dengan-drone-di-lombok-indonesia.webp?s=2048x2048&w=is&k=20&c=g18ohifq8R8VIqq6tGQYsdCgpyi6JvCHDsG_hG7pquQ=', 0),
(9, 'Tour Nusa Penida', 5, 'Nusa Penida adalah sebuah pulau yang terletak di tenggara Bali, Indonesia, terkenal dengan pemandangan alamnya yang menakjubkan dan keindahan pantainya. Pulau ini menawarkan tebing-tebing curam yang dramatis, air laut berwarna turquoise, serta pantai-pantai tersembunyi yang memikat. Salah satu daya tarik utama adalah Kelingking Beach, yang dikenal dengan bentuk tebingnya yang unik dan panorama yang spektakuler.', '2024-12-22', '2024-12-31', 6000000, 'Bali', 1, 1, 1, 1, 10, 0, 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126189.75488379817!2d115.45523833469221!3d-8.74556409521467!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd271194d1319d3%3A0x5c3a3706b2197b7b!2sPenida%20Island!5e0!3m2!1sen!2sid!4v1734758479458!5m2!1sen!2sid', 'https://media.istockphoto.com/id/1269615561/id/foto/titik-sinar-manta-surga-tropis-nusa-penida-flores-labuan-bajo-underwater.webp?s=2048x2048&w=is&k=20&c=ZlEFlhQYVgjsk4OP1bFZcuGiH3LxpO5vSCSRwYHfRVQ=', 'https://media.istockphoto.com/id/2150650548/id/foto/kelingking-beach-in-nusa-penida-with-sunset.webp?s=2048x2048&w=is&k=20&c=fLE3feN9a_UHPpZOnJqdakEyg9439dtuRK-vqJkXJCo=', 'https://media.istockphoto.com/id/1779699086/id/foto/warna-pantai-atuh.webp?s=2048x2048&w=is&k=20&c=eoblLqgTjuekaQi5gqlSFiUERSHWgftp5LdgGvzJ8oI=', 0),
(10, 'Tour Jakarta', 4.5, 'Jakarta adalah ibu kota Indonesia dan merupakan salah satu kota terbesar di Asia Tenggara. Dikenal sebagai pusat ekonomi, budaya, dan politik, Jakarta menawarkan beragam atraksi yang mencerminkan keragaman masyarakatnya. Kota ini dipenuhi dengan gedung pencakar langit, pusat perbelanjaan modern, serta berbagai tempat makan yang menyajikan kuliner dari seluruh nusantara dan internasional.', '2024-12-22', '2025-01-02', 2000000, 'Jakarta', 1, 1, 1, 0, 20, 0, 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d253840.49131655638!2d106.6647040366169!3d-6.229720928595884!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f3e945e34b9d%3A0x5371bf0fdad786a2!2sJakarta!5e0!3m2!1sen!2sid!4v1734758708039!5m2!1sen!2sid', 'https://media.istockphoto.com/id/500798563/id/foto/city-skyline-at-sunset-jakarta-indonesia.webp?s=2048x2048&w=is&k=20&c=mL4oWUEqLj7CTt5WmETwWlPOe5QPeYmgH0Tj35V_tvc=', 'https://media.istockphoto.com/id/1504965518/id/foto/bangunan-kolonial-belanda-tua-di-daerah-kota-tua-dan-museum-fattahilah.webp?s=2048x2048&w=is&k=20&c=4xWMaBXmCZnvGe6MiCljRaI31XQCjixpVLzal_tIiBk=', 'https://media.istockphoto.com/id/186753078/id/foto/monas-tower.webp?s=2048x2048&w=is&k=20&c=UqHQ4VmQ9RduJEcsUOmqjPNKFR6bLpA3b4BhTZyf9UQ=', 0),
(11, 'Tour Bromo', 3, 'Gunung Bromo adalah salah satu gunung berapi paling terkenal di Indonesia, terletak di Provinsi Jawa Timur, dalam kawasan Taman Nasional Bromo Tengger Semeru. Dengan ketinggian sekitar 2.329 meter di atas permukaan laut, Gunung Bromo menawarkan pemandangan yang menakjubkan, terutama saat matahari terbit. Banyak wisatawan datang untuk menyaksikan keindahan alam ini, yang dikelilingi oleh lautan pasir seluas sekitar 10 kilometer persegi.', '2025-01-04', '2025-01-05', 1500000, 'Jawa Timur', 1, 1, 1, 0, 50, 0, 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15806.180047325457!2d112.94271244493407!3d-7.942493323305603!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd637aaab794a41%3A0xada40d36ecd2a5dd!2sGn.%20Bromo!5e0!3m2!1sid!2sid!4v1735230626283!5m2!1sid!2sid', 'https://media.istockphoto.com/id/177800254/id/foto/gunung-berapi-gunung-bromo-jawa-timur-surabuya-indonesia.webp?s=1024x1024&w=is&k=20&c=ATqyFJZ3ZIDFYq2kOOzx4DainHBPJ1XPgeOELpLA49Y=', 'https://media.istockphoto.com/id/179226696/id/foto/gunung-berapi-bromo-di-indonesia.webp?s=1024x1024&w=is&k=20&c=Qtj8BsXjzCfnG19UE4SkeJbBfnLet3qEQZYFJ0B-LzY=', 'https://media.istockphoto.com/id/878230644/id/foto/taman-nasional-bromo-tengger-semeru.webp?s=1024x1024&w=is&k=20&c=EhvCDmJIx-O5ryqxlTM0DafWoJCUb-iUkdQrPGzv2F4=', 0),
(12, 'Hotel Aryaduta Lippo Karawaci', 3.7625, 'Aryaduta Lippo Karawaci adalah sebuah hotel bintang 4 yang berada di Tangerang, memiliki nilai historis sejak tahun 1995. Hotel ini menawarkan kenyamanan modern dengan suasana yang elegan, cocok untuk liburan keluarga atau perjalanan bisnis.', '2025-03-04', '2025-04-15', 1500000, 'Karawaci, Tangerang', 1, 0, 0, 0, 2, 0, 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.2695596428534!2d106.60324399999999!3d-6.228148199999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69fc1b8a93b7f5%3A0x538ce79823c2fe96!2sAryaduta%20Lippo%20Village%20%26%20Aryaduta%20Country%20Club!5e0!3m2!1sen!2sid!4v1735903285260!5m2!1sen!2sid', 'https://lh3.googleusercontent.com/p/AF1QipNKrsC52Tz1p987TuNX9zKlpdwzsaj4J0j2_xRF=s1360-w1360-h1020', 'https://lh3.googleusercontent.com/p/AF1QipPGH_eUuxqMgrhF92na9_hZ9urQrShrMBBkKJ1G=s1360-w1360-h1020', 'https://lh3.googleusercontent.com/p/AF1QipNp7I1Yy9lbwcmgcuKTZcqsj5fN1J5lXP7_4uZ4=s1360-w1360-h1020', 0);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--
CREATE TABLE `transactions` (
    `id` INT(10) NOT NULL AUTO_INCREMENT,
    `trans_id` VARCHAR(255) NULL DEFAULT NULL,
    `user_id` INT(10) NULL DEFAULT NULL,
    `package_id` INT(10) NULL DEFAULT NULL,
    `trans_amount` INT(10) NULL DEFAULT NULL,
    `trans_date` TIMESTAMP NULL DEFAULT NULL,
    `card_no` VARCHAR(255) NULL DEFAULT NULL,
    `val_id` VARCHAR(255) NULL DEFAULT NULL,
    `card_type` VARCHAR(255) NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    FOREIGN KEY (`package_id`) REFERENCES `packages` (`package_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--
CREATE TABLE `testimonials` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `message` TEXT,
    `user_id` INT,
    `package_id` INT,
    `rating` FLOAT,
    `date_created` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    FOREIGN KEY (`package_id`) REFERENCES `packages` (`package_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--
CREATE TABLE `purchases` (
    `id` INT(10) NOT NULL AUTO_INCREMENT,
    `user_id` INT(10) NULL DEFAULT NULL,
    `package_id` INT(10) NULL DEFAULT NULL,
    `total_guests` INT(10) NULL DEFAULT NULL,
    `purchase_date` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    FOREIGN KEY (`package_id`) REFERENCES `packages` (`package_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;