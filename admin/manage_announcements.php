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

// Handle delete announcement request
if (isset($_GET['delete'])) {
    $announcementId = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM announcements WHERE id = ?");
    $stmt->bind_param("i", $announcementId);
    if ($stmt->execute()) {
        header("Location: manage_announcements.php");
        exit;
    } else {
        echo "Error deleting announcement.";
    }
}

// Fetch all announcements from the database
$stmt = $conn->prepare("SELECT id, user_id, created_at, announcement_text FROM announcements");
$stmt->execute();
$result = $stmt->get_result();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Announcements - TrioTrek</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #000; color: #fff; text-align: center; }
        
        /* Navigation */
        .menu-bar { display: flex; justify-content: flex-end; padding: 15px; background: #222; }
        .menu-bar a { color: white; margin-left: 20px; text-decoration: none; padding: 10px 15px; background: #ff6f61; border-radius: 5px; }

        /* Table */
        table { width: 80%; margin: 20px auto; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #fff; }
        th { background: #222; }

        /* Buttons */
        .btn-edit {
            display: inline-block;
            padding: 8px 12px;
            background: #007bff;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            transition: 0.3s;
        }
        .btn-edit:hover { background: #0056b3; }

        .btn-delete {
            display: inline-block;
            padding: 8px 12px;
            background: #dc3545;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            transition: 0.3s;
        }
        .btn-delete:hover { background: #b02a37; }

        /* Back Button */
        .btn-back {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #ff6f61;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn-back:hover { background: #e64c3c; }

    </style>
</head>
<body>

    <div class="menu-bar">
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="../logout.php">Logout</a>
    </div>

    <h1>Manage Announcements</h1>
    
    <table>
        <tr>
            <th>ID</th>
            <th>User ID</th>
            <th>Created At</th>
            <th>Announcement Text</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['user_id']; ?></td>
            <td><?php echo $row['created_at']; ?></td>
            <td><?php echo $row['announcement_text']; ?></td>
            <td>
                <a href="edit_announcement.php?id=<?php echo $row['id']; ?>" class="btn-edit">Edit</a>
                <a href="?delete=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this announcement?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <a href="admin_dashboard.php" class="btn-back">Back to Dashboard</a>

</body>
</html>
