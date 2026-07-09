<?php  
session_start();
require __DIR__ . '/../includes/db.php'; // Include your database connection

// Check if the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// Fetch teachers (users with role 'teacher') from the database
$sql = "SELECT id, username, email, role, password_change_date FROM users WHERE role = 'teacher' ORDER BY password_change_date DESC";
$result = $conn->query($sql);

// Handle teacher deletion
if (isset($_GET['delete'])) {
    $teacher_id = $_GET['delete'];
    $delete_sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $teacher_id);
    if ($stmt->execute()) {
        echo "Teacher deleted successfully!";
        header("Location: manage_teachers.php"); // Refresh page after deletion
        exit;
    } else {
        echo "Error deleting teacher.";
    }
}

// Handle new teacher creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username'], $_POST['email'], $_POST['password'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password for security

    $insert_sql = "INSERT INTO users (username, email, role, password, password_change_date) VALUES (?, ?, 'teacher', ?, NOW())";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("sss", $username, $email, $password);
    if ($stmt->execute()) {
        echo "New teacher added successfully!";
        header("Location: manage_teachers.php"); // Refresh page after adding teacher
        exit;
    } else {
        echo "Error adding teacher.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Teachers - Admin</title>
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
            justify-content: space-between;
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

        h2, h3 {
            text-align: center;
            color: #fff;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
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

        a {
            text-decoration: none;
            color: #ff6f61;
        }

        a:hover {
            color: #fff;
        }

        form {
            margin-top: 20px;
            background: #333;
            padding: 20px;
            border-radius: 10px;
        }

        form label {
            color: #fff;
            display: block;
            margin-bottom: 10px;
        }

        form input {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #555;
            background: #222;
            color: #ddd;
        }

        form button {
            width: 100%;
            padding: 10px;
            background: #ff6f61;
            border: none;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }

        form button:hover {
            background: #ff4a3a;
        }
    </style>
</head>
<body>

<div class="menu">
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="manage_teachers.php">Manage Teachers</a>
    <a href="../logout.php" class="logout-container">Logout</a>
</div>

<div class="container">
    <h2>Manage Teachers</h2>

    <h3>Add New Teacher</h3>
    <form method="POST">
        <label for="username">Username:</label>
        <input type="text" name="username" required>

        <label for="email">Email:</label>
        <input type="email" name="email" required>

        <label for="password">Password:</label>
        <input type="password" name="password" required>

        <button type="submit">Add Teacher</button>
    </form>

    <h3>Current Teachers</h3>

    <?php if ($result->num_rows > 0): ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Password Change Date</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['role']) ?></td>
                <td><?= htmlspecialchars($row['password_change_date']) ?></td>
                <td>
                    <a href="edit_teacher.php?id=<?= $row['id'] ?>">Edit</a> |
                    <a href="manage_teachers.php?delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this teacher?')">Delete</a>
                </td>
            </tr>
        <?php } ?>
    </table>
    <?php else: ?>
        <p>No teachers found.</p>
    <?php endif; ?>
</div>

</body>
</html>
