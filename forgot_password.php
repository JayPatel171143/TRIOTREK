<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Include database connection
require __DIR__ . '/includes/db.php';

// Initialize variables
$success = $error = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);

    // Check if email exists in the database
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Generate a unique reset token
        $token = bin2hex(random_bytes(50));
        $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // Sanitize the token for safety
        $token = $conn->real_escape_string($token);

        // Store token in the database
        $update_sql = "UPDATE users SET password_reset_token = ?, password_reset_expires = ? WHERE email = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sss", $token, $expires, $email);
        $update_stmt->execute();

        // Send password reset email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Gmail SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'jaynp1711@gmail.com'; // Your Gmail
            $mail->Password = 'aqpu goko evux mprh'; // Use App Password (not your actual password)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('your-email@gmail.com', 'TrioTrek Support');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "
                <p>We received a request to reset your password.</p>
                <p>Click the link below to reset your password:</p>
                <p><a href='http://localhost/TrioTrek/reset_password.php?token=$token'>Reset Password</a></p>
                <p>This link will expire in 1 hour.</p>
            ";

            $mail->send();
            $success = "A password reset link has been sent to your email.";
        } catch (Exception $e) {
            $error = "Email could not be sent. Mailer Error: " . $mail->ErrorInfo;
        }
    } else {
        $error = "No account found with this email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - TrioTrek</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #000;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: #333;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            width: 400px;
        }
        input, button {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 6px;
            border: none;
            font-size: 1rem;
        }
        input { border: 1px solid #ddd; }
        button {
            background: #ff6f61;
            color: white;
            cursor: pointer;
        }
        button:hover { background: #e64c3c; }
        .message { margin-top: 10px; padding: 10px; border-radius: 6px; }
        .success { background: #eaf8e6; color: #2c6c2c; }
        .error { background: #fdecea; color: #d9534f; }
    </style>
</head>
<body>

    <div class="container">
        <h2>Forgot Password</h2>
        <?php if ($success) { echo "<p class='message success'>$success</p>"; } ?>
        <?php if ($error) { echo "<p class='message error'>$error</p>"; } ?>
        <form action="forgot_password.php" method="POST">
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit">Send Reset Link</button>
        </form>
        <p><a href="login.php" style="color: #ff6f61;">Back to Login</a></p>
    </div>

</body>
</html>
