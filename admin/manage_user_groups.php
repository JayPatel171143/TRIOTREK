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

// Fetch groups and users
$query = "
    SELECT g.name AS group_name, u.id AS user_id, u.username 
    FROM user_groups ug
    JOIN groups g ON ug.group_id = g.id
    JOIN users u ON ug.user_id = u.id
    ORDER BY g.name, u.username
";
$result = $conn->query($query);

// Organize data while removing duplicate users
$groupedUsers = [];
$seenUsers = []; // Store user IDs to prevent duplicates

foreach ($result as $row) {
    if (!isset($seenUsers[$row['user_id']])) {
        $groupedUsers[$row['group_name']][] = $row; // Add only unique users
        $seenUsers[$row['user_id']] = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage User Groups - TrioTrek</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #121212; color: #fff; text-align: center; padding: 20px; }

        .menu-bar { display: flex; justify-content: flex-end; padding: 15px; background: #222; }
        .menu-bar a { color: white; margin-left: 20px; text-decoration: none; padding: 10px 15px; background: #ff6f61; border-radius: 5px; transition: 0.3s; }
        .menu-bar a:hover { background: #e05b50; }

        h1 { margin: 20px 0; font-size: 24px; }

        table { width: 90%; margin: 20px auto; border-collapse: collapse; box-shadow: 0px 0px 15px rgba(255, 255, 255, 0.1); }
        th, td { padding: 12px; border: 1px solid #fff; text-align: center; }
        th { background: #ff6f61; font-weight: 600; color: white; }
        td { background: #1e1e1e; color: white; }

        .edit-btn { background: #007bff; padding: 6px 12px; border-radius: 5px; color: white; text-decoration: none; font-size: 14px; transition: 0.3s; }
        .edit-btn:hover { background: #0056b3; }

        .back-btn { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #ff6f61; color: white; text-decoration: none; border-radius: 5px; transition: 0.3s; }
        .back-btn:hover { background: #e05b50; }
    </style>
</head>
<body>

    <div class="menu-bar">
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="../logout.php">Logout</a>
    </div>

    <h1>Manage User Groups</h1>

    <table>
        <tr>
            <th>Group Name</th>
            <th>User Name</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($groupedUsers as $groupName => $users): ?>
            <?php $firstRow = true; ?>
            <?php foreach ($users as $user): ?>
                <tr>
                    <?php if ($firstRow): ?>
                        <td rowspan="<?= count($users); ?>"><?php echo htmlspecialchars($groupName); ?></td>
                        <?php $firstRow = false; ?>
                    <?php endif; ?>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><a href="edit_user_group.php?id=<?php echo $user['user_id']; ?>" class="edit-btn">Edit</a></td>
                </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </table>

    <a href="admin_dashboard.php" class="back-btn">Back to Dashboard</a>

</body>
</html>
