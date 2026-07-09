<?php
// Start session
session_start();

// Redirect to login if the user is not an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - TrioTrek</title>
    
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
            background: #000000;
            color: #fff;
            font-family: 'Poppins', sans-serif;
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
            padding: 60px 20px;
            background: #222;
            box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.05);
        }

        .hero h1 {
            font-size: 2.5rem;
            font-weight: 600;
            color: #fff;
        }

        .hero p {
            color: #bbb;
            font-size: 1.2rem;
            margin-top: 10px;
        }

        /* Dashboard Cards */
        .dashboard-container {
            max-width: 1200px;
            margin: 30px auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .card {
            background: #333;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card h3 {
            color: #fff;
            font-size: 1.5rem;
        }

        .card p {
            color: #ccc;
            margin-top: 10px;
            font-size: 1rem;
        }

        .card a {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background: #ff6f61;
            color: white;
            border-radius: 8px;
            text-decoration: none;
            transition: background 0.3s ease;
        }

        .card a:hover {
            background: #e64c3c;
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
        <a href="../user/contactus.php">Contact Us</a>
        <a href="../logout.php">Logout</a>
    </div>

    <!-- Hero Section -->
    <div class="hero">
        <h1>Welcome, Admin</h1>
        <p>You can manage users and monitor password changes from here.</p>
    </div>

    <!-- Dashboard Cards -->
    <div class="dashboard-container">
        <div class="card">
            <h3>Manage Users</h3>
            <p>View and manage all user accounts.</p>
            <a href="manage_users.php">Manage</a>
        </div>
        <div class="card">
            <h3>Recent Password Changes</h3>
            <p>View users who recently changed their passwords.</p>
            <a href="view_recent_password_changes.php">View</a>
        </div>
        <!-- View Login Details Button -->
        <div class="card">
            <h3>View Login Details</h3>
            <p>See all user login details.</p>
            <a href="login_details.php">View</a>
        </div>

        <div class="card">
    <h3>Manage and Edit Ideas</h3>
    <p>View, edit, and delete submitted ideas.</p>
    <a href="manage_ideas.php">Manage</a>
</div>

<div class="card">
    <h3>Manage Announcements</h3>
    <p>View, edit, or delete announcements.</p>
    <a href="manage_announcements.php">Manage</a>
</div>

<!-- Manage User Groups Button -->
<div class="card">
    <h3>Manage User Groups</h3>
    <p>View and manage student groups.</p>
    <a href="manage_user_groups.php">Manage</a>
</div>

<div class="card">
    <h3>Manage project comments</h3>
    <p>View and manage project comments.</p>
    <a href="manage_project_comments.php">Manage</a>

    
</div>

<div class="card">
    <h3>Manage Contact Messages</h3>
    <p>View and respond to messages.</p>
    <a href="manage_contactus.php">Manage</a>
</div>

    </div>

    <!-- Footer -->
    <footer>
        <p>© 2025 TrioTrek</p>
    </footer>

</body>
</html>
