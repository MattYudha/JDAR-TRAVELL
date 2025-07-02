<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer JDAR Travel</title>
    <!-- Font Awesome for social icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }

        .footer {
            background-color: #ffffff;
            color: #1a1a1a;
            width: 100%;
            padding: 60px 0 30px;
        }

        .footer-content {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            flex-wrap: nowrap;
            gap: 40px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none; /* Firefox */
        }

        .footer-content::-webkit-scrollbar {
            display: none; /* Safari and Chrome */
        }

        .footer-column {
            flex: 0 0 auto;
            min-width: 180px;
            display: flex;
            flex-direction: column;
        }

        .footer-column h3 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #1a1a1a;
            letter-spacing: 0.5px;
        }

        .footer-column a {
            color: #005555;
            text-decoration: none;
            font-size: 14px;
            margin-bottom: 12px;
            transition: color 0.3s ease;
        }

        .footer-column a:hover {
            color: #ff6200;
        }

        .get-in-touch p {
            font-size: 14px;
            color: #005555;
            line-height: 1.6;
            margin-bottom: 20px;
            max-width: 260px;
        }

        .social-icons {
            display: flex;
            gap: 16px;
            margin-top: 10px;
        }

        .social-icons a {
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: #008080;
            transition: background-color 0.3s ease;
        }

        .social-icons a:hover {
            background-color: #ff6200;
        }

        .social-icons i {
            font-size: 16px;
            color: #ffffff;
        }

        .footer-bottom {
            max-width: 1280px;
            margin: 40px auto 0;
            padding: 20px 24px;
            border-top: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            color: #005555;
        }

        .footer-links {
            display: flex;
            gap: 20px;
        }

        .footer-links a {
            color: #005555;
            text-decoration: none;
            font-size: 13px;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: #ff6200;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .footer-content {
                gap: 30px;
            }

            .footer-column {
                min-width: 160px;
            }
        }

        @media (max-width: 768px) {
            .footer-content {
                padding: 0 20px;
                flex-wrap: nowrap;
            }

            .footer-bottom {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
        }

        @media (max-width: 480px) {
            .footer {
                padding: 40px 0 20px;
            }

            .footer-column h3 {
                font-size: 16px;
            }

            .footer-column a,
            .get-in-touch p {
                font-size: 13px;
            }

            .social-icons a {
                width: 32px;
                height: 32px;
            }

            .social-icons i {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-column">
                <h3>Produk</h3>
                <a href="#">Produk</a>
                <a href="#">Harga</a>
                <a href="#">Masuk</a>
                <a href="#">Minta Akses</a>
                <a href="#">Kemitraan</a>
            </div>
            
            <div class="footer-column">
                <h3>Tentang Kami</h3>
                <a href="#">Tentang JDAR Travel</a>
                <a href="#">Hubungi Kami</a>
                <a href="#">Fitur</a>
                <a href="#">Karir</a>
            </div>
            
            <div class="footer-column">
                <h3>Sumber Daya</h3>
                <a href="#">Pusat Bantuan</a>
                <a href="#">Pesan Demo</a>
                <a href="#">Status Server</a>
                <a href="#">Blog</a>
            </div>
            
            <div class="footer-column get-in-touch">
                <h3>Hubungi Kami</h3>
                <p>Punya pertanyaan atau masukan? Kami ingin mendengar dari Anda</p>
                <div class="social-icons">
                    <a href="https://www.facebook.com/rahmat.dewa.96780" target="_blank" aria-label="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com/" target="_blank" aria-label="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://www.instagram.com/matt_rynnn/" target="_blank" aria-label="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://www.linkedin.com/" target="_blank" aria-label="LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>© 2025 JDAR Travel. Hak cipta dilindungi.</p>
            <div class="footer-links">
                <a href="#">Syarat Layanan</a>
                <a href="#">Kebijakan Privasi</a>
            </div>
        </div>
    </footer>
</body>
</html>