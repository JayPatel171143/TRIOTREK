
<?php
// Database connection details
$host = 'localhost';      // Database host
$dbname = 'trio_trek';    // Database name
$username_db = 'root';    // Database username
$password_db = '';        // Database password

// Create connection
$conn = new mysqli($host, $username_db, $password_db, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
