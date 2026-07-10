<?php
// Start session
session_start();

// Include database connection
include('../db.php');

// Redirect to login if the user is not an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// Check if an announcement ID is provided
if (!isset($_GET['id'])) {
    die("Invalid request.");
}

$announcementId = $_GET['id'];

// Fetch the existing announcement details
$stmt = $conn->prepare("SELECT id, announcement_text FROM announcements WHERE id = ?");
$stmt->bind_param("i", $announcementId);
$stmt->execute();
$result = $stmt->get_result();

// Check if the announcement exists
if ($result->num_rows === 0) {
    die("Announcement not found.");
}

$announcement = $result->fetch_assoc();

// Handle form submission to update announcement
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $updatedText = $_POST['announcement_text'];

    $updateStmt = $conn->prepare("UPDATE announcements SET announcement_text = ? WHERE id = ?");
    $updateStmt->bind_param("si", $updatedText, $announcementId);

    if ($updateStmt->execute()) {
        header("Location: manage_announcements.php");
        exit;
    } else {
        echo "Error updating announcement.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Announcement - TrioTrek</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #000; color: #fff; text-align: center; }
        
        /* Menu Bar */
        .menu-bar { display: flex; justify-content: flex-end; padding: 15px; background: #222; }
        .menu-bar a { color: white; margin-left: 20px; text-decoration: none; padding: 10px 15px; background: #ff6f61; border-radius: 5px; }
        .menu-bar a:hover { background: #e64c3c; }

        /* Form Container */
        .container {
            max-width: 600px;
            margin: 50px auto; /* Added margin to separate from menu bar */
            background: #222;
            padding: 25px;
            border-radius: 10px;
            text-align: left;
        }

        h2 { text-align: center; margin-bottom: 20px; }

        textarea {
            width: 100%; /* Full width */
            height: 120px;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background: #333;
            color: white;
            resize: vertical;
        }

        button {
            width: 100%; /* Full width */
            padding: 12px;
            border: none;
            background: #ff6f61;
            color: white;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            margin-top: 15px;
        }

        button:hover { background: #e64c3c; }
    </style>
</head>
<body>

    <!-- Menu Bar -->
    <div class="menu-bar">
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="../logout.php">Logout</a>
    </div>

    <!-- Form Container -->
    <div class="container">
        <h2>Edit Announcement</h2>
        <form method="post">
            <label for="announcement_text">Announcement:</label><br>
            <textarea id="announcement_text" name="announcement_text" required><?php echo htmlspecialchars($announcement['announcement_text']); ?></textarea><br>
            <button type="submit">Update Announcement</button>
        </form>
    </div>

</body>
</html>
