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

$organizer_id = $_SESSION['user_id'];
echo "Debug: Organizer ID = $organizer_id<br>";

try {
    $sql = "SELECT * FROM events WHERE user_id = ? AND event_date >= CURDATE() ORDER BY event_date";
    $stmt = $mysqli->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Prepare failed: " . $mysqli->error);
    }
    $stmt->bind_param("i", $organizer_id);
    $stmt->execute();
    $events_result = $stmt->get_result();
    echo "Debug: Number of events found = " . $events_result->num_rows . "<br>";

    $attendees_sql = "SELECT u.name, u.email, u.mobile_no 
                     FROM event_attendees ea 
                     JOIN users u ON ea.user_id = u.id 
                     WHERE ea.event_id = ?";
    $attendees_stmt = $mysqli->prepare($attendees_sql);
    if ($attendees_stmt === false) {
        throw new Exception("Prepare failed for attendees: " . $mysqli->error);
    }
} catch (Exception $e) {
    $error_message = "Error fetching events or attendees: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Upcoming Events</title>
    <style>
        /* Your existing CSS unchanged */
        body {
            font-family: 'Arial', sans-serif;
            background: url("background.webp") no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        header {
            background: rgba(128, 0, 128, 0.9);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }
        .logo img {
            height: 40px;
            width: auto;
            border-radius: 50%;
            display: block;
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
        h2 {
            text-align: center;
            color: white;
            margin-top: 2rem;
        }
        .events-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 20px;
            flex-grow: 1;
        }
        .event-card {
            background: linear-gradient(rgba(128, 0, 128, 0.8), rgba(238, 130, 238, 0.8)), url("background.webp") no-repeat center center;
            background-size: cover;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 300px;
            margin: 15px;
            padding: 15px;
            transition: transform 0.3s ease;
            opacity: 0.92;
            color: white;
        }
        .event-card:hover {
            transform: translateY(-5px);
        }
        .event-details h3 {
            color: #fff;
            margin: 0;
            font-size: 1.5em;
        }
        .event-details p {
            font-size: 1em;
            margin: 5px 0;
        }
        .event-actions {
            margin-top: 15px;
            text-align: center;
        }
        .edit-btn {
            display: inline-block;
            text-decoration: none;
            color: white;
            padding: 10px 20px;
            margin: 5px;
            border-radius: 4px;
            background-color: #8A2BE2;
            transition: background-color 0.3s ease;
        }
        .edit-btn:hover {
            background-color: #6A1BB2;
        }
        .add-event-container {
            text-align: center;
            margin: 20px;
        }
        .add-event-btn {
            display: inline-block;
            text-decoration: none;
            color: white;
            padding: 10px 20px;
            margin: 5px;
            border-radius: 4px;
            background-color: #8B4513;
            transition: background-color 0.3s ease;
        }
        .add-event-btn:hover {
            background-color: #6B3503;
        }
        .error-message {
            color: #ff4444;
            font-weight: bold;
            text-align: center;
            margin: 20px;
        }
        .attendees-list {
            margin-top: 10px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
        }
        .attendees-list h4 {
            margin: 0 0 10px 0;
            color: #ffd700;
        }
        .attendees-list p {
            margin: 5px 0;
        }
        footer {
            background: rgba(128, 0, 128, 0.9);
            color: white;
            padding: 2rem;
            text-align: center;
            margin-top: auto;
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
        @media (max-width: 768px) {
            .nav-menu {
                display: none;
                flex-direction: column;
                position: absolute;
                top: 60px;
                left: 0;
                right: 0;
                background: rgba(128, 0, 128, 0.95);
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
            .event-card {
                width: 90%;
                margin: 15px auto;
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
                <li><a href="index.php#home">Home</a></li>
                <li><a href="index.php#about">About</a></li>
                <li><a href="index.php#contact">Contact</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
            <div class="hamburger">&#9776;</div>
        </nav>
    </header>

    <h2>My Upcoming Events</h2>
    <div class="add-event-container">
        <a href="add_event.php" class="add-event-btn">Add New Event</a>
    </div>

    <?php
    if (isset($error_message)) {
        echo "<div class='error-message'>$error_message</div>";
    } elseif (isset($events_result) && $events_result->num_rows > 0) {
        echo "<div class='events-container'>";
        while ($event = $events_result->fetch_assoc()) {
            $event_name = htmlspecialchars($event['event_name']);
            $event_date = htmlspecialchars($event['event_date']);
            $event_time = htmlspecialchars($event['event_time']);
            $event_id = htmlspecialchars($event['event_id']);
            $venue = htmlspecialchars($event['venue']);

            echo "<div class='event-card'>
                    <div class='event-details'>
                        <h3>$event_name</h3>
                        <p><strong>Date:</strong> $event_date</p>
                        <p><strong>Time:</strong> $event_time</p>
                        <p><strong>Venue:</strong> $venue</p>
                    </div>";

            // Fetch and display attendees
            $attendees_stmt->bind_param("i", $event_id);
            if ($attendees_stmt->execute()) {
                $attendees_result = $attendees_stmt->get_result();
                echo "Debug: Number of attendees for event_id $event_id = " . $attendees_result->num_rows . "<br>";

                echo "<div class='attendees-list'>
                        <h4>Attendees:</h4>";
                if ($attendees_result->num_rows > 0) {
                    while ($attendee = $attendees_result->fetch_assoc()) {
                        $attendee_name = htmlspecialchars($attendee['name']);
                        $attendee_email = htmlspecialchars($attendee['email']);
                        $attendee_mobile = htmlspecialchars($attendee['mobile_no']);
                        echo "<p>$attendee_name - $attendee_email - $attendee_mobile</p>";
                    }
                } else {
                    echo "<p>No attendees yet.</p>";
                }
                echo "</div>";
            } else {
                echo "<div class='attendees-list'><p>Error fetching attendees: " . $attendees_stmt->error . "</p></div>";
            }

            echo "<div class='event-actions'>
                    <a href='edit_event.php?event_id=$event_id' class='edit-btn'>Edit</a>
                  </div>
                  </div>";
        }
        echo "</div>";
        $attendees_stmt->close();
        $stmt->close();
    } else {
        echo "<div class='error-message'>No upcoming events found.</div>";
    }
    ?>

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