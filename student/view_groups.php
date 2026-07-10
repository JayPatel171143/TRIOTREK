<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Database Connection
$host = 'localhost';
$dbname = 'trio_trek';
$username_db = 'root';
$password_db = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username_db, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch the group the user is in
    $stmt = $pdo->prepare("
        SELECT g.id, g.name AS group_name 
        FROM groups g 
        JOIN user_groups ug ON g.id = ug.group_id 
        WHERE ug.user_id = :user_id
    ");
    $stmt->execute(['user_id' => $user_id]);
    $userGroup = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch group members if the user is in a group
    $groupMembers = [];
    if ($userGroup) {
        $stmt = $pdo->prepare("
            SELECT DISTINCT u.username 
            FROM users u 
            JOIN user_groups ug ON u.id = ug.user_id 
            WHERE ug.group_id = :group_id
        ");
        $stmt->execute(['group_id' => $userGroup['id']]);
        $groupMembers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TrioTrek - Your Groups</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #121212;
            color: #ddd;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background: #1c1c1c;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar h2 {
            color: #fff;
        }
        .nav-links a {
            margin: 0 15px;
            text-decoration: none;
            color: #bbb;
        }
        .nav-links a:hover {
            color: #ff6f61;
        }
        .content {
            margin: 50px auto;
            padding: 30px;
            max-width: 900px;
            background: #1f1f1f;
            border-radius: 10px;
            text-align: center;
            width: 90%;
        }
        h3 {
            font-size: 24px;
            color: #fff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            font-size: 20px;
            padding: 12px;
            border: 1px solid #777;
            text-align: left;
            color: #fff;
        }
        table th {
            background: #333;
        }
        .no-group-message {
            font-size: 20px;
            color: #ff6f61;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <h2>TrioTrek</h2>
        <div class="nav-links">
            <a href="../user_dashboard.php">Dashboard</a>
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <div class="content">
        <h3>Your Groups</h3>

        <?php if ($userGroup): ?>
            <h3><?php echo htmlspecialchars($userGroup['group_name']); ?></h3>

            <table>
                <thead>
                    <tr>
                        <th>Group Members</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($groupMembers as $member): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($member['username']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php else: ?>
            <p class="no-group-message">You are not assigned to any group.</p>
        <?php endif; ?>
    </div>

</body>
</html>
