<?php
// Start session
session_start();

// Check if the user is logged in and if the user is a teacher
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'teacher') {
    header("Location: ../login.php");
    exit;
}

// Retrieve the teacher's name from the session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Teacher'; // Default to 'Teacher' if not set

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - TrioTrek</title>
    
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

        /* Navbar */
        .navbar {
            background: #1c1c1c;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar h2 {
            color: #fff;
            font-weight: 600;
        }

        .nav-links a {
            margin: 0 15px;
            text-decoration: none;
            color: #bbb;
            font-size: 1rem;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: #ff6f61;
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

    <!-- Navbar -->
    <div class="navbar">
        <h2>TrioTrek</h2>
        <div class="nav-links">
            <!-- Dashboard button, clicking it will stay on the same page -->
            <a href="#" onclick="window.location.reload();">Dashboard</a>
            <!-- Logout button -->
            <a href="../contactus.php">Contact Us</a>
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <!-- Hero Section -->
    <div class="hero" id="dashboard">
        <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
        <p>You can manage student projects and ideas from here.</p>
    </div>

    <!-- Dashboard Cards -->
    <div class="dashboard-container">
        <div class="card">
            <h3>Manage Projects</h3>
            <p>Organize and oversee student projects.</p>
            <a href="manage_projects.php">Manage</a>
        </div>
        <div class="card">
            <h3>View Submissions</h3>
            <p>Check and review student submissions.</p>
            <a href="view_submissions.php">View</a>
        </div>
        <div class="card">
            <h3>Comment on Projects</h3>
            <p>Provide feedback on student projects.</p>
            <a href="comment_on_project.php">Comment</a>
        </div>
        <!-- Replaced Action on Projects button with Send Announcements button -->
        <div class="card">
            <h3>Send Announcements</h3>
            <p>Send important announcements to students.</p>
            <a href="send_announcements.php">Send</a>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>© 2025 TrioTrek</p>
    </footer>

</body>
</html>
