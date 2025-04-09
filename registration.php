<?php
include 'db.php'; // Include database connection at the top

// Process registration before any output
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mobile_no = $_POST['mobile_no'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
    $type = $_POST['type']; 
    
    // Check if email already exists
    $sql_check = "SELECT COUNT(*) FROM users WHERE email = ?";
    $con_check = $mysqli->prepare($sql_check);
    $con_check->bind_param("s", $email);
    $con_check->execute();
    $con_check->bind_result($email_exists);
    $con_check->fetch();
    $con_check->close();

    if ($email_exists > 0) {
        $error = "Email already exists. Please use a different email.";
    } else {
        // Insert new user
        $sql = "INSERT INTO users (name, email, mobile_no, password, type) VALUES (?, ?, ?, ?, ?)";
        $con = $mysqli->prepare($sql);
        $con->bind_param("sssss", $name, $email, $mobile_no, $password, $type);
        if ($con->execute()) {
            header("Location: login.php"); // Redirect to login page
            exit(); // Stop execution to prevent further output
        } else {
            $error = "Registration failed: " . $mysqli->error;
        }
        $con->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
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
    background: #ffd700; /* Gold accent */
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

.registration-form {
    background: linear-gradient(45deg, rgba(107, 72, 255, 0.85), rgba(42, 82, 152, 0.85));
    padding: 2.5rem;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    text-align: center;
    max-width: 400px;
    width: 100%;
    transition: transform 0.5s ease, opacity 0.5s ease;
    opacity: 0.95;
    transform: translateY(10px);
}

.registration-form:hover {
    transform: translateY(-5px) scale(1.02);
}

.registration-form label {
    font-size: 1.1em;
    margin-bottom: 10px;
    color: white;
    display: block;
    width: 80%;
    text-align: left;
}

.registration-form input[type="text"],
.registration-form input[type="email"],
.registration-form input[type="password"],
.registration-form input[type="tel"],
.registration-form select {
    width: 80%;
    padding: 12px;
    margin-bottom: 15px;
    border: none;
    border-radius: 8px;
    font-size: 1em;
    background-color: #f9f9f9;
    transition: all 0.3s ease;
}

.registration-form input:focus,
.registration-form select:focus {
    outline: none;
    box-shadow: 0 0 10px rgba(107, 72, 255, 0.7);
    transform: scale(1.02);
}

.registration-form button[type="submit"] {
    padding: 12px 20px;
    background-color: #ffd700;
    color: #1e3c72;
    border: none;
    border-radius: 50px;
    font-size: 1.1em;
    cursor: pointer;
    transition: all 0.3s ease;
}

.registration-form button:hover {
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
    margin-top: 30px;
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.2);
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

/* Floating Action Button */
.fab {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 70px;
    height: 70px;
    background: #ffd700;
    color: #1e3c72;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5em;
    cursor: pointer;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
    transition: all 0.4s ease;
    animation: bounceGlow 2s infinite ease-in-out;
}

.fab:hover {
    background: #ffeb3b;
    transform: scale(1.15) rotate(15deg);
    box-shadow: 0 10px 30px rgba(255, 235, 59, 0.7);
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

    .hamburger {
        display: block;
    }
}

</style>
</head>
<body>
    <!-- Header with Navbar -->
    <header>
        <nav class="navbar">
            <a href="index.php" class="logo">
                <img src="logo.png" alt="EventMaster Logo">
            </a>
            <ul class="nav-menu">
                <li><a href="home.php#home">Home</a></li>
                <li><a href="home.php#about">About</a></li>
                <li><a href="home.php#contact">Contact</a></li>
                <li><a href="login.php">Login</a></li>
            </ul>
            <div class="hamburger">&#9776;</div>
        </nav>
    </header>

    <!-- Main Content -->
    <div>
        <h2>Let's Make Your Event Unforgettable!</h2>
        <div class="form-container">
            <form class="registration-form" method="POST">
                <?php if (isset($error)): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" required>
                
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>
                
                <label for="mobile_no">Mobile No.:</label>
                <input type="tel" name="mobile_no" id="mobile_no" required pattern="[0-9]{10}" title="Enter a valid 10-digit mobile number">
                
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
                
                <label for="type">User Type:</label>
                <select name="type" id="type" required>
                    <option value="organiser">Organiser</option>
                    <option value="attendee">Attendee</option>
                </select>
                
                <button type="submit">Sign Up</button>
                <label style="display: block; text-align: center; font-size: 1em; margin-top: 20px;">
                    Already Have an Account? 
                    <a href="login.php" class="login-button" style="color:rgb(46, 55, 55);">Sign In</a>
                </label>
            </form>
        </div>
    </div>

    <!-- Footer -->
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

    <!-- JavaScript for hamburger menu -->
    <script>
        const hamburger = document.querySelector('.hamburger');
        const navMenu = document.querySelector('.nav-menu');

        hamburger.addEventListener('click', () => {
            navMenu.classList.toggle('active');
        });
    </script>
</body>
</html>