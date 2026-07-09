<?php
// Start session to get user data
session_start();

// Check if the user is logged in, otherwise redirect to login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Include the database connection file
include(__DIR__ . '/../includes/db.php');

// Fetch the user's ID from the session
$user_id = $_SESSION['user_id'];

// Initialize the comments array and projects array
$comments = [];
$projects = [];

// Step 1: Get the group_id of the logged-in user
$sql_group = "SELECT group_id FROM user_groups WHERE user_id = ?";
$stmt_group = $conn->prepare($sql_group);
$stmt_group->bind_param("i", $user_id);
$stmt_group->execute();
$result_group = $stmt_group->get_result();

if ($result_group->num_rows === 0) {
    echo "⚠️ You are not part of any group.";
    exit;
}

// Fetch the group ID
$row_group = $result_group->fetch_assoc();
$group_id = $row_group['group_id'];
$stmt_group->close();

// Step 2: Fetch all project ideas submitted by this group
$sql_ideas = "SELECT id, title FROM ideas WHERE group_id = ?";
$stmt_ideas = $conn->prepare($sql_ideas);
$stmt_ideas->bind_param("i", $group_id);
$stmt_ideas->execute();
$result_ideas = $stmt_ideas->get_result();

if ($result_ideas->num_rows === 0) {
    echo "⚠️ No ideas found for your group.";
    exit;
}

// Store project details
while ($row = $result_ideas->fetch_assoc()) {
    $projects[$row['id']] = $row['title'];
}
$stmt_ideas->close();

// Step 3: Fetch comments for all ideas of this group
if (!empty($projects)) {
    $project_ids = implode(",", array_keys($projects));

    $sql_comments = "SELECT pc.project_id, pc.comment, pc.created_at, u.username AS teacher_name 
                     FROM project_comments pc
                     JOIN users u ON pc.teacher_id = u.id
                     WHERE pc.project_id IN ($project_ids)
                     ORDER BY pc.created_at DESC";

    $result_comments = $conn->query($sql_comments);

    if ($result_comments->num_rows === 0) {
        echo "⚠️ No comments found for your group's projects.";
        exit;
    }

    while ($row = $result_comments->fetch_assoc()) {
        $comments[] = $row;
    }
} else {
    echo "⚠️ No projects found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Comments - TrioTrek</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #000000; color: #fff; }
        .navbar { background: #1c1c1c; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h2 { color: #fff; font-weight: 600; }
        .nav-links a { margin: 0 15px; text-decoration: none; color: #bbb; transition: color 0.3s ease; }
        .nav-links a:hover { color: #ff6f61; }
        .comments-section { max-width: 1200px; margin: 30px auto; padding: 20px; }
        .comments-section h1 { font-size: 2rem; color: #fff; font-weight: 600; }
        .comments-section ul { list-style: none; padding: 0; }
        .comments-section ul li { background: #333; padding: 15px; margin-bottom: 10px; border-radius: 8px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); color: #ccc; }
        .comments-section ul li strong { color: #ff6f61; }
        footer { text-align: center; padding: 20px; margin-top: 40px; color: #bbb; }
    </style>
</head>
<body>
    <div class="navbar">
        <h2>TrioTrek</h2>
        <div class="nav-links">
            <a href="user_dashboard.php">Dashboard</a>
            <a href="../logout.php">Logout</a>
        </div>
    </div>
    <div class="comments-section">
        <h1>Your Group's Project Comments</h1>
        <?php if (count($comments) > 0): ?>
            <ul>
                <?php foreach ($comments as $comment): ?>
                    <li>
                        <strong>Project: </strong><?php echo htmlspecialchars($projects[$comment['project_id']]); ?><br>
                        <strong>Teacher: </strong><?php echo htmlspecialchars($comment['teacher_name']); ?><br>
                        <strong>Comment: </strong><?php echo htmlspecialchars($comment['comment']); ?><br>
                        <small><em>Posted on: <?php echo htmlspecialchars($comment['created_at']); ?></em></small>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No comments yet.</p>
        <?php endif; ?>
    </div>
    <footer>
        <p>© 2025 TrioTrek</p>
    </footer>
</body>
</html>
