<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $venue = $_POST['venue'];
    $ticket_price = $_POST['ticket_price'];

    $sql = "UPDATE events SET event_name = ?, event_date = ?, event_time = ?, venue = ?, ticket_price = ? WHERE event_id = ?";
    $con = $mysqli->prepare($sql);
    $con->bind_param("sssssi", $event_name, $event_date, $event_time, $venue, $ticket_price, $event_id); 
    $con->execute();

    $update_success = true;
}

$search_type = '';
$search_month = '';
$search_year = '';
$events = [];

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $search_type = $_GET['event_type'] ?? '';
    $search_month = $_GET['event_month'] ?? '';
    $search_year = $_GET['event_year'] ?? '';
    
    $sql = "SELECT * FROM events WHERE 1 = 1";
    $params = [];

    if (!empty($search_type)) {
        $sql .= " AND event_type = ?";
        $params[] = $search_type;
    }
    
    if (!empty($search_month) && !empty($search_year)) {
        $sql .= " AND MONTH(event_date) = ? AND YEAR(event_date) = ?";
        $params[] = $search_month;
        $params[] = $search_year;
    }

    $con = $mysqli->prepare($sql);

    if (!empty($params)) {
        $types = str_repeat("s", count($params)); 
        $con->bind_param($types, ...$params);
    }

    $con->execute();
    $result = $con->get_result(); 
    $events = $result->fetch_all(MYSQLI_ASSOC);
}

if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];
    $sql = "SELECT * FROM events WHERE event_id = ?";
    $con = $mysqli->prepare($sql);
    $con->bind_param("i", $event_id); 
    $con->execute();
    $result = $con->get_result();
    $event_to_edit = $result->fetch_assoc(); 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management</title>
    <style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #6b48ff 100%);
    animation: gradientFlow 15s ease infinite;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    color: #ffffff;
    width: 96.8vw;
}

/* Smooth gradient animation */
@keyframes gradientFlow {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* Header */
header {
    background: rgba(42, 82, 152, 0.9);
    width: 100%;
    padding: 1rem 2rem;
    display: flex;
    justify-content: space-between; /* Aligns logo left and nav right */
    align-items: center;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    border-radius: 0 0 12px 12px;
    position: sticky;
    top: 0;
    z-index: 1000;
}

.navbar {
    /* max-width: 1200px; */
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo img {
    margin-left:100px;
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
    margin: 2rem 0;
    font-size: 2em;
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
}

/* Events Section */
.event-container {
    display: flex;
flex-direction: row;
flex-wrap: wrap; 
justify-content: center; 
align-items: center; 
padding: 20px;
gap: 20px;
}

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

.event-details h3 {
    color: white;
    margin: 0 0 10px 0;
    font-size: 1.6em;
    text-align: center;
}

.event-details p {
    font-size: 1em;
    margin: 8px 0;
    line-height: 1.4;
}

/* Centering event actions */
.event-actions {
    margin-top: 20px;
    text-align: center;
}

/* Buttons */
button {
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
    display: block; /* Centering button */
}

/* Button Styles */
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
    margin-top: 30px;
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
    .events-container {
        flex-wrap: wrap;
        justify-content: center;
        overflow-x: hidden;
    }

    .event-card {
        width: 90%;
        max-width: 320px;
        margin: 15px auto;
    }

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

    .nav-menu li {
        margin: 1rem 0;
    }

    .hamburger {
        display: block;
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
    <h2>Event Master</h2>

    <?php if (isset($update_success)): ?>
        <div class="alert">Event updated successfully!</div>
    <?php endif; ?>

    <!-- Event Update Form -->
    <?php if (isset($event_to_edit)): ?>
        <div class="form-container">
            <h3>Edit Event</h3>
            <form method="POST">
                <input type="hidden" name="event_id" value="<?php echo $event_to_edit['event_id']; ?>">
                <label>Event Name:</label>
                <input type="text" name="event_name" value="<?php echo htmlspecialchars($event_to_edit['event_name']); ?>" required>
                <label>Date:</label>
                <input type="date" name="event_date" value="<?php echo htmlspecialchars($event_to_edit['event_date']); ?>" required>
                <label>Time:</label>
                <input type="time" name="event_time" value="<?php echo htmlspecialchars($event_to_edit['event_time']); ?>" required>
                <label>Venue:</label>
                <input type="text" name="venue" value="<?php echo htmlspecialchars($event_to_edit['venue']); ?>" required>
                <label>Ticket Price:</label>
                <input type="number" step="0.01" name="ticket_price" value="<?php echo htmlspecialchars($event_to_edit['ticket_price']); ?>" required>
                <button type="submit">Update Event</button>
            </form>
        </div>
    <?php endif; ?>

    <!-- Event Search Form -->
    <div class="form-container">
        <h3>Search Events</h3>
        <form method="GET" action="">
            <label for="event_type">Event Type:</label>
            <input type="text" name="event_type" id="event_type" value="<?php echo htmlspecialchars($search_type); ?>">
            <label for="event_month">Month:</label>
            <select name="event_month" id="event_month">
                <option value="">Select Month</option>
                <?php
                for ($m = 1; $m <= 12; $m++) {
                    echo '<option value="' . $m . '"' . (($search_month == $m) ? ' selected' : '') . '>' . date("F", mktime(0, 0, 0, $m, 1)) . '</option>';
                }
                ?>
            </select>
            <label for="event_year">Year:</label>
            <input type="number" name="event_year" id="event_year" min="2000" max="<?php echo date("Y"); ?>" value="<?php echo htmlspecialchars($search_year); ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <!-- Display Event Search Results -->
    <div class="event-container">
        <?php if (!empty($events)): ?>
            <?php foreach ($events as $row): ?>
                <div class="event-card">
                    <p><strong>Event Name:</strong> <?php echo htmlspecialchars($row['event_name']); ?></p>
                    <p><strong>Date:</strong> <?php echo htmlspecialchars($row['event_date']); ?></p>
                    <p><strong>Time:</strong> <?php echo htmlspecialchars($row['event_time']); ?></p>
                    <p><strong>Venue:</strong> <?php echo htmlspecialchars($row['venue']); ?></p>
                    <p><strong>Ticket Price:</strong> Rs.<?php echo htmlspecialchars($row['ticket_price']); ?></p>
                    <p><strong>Available Seats:</strong> <?php echo htmlspecialchars($row['available_seats']); ?></p>
                    <p><strong>Event Type:</strong> <?php echo htmlspecialchars($row['event_type']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="event-card">
                <p>No events found for the selected criteria.</p>
            </div>
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
