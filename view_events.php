<?php 
session_start(); // Move session_start() to the top of the file
include 'db.php'; 

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<p style='color: #ff4444; text-align: center;'>Please log in to view your events.</p>";
    exit();
}
?>
<!DOCTYPE html> 
<html lang="en"> 
<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Upcoming Events</title> 
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
    color: #ffffff;
    width: 94.5vw;
}

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
    justify-content: space-between;
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
.events-container {
    display: flex;
    flex-wrap: nowrap;
    justify-content: flex-start;
    padding: 20px;
    overflow-x: auto;
    gap: 20px;
    flex-grow: 1;
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
    color: white;
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

.event-actions {
    margin-top: 20px;
    text-align: center;
}

.edit-btn, .delete-btn, .add-event-btn, .show-more-btn {
    display: inline-block;
    text-decoration: none;
    color: white;
    padding: 10px 20px;
    margin: 5px;
    border-radius: 50px;
    font-size: 1.1em;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.4s ease;
}

.edit-btn {
    background: linear-gradient(45deg, #ff9800, #ff5722);
}

.edit-btn:hover {
    background: linear-gradient(45deg, #ffb74d, #ff7043);
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(255, 87, 34, 0.6);
}

.delete-btn {
    background: linear-gradient(45deg, #ff9800, #ff5722);
}

.delete-btn:hover {
    background: linear-gradient(45deg, #ffb74d, #ff7043);
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(255, 87, 34, 0.6);
}

.add-event-btn {
    background: linear-gradient(45deg, #ff9800, #ff5722);
}

.add-event-btn:hover {
    background: linear-gradient(45deg, #ffb74d, #ff7043);
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(255, 87, 34, 0.6);
}

.show-more-btn {
    background: linear-gradient(45deg, #ff9800, #ff5722);
}

.show-more-btn:hover {
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

    .event-details h3 {
        font-size: 1.4em;
    }

    .event-details p {
        font-size: 0.95em;
    }

    .edit-btn, .delete-btn, .add-event-btn, .show-more-btn {
        padding: 8px 16px;
        font-size: 0.9em;
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
    <header> 
        <nav class="navbar"> 
            <a href="index.php" class="logo"> 
                <img src="logo.png" alt="EventMaster Logo"> 
            </a> 
            <ul class="nav-menu"> 
                <li><a href="index.php#home">Home</a></li> 
                <li><a href="index.php#about">About</a></li> 
                <li><a href="index.php#contact">Contact</a></li> 
                <li><a href="search_events.php">Search Events</a></li> 
                <li><a href="login.php">Logout</a></li> 
            </ul> 
            <div class="hamburger">☰</div> 
        </nav> 
    </header> 
     
<?php 
try { 
    // Modify the SQL query to filter events by the logged-in user's ID
    $user_id = $_SESSION['user_id']; // Get the logged-in user's ID from the session
    $sql = "SELECT e.*, u.name AS organizer_name 
            FROM events e 
            JOIN users u ON e.user_id = u.id 
            WHERE e.event_date >= CURDATE() 
            AND e.user_id = ? 
            ORDER BY e.event_date"; 
    $stmt = $mysqli->prepare($sql); // Use prepared statement to prevent SQL injection
    $stmt->bind_param("i", $user_id); // Bind the user_id parameter
    $stmt->execute(); 
    $con = $stmt->get_result(); 

    echo "<h2>Upcoming Events</h2>"; 
 
    echo "<div class='add-event-container'> 
            <a href='add_event.php' class='add-event-btn'>Add New Event</a> 
          </div>"; 
 
    if ($con->num_rows > 0) { 
        echo "<div class='events-container'>"; 
        while ($row = $con->fetch_assoc()) { 
            $event_name = htmlspecialchars($row['event_name']); 
            $event_date = htmlspecialchars($row['event_date']); 
            $event_time = htmlspecialchars($row['event_time']); 
            $venue = htmlspecialchars($row['venue']); 
            $organizer_name = htmlspecialchars($row['organizer_name']); 
            $ticket_price = htmlspecialchars($row['ticket_price']); 
            $available_seats = htmlspecialchars($row['available_seats']); 
            $event_type = htmlspecialchars($row['event_type']); 
            $event_id = htmlspecialchars($row['event_id']);  
 
            echo "<div class='event-card'> 
                    <div class='event-details'> 
                        <h3>$event_name</h3> 
                        <p><strong>Date:</strong> $event_date</p> 
                        <p><strong>Time:</strong> $event_time</p> 
                        <p><strong>Venue:</strong> $venue</p> 
                        <p><strong>Organizer:</strong> $organizer_name</p> 
                        <p><strong>Ticket Price:</strong> RS. $ticket_price</p> 
                        <p><strong>Available Seats:</strong> $available_seats</p> 
                        <p><strong>Event Type:</strong> $event_type</p> 
                    </div> 
                    <div class='event-actions'> 
                        <a href='edit_event.php?event_id=$event_id' class='edit-btn'>Edit</a> 
                        <a href='delete_event.php?event_id=$event_id' class='delete-btn' onclick=\"return confirm('Are you sure you want to delete this event?');\">Delete</a>"; 
 
            if ($event_type === "Wedding") { 
                echo "<a href='wedding_details.php?event_id=$event_id' class='show-more-btn'>Show More</a>"; 
            } 
 
            echo "  </div> 
                  </div>"; 
        } 
        echo "</div>"; 
    } else { 
        echo "<p style='color: white; text-align: center;'>No upcoming events found for you.</p>"; 
    } 
    $stmt->close(); // Close the prepared statement
} catch (Exception $e) { 
    echo "<p style='color: #ff4444; text-align: center;'>Error fetching events: " . $e->getMessage() . "</p>"; 
} 
?> 
 
    <footer> 
        <div class="footer-content"> 
            <p>© 2025 EventMaster. All rights reserved.</p> 
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