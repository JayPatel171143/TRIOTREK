<?php
// Include database connection
include(__DIR__ . '/includes/db.php');

// Set timezone to IST
date_default_timezone_set('Asia/Kolkata');

// Initialize variables
$success = $error = "";

// Check if token is provided in the URL
if (isset($_GET['token']) && !empty($_GET['token'])) {
    $token = $_GET['token'];

    // Sanitize input to prevent SQL injection
    $token = $conn->real_escape_string($token);

    // Get current timestamp
    $current_time = date("Y-m-d H:i:s");

    // Check if token exists and is valid
    $sql = "SELECT email, password_reset_expires FROM users WHERE password_reset_token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $email = $row['email'];
        $expires = $row['password_reset_expires'];

        // Debugging: Check expiration times
        if ($expires < $current_time) {
            // If token is expired, regenerate a new reset link and update the database
            $new_token = bin2hex(random_bytes(16));
            $new_expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

            $update_token_sql = "UPDATE users SET password_reset_token = ?, password_reset_expires = ? WHERE email = ?";
            $update_stmt = $conn->prepare($update_token_sql);
            $update_stmt->bind_param("sss", $new_token, $new_expiry, $email);
            $update_stmt->execute();

            $error = "Your reset link has expired. A new reset link has been generated. <br> 
                      <a href='reset_password.php?token=$new_token'>Click here to reset your password</a>";
        } else {
            // Allow password reset
            if (isset($_POST['submit'])) {
                $new_password = $_POST['password'];

                // Validate password strength
                if (strlen($new_password) < 8) {
                    $error = "Password must be at least 8 characters long.";
                } elseif (!preg_match("/[A-Za-z]/", $new_password) || !preg_match("/[0-9]/", $new_password)) {
                    $error = "Password must contain both letters and numbers.";
                } else {
                    // Hash the new password
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                    // Update password and remove token
                    $update_sql = "UPDATE users SET password = ?, password_reset_token = NULL, password_reset_expires = NULL WHERE email = ?";
                    $update_stmt = $conn->prepare($update_sql);
                    $update_stmt->bind_param("ss", $hashed_password, $email);

                    if ($update_stmt->execute()) {
                        $success = "Your password has been reset successfully. <a href='login.php'>Login here</a>.";
                    } else {
                        $error = "Database error. Please try again.";
                    }
                }
            }
        }
    } else {
        $error = "Invalid or expired token. Please request a new password reset link.";
    }
} else {
    $error = "No token provided. Please check your reset link.";
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - TrioTrek</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #000; color: #fff; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .container { background: #333; padding: 40px; border-radius: 12px; box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1); width: 400px; text-align: center; }
        h2 { color: #fff; margin-bottom: 15px; }
        input { width: 100%; padding: 12px; margin: 8px 0; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem; }
        button { width: 100%; padding: 12px; background: #ff6f61; border: none; color: white; font-size: 1.2rem; border-radius: 8px; cursor: pointer; }
        button:hover { background: #e64c3c; }
        p { margin-top: 15px; font-size: 1rem; }
        .message { margin-bottom: 10px; padding: 10px; border-radius: 6px; font-size: 1rem; }
        .success { background: #eaf8e6; color: #2c6c2c; }
        .error { background: #fdecea; color: #d9534f; }
    </style>
</head>
<body>

<div class="container">
    <h2>Reset Password</h2>

    <?php if ($success) { ?>
        <p class="message success"><?php echo $success; ?></p>
    <?php } ?>

    <?php if ($error) { ?>
        <p class="message error"><?php echo $error; ?></p>
    <?php } ?>

    <?php if (!$success && empty($error)) { ?>
    <form action="" method="POST">
        <input type="password" name="password" placeholder="Enter new password" required>
        <button type="submit" name="submit">Reset Password</button>
    </form>
    <?php } ?>
</div>

</body>
</html>
