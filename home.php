<?php
include 'db.php'; // Database connection

// Handle Contact Form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';

    if (!empty($name) && !empty($email) && !empty($message)) {
        $sql = "INSERT INTO contact_queries (name, email, message) VALUES (?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sss", $name, $email, $message);

        if ($stmt->execute()) {
            echo "<script>
                    alert('Your query has been accepted and will be resolved soon!');
                    document.querySelector('.contact-form').reset();
                    document.getElementById('queryModal').style.display = 'none';
                  </script>";
        } else {
            echo "<script>alert('Error submitting your query: " . addslashes($mysqli->error) . "');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Please fill in all fields in the contact form.');</script>";
    }
}

// Handle Event Form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['fullName'])) {
    $fullName = $_POST['fullName'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $eventType = $_POST['eventType'] ?? '';
    $estimatePrice = $_POST['estimatePrice'] ?? '';
    $numPersons = $_POST['numPersons'] ?? '';

    $validEventTypes = ['Wedding', 'Concert', 'Corporate', 'Party', 'Other'];

    if (!empty($fullName) && !empty($email) && !empty($phone) && !empty($address) && 
        !empty($eventType) && in_array($eventType, $validEventTypes) && 
        !empty($estimatePrice) && !empty($numPersons)) {
        $sql = "INSERT INTO event_requests (full_name, email, phone, address, event_type, estimate_price, num_persons) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sssssdi", $fullName, $email, $phone, $address, $eventType, $estimatePrice, $numPersons);

        if ($stmt->execute()) {
            echo "<script>
                    alert('Your event request has been submitted successfully!');
                    document.querySelector('.event-form').reset();
                    document.getElementById('eventModal').style.display = 'none';
                  </script>";
        } else {
            echo "<script>alert('Error submitting your event request: " . addslashes($mysqli->error) . "');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Please fill in all fields correctly in the event form.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventMaster - Home</title>
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

/* Subtle overlay with parallax-like effect */
body::before {
    
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

.section {
    max-width: 900px;
    margin: 3rem auto;
    background: linear-gradient(45deg, rgba(107, 72, 255, 0.85), rgba(42, 82, 152, 0.85));
    border-radius: 12px;
    padding: 2.5rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    text-align: center;
    transition: transform 0.5s cubic-bezier(0.68, -0.55, 0.27, 1.55), opacity 0.5s ease;
    opacity: 0;
    transform: translateY(50px);
}

.section.visible {
    opacity: 1;
    transform: translateY(0);
}

.section:hover {
    transform: translateY(-10px) scale(1.02);
}

h1, h2 {
    color: #ffd700; /* Gold for headings */
    margin-bottom: 1.5rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

p {
    font-size: 1.2em;
    line-height: 1.8;
    margin: 0 0 1.5rem;
}

.cta-btn {
    display: inline-block;
    padding: 12px 28px;
    background: #ffd700;
    color: #1e3c72;
    text-decoration: none;
    border-radius: 50px;
    font-size: 1.2em;
    font-weight: 600;
    transition: all 0.4s ease;
    box-shadow: 0 5px 15px rgba(255, 215, 0, 0.4);
}

.cta-btn:hover {
    background: #ffeb3b;
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(255, 215, 0, 0.6);
}

.contact-form, .event-form {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.contact-form input,
.contact-form textarea,
.event-form input,
.event-form textarea,
.event-form select {
    width: 85%;
    padding: 12px;
    margin: 12px 0;
    border: none;
    border-radius: 8px;
    font-size: 1.1em;
    background: rgba(255, 255, 255, 0.9);
    box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.contact-form input:focus,
.contact-form textarea:focus,
.event-form input:focus,
.event-form textarea:focus,
.event-form select:focus {
    outline: none;
    box-shadow: 0 0 10px rgba(107, 72, 255, 0.7);
    transform: scale(1.02);
}

.contact-form textarea,
.event-form textarea {
    resize: vertical;
    min-height: 120px;
}

.contact-form button,
.event-form button {
    padding: 12px 28px;
    background: #ffd700;
    color: #1e3c72;
    border: none;
    border-radius: 50px;
    font-size: 1.1em;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.4s ease;
}

.contact-form button:hover,
.event-form button:hover {
    background: #ffeb3b;
    transform: scale(1.05);
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
    transform: scale(1.15) rotate(360deg);
    box-shadow: 0 10px 30px rgba(255, 235, 59, 0.7);
}

@keyframes bounceGlow {
    0%, 100% { transform: translateY(0); box-shadow: 0 6px 20px rgba(255, 215, 0, 0.5); }
    50% { transform: translateY(-10px); box-shadow: 0 10px 30px rgba(255, 215, 0, 0.8); }
}

.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    z-index: 2000;
    align-items: center;
    justify-content: center;
    animation: fadeInModal 0.5s ease-out;
}

.modal-content {
    background: linear-gradient(45deg, rgba(107, 72, 255, 0.9), rgba(42, 82, 152, 0.9));
    padding: 2.5rem;
    border-radius: 12px;
    width: 90%;
    max-width: 550px;
    max-height: 85vh;
    overflow-y: auto;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
    transform: scale(0.9);
    animation: modalPop 0.4s ease-out forwards;
   scrollbar-width:none;
}

@keyframes fadeInModal {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes modalPop {
    from { transform: scale(0.9); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

.close-btn {
    position: absolute;
    top: 15px;
    right: 15px;
    font-size: 2em;
    color: #ffd700;
    cursor: pointer;
    background: none;
    border: none;
    transition: transform 0.3s ease;
}

.close-btn:hover {
    transform: rotate(90deg);
}

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

    .section {
        margin: 2rem;
        max-width: 95%;
    }
}

@media (max-width: 480px) {
    .section {
        padding: 1.5rem;
    }

    .contact-form input,
    .contact-form textarea,
    .event-form input,
    .event-form textarea,
    .event-form select {
        width: 95%;
    }

    .fab {
        width: 60px;
        height: 60px;
        font-size: 2em;
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
                <li><a href="#home">Home</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#contact">Contact</a></li>
                <li><a href="#" id="planEventLink">Plan Event</a></li>
                <li><a href="login.php">Login</a></li>
            </ul>
            <div class="hamburger">&#9776;</div>
        </nav>
    </header>

    <section id="home" class="section">
        <h1>Welcome to EventMaster</h1>
        <p>
            Get ready to plan and enjoy epic events with EventMaster! Whether you’re the mastermind behind the next big bash or just looking to join the fun, we make it all happen. Organizers, we’ve got your back with simple tools to set up, sell tickets, and spread the word—zero stress! Attendees, dive into a lineup of awesome events, from chill hangouts to blowout parties, all picked just for you. With EventMaster, it’s less hassle, more vibes, and memories you’ll talk about forever.
        </p>
        <a href="registration.php" class="cta-btn">Get Started</a>
    </section>

    <section id="about" class="section">
        <h2>About Us</h2>
        <p>
            EventMaster is your one-stop platform for event management, designed to make every occasion extraordinary. Whether you’re planning a dreamy wedding, an electrifying concert, a corporate gathering, or a cozy family reunion, we provide intuitive tools to create, manage, and enjoy events with ease. Organizers can streamline everything—scheduling, ticketing, guest lists, and promotion—while attendees can explore and book unforgettable experiences tailored to their passions. Our mission is to bring people together, turning moments into lasting memories through seamless planning and vibrant celebrations. 
        </p>
    </section>

    <section id="contact" class="section">
        <strong>Contact Us:</strong><br>
        <strong>Address:</strong> Happynest PG, Law Gate, Maheru, 144411, Punjab, India<br>
        <strong>Email:</strong> gk7079020@gmail.com<br>
        <strong>Phone:</strong> +91 7079050558
    </section>

    <section id="privacy" class="section">
        <h2>Privacy Policy</h2>
        <p>
            At EventMaster, we value your privacy and are committed to protecting your personal information. This Privacy Policy outlines how we collect, use, and safeguard your data when you use our platform. We collect information such as your name, email, and event preferences to provide personalized services and improve your experience. Your data is securely stored and only shared with event organizers when necessary for ticket bookings or event management. We do not sell your information to third parties. You can update or delete your data by contacting us at <a href="mailto:gk7079020@gmail.com" style="color:rgb(75, 99, 98);">gk7079020@gmail.com</a>. By using EventMaster, you agree to this policy, which may be updated periodically—check back for the latest version.
        </p>
    </section>

    <section id="terms" class="section">
        <h2>Terms & Conditions</h2>
        <p>
            Welcome to EventMaster! By using our platform, you agree to these Terms & Conditions. Users must register with accurate information and are responsible for maintaining account security. Organizers agree to provide truthful event details and honor ticket commitments, while attendees must follow event rules and payment terms. EventMaster is not liable for event cancellations, changes, or disputes between users—such issues should be resolved directly with organizers. We reserve the right to suspend accounts for misuse or violation of terms. Refunds are handled per event-specific policies. For questions, contact us at <a href="mailto:gk7079020@gmail.com" style="color: rgb(75, 99, 98)">gk7079020@gmail.com</a>. These terms may be updated, so review them regularly.
        </p>
    </section>

    <div class="fab" id="fab">?</div>

    <!-- Query Modal -->
    <div class="modal" id="queryModal">
        <div class="modal-content">
            <button class="close-btn" id="closeQueryModal">×</button>
            <h2>Query?</h2>
            <p>Have questions or need assistance? Reach out to us!</p>
            <form class="contact-form" method="POST" action="">
                <input type="text" name="name" placeholder="Your Name" required>
                <input type="email" name="email" placeholder="Your Email" required>
                <textarea name="message" placeholder="Your Message" required rows="5"></textarea>
                <button type="submit">Send Message</button>
            </form>
        </div>
    </div>

    <!-- Event Modal -->
    <div class="modal" id="eventModal">
        <div class="modal-content">
            <button class="close-btn" id="closeEventModal">×</button>
            <h2>Plan an Event</h2>
            <form class="event-form" method="POST" action="">
                <!-- <label for="fullName">Full Name</label> -->
                <input type="text" id="fullName" name="fullName" placeholder="Your Full Name" required>

                <!-- <label for="email">Email</label> -->
                <input type="email" id="email" name="email" placeholder="Your Email" required>

                <!-- <label for="phone">Phone</label> -->
                <input type="tel" id="phone" name="phone" placeholder="Your Phone Number" required>

                <!-- <label for="address">Address</label> -->
                <textarea id="address" name="address" placeholder="Your Address" required></textarea>

                <!-- <label for="eventType">Event Type</label> -->
                <select id="eventType" name="eventType" required>
                    <option value="">Select Event Type</option>
                    <option value="Wedding">Wedding</option>
                    <option value="Concert">Concert</option>
                    <option value="Corporate">Corporate</option>
                    <option value="Party">Party</option>
                    <option value="Other">Other</option>
                </select>

                <!-- <label for="estimatePrice">Estimated Price (₹)</label> -->
                <input type="number" id="estimatePrice" name="estimatePrice" placeholder="Estimated Price" min="0" required>

                <!-- <label for="numPersons">Number of Persons</label> -->
                <input type="number" id="numPersons" name="numPersons" placeholder="Number of Persons" min="1" required>

                <button type="submit">Submit Event Request</button>
            </form>
        </div>
    </div>

    <footer>
        <div class="footer-content">
            <p>&copy; 2025 EventMaster. All rights reserved.</p>
            <div class="footer-links">
                <a href="#privacy">Privacy Policy</a>
                <a href="#terms">Terms & Conditions</a>
                <a href="#contact">Contact Us</a>
            </div>
        </div>
    </footer>

    <script>
        const hamburger = document.querySelector('.hamburger');
        const navMenu = document.querySelector('.nav-menu');
        const fab = document.getElementById('fab');
        const queryModal = document.getElementById('queryModal');
        const closeQueryModal = document.getElementById('closeQueryModal');
        const planEventLink = document.getElementById('planEventLink');
        const eventModal = document.getElementById('eventModal');
        const closeEventModal = document.getElementById('closeEventModal');

        // Hamburger menu
        hamburger.addEventListener('click', () => {
            navMenu.classList.toggle('active');
        });

        // FAB opens Query Modal
        fab.addEventListener('click', () => {
            queryModal.style.display = 'flex';
            fab.style.animation = 'none';
        });

        // Plan Event link opens Event Modal
        planEventLink.addEventListener('click', (e) => {
            e.preventDefault();
            eventModal.style.display = 'flex';
        });

        // Close Query Modal
        closeQueryModal.addEventListener('click', () => {
            queryModal.style.display = 'none';
        });

        // Close Event Modal
        closeEventModal.addEventListener('click', () => {
            eventModal.style.display = 'none';
        });

        // Close modals when clicking outside
        window.addEventListener('click', (event) => {
            if (event.target === queryModal) {
                queryModal.style.display = 'none';
            }
            if (event.target === eventModal) {
                eventModal.style.display = 'none';
            }
        });

        // Stop FAB animations after first interaction
        fab.addEventListener('mouseover', () => {
            fab.style.animation = 'none';
        }, { once: true });

        
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