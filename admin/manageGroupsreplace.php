<?php
session_start();

// Check if the user is logged in, otherwise redirect to login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id']; // Get logged-in user ID

// Validate request parameters
if (!isset($_GET['group_id']) || !isset($_GET['member_id'])) {
    echo "Invalid request!";
    exit;
}

$group_id = $_GET['group_id'];
$member_to_replace = $_GET['member_id'];

// Database connection details
$host = 'localhost';
$dbname = 'trio_trek';
$username_db = 'root';
$password_db = '';

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username_db, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the logged-in user is the creator of the group
    $stmt = $pdo->prepare("SELECT created_by FROM groups WHERE id = :group_id");
    $stmt->execute(['group_id' => $group_id]);
    $group = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$group || $group['created_by'] != $user_id) {
        echo "You are not authorized to replace members in this group!";
        exit;
    }

    // Fetch all students who are NOT already in the group
    $stmt = $pdo->prepare("
        SELECT id, username 
        FROM users 
        WHERE id NOT IN (SELECT user_id FROM user_groups WHERE group_id = :group_id) 
        AND role = 'student'
    ");
    $stmt->execute(['group_id' => $group_id]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch the username of the member being replaced
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = :member_id");
    $stmt->execute(['member_id' => $member_to_replace]);
    $member_name = $stmt->fetchColumn();

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Replace Member - TrioTrek</title>
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

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        select, button {
            padding: 10px;
            border-radius: 8px;
            border: none;
            margin: 10px;
        }

        button {
            background: #ff6f61;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background: #e64c3c;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <h2>TrioTrek</h2>
        <div class="nav-links">
            <a href="../user_dashboard.php">Dashboard</a>
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <!-- Content Section -->
    <div class="content">
        <h3>Replace Member in Group</h3>

        <p><center>You are about to replace <strong><?php echo htmlspecialchars($member_name); ?></strong> in the group.</center></p>

        <form method="POST" action="replace_member_action.php">
            <input type="hidden" name="group_id" value="<?php echo htmlspecialchars($group_id); ?>">
            <input type="hidden" name="member_to_replace" value="<?php echo htmlspecialchars($member_to_replace); ?>">

            <label for="new_member">Select a new member:</label>
            <select name="new_member" id="new_member" required>
                <option value="">-- Select a new member --</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?php echo htmlspecialchars($user['id']); ?>"><?php echo htmlspecialchars($user['username']); ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Replace Member</button>
        </form>
    </div>

</body>
</html>
