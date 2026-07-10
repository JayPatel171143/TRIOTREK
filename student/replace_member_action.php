<?php
session_start();

// Check if the user is logged in, otherwise redirect to login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id']; // Get logged-in user ID

// Check if group_id, member_to_replace, and new_member are provided
if (!isset($_POST['group_id']) || !isset($_POST['member_to_replace']) || !isset($_POST['new_member'])) {
    echo "Invalid request!";
    exit;
}

$group_id = $_POST['group_id'];
$member_to_replace = $_POST['member_to_replace']; // Might be an ID or a username
$new_member_id = $_POST['new_member'];

// Database connection details
$host = 'localhost'; // Database host
$dbname = 'trio_trek'; // Database name
$username_db = 'root'; // Database username
$password_db = ''; // Database password

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username_db, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if member_to_replace is an ID or a username
    if (is_numeric($member_to_replace)) {
        $member_to_replace_id = $member_to_replace; // It's already an ID
    } else {
        // Fetch the user ID from the users table using the username
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :member_to_replace LIMIT 1");
        $stmt->execute(['member_to_replace' => $member_to_replace]);
        $member_to_replace_id = $stmt->fetchColumn();

        if (!$member_to_replace_id) {
            echo "Error: The member '$member_to_replace' was not found in the users table.";
            exit;
        }
    }

    // Check if the member exists in the group
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_groups WHERE group_id = :group_id AND user_id = :member_to_replace_id");
    $stmt->execute(['group_id' => $group_id, 'member_to_replace_id' => $member_to_replace_id]);
    $is_member_in_group = $stmt->fetchColumn();

    if (!$is_member_in_group) {
        echo "Error: The member is not part of this group.";
        exit;
    }

    // Remove the existing member from the group
    $stmt = $pdo->prepare("DELETE FROM user_groups WHERE group_id = :group_id AND user_id = :member_to_replace_id");
    $stmt->execute(['group_id' => $group_id, 'member_to_replace_id' => $member_to_replace_id]);

    // Add the new member to the group
    $stmt = $pdo->prepare("INSERT INTO user_groups (group_id, user_id) VALUES (:group_id, :new_member_id)");
    $stmt->execute(['group_id' => $group_id, 'new_member_id' => $new_member_id]);

    // Redirect to manage_groups.php
    header("Location: manage_groups.php?group_id=" . $group_id);
    exit;

} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage();
    exit;
}
