<?php
include 'db.php'; // Connect to the database

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['event_id']) && isset($_POST['num_attendees'])) {
    $event_id = $_POST['event_id'];
    $num_attendees = $_POST['num_attendees'];

    $sql = "SELECT event_name, ticket_price, available_seats, event_date, event_time FROM events WHERE event_id = ?";
    $con = $mysqli->prepare($sql);
    $con->bind_param("i", $event_id); 
    $con->execute();
    $result = $con->get_result();
    $event = $result->fetch_assoc();

    if ($event) {
        if ($num_attendees <= $event['available_seats']) {
            $total_price = $event['ticket_price'] * $num_attendees;
            $new_available_seats = $event['available_seats'] - $num_attendees;
            $sql_update_seats = "UPDATE events SET available_seats = ? WHERE event_id = ?";
            $stmt_update = $mysqli->prepare($sql_update_seats);
            $stmt_update->bind_param("ii", $new_available_seats, $event_id); 
            $stmt_update->execute();
            $booking_success = true;
        } else {
            $error = "The number of attendees exceeds the available seats. Please select a smaller number.";
        }
    }
} else {
    $sql = "SELECT event_id, event_name, event_date, event_time, venue, ticket_price, available_seats 
            FROM events 
            WHERE event_type != 'wedding'";
    $con = $mysqli->query($sql);
    $events = $con->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Event Tickets</title>
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

.event-list, .booking-details {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex-grow: 1;
    padding: 0 1rem;
}

/* Event & Booking Cards */
.event-card, .booking-form {
    background: rgba(42, 82, 152, 0.9);
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    margin: 15px;
    padding: 2rem;
    transition: transform 0.3s ease;
    color: white;
    max-height: calc(100vh - 200px);
    overflow-y: auto;

}

.event-card:hover, .booking-form:hover {
    transform: translateY(-5px);
}

label {
    font-size: 1.1em;
    margin-bottom: 10px;
    display: block;
    color: white;
}

input[type="number"], select {
    width: 80%;
    padding: 10px;
    margin: 15px 0;
    border: none;
    border-radius: 5px;
    font-size: 1em;
    background: rgba(255, 255, 255, 0.2);
    color: white;
    transition: border-color 0.3s ease;
}

input[type="number"]:focus, select:focus {
    outline: none;
    border: 2px solid #ffd700;
}

/* Buttons */
button[type="submit"], .go-back-button, .print-button {
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
    text-decoration: none;
    text-align: center;
    display:flex;
}

button[type="submit"]:hover, .go-back-button:hover, .print-button:hover {
    background: linear-gradient(45deg, #ffb74d, #ff7043);
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(255, 87, 34, 0.6);
}

.total-price {
    font-size: 1.2em;
    font-weight: bold;
    margin-top: 10px;
    color: #ffd700;
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

    .event-card, .booking-form {
        max-width: 90%;
    }
}

@media (max-width: 480px) {
    input[type="number"], button[type="submit"], .go-back-button, .print-button {
        width: 90%;
    }
}

/* Print Styles */
@media print {
    header, footer, .go-back-button, .print-button {
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
            <a href="index.php" class="logo">
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
    <h2>Book Event Tickets</h2>

    <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($booking_success)): ?>
        <div class="booking-details">
            <div class="event-card">
                <h3>Booking Successful!</h3>
                <p><strong>Event:</strong> <?php echo htmlspecialchars($event['event_name']); ?></p>
                <p><strong>Date:</strong> <?php echo htmlspecialchars($event['event_date']); ?></p>
                <p><strong>Time:</strong> <?php echo htmlspecialchars($event['event_time']); ?></p>
                <p><strong>Number of Attendees:</strong> <?php echo htmlspecialchars($num_attendees); ?></p>
                <p class="total-price"><strong>Total Price:</strong> Rs. <?php echo htmlspecialchars(number_format($total_price, 2)); ?></p>
                <button class="print-button" onclick="window.print()">Print Ticket</button>
                <a href="book_ticket.php" class="go-back-button">Book Again</a>
            </div>
        </div>
    <?php elseif (isset($error)): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php else: ?>
        <div class="event-list">
            <?php foreach ($events as $event): ?>
                <div class="event-card">
                    <p><strong>Event Name:</strong> <?php echo htmlspecialchars($event['event_name']); ?></p>
                    <p><strong>Date:</strong> <?php echo htmlspecialchars($event['event_date']); ?></p>
                    <p><strong>Time:</strong> <?php echo htmlspecialchars($event['event_time']); ?></p>
                    <p><strong>Venue:</strong> <?php echo htmlspecialchars($event['venue']); ?></p>
                    <p><strong>Ticket Price:</strong> Rs. <?php echo htmlspecialchars($event['ticket_price']); ?></p>
                    <p><strong>Available Seats:</strong> <?php echo htmlspecialchars($event['available_seats']); ?></p>
                    <form class="booking-form" method="POST">
                        <label for="num_attendees_<?php echo $event['event_id']; ?>">Enter Number of Attendees:</label>
                        <input type="number" id="num_attendees_<?php echo $event['event_id']; ?>" name="num_attendees" min="1" max="<?php echo $event['available_seats']; ?>" required>
                        <input type="hidden" name="event_id" value="<?php echo $event['event_id']; ?>">
                        <button type="submit">Book Ticket</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

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