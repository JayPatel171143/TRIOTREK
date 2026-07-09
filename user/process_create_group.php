<?php
// Start session to get user data
session_start();

// Debug: Check the request method
var_dump($_SERVER['REQUEST_METHOD']); // Should output 'POST'

// Check if the user is logged in, otherwise redirect to login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Check if the CSRF token is set and matches the session token
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debug: Check CSRF token values
    var_dump($_SESSION['csrf_token']);
    var_dump($_POST['csrf_token']);
    
    // Check CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        // Invalid CSRF token, redirect or show an error
        die("Invalid CSRF token.");
    }

    // Database connection details
    $host = 'localhost'; // Database host
    $dbname = 'trio_trek'; // Database name
    $username_db = 'root'; // Database username
    $password_db = ''; // Database password

    try {
        // Create PDO connection
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username_db, $password_db);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Check if the form was submitted with selected students
        if (isset($_POST['students']) && !empty($_POST['students'])) {
            $selected_students = $_POST['students']; // Array of student IDs

            // Create the new group (assuming the group table structure)
            $group_name = "New Group"; // You can change this to allow users to set a group name
            $created_by = $_SESSION['user_id']; // The user who is creating the group

            // Insert the group into the database
            $stmt = $pdo->prepare("INSERT INTO groups (name, created_by) VALUES (:name, :created_by)");
            $stmt->execute(['name' => $group_name, 'created_by' => $created_by]);

            // Get the last inserted group ID
            $group_id = $pdo->lastInsertId();

            // Now, associate the selected students with this group
            foreach ($selected_students as $student_id) {
                // Insert each student into the group_students table (assuming it exists)
                $stmt = $pdo->prepare("INSERT INTO group_students (group_id, student_id) VALUES (:group_id, :student_id)");
                $stmt->execute(['group_id' => $group_id, 'student_id' => $student_id]);
            }

            // Redirect to the dashboard or another page
            header("Location: user_dashboard.php");
            exit;

        } else {
            // No students selected
            echo "No students were selected for the group.";
            exit;
        }

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
} else {
    // Invalid request method
    die("Invalid request.");
}
