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

// Validate comment ID
if (!isset($_GET['id'])) {
    die("Invalid request.");
}

$commentId = $_GET['id'];

// Fetch existing comment details
$stmt = $conn->prepare("SELECT comment FROM project_comments WHERE id = ?");
$stmt->bind_param("i", $commentId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Comment not found.");
}

$comment = $result->fetch_assoc();

// Handle form submission to update comment
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $updatedComment = $_POST['comment'];

    $updateStmt = $conn->prepare("UPDATE project_comments SET comment = ? WHERE id = ?");
    $updateStmt->bind_param("si", $updatedComment, $commentId);

    if ($updateStmt->execute()) {
        header("Location: manage_project_comments.php");
        exit;
    } else {
        echo "Error updating comment.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Project Comment - TrioTrek</title>
    <style>
        body { background: #000; color: #fff; text-align: center; font-family: Arial, sans-serif; padding: 20px; }
        .container { max-width: 600px; margin: auto; background: #222; padding: 20px; border-radius: 10px; }
        h2 { margin-bottom: 20px; }
        textarea { width: 100%; height: 100px; padding: 10px; margin-bottom: 15px; border: none; border-radius: 5px; background: #333; color: white; }
        button { padding: 10px 15px; border: none; background: #ff6f61; color: white; border-radius: 5px; cursor: pointer; }
        button:hover { background: #e64c3c; }
        .menu-bar { background: #222; padding: 15px; text-align: right; }
        .menu-bar a { color: white; margin-left: 20px; text-decoration: none; padding: 10px 15px; background: #ff6f61; border-radius: 5px; }
    </style>
</head>
<body>

    <div class="menu-bar">
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="../logout.php">Logout</a>
    </div>

    <div class="container">
        <h2>Edit Project Comment</h2>
        <form method="post">
            <textarea name="comment" required><?php echo htmlspecialchars($comment['comment']); ?></textarea><br>
            <button type="submit">Update Comment</button>
        </form>
    </div>

</body>
</html>
