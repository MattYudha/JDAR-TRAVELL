<!DOCTYPE html>
<html lang="en">

<?php
if (!isset($_SESSION)) {
    session_start();
}

if (isset($_SESSION["logged_in"])) {
    echo '<script> location.href = "./index.php" </script>';
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration - triptrip</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .registration-header {
            position: relative;
            background: linear-gradient(135deg, #4AC7B7 0%, #A3D8F4 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding-top: 80px;
            padding-bottom: 20px;
        }

        .registration {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            max-width: 900px;
            margin: 20px;
            gap: 20px;
        }

        .illustration-section {
            flex: 1;
            padding: 2px;
            margin: 1px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 20px;
            box-sizing: border-box;
        }

        .illustration-section img {
            width: 100%;
            max-width: 400px;
            height: auto;
            border-radius: 10px;
        }

        .from-box {
            flex: 1;
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 40px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 450px;
        }

        .from-box h1 {
            font-size: 2rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        .from-box p.subtitle {
            font-size: 1rem;
            color: #666;
            margin-bottom: 30px;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-field {
            position: relative;
            margin-bottom: 17px;
        }

        .input-field i {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            color: #777;
        }

        .input-field input {
            width: 100%;
            padding: 12px 15px 12px 40px;
            border: 1px solid #ddd;
            border-radius: 20px;
            outline: none;
            font-size: 1rem;
            background: #f5f5f5;
            transition: border-color 0.3s ease, background 0.3s ease;
        }

        .input-field input:focus {
            border-color: #4AC7B7;
            background: #fff;
        }

        .input-field input::placeholder {
            color: #aaa;
        }

        .input-field .show_password {
            position: absolute;
            top: 50%;
            right: -160px;
            transform: translateY(-50%);
            color: #777;
            cursor: pointer;
        }

        .btn-field {
            text-align: center;
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        #signupBtn, #signinBtn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        #signupBtn {
            background-color: #4AC7B7;
            color: white;
        }

        #signupBtn:hover {
            background-color: #3AB0A1;
        }

        #signinBtn {
            background-color: #007bff;
            color: white;
        }

        #signinBtn:hover {
            background-color: #0056b3;
        }

        #signupBtn.disable, #signinBtn.disable {
            background-color: #ccc;
            color: #666;
            cursor: not-allowed;
        }

        .notification {
            text-align: center;
            margin-bottom: 20px;
        }

        p {
            text-align: center;
            font-weight: bold;
            color: #666;
            margin-top: 10px;
        }

        nav {
            background-color: #fff;
            padding: 15px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .nav-links li {
            margin-left: 20px;
        }

        .nav-links a {
            color: #333;
            font-size: 1.1rem;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: #007bff;
        }

        .fa-bars {
            font-size: 1.5rem;
            color: #333;
            cursor: pointer;
            display: none;
        }

        footer {
            background-color: #fff;
            color: white;
            text-align: center;
            padding: 20px;
            position: relative;
            bottom: 0;
            width: 100%;
        }

        @media (max-width: 768px) {
            .fa-bars {
                display: block;
            }

            .nav-links {
                display: none;
                flex-direction: column;
                position: absolute;
                top: 60px;
                right: 20px;
                background-color: #fff;
                padding: 10px;
                box-shadow: 0 2px 10px rgba(138, 6, 6, 0.1);
            }

            .nav-links.active {
                display: flex;
            }

            .nav-links li {
                margin: 10px 0;
            }

            .registration {
                flex-direction: column;
                margin: 10px;
            }

            .illustration-section {
                display: block;
                padding: 10px;
            }

            .illustration-section img {
                max-width: 200px;
            }

            .from-box {
                padding: 30px;
                margin: 0;
                max-width: 100%;
            }

            .from-box h1 {
                font-size: 1.5rem;
            }

            .from-box p.subtitle {
                font-size: 0.9rem;
            }

            .btn-field {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="registration-header">
        <nav id="navBar">
            <img src="logo.png" alt="JDAR Logo" style="float: right; width: 65px; height: auto; margin-right: 20px;">
            <ul class="nav-links">
                <li><a href="./index.php">Popular Places</a></li>
                <li><a href="./listing.php">All Packages</a></li>
            </ul>
            <i class="fa-solid fa-bars" onclick="togglebtn()"></i>
        </nav>
        <div class="registration">
            <div class="illustration-section">
                <img src="animasi.png" alt="Registration Illustration">
            </div>
            <div class="from-box">
                <h1 id="title">Sign Up</h1>
                <p class="subtitle" id="subtitle">Join triptrip Today</p>
                <div class="notification" id="notification"></div>
                <form id="registrationForm">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="input-group">
                        <div class="input-field" id="nameField">
                            <i class="fa-solid fa-user"></i>
                            <input required type="text" placeholder="Full Name" name="username" id="username">
                        </div>
                        <div class="input-field">
                            <i class="fa-solid fa-envelope"></i>
                            <input required type="email" placeholder="Email Address" name="email" id="email">
                        </div>
                        <div class="input-field">
                            <i class="fa-solid fa-lock"></i>
                            <input required type="password" placeholder="Password" name="password" id="password">
                            <i class="fa-solid fa-eye show_password" id="togglePassword"></i>
                        </div>
                        <div class="input-field" id="confirmPasswordField">
                            <i class="fa-solid fa-lock"></i>
                            <input required type="password" placeholder="Confirm Password" name="confirm_password" id="confirm_password">
                            <i class="fa-solid fa-eye show_password" id="toggleConfirmPassword"></i>
                        </div>
                        <p id="passwordReminder">Ingat kata sandi Anda dengan baik!</p>
                    </div>
                    <div class="btn-field">
                        <button type="button" id="signupBtn">Daftar</button>
                        <button type="button" id="signinBtn" class="disable">Masuk</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer>
        <?php include "./components/_footer.php" ?>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle hamburger menu
        function togglebtn() {
            const navLinks = document.querySelector('.nav-links');
            navLinks.classList.toggle('active');
        }

        // Toggle Password Visibility
        function togglePassword(fieldId, toggleId) {
            const input = document.getElementById(fieldId);
            const icon = document.getElementById(toggleId);
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }

        document.getElementById("togglePassword").addEventListener("click", () => togglePassword("password", "togglePassword"));
        document.getElementById("toggleConfirmPassword").addEventListener("click", () => togglePassword("confirm_password", "toggleConfirmPassword"));

        // Elements
        const signupBtn = document.getElementById("signupBtn");
        const signinBtn = document.getElementById("signinBtn");
        const title = document.getElementById("title");
        const subtitle = document.getElementById("subtitle");
        const nameField = document.getElementById("nameField");
        const confirmPasswordField = document.getElementById("confirmPasswordField");
        const passwordReminder = document.getElementById("passwordReminder");
        const notification = document.getElementById("notification");
        const form = document.getElementById("registrationForm");

        // Toggle between Sign Up and Sign In
        let isSignUpMode = true;

        signupBtn.addEventListener("click", function () {
            if (!isSignUpMode) {
                // Switch to Sign Up mode
                isSignUpMode = true;
                title.textContent = "Sign Up";
                subtitle.textContent = "Join triptrip Today";
                signupBtn.classList.remove("disable");
                signinBtn.classList.add("disable");
                nameField.style.display = "block";
                confirmPasswordField.style.display = "block";
                passwordReminder.style.display = "block";
                form.reset();
                notification.innerHTML = "";
            } else {
                // Handle Sign Up
                handleSubmit(true);
            }
        });

        signinBtn.addEventListener("click", function () {
            if (isSignUpMode) {
                // Switch to Sign In mode
                isSignUpMode = false;
                title.textContent = "Sign In";
                subtitle.textContent = "Welcome back to triptrip";
                signupBtn.classList.add("disable");
                signinBtn.classList.remove("disable");
                nameField.style.display = "none";
                confirmPasswordField.style.display = "none";
                passwordReminder.style.display = "none";
                form.reset();
                notification.innerHTML = "";
            } else {
                // Handle Sign In
                handleSubmit(false);
            }
        });

        // Handle Form Submission
        function handleSubmit(isSignUp) {
            const username = isSignUp ? document.getElementById("username").value : null;
            const email = document.getElementById("email").value;
            const password = document.getElementById("password").value;
            const confirmPassword = isSignUp ? document.getElementById("confirm_password").value : null;
            const csrfToken = document.querySelector('input[name="csrf_token"]').value;

            // Validasi dasar
            if (isSignUp && !username) {
                notification.innerHTML = '<div class="alert alert-danger">Nama lengkap wajib diisi!</div>';
                return;
            }
            if (!email || !password) {
                notification.innerHTML = '<div class="alert alert-danger">Email dan kata sandi wajib diisi!</div>';
                return;
            }

            if (isSignUp && password !== confirmPassword) {
                notification.innerHTML = '<div class="alert alert-danger">Kata sandi dan konfirmasi kata sandi tidak cocok!</div>';
                return;
            }

            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                notification.innerHTML = '<div class="alert alert-danger">Format email tidak valid!</div>';
                return;
            }

            // Kirim data ke backend via AJAX
            const formData = new FormData();
            if (isSignUp) {
                formData.append("username", username);
            }
            formData.append("email", email);
            formData.append("password", password);
            formData.append("csrf_token", csrfToken);

            const endpoint = isSignUp ? "./api/register.php" : "./api/login.php";

            fetch(endpoint, {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    notification.innerHTML = `<div class="alert alert-success">${isSignUp ? "Pendaftaran berhasil!" : "Login berhasil!"} Redirecting...</div>`;
                    setTimeout(() => {
                        window.location.href = "./index.php";
                    }, 2000);
                } else {
                    // Handle specific error messages for login
                    if (!isSignUp) {
                        if (data.message === "user_not_found") {
                            notification.innerHTML = '<div class="alert alert-danger">Akun tidak ditemukan!</div>';
                        } else if (data.message === "invalid_password") {
                            notification.innerHTML = '<div class="alert alert-danger">Password atau Email salah!</div>';
                        } else {
                            notification.innerHTML = `<div class="alert alert-danger">${data.message || "Login gagal!"}</div>`;
                        }
                    } else {
                        notification.innerHTML = `<div class="alert alert-danger">${data.message || "Pendaftaran gagal!"}</div>`;
                    }
                }
            })
            .catch(error => {
                notification.innerHTML = '<div class="alert alert-danger">Pasword atau Email Salah, coba lagi!</div>';
            });
        }
    </script>
</body>
</html>