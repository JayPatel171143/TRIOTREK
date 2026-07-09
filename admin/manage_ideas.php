<?php
// Start session
session_start();

// Include database connection
include(__DIR__ . '/../includes/db.php');

// Redirect if not admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// Handle delete request
if (isset($_GET['delete'])) {
    $ideaId = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM ideas WHERE id = ?");
    $stmt->bind_param("i", $ideaId);
    if ($stmt->execute()) {
        header("Location: manage_ideas.php");
        exit;
    } else {
        echo "Error deleting idea.";
    }
}

// Fetch all ideas
$stmt = $conn->prepare("SELECT id, user_id, group_id, title, description, submitted_at, status FROM ideas");
$stmt->execute();
$result = $stmt->get_result();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Ideas - TrioTrek</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #000;
            color: #fff;
            font-family: 'Poppins', sans-serif;
        }

        /* Menu Bar */
        .menu-bar {
            display: flex;
            justify-content: space-between;
            padding: 15px 30px;
            background: #222;
        }

        .menu-bar a {
            text-decoration: none;
            color: white;
            padding: 10px 15px;
            background: #ff6f61;
            border-radius: 5px;
            transition: background 0.3s ease;
        }

        .menu-bar a:hover {
            background: #e64c3c;
        }

        /* Page Content */
        .container {
            width: 80%;
            margin: auto;
            text-align: center;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            border: 1px solid #fff;
        }

        th {
            background-color: #ff6f61;
            color: white;
        }

        td {
            background-color: #333;
        }

        /* Edit & Delete Buttons */
        .action-buttons a {
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
            color: white;
        }

        .edit-btn {
            background:rgb(14, 182, 216);
        }

        .delete-btn {
            background: #ff4444;
        }

        .delete-btn:hover {
            background: #cc0000;
        }
    </style>
</head>
<body>

    <!-- Menu Bar -->
    <div class="menu-bar">
        <h1>Manage Ideas</h1>
        <div>
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <table>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Group ID</th>
                <th>Title</th>
                <th>Description</th>
                <th>Submitted At</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['user_id']; ?></td>
                <td><?php echo $row['group_id']; ?></td>
                <td><?php echo $row['title']; ?></td>
                <td><?php echo $row['description']; ?></td>
                <td><?php echo date("F j, Y, g:i a", strtotime($row['submitted_at'])); ?></td>
                <td><?php echo $row['status']; ?></td>
                <td class="action-buttons">
                    <a href="edit_idea.php?id=<?php echo $row['id']; ?>" class="edit-btn">Edit</a> |
                    <a href="?delete=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

</body>
</html>
