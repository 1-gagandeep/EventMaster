<?php
include 'db.php';
session_start();

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id']) || strtolower($_SESSION['user_type']) !== 'organiser') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Verify user exists
$check_sql = "SELECT id FROM users WHERE id = ?";
$check_stmt = $mysqli->prepare($check_sql);
$check_stmt->bind_param("i", $user_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();
if ($check_result->num_rows === 0) {
    die("Error: User ID $user_id does not exist in the users table. Please log in with a valid account.");
}
$check_stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $venue = $_POST['venue'];
    $contact_number = $_POST['contact_number'];
    $ticket_price = (float)$_POST['ticket_price'];
    $available_seats = (int)$_POST['available_seats'];
    $event_type = $_POST['event_type'];

    $sql = "INSERT INTO events (user_id, event_name, event_date, event_time, venue, contact_number, ticket_price, available_seats, event_type)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $mysqli->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . $mysqli->error);
    }
    
    $stmt->bind_param("isssssdis", $user_id, $event_name, $event_date, $event_time, $venue, $contact_number, $ticket_price, $available_seats, $event_type);
    if ($stmt->execute()) {
        
        header("Location: view_events.php");
    } else {
        echo "Failed to add event: " . $stmt->error . "<br>";
    }
    $stmt->close();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Event</title>
    <style>
    /* Import modern fonts */
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

/* Subtle overlay effect */
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
    color: #ffd700;
    margin-top: 2rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.form-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 70vh;
    flex-grow: 1;
}

.add-form {
    background: linear-gradient(45deg, rgba(107, 72, 255, 0.85), rgba(42, 82, 152, 0.85));
    padding: 2.5rem;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    max-width: 450px;
    width: 100%;
    opacity: 0.95;
    color: white;
    transition: transform 0.5s cubic-bezier(0.68, -0.55, 0.27, 1.55);
}

.add-form:hover {
    transform: translateY(-5px) scale(1.02);
}

label {
    font-size: 1.2em;
    color: #ffffff;
    margin-bottom: 10px;
    display: block;
}

input[type="text"],
input[type="date"],
input[type="time"],
input[type="number"] {
    width: 85%;
    padding: 12px;
    margin-bottom: 15px;
    border: none;
    border-radius: 8px;
    font-size: 1.1em;
    background: rgba(255, 255, 255, 0.9);
    transition: all 0.3s ease;
}

input:focus {
    outline: none;
    box-shadow: 0 0 10px rgba(107, 72, 255, 0.7);
    transform: scale(1.02);
}

button {
    width: 85%;
    padding: 12px;
    background: #ffd700;
    color: #1e3c72;
    border: none;
    border-radius: 50px;
    font-size: 1.2em;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.4s ease;
    box-shadow: 0 5px 15px rgba(255, 215, 0, 0.4);
}

button:hover {
    background: #ffeb3b;
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(255, 215, 0, 0.6);
}

footer {
    background: rgba(30, 60, 114, 0.95);
    color: #ffffff;
    padding: 2.5rem;
    text-align: center;
    margin-top: auto;
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.2);
}

.footer-content {
    max-width: 1200px;
    margin: 0 auto;
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

/* Responsive Design */
@media (max-width: 768px) {
    .nav-menu {
        display: none;
        flex-direction: column;
        position: absolute;
        top: 70px;
        left: 0;
        right: 0;
        background: rgba(30, 60, 114, 0.98);
        padding: 1.5rem;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
    }

    .nav-menu.active {
        display: flex;
    }

    .nav-menu li {
        margin: 1.5rem 0;
    }

    .hamburger {
        display: block;
    }

    .add-form {
        max-width: 90%;
    }
}

@media (max-width: 480px) {
    .add-form {
        padding: 1.5rem;
    }

    input[type="text"],
    input[type="date"],
    input[type="time"],
    input[type="number"] {
        width: 95%;
    }

    button {
        width: 95%;
    }
}
</style>
</head>
<body>
    <header>
        <nav class="navbar">
            <a href="#" class="logo">
                <img src="logo.png" alt="EventMaster Logo">
            </a>
            <ul class="nav-menu">
                <li><a href="home.php#home">Home</a></li>
                <li><a href="home.php#about">About</a></li>
                <li><a href="home.php#contact">Contact</a></li>
                <li><a href="login.php">Logout</a></li>
            </ul>
            <div class="hamburger">&#9776;</div>
        </nav>
    </header>

    <h2>Add New Event</h2>
    <div class="form-container">
        <form class="add-form" method="POST">
            <div class="form-group">
                <label for="event_name">Event Name:</label>
                <input type="text" name="event_name" id="event_name" required>
            </div>
            <div class="form-group">
                <label for="event_date">Date:</label>
                <input type="date" name="event_date" id="event_date" required>
            </div>
            <div class="form-group">
                <label for="event_time">Time:</label>
                <input type="time" name="event_time" id="event_time" required>
            </div>
            <div class="form-group">
                <label for="venue">Venue:</label>
                <input type="text" name="venue" id="venue" required>
            </div>
            <div class="form-group">
                <label for="contact_number">Contact Number:</label>
                <input type="text" name="contact_number" id="contact_number" required>
            </div>
            <div class="form-group">
                <label for="ticket_price">Ticket Price:</label>
                <input type="number" step="0.01" name="ticket_price" id="ticket_price" required>
            </div>
            <div class="form-group">
                <label for="available_seats">Available Seats:</label>
                <input type="number" name="available_seats" id="available_seats" required>
            </div>
            <div class="form-group">
                <label for="event_type">Event Type:</label>
                <input type="text" name="event_type" id="event_type" required>
            </div>
            <button type="submit">Add Event</button>
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

        document.addEventListener('DOMContentLoaded', () => {
    const sections = document.querySelectorAll('.section');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.2 });

    sections.forEach(section => observer.observe(section));
});
    </script>
</body>
</html>