<?php
// Start session
session_start();



// Check if the user is logged in and if the user is a teacher
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'teacher') {
    header("Location: ../login.php");
    exit;
}

// Check if 'user_id' session variable is set (updated from 'id' to 'user_id')
if (!isset($_SESSION['user_id'])) {
    die("Error: User ID not found in session.");
}

$user_id = $_SESSION['user_id'];  // Now using 'user_id' from the session

// Include database connection file (replace with your actual DB connection details)
include __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the announcement text from the form
    $announcement_text = mysqli_real_escape_string($conn, $_POST['announcement_text']);

    // Step 1: Verify if the logged-in user is a teacher
    $sql_check_teacher = "SELECT * FROM users WHERE id = ? AND role = 'teacher'";
    $stmt_check_teacher = $conn->prepare($sql_check_teacher);
    $stmt_check_teacher->bind_param("i", $user_id);
    
    // Execute query and handle potential errors
    $stmt_check_teacher->execute();
    if ($stmt_check_teacher->error) {
        echo "Error in query: " . $stmt_check_teacher->error;  // Debugging output for any issues with the query
    }

    $result_check = $stmt_check_teacher->get_result();

    // If the user is a teacher, proceed with announcement insertion
    if ($result_check->num_rows > 0) {
        // Step 2: Insert the announcement into the database
        $sql_insert = "INSERT INTO announcements (user_id, announcement_text) VALUES (?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("is", $user_id, $announcement_text);

        if ($stmt_insert->execute()) {
            $message = "Announcement sent successfully!";
        } else {
            $message = "Error sending announcement: " . mysqli_error($conn);
        }
        $stmt_insert->close();
    } else {
        $message = "Error: User is not a teacher or ID does not exist.";
    }

    $stmt_check_teacher->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Announcements - TrioTrek</title>
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

        /* Announcement Form Section */
        .form-container {
            max-width: 800px;
            margin: 30px auto;
            background: #333;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .form-container h2 {
            font-size: 1.8rem;
            color: #fff;
            margin-bottom: 20px;
        }

        .form-container textarea {
            width: 100%;
            padding: 15px;
            background: #222;
            color: #fff;
            border: 1px solid #555;
            border-radius: 8px;
            font-size: 1rem;
            margin-bottom: 20px;
        }

        .form-container button {
            padding: 10px 20px;
            background: #ff6f61;
            color: white;
            border-radius: 8px;
            border: none;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .form-container button:hover {
            background: #e64c3c;
        }

        /* Message Display */
        .message {
            text-align: center;
            font-size: 1.2rem;
            color:rgb(115, 255, 97);
            margin-top: 20px;
        }

    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <h2>TrioTrek</h2>
        <div class="nav-links">
            <a href="teacher_dashboard.php">Dashboard</a>
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <!-- Announcement Form -->
    <div class="form-container">
        <h2>Send Announcements</h2>
        <?php
        if (isset($message)) {
            echo "<div class='message'>$message</div>";
        }
        ?>
        <form action="send_announcements.php" method="POST">
            <textarea name="announcement_text" rows="5" placeholder="Write your announcement here..." required></textarea>
            <button type="submit">Send Announcement</button>
        </form>
    </div>

</body>
</html>
