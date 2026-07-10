<?php 
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'teacher') {
    header("Location: ../login.php");
    exit;
}
include "../db.php";

// Get project ID from the URL
$submission_id = isset($_GET['id']) ? $_GET['id'] : null;

// Initialize success message variable
$success_message = '';
$project_title = '';  // To store the project title

if ($submission_id) {
    // Get the current project data
    $sql = "SELECT * FROM ideas WHERE id='$submission_id'";
    $result = $conn->query($sql);
    if ($result->num_rows == 0) {
        echo "Project not found!";
        exit;
    }
    
    $project = $result->fetch_assoc();
    $project_title = $project['title'];  // Get the project title

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $status = $_POST['status'];  // Get the new status (Approved or Rejected)

        // Update the status in the ideas table
        $sql = "UPDATE ideas SET status='$status' WHERE id='$submission_id'"; 
        if ($conn->query($sql)) {
            // Set success message if the update was successful
            if ($status == 'Approved') {
                $success_message = "The project '$project_title' has been approved!";
            } else {
                $success_message = "The project '$project_title' has been rejected.";
            }
        } else {
            echo "Error updating status: " . $conn->error;
        }
    }
} else {
    echo "Invalid project ID!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Project Action</title>
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
            justify-content: flex-end; /* Align items to the right */
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
            max-width: 600px;
            background: #1f1f1f;
            border-radius: 10px;
        }

        h2 {
            text-align: center;
            color: #fff;
        }

        form {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        select, button {
            padding: 10px;
            margin: 10px 0;
            background: #333;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
        }

        select:hover, button:hover {
            background: #ff6f61;
        }

        button {
            cursor: pointer;
        }

        /* Success message styling */
        .success-message {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 20px 0;
            padding: 10px;
        }

        /* Green success message when approved */
        .approved {
            color: green;
            background-color: #2d2d2d;
            border-radius: 5px;
        }

        /* Red message when rejected */
        .rejected {
            color: red;
            background-color: #2d2d2d;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="menu">
    <!-- Dashboard and Logout buttons aligned to the right -->
    <a href="teacher_dashboard.php">Dashboard</a>
    <a href="../logout.php" class="logout-container">Logout</a>
</div>

<div class="container">
    <h2>Approve or Reject Project</h2>

    <!-- Display success message if the project status is updated -->
    <?php if ($success_message): ?>
        <div class="success-message <?php echo ($status == 'Approved') ? 'approved' : 'rejected'; ?>">
            <?= $success_message ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <select name="status">
            <option value="Approved">Approve</option>
            <option value="Rejected">Reject</option>
        </select>
        <br>
        <button type="submit">Submit Action</button>
    </form>
</div>

</body>
</html>
