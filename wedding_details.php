<?php
include 'db.php';

$show_details = false;
$bride_name = '';
$groom_name = '';
$event = null;
$error_message = '';

if (isset($_POST['bride_name']) && isset($_POST['groom_name'])) {
    $bride_name = htmlspecialchars($_POST['bride_name']);
    $groom_name = htmlspecialchars($_POST['groom_name']);
    $show_details = true;

    if (isset($_GET['event_id']) && is_numeric($_GET['event_id'])) {
        $event_id = intval($_GET['event_id']);

        try {
            $sql = "SELECT * FROM events WHERE event_id = ? AND event_type = 'Wedding'";
            $con = $mysqli->prepare($sql);
            $con->bind_param("i", $event_id);
            $con->execute();
            $result = $con->get_result();

            if ($result->num_rows > 0) {
                $event = $result->fetch_assoc();
            } else {
                $error_message = "No wedding details found for this event.";
            }
        } catch (Exception $e) {
            $error_message = "Error fetching wedding details: " . $e->getMessage();
        }
    } else {
        $error_message = "Invalid event ID.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wedding Details</title>
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

/* Container */
.container {
    max-width: 800px;
    margin: 2rem auto;
    background: linear-gradient(rgba(42, 82, 152, 0.8), rgba(70, 105, 200, 0.8));
    border-radius: 8px;
    padding: 2rem;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    text-align: center;
    color: white;
    flex-grow: 1;
    transition: transform 0.3s ease;
    opacity: 0.92;
}

.container:hover {
    transform: translateY(-5px);
}

/* Forms */
form input {
    width: 80%;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 1em;
    background-color: rgba(255, 255, 255, 0.2);
    color: white;
    transition: border-color 0.3s ease;
}

form input:focus {
    outline: none;
    border-color: #ffd700;
}

form button {
    padding: 10px 20px;
    background: linear-gradient(45deg, #ff9800, #ff5722);
    color: white;
    border: none;
    border-radius: 50px;
    font-size: 1.1em;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.4s ease;
}

form button:hover {
    background: linear-gradient(45deg, #ffb74d, #ff7043);
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(255, 87, 34, 0.6);
}

/* Events Section */
.events-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;
    padding: 20px;
    gap: 20px;
}

/* Event Cards */
.event-card {
    background: rgba(42, 82, 152, 0.9);
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    width: 320px;
    min-width: 280px;
    padding: 20px;
    transition: transform 0.3s ease;
    opacity: 0.92;
    text-align: center;
}

.event-card:hover {
    transform: translateY(-5px);
}

/* Buttons */
button, .back-btn {
    display: inline-block;
    text-decoration: none;
    color: white;
    padding: 10px 20px;
    margin: 5px auto;
    border-radius: 50px;
    font-size: 1.1em;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.4s ease;
    border: none;
    display: block;
}

.edit-btn, .delete-btn, .add-event-btn, .show-more-btn {
    background: linear-gradient(45deg, #ff9800, #ff5722);
}

.edit-btn:hover, .delete-btn:hover, .add-event-btn:hover, .show-more-btn:hover {
    background: linear-gradient(45deg, #ffb74d, #ff7043);
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(255, 87, 34, 0.6);
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

    .container {
        margin: 1rem;
        max-width: 90%;
    }
}

@media (max-width: 480px) {
    form input, form button {
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

    .event-card {
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
    <div class="container">
        <?php if ($show_details): ?>
            <?php if ($bride_name && $groom_name): ?>
                <marquee behavior="scroll" direction="left" scrollamount="8" class="marquee">
                    🌸 Congratulations to <?php echo $bride_name; ?> & <?php echo $groom_name; ?> on their Special Day! 🌸
                </marquee>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php elseif ($event): ?>
                <h2>Wedding Details</h2>
                <div class="details">
                    <p><strong>Event Name:</strong> <?php echo htmlspecialchars($event['event_name']); ?></p>
                    <p><strong>Date:</strong> <?php echo htmlspecialchars($event['event_date']); ?></p>
                    <p><strong>Time:</strong> <?php echo htmlspecialchars($event['event_time']); ?></p>
                    <p><strong>Venue:</strong> <?php echo htmlspecialchars($event['venue']); ?></p>
                    <p><strong>Available Seats:</strong> <?php echo htmlspecialchars($event['available_seats']); ?></p>
                </div>
            <?php endif; ?>
            <a href="view_events.php" class="back-btn">Back to Events</a>
        <?php else: ?>
            <h2>Enter Bride & Groom Names</h2>
            <form method="POST">
                <input type="text" name="bride_name" placeholder="Enter Bride Name" required>
                <input type="text" name="groom_name" placeholder="Enter Groom Name" required>
                <button type="submit">Show Wedding Details</button>
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