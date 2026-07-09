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

    // Check if the user is already in a group
    $stmt = $pdo->prepare("
        SELECT g.name AS group_name 
        FROM groups g 
        JOIN user_groups ug ON g.id = ug.group_id 
        WHERE ug.user_id = :user_id
    ");
    $stmt->execute(['user_id' => $user_id]);
    $userGroup = $stmt->fetch(PDO::FETCH_ASSOC);

    // If the user is NOT in a group, fetch students who are also not assigned
    if (!$userGroup) {
        $stmt = $pdo->prepare("
            SELECT id, username, email 
            FROM users 
            WHERE role = 'student' 
            AND id NOT IN (SELECT user_id FROM user_groups)
        ");
        $stmt->execute();
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// CSRF Protection
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF Token");
    }

    // Prevent group creation if the user is already in a group
    if ($userGroup) {
        $_SESSION['group_creation_error'] = "You are already in the group: " . htmlspecialchars($userGroup['group_name']);
        header("Location: create_group.php");
        exit;
    }

    if (empty($_POST['group_name']) || !isset($_POST['students']) || empty($_POST['students'])) {
        $_SESSION['group_creation_error'] = "Group name and students selection are required.";
        header("Location: create_group.php");
        exit;
    }

    $group_name = trim($_POST['group_name']);
    $selected_students = $_POST['students'];

    try {
        $pdo->beginTransaction();

        // Insert group into `groups` table
        $stmt = $pdo->prepare("INSERT INTO groups (name, created_by) VALUES (:group_name, :created_by)");
        $stmt->execute(['group_name' => $group_name, 'created_by' => $user_id]);
        $group_id = $pdo->lastInsertId();

        // Insert students into `user_groups` table
        $stmt = $pdo->prepare("INSERT INTO user_groups (user_id, group_id) VALUES (:user_id, :group_id)");
        foreach ($selected_students as $student_id) {
            $stmt->execute(['user_id' => $student_id, 'group_id' => $group_id]);
        }

        // Also add the creator to the group
        $stmt->execute(['user_id' => $user_id, 'group_id' => $group_id]);

        $pdo->commit();
        $_SESSION['group_creation_success'] = "Group '$group_name' created successfully!";
        header("Location: create_group.php");
        exit;

    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['group_creation_error'] = "Error: " . $e->getMessage();
        header("Location: create_group.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TrioTrek - Create Group</title>
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

h3, label {
    font-size: 24px;
    color: #fff;
}

input[type="text"], table {
    width: 100%;
    font-size: 20px;
    padding: 15px;
    margin: 10px 0;
    border-radius: 5px;
    border: 1px solid #777;
    background: #222;
    color: #ddd;
}

table {
    border-collapse: collapse;
}

table th, table td {
    font-size: 22px;
    padding: 15px;
    border: 1px solid #777;
    text-align: left;
}

input[type="checkbox"] {
    transform: scale(1.5);
    margin: 10px;
}

.btn {
    background: #ff6f61;
    color: white;
    padding: 15px 25px;
    font-size: 20px;
    border-radius: 5px;
    text-decoration: none;
    display: inline-block;
    margin-top: 20px;
    transition: background 0.3s ease;
    border: none;
    cursor: pointer;
    width: 100%;
}

.btn:hover {
    background: #e64c3c;
}

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

    <div class="content">
        <h3>Create a New Group</h3>

        <!-- Show success or error message -->
        <?php if (isset($_SESSION['group_creation_success'])): ?>
            <p class="success-message"><?php echo $_SESSION['group_creation_success']; ?></p>
            <?php unset($_SESSION['group_creation_success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['group_creation_error'])): ?>
            <p class="error-message"><?php echo $_SESSION['group_creation_error']; ?></p>
            <?php unset($_SESSION['group_creation_error']); ?>
        <?php endif; ?>

        <?php if ($userGroup): ?>
            <p class="error-message">You are already in the group: <strong><?php echo htmlspecialchars($userGroup['group_name']); ?></strong>. You cannot create another group.</p>
        <?php else: ?>
            <form action="create_group.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <label for="group_name">Group Name:</label>
                <input type="text" name="group_name" id="group_name" required>
                
                <h3>Select Students:</h3>
                <?php if (empty($students)): ?>
                    <p>No students available to create a group.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Select</th>
                                <th>Name</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><input type="checkbox" name="students[]" value="<?php echo $student['id']; ?>"></td>
                                    <td><?php echo htmlspecialchars($student['username']); ?></td>
                                    <td><?php echo htmlspecialchars($student['email']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <button type="submit" class="btn">Create Group</button>
            </form>
        <?php endif; ?>
    </div>

</body>
</html>
