<?php
session_start();
include 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch data from contact_queries
$sql_contact = "SELECT * FROM contact_queries";
$result_contact = $mysqli->query($sql_contact);

// Fetch data from event_requests
$sql_event = "SELECT * FROM event_requests";
$result_event = $mysqli->query($sql_event);

// Handle logout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
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

        header {
            background: rgba(42, 82, 152, 0.9);
            width: 100%;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            border-radius: 0 0 12px 12px;
        }

        header h1 {
            margin: 0;
            font-size: 1.8em;
            font-weight: 700;
            color: #ffd700;
        }

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

        .content {
            width: 90%;
            max-width: 1200px;
            margin-top: 2rem;
        }

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
    </style>
</head>
<body>
    <header>
        <h1>Admin Panel</h1>
        <form method="post">
            <button type="submit" name="logout" class="logout-btn">Logout</button>
        </form>
    </header>

    <div class="content">
        <h2>Contact Queries</h2>
        <div class="table-container">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Created At</th>
                </tr>
                <?php while ($row = $result_contact->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo isset($row['id']) ? $row['id'] : 'N/A'; ?></td>
                        <td><?php echo isset($row['name']) ? $row['name'] : 'N/A'; ?></td>
                        <td><?php echo isset($row['email']) ? $row['email'] : 'N/A'; ?></td>
                        <td><?php echo isset($row['message']) ? $row['message'] : 'N/A'; ?></td>
                        <td><?php echo isset($row['created_at']) ? $row['created_at'] : 'N/A'; ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>

        <h2>Event Requests</h2>
        <div class="table-container">
            <table>
                <tr>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Event Type</th>
                    <th>Estimated Price</th>
                    <th>Number of Persons</th>
                    <th>Created At</th>
                </tr>
                <?php while ($row = $result_event->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['full_name'] ?? 'N/A'; ?></td>
                        <td><?php echo $row['email'] ?? 'N/A'; ?></td>
                        <td><?php echo $row['phone'] ?? 'N/A'; ?></td>
                        <td><?php echo $row['address'] ?? 'N/A'; ?></td>
                        <td><?php echo $row['event_type'] ?? 'N/A'; ?></td>
                        <td><?php echo $row['estimate_price'] ?? ($row['estimated_price'] ?? 'N/A'); ?></td>
                        <td><?php echo $row['num_persons'] ?? 'N/A'; ?></td>
                        <td><?php echo $row['created_at'] ?? 'N/A'; ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 EventMaster. All rights reserved.</p>
    </footer>
</body>
</html>

<?php
$mysqli->close();
?>
