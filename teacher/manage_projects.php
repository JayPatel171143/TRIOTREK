<?php 
session_start();
require '../db.php'; // Database connection

// Check if the user is logged in as a teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../login.php");
    exit;
}

// Fetch projects submitted by students along with group names
$sql = "SELECT ideas.id, ideas.title, ideas.description, ideas.status, ideas.submitted_at, groups.name AS group_name 
        FROM ideas 
        JOIN groups ON ideas.group_id = groups.id 
        ORDER BY ideas.submitted_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Projects - TrioTrek</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #121212;
            color: #ddd;
            margin: 0;
            padding: 0;
        }

        .menu {
            background: #1c1c1c;
            padding: 15px 40px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        .menu a {
            margin: 0 15px;
            text-decoration: none;
            color: #bbb;
        }

        .menu a:hover {
            color: #ff6f61;
        }

        .container {
            margin: 50px auto;
            padding: 20px;
            max-width: 1000px;
            background: #1f1f1f;
            border-radius: 10px;
        }

        h2 {
            text-align: center;
            color: #fff;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #555;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background: #333;
            color: #fff;
        }

        td {
            background: #222;
        }

        tr:hover {
            background: #444;
        }

        a {
            text-decoration: none;
            color: #ff6f61;
        }

        a:hover {
            color: #fff;
        }
    </style>
</head>
<body>

<div class="menu">
    <a href="teacher_dashboard.php">Dashboard</a>
    <a href="../logout.php" class="logout-container">Logout</a>
</div>

<div class="container">
    <h2>Manage Student Projects</h2>

    <table border="1">
        <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Status</th>
            <th>Submitted At</th>
            <th>Group</th> <!-- New column for group name -->
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
                <td><?= $row['submitted_at'] ?></td>
                <td><?= htmlspecialchars($row['group_name']) ?></td> <!-- Display the group name -->
                <td><a href="action_on_project.php?id=<?= $row['id'] ?>">Take Action</a></td>
            </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>
