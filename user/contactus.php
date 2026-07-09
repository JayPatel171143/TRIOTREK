<?php
// Start session
session_start();

// Include database connection
include(__DIR__ . '/../includes/db.php');

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    $stmt = $conn->prepare("INSERT INTO contact_us (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $subject, $message);

    if ($stmt->execute()) {
        $successMessage = "Your message has been sent!";
    } else {
        $errorMessage = "Error sending message.";
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - TrioTrek</title>
    <style>
        body { background: #000; color: #fff; text-align: center; font-family: Arial, sans-serif; }
        .container { width: 50%; margin: auto; padding: 20px; background: #222; border-radius: 10px; margin-top: 50px; }
        h2 { color: white; }
        input, textarea { width: 100%; padding: 10px; margin: 10px 0; border-radius: 5px; border: none; }
        textarea { height: 100px; }
        button { padding: 10px 15px; border: none; background: #ff6f61; color: white; border-radius: 5px; cursor: pointer; }
        button:hover { background: #e64c3c; }
        .menu-bar { background: #222; padding: 15px; text-align: right; }
        .menu-bar a { color: white; margin-left: 20px; text-decoration: none; padding: 10px 15px; background: #ff6f61; border-radius: 5px; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>

    <div class="menu-bar">
        <a href="../admin/admin_dashboard.php">Dashboard</a>
        <a href="../logout.php">Logout</a>
    </div>

    <div class="container">
        <h2>Contact Us</h2>
        <?php if (isset($successMessage)) echo "<p class='success'>$successMessage</p>"; ?>
        <?php if (isset($errorMessage)) echo "<p class='error'>$errorMessage</p>"; ?>
        <form method="post">
            <input type="text" name="name" placeholder="Your Name" required>
            <input type="email" name="email" placeholder="Your Email" required>
            <input type="text" name="subject" placeholder="Subject" required>
            <textarea name="message" placeholder="Your Message" required></textarea>
            <button type="submit">Send</button>
        </form>
    </div>

</body>
</html>
