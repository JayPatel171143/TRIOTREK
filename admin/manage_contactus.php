<?php
// Start session
session_start();

// Include database connection
include('../db.php');

// Redirect if not admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// Handle delete request
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM contact_us WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: manage_contactus.php");
        exit;
    } else {
        echo "Error deleting message.";
    }
}

// Fetch all contact messages
$stmt = $conn->prepare("SELECT * FROM contact_us ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Contact Messages - TrioTrek</title>
    <style>
        body { background: #000; color: #fff; text-align: center; font-family: Arial, sans-serif; }
        .menu-bar { background: #222; padding: 15px; text-align: right; }
        .menu-bar a { color: white; margin-left: 20px; text-decoration: none; padding: 10px 15px; background: #ff6f61; border-radius: 5px; }
        table { width: 80%; margin: 20px auto; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #fff; }
        th { background: #222; }
        .delete-btn { background: red; color: white; padding: 5px 10px; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>

    <div class="menu-bar">
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="../logout.php">Logout</a>
    </div>

    <h1>Manage Contact Messages</h1>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Subject</th>
            <th>Message</th>
            <th>Created At</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['email']; ?></td>
            <td><?php echo $row['subject']; ?></td>
            <td><?php echo $row['message']; ?></td>
            <td><?php echo $row['created_at']; ?></td>
            <td>
                <a href="?delete=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

</body>
</html>
