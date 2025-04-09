<?php
include 'db.php';
session_start();

// Process login before any output
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ?";
    $con = $mysqli->prepare($sql);
    $con->bind_param("s", $email);
    $con->execute();
    $result = $con->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_type'] = $user['type'];
        if (strtolower($user['type']) === 'organiser') {
            header("Location: view_events.php");
        } else {
            header("Location: book_ticket.php");
        }
        exit();
    } else {
        $error_message = "Invalid email or password.";
    }
    $con->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #6b48ff 100%);
    background-size: 200% 200%;
    animation: gradientFlow 15s ease infinite;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    color: #ffffff;
    min-height: 100vh;
    overflow-x: hidden;
    position: relative;
}

/* Dynamic gradient background animation */
@keyframes gradientFlow {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* Subtle overlay with parallax-like effect */
body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle at center, rgba(255, 255, 255, 0.1) 0%, rgba(0, 0, 0, 0.5) 100%);
    opacity: 0.8;
    z-index: -1;
    animation: overlayShift 20s ease infinite;
}

@keyframes overlayShift {
    0% { transform: translateY(0); }
    50% { transform: translateY(-20px); }
    100% { transform: translateY(0); }
}

header {
    background: rgba(30, 60, 114, 0.95);
    padding: 1.5rem 2rem;
    position: sticky;
    top: 0;
    z-index: 1000;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    backdrop-filter: blur(10px);
}

.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 1200px;
    margin: 0 auto;
}

.logo img {
    height: 50px;
    width: auto;
    border-radius: 50%;
    transition: transform 0.4s ease;
}

.logo img:hover {
    transform: rotate(360deg) scale(1.1);
}

.nav-menu {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
}

.nav-menu li {
    margin-left: 2.5rem;
    position: relative;
}

.nav-menu a {
    color: #ffffff;
    text-decoration: none;
    font-size: 1.2em;
    font-weight: 600;
    transition: all 0.3s ease;
    position: relative;
}

.nav-menu a::after {
    content: '';
    position: absolute;
    width: 0;
    height: 2px;
    bottom: -5px;
    left: 0;
    background: #ffd700;
    transition: width 0.3s ease;
}

.nav-menu a:hover::after {
    width: 100%;
}

.nav-menu a:hover {
    color: #ffd700;
}

.hamburger {
    display: none;
    color: #ffffff;
    font-size: 2em;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.hamburger:hover {
    transform: scale(1.1);
}

h2 {
    text-align: center;
    color: white;
    margin-top: 2rem;
}

.form-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 70vh;
    flex-grow: 1;
}

.login-form {
    background: linear-gradient(45deg, rgba(107, 72, 255, 0.85), rgba(42, 82, 152, 0.85));
    padding: 2.5rem;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    max-width: 400px;
    width: 100%;
    opacity: 0.95;
    color: white;
    display: flex;
    flex-direction: column;
    align-items: center;
    transition: transform 0.5s ease, opacity 0.5s ease;
    transform: translateY(10px);
}

.login-form:hover {
    transform: translateY(-5px) scale(1.02);
}

.login-form label {
    font-size: 1.1em;
    margin-bottom: 10px;
    color: white;
    display: block;
    width: 80%;
    text-align: left;
}

.login-form input[type="email"],
.login-form input[type="password"],
.login-form input[type="text"],
.login-form input[type="tel"],
.login-form select {
    width: 80%;
    padding: 12px;
    margin-bottom: 15px;
    border: none;
    border-radius: 8px;
    font-size: 1em;
    background-color: #f9f9f9;
    transition: all 0.3s ease;
}

.login-form input:focus,
.login-form select:focus {
    outline: none;
    box-shadow: 0 0 10px rgba(107, 72, 255, 0.7);
    transform: scale(1.02);
}

.login-form button[type="submit"] {
    padding: 12px 20px;
    background-color: #ffd700;
    color: #1e3c72;
    border: none;
    border-radius: 50px;
    font-size: 1.1em;
    cursor: pointer;
    transition: all 0.3s ease;
}

.login-form button:hover {
    background-color: #ffeb3b;
    transform: scale(1.05);
}

.error-message {
    color: #ff4444;
    font-weight: bold;
    text-align: center;
    margin-bottom: 15px;
}

footer {
    background: rgba(30, 60, 114, 0.95);
    color: white;
    padding: 2.5rem;
    text-align: center;
    margin-top: auto;
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.2);
}

.footer-content {
    max-width: 1200px;
    margin: 0 auto;
}

.footer-links {
    margin: 1rem 0;
}

.footer-links a {
    color: #ffd700;
    text-decoration: none;
    margin: 0 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.footer-links a:hover {
    color: #ffeb3b;
    text-shadow: 0 0 10px rgba(255, 235, 59, 0.5);
}

.social-icons {
    margin-top: 1rem;
}

.social-icons a {
    color: white;
    font-size: 1.5em;
    margin: 0 0.5rem;
    transition: color 0.3s ease;
}

.social-icons a:hover {
    color: #ffd700;
}


@keyframes bounceGlow {
    0%, 100% { transform: translateY(0); box-shadow: 0 6px 20px rgba(255, 215, 0, 0.5); }
    50% { transform: translateY(-10px); box-shadow: 0 10px 30px rgba(255, 215, 0, 0.8); }
}

@media (max-width: 768px) {
    .nav-menu {
        display: none;
        flex-direction: column;
        position: absolute;
        top: 60px;
        left: 0;
        right: 0;
        background: rgba(30, 60, 114, 0.95);
        padding: 1rem;
    }

    .nav-menu.active {
        display: flex;
    }

    .nav-menu li {
        margin: 1rem 0;
    }

    .hamburger {
        display: block;
    }

    .login-form {
        margin: 0 1rem;
    }
}
</style>
</head>
<body>
    <header>
        <nav class="navbar">
            <a href="index.php" class="logo">
                <img src="logo.png" alt="EventMaster Logo">
            </a>
            <ul class="nav-menu">
                <li><a href="home.php#home">Home</a></li>
                <li><a href="home.php#about">About</a></li>
                <li><a href="home.php#contact">Contact</a></li>
                <li><a href="registration.php">Sign Up</a></li>
            </ul>
            <div class="hamburger">&#9776;</div>
        </nav>
    </header>

    <h2>Welcome Back!</h2>
    <div class="form-container">
        <form class="login-form" method="POST">
            <?php if (isset($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
            <button type="submit">Sign In</button>
            <label style="display: block; text-align: center; font-size: 1em; margin-top: 20px;">
                Don't Have an Account? 
                <a href="registration.php" style="color:rgb(46, 55, 55);">Sign Up</a>
            </label>
        </form>
    </div>

    <footer>
        <div class="footer-content">
        <p>&copy; 2025 EventMaster. All rights reserved.</p>
            <div class="footer-links">
            <a href="home.php#privacy">Privacy Policy</a>
                <a href="home.php#terms">Terms & Conditions</a>
                <a href="home.php#contact">Contact Us</a>
            </div>
        </div>
    </footer>

    <script>
        const hamburger = document.querySelector('.hamburger');
        const navMenu = document.querySelector('.nav-menu');
        hamburger.addEventListener('click', () => {
            navMenu.classList.toggle('active');
        });
    </script>
</body>
</html>