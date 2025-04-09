<?php
include 'db.php';

$event = null; 

if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];
    $sql = "SELECT * FROM events WHERE event_id = ?";
    $con = $mysqli->prepare($sql);
    $con->bind_param("i", $event_id);
    $con->execute();
    $result = $con->get_result();
    $event = $result->fetch_assoc(); 

    if (!$event) {
        $error_message = "Event not found.";
    } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $event_name = $_POST['event_name'];
        $event_date = $_POST['event_date'];
        $event_time = $_POST['event_time'];
        $venue = $_POST['venue'];
        $ticket_price = $_POST['ticket_price'];

        $sql = "UPDATE events SET event_name = ?, event_date = ?, event_time = ?, venue = ?, ticket_price = ? WHERE event_id = ?";
        $con = $mysqli->prepare($sql);
        $con->bind_param("ssssdi", $event_name, $event_date, $event_time, $venue, $ticket_price, $event_id);
        $con->execute();

        header("Location: view_events.php");
        exit; 
    }
} else {
    $error_message = "Invalid event ID.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #6b48ff 100%);
    background-size: 200% 200%;
    animation: gradientFlow 15s ease infinite;
    margin: 0;
    padding: 0;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    color: #ffffff;
}

@keyframes gradientFlow {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* Header and Navbar */
header {
    background: rgba(42, 82, 152, 0.9);
    width: 100%;
    padding: 1rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    border-radius: 0 0 12px 12px;
    position: sticky;
    top: 0;
    z-index: 1000;
}

.navbar {
    max-width: 1200px;
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo img {
    height: 40px;
    border-radius: 50%;
}

.nav-menu {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
}

.nav-menu li {
    margin-left: 2rem;
}

.nav-menu a {
    color: white;
    text-decoration: none;
    font-size: 1.1em;
    font-weight: 600;
    transition: color 0.3s ease;
}

.nav-menu a:hover {
    color: #ffd700;
}

.hamburger {
    display: none;
    font-size: 1.5em;
    color: white;
    cursor: pointer;
}

/* Main Content */
h2 {
    text-align: center;
    margin-top: 2rem;
    color: white;
}

.form-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 70vh;
    flex-grow: 1;
}

.edit-form {
    background: rgba(42, 82, 152, 0.9);
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    max-width: 400px;
    width: 100%;
    opacity: 0.92;
    color: white;
    transition: transform 0.3s ease;
}

.edit-form:hover {
    transform: translateY(-5px);
}

label {
    font-size: 1.1em;
    margin-bottom: 10px;
    display: block;
    color: white;
}

input[type="text"], 
input[type="date"], 
input[type="time"], 
input[type="number"] {
    width: 80%;
    padding: 10px;
    margin-bottom: 15px;
    border: none;
    border-radius: 5px;
    font-size: 1em;
    background: rgba(255, 255, 255, 0.2);
    color: white;
    transition: border-color 0.3s ease;
}

input:focus {
    outline: none;
    border: 2px solid #ffd700;
}

/* Buttons */
button[type="submit"] {
    padding: 12px 20px;
    background: linear-gradient(45deg, #ff9800, #ff5722);
    color: white;
    border: none;
    border-radius: 50px;
    font-size: 1.1em;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.4s ease;
    width: 80%;
}

button[type="submit"]:hover {
    background: linear-gradient(45deg, #ffb74d, #ff7043);
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(255, 87, 34, 0.6);
}

.alert {
    text-align: center;
    color: #ff4444;
    font-weight: bold;
    margin: 20px;
}

/* Footer */
footer {
    background: rgba(42, 82, 152, 0.9);
    color: white;
    width: 100%;
    padding: 2rem;
    text-align: center;
    font-size: 1.1em;
    font-weight: 600;
    margin-top: auto;
    border-radius: 12px 12px 0 0;
}

.footer-links a {
    color: #ffd700;
    text-decoration: none;
    margin: 0 1rem;
    transition: color 0.3s ease;
}

.footer-links a:hover {
    color: white;
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

/* Responsive Design */
@media (max-width: 768px) {
    .nav-menu {
        display: none;
        flex-direction: column;
        position: absolute;
        top: 60px;
        left: 0;
        right: 0;
        background: rgba(42, 82, 152, 0.95);
        padding: 1rem;
    }

    .nav-menu.active {
        display: flex;
    }

    .hamburger {
        display: block;
    }

    .edit-form {
        max-width: 90%;
    }
}

@media (max-width: 480px) {
    input[type="text"], 
    input[type="date"], 
    input[type="time"], 
    input[type="number"], 
    button[type="submit"] {
        width: 90%;
    }
}

/* Print Styles */
@media print {
    header, footer {
        display: none;
    }

    body {
        background: none;
        color: black;
    }

    .edit-form {
        background: none;
        border: 1px solid black;
        box-shadow: none;
    }
}

</style>
</head>
<body>
    <!-- Header with Navbar -->
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

    <!-- Main Content -->
    <h2>Edit Event</h2>
    <div class="form-container">
        <?php if (isset($error_message)): ?>
            <p class="alert"><?php echo $error_message; ?></p>
        <?php elseif ($event): ?>
            <form class="edit-form" method="POST">
                <label>Event Name:</label>
                <input type="text" name="event_name" value="<?php echo htmlspecialchars($event['event_name']); ?>" required>
                
                <label>Date:</label>
                <input type="date" name="event_date" value="<?php echo htmlspecialchars($event['event_date']); ?>" required>
                
                <label>Time:</label>
                <input type="time" name="event_time" value="<?php echo htmlspecialchars($event['event_time']); ?>" required>
                
                <label>Venue:</label>
                <input type="text" name="venue" value="<?php echo htmlspecialchars($event['venue']); ?>" required>
                
                <label>Ticket Price:</label>
                <input type="number" step="0.01" name="ticket_price" value="<?php echo htmlspecialchars($event['ticket_price']); ?>" required>
                
                <button type="submit">Update Event</button>
            </form>
        <?php endif; ?>
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