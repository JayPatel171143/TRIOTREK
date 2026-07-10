<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'teacher') {
    header("Location: ../login.php");
    exit;
}
include "../db.php";

// Fetch all the projects from the 'ideas' table
$sql = "SELECT id, title, description, submitted_at, status FROM ideas ORDER BY submitted_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>View Submitted Projects</title>
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
            justify-content: flex-end; /* Align items to the right */
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
            max-width: 900px;
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
    <!-- Dashboard and Logout buttons aligned to the right -->
    <a href="teacher_dashboard.php">Dashboard</a>
    <a href="../logout.php" class="logout-container">Logout</a>
</div>

<div class="container">
    <h2>All Submitted Projects</h2>

    <!-- Projects Table -->
    <table>
        <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Submitted At</th>
            <th>Status</th>
            <th>Action</th> <!-- Add an Action column for viewing details -->
        </tr>

        <?php 
        // Check if there are any projects
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                echo "<td>" . htmlspecialchars($row['submitted_at']) . "</td>";
                echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                echo "<td><a href='project_details.php?id=" . $row['id'] . "'>View Details</a></td>"; // Add link to view full details
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5' style='text-align:center;'>No projects submitted yet.</td></tr>";
        }
        ?>
    </table>
</div>

</body>
</html>
