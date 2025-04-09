<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$organizer_name = $_SESSION['username'];
$events = [];
$attendees = [];

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];

    // Fetch event details for verification
    $sql_event = "SELECT event_name FROM events WHERE event_id = ? AND organizer_name = ?";
    $stmt_event = $mysqli->prepare($sql_event);
    $stmt_event->bind_param("is", $event_id, $organizer_name);
    $stmt_event->execute();
    $event_result = $stmt_event->get_result();
    $event = $event_result->fetch_assoc();

    if ($event) {
        // Fetch attendees for the selected event
        $sql_attendees = "SELECT b.booking_id, b.num_attendees, b.booking_date, u.username, u.email 
                         FROM bookings b 
                         JOIN users u ON b.user_id = u.user_id 
                         WHERE b.event_id = ?";
        $stmt_attendees = $mysqli->prepare($sql_attendees);
        $stmt_attendees->bind_param("i", $event_id);
        $stmt_attendees->execute();
        $attendees_result = $stmt_attendees->get_result();
        $attendees = $attendees_result->fetch_all(MYSQLI_ASSOC);
    } else {
        $error = "You are not the organizer of this event or the event does not exist.";
    }
}

// Fetch all events organized by the current user
$sql_events = "SELECT event_id, event_name FROM events WHERE organizer_name = ?";
$stmt_events = $mysqli->prepare($sql_events);
$stmt_events->bind_param("s", $organizer_name);
$stmt_events->execute();
$events_result = $stmt_events->get_result();
$events = $events_result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendees Details</title>
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

/* Header and Navbar Styles */
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

header h1 {
    margin: 0;
    font-size: 1.8em;
    font-weight: 700;
    color: #ffd700;
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
    color: white;
    font-size: 1.5em;
    cursor: pointer;
}

/* Logout Button */
.logout-btn {
    background: linear-gradient(45deg, #ff9800, #ff5722);
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 50px;
    font-size: 1.1em;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.4s ease;
    box-shadow: 0 5px 15px rgba(255, 87, 34, 0.4);
    text-decoration: none;
}

.logout-btn:hover {
    background: linear-gradient(45deg, #ffb74d, #ff7043);
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(255, 87, 34, 0.6);
}

/* Main content styles */
.content {
    width: 90%;
    max-width: 1200px;
    margin-top: 2rem;
}

/* Table Styling */
.table-container {
    width: 100%;
    overflow-x: auto;
    margin-bottom: 2rem;
    padding: 1rem;
    background: rgba(42, 82, 152, 0.85);
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    font-size: 1.1em;
}

th {
    background: rgba(107, 72, 255, 0.9);
    font-weight: 700;
}

tr:hover {
    background: rgba(255, 255, 255, 0.15);
    transition: background 0.3s ease-in-out;
}

/* Event and Attendee Cards */
.event-card, .attendee-card, form {
    background: rgba(42, 82, 152, 0.9);
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    margin: 15px;
    padding: 2rem;
    transition: transform 0.3s ease;
    color: white;
    max-width: 500px;
    max-height: calc(100vh - 200px);
    overflow-y: auto;
}

.event-card:hover, .attendee-card:hover {
    transform: translateY(-5px);
}

.event-card p, .attendee-card p {
    margin: 5px 0;
    color: white;
}

.event-card strong, .attendee-card strong {
    color: #ffd700;
}

/* Form Elements */
label {
    font-size: 1.1em;
    margin-bottom: 10px;
    color: white;
    display: block;
}

select {
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

select:focus {
    outline: none;
    border: 2px solid #ffd700;
}

/* Submit Button */
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

/* Footer Styles */
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
        background: rgba(42, 82, 152, 0.9);
        padding: 1rem;
    }

    .nav-menu.active {
        display: flex;
    }

    .hamburger {
        display: block;
    }

    .event-card, .attendee-card, form {
        max-width: 90%;
    }
}

@media (max-width: 480px) {
    select, button[type="submit"] {
        width: 90%;
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
                <li><a href="index.php#home">Home</a></li>
                <li><a href="index.php#about">About</a></li>
                <li><a href="index.php#contact">Contact</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
            <div class="hamburger">&#9776;</div>
        </nav>
    </header>

    <!-- Main Content -->
    <h2>Attendees Details</h2>

    <!-- Event Selection Form -->
    <div class="event-selector">
        <h3>Select an Event</h3>
        <form method="GET" action="">
            <label for="event_id">Your Events:</label>
            <select name="event_id" id="event_id" required>
                <option value="">-- Select an Event --</option>
                <?php foreach ($events as $event): ?>
                    <option value="<?php echo $event['event_id']; ?>" <?php echo (isset($_GET['event_id']) && $_GET['event_id'] == $event['event_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($event['event_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">View Attendees</button>
        </form>
    </div>

    <!-- Display Attendees -->
    <?php if (isset($error)): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php elseif (!empty($attendees)): ?>
        <div class="attendees-container">
            <h3>Attendees for <?php echo htmlspecialchars($event['event_name']); ?></h3>
            <?php foreach ($attendees as $attendee): ?>
                <div class="attendee-card">
                    <p><strong>Booking ID:</strong> <?php echo htmlspecialchars($attendee['booking_id']); ?></p>
                    <p><strong>Username:</strong> <?php echo htmlspecialchars($attendee['username']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($attendee['email']); ?></p>
                    <p><strong>Number of Attendees:</strong> <?php echo htmlspecialchars($attendee['num_attendees']); ?></p>
                    <p><strong>Booking Date:</strong> <?php echo htmlspecialchars($attendee['booking_date']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php elseif (isset($_GET['event_id'])): ?>
        <div class="attendees-container">
            <div class="attendee-card">
                <p>No attendees found for this event.</p>
            </div>
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