<?php
// Start session
session_start();

// Check if the user is logged in, otherwise redirect to login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Include database connection file (replace with your actual DB connection details)
include __DIR__ . '/../includes/db.php';

// Fetch announcements from the database
$sql = "SELECT a.id, a.announcement_text, a.created_at, u.username 
        FROM announcements a 
        JOIN users u ON a.user_id = u.id
        ORDER BY a.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Announcements - TrioTrek</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        /* Body Styling */
        body {
            background: #000000;
            color: #fff;
            font-family: 'Poppins', sans-serif;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #333;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #fff;
            font-size: 1.8rem;
            margin-bottom: 20px;
        }

        .announcement {
            background: #444;
            margin: 15px 0;
            padding: 20px;
            border-radius: 10px;
        }

        .announcement h3 {
            color: #ff6f61;
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        .announcement p {
            color: #ccc;
            font-size: 1rem;
        }

        .announcement .info {
            font-size: 0.9rem;
            color: #aaa;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Announcements</h2>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="announcement">';
                echo '<h3>' . htmlspecialchars($row['announcement_text']) . '</h3>';
                echo '<p class="info">Sent by: ' . htmlspecialchars($row['username']) . ' | Date: ' . htmlspecialchars($row['created_at']) . '</p>';
                echo '</div>';
            }
        } else {
            echo '<p>No announcements found.</p>';
        }
        ?>
    </div>

</body>
</html>
