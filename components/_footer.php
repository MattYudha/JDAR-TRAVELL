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
            font-family: 'Arial', sans-serif;
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
            color: #333;
            padding: 0;
            width: 100%;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            padding: 30px 20px;
            flex-wrap: wrap;
        }

        .footer-column {
            display: flex;
            flex-direction: column;
            min-width: 200px;
            margin-bottom: 20px;
        }

        .footer-column h3 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
        }

        .footer-column a {
            color: #666;
            text-decoration: none;
            margin-bottom: 10px;
            font-size: 14px;
            transition: color 0.2s;
        }

        .footer-column a:hover {
            color: #333;
        }

        .get-in-touch p {
            margin-bottom: 15px;
            font-size: 14px;
            color: #333;
            max-width: 220px;
            line-height: 1.4;
        }

        .social-icons {
            display: flex;
            gap: 15px;
            margin-top: 5px;
        }

        .social-icons a {
            text-decoration: none;
        }

        .social-icons i {
            font-size: 18px;
            color: #666;
            transition: color 0.2s;
        }

        .social-icons i:hover {
            color: #333;
        }

        .footer-bottom {
            text-align: center;
            padding: 20px 0;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #eaeaea;
            background-color: #ffffff;
        }

        .footer-links {
            margin-top: 5px;
        }

        .footer-links a {
            color: #666;
            text-decoration: none;
            margin: 0 5px;
            font-size: 12px;
        }

        .footer-links a:hover {
            color: #333;
        }

        @media (max-width: 768px) {
            .footer-content {
                flex-direction: column;
                align-items: flex-start;
            }

            .footer-column {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Footer starts here -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-column">
                <h3>Products</h3>
                <a href="#">Product</a>
                <a href="#">Pricing</a>
                <a href="#">Log in</a>
                <a href="#">Request access</a>
                <a href="#">Partnerships</a>
            </div>
            
            <div class="footer-column">
                <h3>About us</h3>
                <a href="#">About JDAR Travel</a>
                <a href="#">Contact us</a>
                <a href="#">Features</a>
                <a href="#">Careers</a>
            </div>
            
            <div class="footer-column">
                <h3>Resources</h3>
                <a href="#">Help center</a>
                <a href="#">Book a demo</a>
                <a href="#">Server status</a>
                <a href="#">Blog</a>
            </div>
            
            <div class="footer-column get-in-touch">
                <h3>Get in touch</h3>
                <p>Questions or feedback? We'd love to hear from you</p>
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
            <p>© 2024 JDAR Travel. All rights reserved.</p>
            <div class="footer-links">
                <a href="#">Terms of Service</a>
                <a href="#">Privacy Policy</a>
            </div>
        </div>
    </footer>
</body>
</html>
