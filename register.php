<?php 
// Include the database connection
include(__DIR__ . '/includes/db.php');

// Create table if it doesn't exist
$tableCreationQuery = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
)";
$conn->query($tableCreationQuery);

// Registration Logic
if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password']; 
    $confirm_password = $_POST['confirm_password']; 

    if ($password === $confirm_password) { 
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if email already exists using prepared statement
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Email already registered.";
        } else {
            // Insert user into the database using prepared statement
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashed_password);
            if ($stmt->execute()) {
                $success = "Registration successful. <a href='login.php'>Login here</a>";
            } else {
                $error = "Error: " . $conn->error;
            }
        }
    } else {
        $error = "Passwords do not match.";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - TrioTrek</title>

    <!-- Google Fonts -->
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
            background: #181717; /* Dark mode background */
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            padding: 20px;
        }

        /* Page Container */
        .container {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 90%;
            max-width: 1000px;
            gap: 40px; /* Adds space between image and box */
        }

        /* Left Side Image */
        .left-image {
            flex: 1;
            text-align: center;
        }

        .left-image img {
            width: 100%;
            max-width: 450px;
            height: auto;
            border-radius: 10px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.3);
        }

        /* Registration Box */
        .register-container {
            background: #333;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
            flex: 1;

            position: relative;
            right: -500px; /* Start hidden on the right */
            transition: right 0.8s ease-in-out;
        }

        /* Class to make it pop up */
        .show-register {
            right: 0; /* Moves it into view */
        }

        .register-container h2 {
            color: #fff;
            margin-bottom: 15px;
        }

        /* Input Fields */
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
            border-color: #ff6f61;
            outline: none;
            box-shadow: 0px 0px 5px rgba(255, 111, 97, 0.5);
        }

        /* Submit Button */
        button {
            width: 100%;
            padding: 12px;
            background: rgb(128, 82, 77);
            border: none;
            color: white;
            font-size: 1.2rem;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background: #e64c3c;
        }

        /* Links */
        p {
            margin-top: 15px;
            font-size: 1rem;
        }

        p a {
            color: rgb(128, 85, 81);
            text-decoration: none;
            font-weight: 600;
        }

        p a:hover {
            color: rgb(128, 87, 83);
        }

        /* Success & Error Messages */
        .message {
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 6px;
            font-size: 1rem;
            text-align: center;
        }

        .success {
            background: #eaf8e6;
            color: #2c6c2c;
        }

        .error {
            background: #fdecea;
            color: #d9534f;
        }

        /* Responsive Design */
        @media (max-width: 800px) {
            .container {
                flex-direction: column;
                text-align: center;
            }

            .left-image, .register-container {
                width: 100%;
            }

            .left-image img {
                max-width: 300px;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Left Side Image -->
        <div class="left-image">
            <img src="assets/logo.jpg" alt="Join TrioTrek">
        </div>

        <!-- Registration Form -->
        <div class="register-container" id="registerBox">
            <h2>Create Your Account</h2>

            <?php if (isset($success)) { ?>
                <p class="message success"><?php echo $success; ?></p>
            <?php } ?>

            <?php if (isset($error)) { ?>
                <p class="message error"><?php echo $error; ?></p>
            <?php } ?>

            <form action="register.php" method="POST">
                <input type="text" id="username" name="username" placeholder="Username" required>
                <input type="email" id="email" name="email" placeholder="Email Address" required>
                <input type="password" id="password" name="password" placeholder="Password" required>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                <button type="submit" name="submit">Register</button>
            </form>
            
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>

    <script>
    window.onload = function() {
        setTimeout(function() {
            document.getElementById("registerBox").classList.add("show-register");
        }, 500); // Delay the pop-up effect slightly
    };
</script>

</body>
</html>