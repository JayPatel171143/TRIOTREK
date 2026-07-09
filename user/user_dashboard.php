<?php
// Start session to get user data
session_start();

// Check if the user is logged in, otherwise redirect to login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Check if session variables are set before using them
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest'; 
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'No email provided';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TrioTrek - Dashboard</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <!-- Inline CSS -->
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        /* Body Styling */
        body {
            background: #000000; /* Set background to black */
            color: #fff; /* Set text color to white for better contrast */
        }

        /* Navbar */
        .navbar {
            background: #1c1c1c; /* Dark background for navbar */
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar h2 {
            color: #fff; /* White text for navbar title */
            font-weight: 600;
        }

        .nav-links a {
            margin: 0 15px;
            text-decoration: none;
            color: #bbb; /* Lighter text for links */
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
            background: #222; /* Slightly darker background for the hero section */
            box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.05);
        }

        .hero h1 {
            font-size: 2.5rem;
            font-weight: 600;
            color: #fff; /* White text for hero heading */
        }

        .hero p {
            color: #bbb; /* Light text for hero description */
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
            background: #333; /* Dark background for cards */
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
            color: #fff; /* White text for card heading */
            font-size: 1.5rem;
        }

        .card p {
            color: #ccc; /* Light text for card description */
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
            color: #bbb; /* Light text for footer */
            font-size: 1rem;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <h2>TrioTrek</h2>
        <div class="nav-links">
            <a href="#dashboard">Dashboard</a>
            <a href="view_announcements.php">Announcements</a> <!-- Added Announcements Link -->
            <a href="../user/contactus.php">Contact Us</a>
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <!-- Hero Section -->
    <div class="hero" id="dashboard">
        <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
        <p>Find your groups and ideas easily</p>
    </div>

    <!-- Dashboard Cards -->
    <div class="dashboard-container">
        <div class="card">
            <h3>Create a Group</h3>
            <p>Start a new project with your team.</p>
            <a href="create_group.php">Create</a>
        </div>
        <div class="card">
            <h3>Submit an Idea</h3>
            <p>Share innovative thoughts with the community.</p>
            <a href="submit_idea.php">Submit</a>
        </div>
        <div class="card">
            <h3>View Your Groups</h3>
            <p>Check out the groups you are part of.</p>
            <a href="view_groups.php">View</a>
        </div>
        <div class="card">
            <h3>Manage Groups</h3>
            <p>Organize and manage your groups.</p>
            <a href="manage_Groups.php">Manage</a>
        </div>

        <!-- New "View Comments" Card -->
        <div class="card">
            <h3>View Comments</h3>
            <p>Check the feedback from your teacher on your projects.</p>
            <a href="view_comments.php">View Comments</a>
        </div>

        <div class="card">
            <h3>Project Status</h3>
            <p>Track and update the progress of your project.</p>
            <a href="project_status.php">View Status</a>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>© 2025 TrioTrek</p>
    </footer>

</body>
</html>
