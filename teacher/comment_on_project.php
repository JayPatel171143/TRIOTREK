<?php
session_start();
require '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['project_id'], $_POST['comment'])) {
    $project_id = $_POST['project_id'];
    $comment = trim($_POST['comment']);
    $teacher_id = $_SESSION['user_id'];

    if (!empty($comment)) {
        $stmt = $conn->prepare("INSERT INTO project_comments (project_id, teacher_id, comment, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iis", $project_id, $teacher_id, $comment);
        if ($stmt->execute()) {
            $message = "Comment added successfully!";
        } else {
            $message = "Error adding comment.";
        }
        $stmt->close();
    }
}

$sql = "SELECT id, title FROM ideas ORDER BY submitted_at DESC";
$projects = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Comment on Projects</title>
    <link rel="stylesheet" href="styles.css">
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

        form {
            display: flex;
            flex-direction: column;
            margin: 20px 0;
        }

        select, textarea {
            padding: 10px;
            margin-bottom: 20px;
            background: #333;
            color: #ddd;
            border: 1px solid #444;
            border-radius: 5px;
        }

        button {
            padding: 10px;
            background-color: #ff6f61;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #e55d53;
        }

        p {
            text-align: center;
            color: green;
        }
    </style>
</head>
<body>

<div class="menu">
    <!-- Only Dashboard and Logout aligned to the right -->
    <a href="teacher_dashboard.php">Dashboard</a>
    <a href="../logout.php" class="logout-container">Logout</a>
</div>

<div class="container">
    <h2>Comment on Projects</h2>
    
    <form method="POST">
        <label>Select Project:</label>
        <select name="project_id" required>
            <?php while ($row = $projects->fetch_assoc()) { ?>
                <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['title']) ?></option>
            <?php } ?>
        </select>

        <label>Comment:</label>
        <textarea name="comment" required></textarea>

        <button type="submit">Submit Comment</button>
    </form>

    <p><?= $message ?? '' ?></p>
</div>

</body>
</html>
