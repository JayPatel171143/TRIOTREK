<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'teacher') {
    header("Location: ../login.php");
    exit;
}
include __DIR__ . '/../includes/db.php';

// Get the project ID from the URL
$project_id = isset($_GET['id']) ? $_GET['id'] : null;

if ($project_id) {
    // Fetch project details from 'ideas' table
    $sql = "SELECT * FROM ideas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $project_result = $stmt->get_result();
    
    if ($project_result->num_rows > 0) {
        $project = $project_result->fetch_assoc();
    } else {
        die("Project not found.");
    }
    
    // Fetch unique group members for this project
    $sql_members = "SELECT DISTINCT u.username 
                    FROM users u
                    JOIN user_groups ug ON u.id = ug.user_id
                    WHERE ug.group_id = ?";
    $stmt_members = $conn->prepare($sql_members);
    $stmt_members->bind_param("i", $project['group_id']);
    $stmt_members->execute();
    $members_result = $stmt_members->get_result();
    $members = [];
    while ($row = $members_result->fetch_assoc()) {
        $members[] = $row['username'];
    }
} else {
    die("Invalid project ID.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Project Details</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background: #121212; 
            color: #ddd; 
            margin: 0;
            padding: 0;
        }

        .header {
            background: #1f1f1f;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            margin: 0;
            color: #ff6f61;
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
        }

        .menu a:hover {
            background: #e55b50;
        }

        .container { 
            max-width: 900px; 
            margin: 50px auto; 
            padding: 20px; 
            background: #1f1f1f; 
            border-radius: 10px; 
        }

        h2 { 
            text-align: center;
            color: #fff; 
        }

        .project-details { 
            background: #333; 
            padding: 20px; 
            border-radius: 10px; 
        }

        .project-details p { 
            margin: 10px 0; 
        }

        .project-details h3 {
            color: #ff6f61; 
        }

        .download-btn {
            display: block; 
            margin-top: 10px; 
            padding: 10px; 
            background: #ff6f61; 
            color: white; 
            text-align: center; 
            border-radius: 5px; 
            text-decoration: none; 
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Project Details</h1>
    <div class="menu">
        <a href="teacher_dashboard.php">Dashboard</a>
        <a href="../logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <h2>Project Details</h2>

    <div class="project-details">
        <h3><?php echo htmlspecialchars($project['title']); ?></h3>
        <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($project['description'])); ?></p>
        <p><strong>Submitted At:</strong> <?php echo htmlspecialchars($project['submitted_at']); ?></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($project['status']); ?></p>

        <p><strong>Group Members:</strong> <?php echo implode(', ', array_unique($members)) ?: "No members assigned."; ?></p>

        <?php 
        if (!empty($project['file'])): 
            $file_path = "uploads/" . basename($project['file']);
            if (file_exists($file_path)): ?>
                <a class="download-btn" href="../download.php?file=<?php echo urlencode($project['file']); ?>">Download File</a>
            <?php else: ?>
                <p style="color: red;">File not found on server.</p>
            <?php endif; ?>
        <?php else: ?>
            <p>No file uploaded.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
