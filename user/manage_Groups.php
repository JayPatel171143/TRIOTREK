<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id']; // Get the logged-in user ID

// Database connection details
$host = 'localhost';
$dbname = 'trio_trek';
$username_db = 'root';
$password_db = '';

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username_db, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch groups where the user is a member
    $stmt = $pdo->prepare("
        SELECT DISTINCT g.id AS group_id, g.name AS group_name
        FROM groups g
        JOIN user_groups ug ON g.id = ug.group_id
        WHERE ug.user_id = :user_id
    ");
    $stmt->execute(['user_id' => $user_id]);
    $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch members for each group
    $group_members = [];
    foreach ($groups as $group) {
        $stmt = $pdo->prepare("
            SELECT DISTINCT u.id AS member_id, u.username AS member_name
            FROM users u
            JOIN user_groups ug ON u.id = ug.user_id
            WHERE ug.group_id = :group_id
        ");
        $stmt->execute(['group_id' => $group['group_id']]);
        $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Store the group and its members
        $group_members[$group['group_id']] = [
            'group_name' => $group['group_name'],
            'members' => $members
        ];
    }

} catch (PDOException $e) {
    echo "Database Connection Failed: " . $e->getMessage();
    exit;
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
            padding: 20px;
            max-width: 900px;
            background: #1f1f1f;
            border-radius: 10px;
        }

        h3 {
            text-align: center;
            color: #fff;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            background: #222;
        }

        table, th, td {
            border: 1px solid #555;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background: #333;
            color: #fff;
        }

        td {
            background: #222;
        }

        tr:hover {
            background: #444;
        }

        .group-table {
            margin-bottom: 30px;
        }

        /* Styling for the "Replace" button */
        .replace-button {
            color: white;
            background-color: lightcoral;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 5px;
        }

        .replace-button:hover {
            background-color: darkred;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <h2>TrioTrek</h2>
        <div class="nav-links">
            <a href="user_dashboard.php">Dashboard</a>
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <!-- Content Section -->
    <div class="content">
        <h3>Your Groups</h3>

        <?php if (count($group_members) > 0): ?>
            <?php foreach ($group_members as $group_id => $group_data): ?>
                <div class="group-table">
                    <h3><?php echo htmlspecialchars($group_data['group_name']); ?></h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Group Members</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($group_data['members'] as $member): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($member['member_name']); ?></td>
                                    <td>
                                        <a href="manageGroupsreplace.php?group_id=<?php echo $group_id; ?>&member_id=<?php echo $member['member_id']; ?>" class="replace-button">
                                            Replace
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>You are not a member of any group.</p>
        <?php endif; ?>
    </div>

</body>
</html>
