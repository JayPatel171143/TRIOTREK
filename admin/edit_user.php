<?php
// Start session
session_start();

// Include database connection
include(__DIR__ . '/../includes/db.php');

// Redirect if not an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// Check if user ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage_users.php");
    exit;
}

$userId = $_GET['id'];

// Fetch user details
$stmt = $conn->prepare("SELECT username, email, role FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "User not found!";
    exit;
}

$user = $result->fetch_assoc();

// Handle form submission
if (isset($_POST['update_user'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    // Update user in database
    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?");
    $stmt->bind_param("sssi", $username, $email, $role, $userId);
    
    if ($stmt->execute()) {
        header("Location: manage_users.php");
        exit;
    } else {
        echo "Error updating user.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - TrioTrek</title>
    <style>
        body { font-family: Arial, sans-serif; background: #000; color: #fff; text-align: center; padding: 40px; }
        form { display: inline-block; text-align: left; background: #222; padding: 100px; border-radius: 10px; }
        label { display: block; margin-top: 10px; }
        input, select { width: 100%; padding: 8px; margin-top: 5px; }
        button { margin-top: 15px; padding: 10px 15px; background: #ff6f61; color: white; border: none; cursor: pointer; }
        button:hover { background: #e64c3c; }
    </style>
</head>
<body>

    <h1>Edit User</h1>
    <form method="POST">
        <label>Username:</label>
        <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

        <label>Role:</label>
        <select name="role">
            <option value="admin" <?php if ($user['role'] == 'admin') echo 'selected'; ?>>Admin</option>
            <option value="student" <?php if ($user['role'] == 'student') echo 'selected'; ?>>student</option>
            <option value="teacher" <?php if ($user['role'] == 'teacher') echo 'selected'; ?>>teacher</option>
        </select>

        <button type="submit" name="update_user">Update</button>
    </form>

    <br>
    <a href="manage_users.php" style="color: #ff6f61;">Cancel</a>

</body>
</html>
