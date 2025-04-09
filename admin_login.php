<?php
session_start();

// Check if already logged in
if (isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_panel.php");
    exit();
}

// Hardcoded admin credentials
$admin_username = "admin";
$admin_password = "admin70790";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username === $admin_username && $password === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin_panel.php");
        exit();
    } else {
        $error_message = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
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
    justify-content: center;
    align-items: center;
}

/* Dynamic gradient background animation */
@keyframes gradientFlow {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* Form container */
.form-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    width: 100%;
}

/* Login Form */
.login-form {
    background: linear-gradient(45deg, rgba(107, 72, 255, 0.85), rgba(42, 82, 152, 0.85));
    padding: 2.5rem;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    max-width: 450px;
    width: 90%;
    opacity: 0.95;
    color: white;
    display: flex;
    flex-direction: column;
    align-items: center;
    transition: transform 0.5s cubic-bezier(0.68, -0.55, 0.27, 1.55);
}

.login-form:hover {
    transform: translateY(-5px) scale(1.02);
}

.login-form label {
    font-size: 1.2em;
    margin-bottom: 10px;
    color: #ffffff;
    display: block;
    width: 85%;
    text-align: left;
}

.login-form input {
    width: 85%;
    padding: 12px;
    margin-bottom: 15px;
    border: none;
    border-radius: 8px;
    font-size: 1.1em;
    background: rgba(255, 255, 255, 0.9);
    transition: all 0.3s ease;
}

.login-form input:focus {
    outline: none;
    box-shadow: 0 0 10px rgba(107, 72, 255, 0.7);
    transform: scale(1.02);
}

.login-form button {
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

.login-form button:hover {
    background: #ffeb3b;
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(255, 215, 0, 0.6);
}

.error-message {
    color: #ff4444;
    font-weight: bold;
    text-align: center;
    margin-bottom: 15px;
}

/* Media Query for Smaller Screens */
@media (max-width: 480px) {
    .login-form {
        padding: 1.5rem;
    }

    .login-form label, 
    .login-form input {
        width: 100%;
    }

    .login-form button {
        width: 60%;
    }
}
</style>
</head>
<body>
    <div class="form-container">
        <form class="login-form" method="POST">
            <h2>Admin Login</h2>
            <?php if (isset($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
            <button type="submit">Login</button>
        </form>
    </div>


</body>
</html>