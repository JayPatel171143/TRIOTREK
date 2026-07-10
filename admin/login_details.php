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

// Fetch all users from the database using prepared statements
$stmt = $conn->prepare("SELECT id, username, email, role, created_at FROM users");
$stmt->execute();
$result = $stmt->get_result();

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Details - TrioTrek</title>
    
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
            background: #000000;
            color: #fff;
        }

        /* Header with Menu */
        .header {
            background: #222;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            margin: 0;
            color: #ff6f61;
            font-size: 1.8rem;
        }

        .menu {
            display: flex;
            gap: 15px;
        }

        .menu a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            background: #ff6f61;
            border-radius: 5px;
            font-size: 1rem;
        }

        .menu a:hover {
            background: #e55b50;
        }

        /* Table Styling */
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            border: 1px solid #fff;
            text-align: center;
        }

        th {
            background-color: #ff6f61;
            color: #fff;
        }

        td {
            background-color: #333;
        }

        /* Footer */
        footer {
            text-align: center;
            padding: 20px;
            margin-top: 40px;
            color: #bbb;
            font-size: 1rem;
        }
    </style>
</head>
<body>

<!-- Header Section -->
<div class="header">
    <h1>Login Details</h1>
    <div class="menu">
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="../logout.php">Logout</a>
    </div>
</div>

<!-- User Login Details Table -->
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Registration Date</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['username']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['role']; ?></td>
                    <td>
                        <?php echo isset($row['created_at']) ? date("F j, Y, g:i a", strtotime($row['created_at'])) : 'Not available'; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">No users found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- Footer -->
<footer>
    <p>© 2025 TrioTrek</p>
</footer>

</body>
</html>
