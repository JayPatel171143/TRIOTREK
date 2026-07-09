<?php
// Start session
session_start();

// Include database connection
include(__DIR__ . '/../includes/db.php');

// Redirect to login if the user is not an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// Fetch users who recently changed their passwords
$stmt = $conn->prepare("SELECT id, username, email, role, password_changed_at FROM users WHERE password_changed_at IS NOT NULL ORDER BY password_changed_at DESC");
$stmt->execute();
$result = $stmt->get_result();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recent Password Changes - TrioTrek</title>
    <style>
        body { font-family: Arial, sans-serif; background: #000; color: #fff; text-align: center; }
        table { width: 80%; margin: 20px auto; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #fff; }
        th { background: #222; }
        a { color: #ff6f61; text-decoration: none; }
    </style>
</head>
<body>
    <h1>Recent Password Changes</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Password Changed At</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['username']; ?></td>
            <td><?php echo $row['email']; ?></td>
            <td><?php echo $row['role']; ?></td>
            <td><?php echo $row['password_changed_at']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <a href="admin_dashboard.php">Back to Dashboard</a>
</body>
</html>
