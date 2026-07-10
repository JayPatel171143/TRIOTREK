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

// Check if a user ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Invalid request.";
    exit;
}

$userId = $_GET['id'];

// Fetch the current user’s group
$query = "
    SELECT ug.group_id, u.username, g.name AS group_name 
    FROM user_groups ug
    JOIN users u ON ug.user_id = u.id
    JOIN groups g ON ug.group_id = g.id
    WHERE ug.user_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();

// Fetch all groups for dropdown
$groupsQuery = "SELECT id, name FROM groups ORDER BY name";
$groupsResult = $conn->query($groupsQuery);
$groups = [];
while ($row = $groupsResult->fetch_assoc()) {
    $groups[] = $row;
}

// Fetch all students for Replace User dropdown
$studentsQuery = "SELECT id, username FROM users WHERE role = 'student' AND id != ?";
$studentsStmt = $conn->prepare($studentsQuery);
$studentsStmt->bind_param("i", $userId);
$studentsStmt->execute();
$studentsResult = $studentsStmt->get_result();
$students = [];
while ($row = $studentsResult->fetch_assoc()) {
    $students[] = $row;
}

// Handle form submission (update user group or replace user)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_group'])) {
        // Update the group
        $newGroupId = $_POST['group_id'];
        $updateStmt = $conn->prepare("UPDATE user_groups SET group_id = ? WHERE user_id = ?");
        $updateStmt->bind_param("ii", $newGroupId, $userId);
        if ($updateStmt->execute()) {
            header("Location: manage_user_groups.php");
            exit;
        } else {
            echo "Error updating user group.";
        }
    } elseif (isset($_POST['replace_user'])) {
        // Replace the user in the group
        $newUserId = $_POST['new_user_id'];
        $replaceStmt = $conn->prepare("UPDATE user_groups SET user_id = ? WHERE user_id = ?");
        $replaceStmt->bind_param("ii", $newUserId, $userId);
        if ($replaceStmt->execute()) {
            header("Location: manage_user_groups.php");
            exit;
        } else {
            echo "Error replacing user.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User Group - TrioTrek</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #000; color: #fff; text-align: center; }
        .container { width: 40%; margin: 50px auto; padding: 20px; background: #222; border-radius: 10px; }
        h1 { margin-bottom: 20px; }
        label, select, button { display: block; margin: 10px auto; width: 80%; padding: 10px; font-size: 16px; }
        button { background: #ff6f61; color: white; border: none; cursor: pointer; border-radius: 5px; }
        button:hover { background: #e05b50; }
    </style>
</head>
<body>

    <div class="container">
        <h1>Edit User Group</h1>
        <p><strong>User:</strong> <?php echo htmlspecialchars($userData['username']); ?></p>
        <p><strong>Current Group:</strong> <?php echo htmlspecialchars($userData['group_name']); ?></p>

        <!-- Update Group Form -->
        <form method="POST">
            <label for="group">Select New Group:</label>
            <select name="group_id" id="group">
                <?php foreach ($groups as $group): ?>
                    <option value="<?php echo $group['id']; ?>" 
                        <?php echo ($group['id'] == $userData['group_id']) ? "selected" : ""; ?>>
                        <?php echo htmlspecialchars($group['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="update_group">Update Group</button>
        </form>

        <!-- Replace User Form -->
        <form method="POST">
            <label for="replace">Replace User With:</label>
            <select name="new_user_id" id="replace">
                <option value="">Select User</option>
                <?php foreach ($students as $student): ?>
                    <option value="<?php echo $student['id']; ?>"><?php echo htmlspecialchars($student['username']); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="replace_user">Replace User</button>
        </form>

        <a href="manage_user_groups.php">Back to Manage Groups</a>
    </div>

</body>
</html>
