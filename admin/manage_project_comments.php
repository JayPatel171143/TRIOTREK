<?php
// Start session
session_start();

// Include database connection
include(__DIR__ . '/../includes/db.php');

// Redirect to login if not admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// Handle delete request
if (isset($_GET['delete'])) {
    $commentId = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM project_comments WHERE id = ?");
    $stmt->bind_param("i", $commentId);
    if ($stmt->execute()) {
        header("Location: manage_project_comments.php");
        exit;
    } else {
        echo "Error deleting comment.";
    }
}

// Fetch all project comments with teacher names
$query = "
    SELECT pc.id, pc.project_id, pc.comment, pc.created_at, 
           u.username AS teacher_name 
    FROM project_comments pc
    JOIN users u ON pc.teacher_id = u.id
    WHERE u.role = 'teacher'
    ORDER BY pc.created_at DESC
";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Project Comments - TrioTrek</title>
    <style>
        body { background: #000; color: #fff; text-align: center; font-family: Arial, sans-serif; }
        .menu-bar { background: #222; padding: 15px; text-align: right; }
        .menu-bar a { color: white; margin-left: 20px; text-decoration: none; padding: 10px 15px; background: #ff6f61; border-radius: 5px; }
        table { width: 80%; margin: 20px auto; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #fff; }
        th { background: #222; }
        .edit-btn { background: blue; color: white; padding: 5px 10px; text-decoration: none; border-radius: 5px; }
        .delete-btn { background: red; color: white; padding: 5px 10px; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>

    <div class="menu-bar">
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="../logout.php">Logout</a>
    </div>

    <h1>Manage Project Comments</h1>

    <table>
        <tr>
            <th>ID</th>
            <th>Project ID</th>
            <th>Teacher Name</th>
            <th>Comment</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['project_id']; ?></td>
            <td><?php echo htmlspecialchars($row['teacher_name']); ?></td>
            <td><?php echo htmlspecialchars($row['comment']); ?></td>
            <td><?php echo $row['created_at']; ?></td>
            <td>
                <a href="edit_project_comment.php?id=<?php echo $row['id']; ?>" class="edit-btn">Edit</a>
                <a href="?delete=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

</body>
</html>
