<?php
// Start session
session_start();

// Include database connection
include('../db.php');

// Redirect to login if the user is not an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// Search functionality
$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $stmt = $conn->prepare("SELECT id, username, email, role, created_at FROM users WHERE username LIKE ?");
    $searchTerm = "%" . $search . "%";
    $stmt->bind_param("s", $searchTerm);
} else {
    $stmt = $conn->prepare("SELECT id, username, email, role, created_at FROM users");
}

$stmt->execute();
$result = $stmt->get_result();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - TrioTrek</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #000;
            color: #fff;
        }

        /* Menu Bar */
        .menu-bar {
            background: #222;
            padding: 15px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        .menu-bar a {
            color: #ff6f61;
            text-decoration: none;
            font-weight: bold;
            padding: 10px 15px;
            margin-left: 10px;
            transition: 0.3s;
        }

        .menu-bar a:hover {
            color: #e64c3c;
        }

        /* Hero Section */
        .hero {
            text-align: center;
            padding: 30px 20px;
            background: #222;
            box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.05);
        }

        .hero h1 {
            font-size: 2.5rem;
            font-weight: 600;
        }

        .hero p {
            color: #bbb;
            font-size: 1.2rem;
            margin-top: 10px;
        }

        /* Search Box */
        .search-container {
            text-align: center;
            margin: 20px 0;
        }

        .search-container input {
            width: 40%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            background: #333;
            color: #fff;
        }

        .search-container button {
            padding: 10px 15px;
            border: none;
            background: #ff6f61;
            color: #fff;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 5px;
        }

        .search-container button:hover {
            background: #e64c3c;
        }

        /* Table Styling */
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            border: 1px solid #fff;
            text-align: center;
        }

        th {
            background-color: #ff6f61;
            color: #fff;
        }

        td {
            background-color: #333;
        }

        /* Action Buttons */
        .actions a {
            padding: 8px 12px;
            margin: 5px;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
        }

        .delete-btn {
            background: #e64c3c;
            color: #fff;
        }

        .edit-btn {
            background: #3498db;
            color: #fff;
        }

        .delete-btn:hover {
            background: #c0392b;
        }

        .edit-btn:hover {
            background: #2980b9;
        }

        /* Footer */
        footer {
            text-align: center;
            padding: 20px;
            margin-top: 40px;
            color: #bbb;
            font-size: 1rem;
        }
    </style>
</head>
<body>

    <!-- Menu Bar -->
    <div class="menu-bar">
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="../logout.php">Logout</a>
    </div>

    <!-- Hero Section -->
    <div class="hero">
        <h1>Manage Users</h1>
        <p>View, edit, or remove users from the system.</p>
    </div>

    <!-- Search Bar -->
    <div class="search-container">
        <form method="GET">
            <input type="text" name="search" placeholder="Search by username..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <!-- User Management Table -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['role']; ?></td>
                        <td><?php echo isset($row['created_at']) ? date("F j, Y, g:i a", strtotime($row['created_at'])) : 'Not available'; ?></td>
                        <td class="actions">
                            <a href="?delete=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure?');">Delete</a>
                            <a href="edit_user.php?id=<?php echo $row['id']; ?>" class="edit-btn">Edit</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No users found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Footer -->
    <footer>
        <p>© 2025 TrioTrek</p>
    </footer>

</body>
</html>
