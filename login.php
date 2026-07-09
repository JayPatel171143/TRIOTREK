<?php 
// Include the database connection
include(__DIR__ . '/includes/db.php');

// Initialize variables to hold error or success messages
$success = $error = "";

// Start the session to store login status
session_start();

// Process login form when submitted
if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if email exists
    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Fetch user data
        $row = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $row['password'])) {
            // Store user data in session
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['role'] = $row['role']; // Store the role

            // Redirect based on role
            if ($_SESSION['role'] == 'admin') {
                header("Location: admin/admin_dashboard.php");
                exit;
            } elseif ($_SESSION['role'] == 'teacher') {
                header("Location: user/teacher_dashboard.php");
                exit;
            } else {
                header("Location: user/user_dashboard.php");
                exit;
            }
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No account found with this email.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TrioTrek</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
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
            background: #000000;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            overflow: hidden;
        }

        .container {
            display: flex;
            width: 80%;
            max-width: 1000px;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            position: relative;
        }

        /* Left Side Image */
        .left-image img {
            width: 450px;
            max-width: 100%;
            border-radius: 10px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.3);
        }

        /* Login Box */
        .login-container {
            background: #333;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
            position: absolute;
            bottom: -300px; /* Hidden initially */
            left: 80%;
            transform: translateX(-50%);
            transition: bottom 0.5s ease-in-out;
        }

        .login-container h2 {
            color: #fff;
            margin-bottom: 15px;
        }

        input {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            transition: 0.3s;
        }

        input:focus {
            border-color: rgb(78, 40, 37);
            outline: none;
            box-shadow: 0px 0px 5px rgba(255, 111, 97, 0.5);
        }

        button {
            width: 100%;
            padding: 12px;
            background: rgb(115, 68, 64);
            border: none;
            color: white;
            font-size: 1.2rem;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background: rgb(129, 78, 72);
        }

        p {
            margin-top: 15px;
            font-size: 1rem;
        }

        p a {
            color: rgb(147, 58, 50);
            text-decoration: none;
            font-weight: 600;
        }

        p a:hover {
            color: rgb(147, 68, 60);
        }

        .message {
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 6px;
            font-size: 1rem;
        }

        .success {
            background: #eaf8e6;
            color: #2c6c2c;
        }

        .error {
            background: #fdecea;
            color: #d9534f;
        }

        /* Animation to slide up login box */
        .show-login {
            bottom: 20px;
        }

        @media (max-width: 800px) {
            .container {
                flex-direction: column;
                text-align: center;
            }
            .login-container {
                position: relative;
                bottom: 0;
                left: auto;
                transform: none;
                width: 100%;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Left Side Image -->
        <div class="left-image">
            <img src="assets/logo.jpg" alt="Welcome to TrioTrek">
        </div>

        <!-- Login Form -->
        <div class="login-container" id="loginBox">
            <h2>Login to TrioTrek</h2>

            <?php if ($success) { ?>
                <p class="message success"><?php echo $success; ?></p>
            <?php } ?>

            <?php if ($error) { ?>
                <p class="message error"><?php echo $error; ?></p>
            <?php } ?>

            <form action="login.php" method="POST">
                <input type="email" id="email" name="email" placeholder="Email Address" required>
                <input type="password" id="password" name="password" placeholder="Password" required>
                <button type="submit" name="submit">Login</button>
            </form>
            
            <p class="forgot-password"><a href="forgot_password.php">Forgot Password?</a></p>
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </div>

    <script>
        // Wait for page to load, then show the login box
        window.onload = function() {
            document.getElementById("loginBox").classList.add("show-login");
        };
    </script>

</body>
</html>
