<?php 
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Database connection
require __DIR__ . '/../includes/db.php';

$user_id = $_SESSION['user_id'];
$message = "";

// Fetch groups the user is part of
$groups = [];
$stmt = $conn->prepare("
    SELECT g.id, g.name 
    FROM groups g
    JOIN user_groups ug ON g.id = ug.group_id
    WHERE ug.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $groups = $result->fetch_all(MYSQLI_ASSOC);
}
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idea_title = trim($_POST['idea_title']);
    $idea_description = trim($_POST['idea_description']);
    $group_id = isset($_POST['group_id']) ? intval($_POST['group_id']) : 0;
    $upload_success = false;
    $file_name = "";
    $target_file = "";

    // File Upload Handling
    if (!empty($_FILES['idea_file']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_name = basename($_FILES["idea_file"]["name"]);
        $target_file = $target_dir . time() . "_" . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['pdf', 'doc', 'docx', 'png', 'jpg', 'jpeg'];

        if (in_array($file_type, $allowed_types) && move_uploaded_file($_FILES["idea_file"]["tmp_name"], $target_file)) {
            $upload_success = true;
        } else {
            $message = "<p class='error'>File upload failed. Only PDF, DOC, PNG, JPG allowed.</p>";
        }
    }

    // Insert idea into database
    if (!empty($idea_title) && !empty($idea_description) && $group_id > 0) {
        $stmt = $conn->prepare("INSERT INTO ideas (user_id, group_id, title, description, file, submitted_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("iisss", $user_id, $group_id, $idea_title, $idea_description, $target_file);
        
        if ($stmt->execute()) {
            $message = "<p class='success'>Idea submitted successfully!</p>";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Idea - TrioTrek</title>
    <style>
        body { background: #000; color: #fff; font-family: Arial, sans-serif; margin: 0; padding: 0; }
        
        /* Navigation Bar */
        .navbar {
            background: #222;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar h1 {
            margin: 0;
            color: #ff6f61;
            font-size: 22px;
        }
        .navbar .nav-links {
            display: flex;
            gap: 20px;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            font-size: 16px;
        }
        .navbar a:hover {
            text-decoration: underline;
        }

        /* Container */
        .container { max-width: 600px; margin: 50px auto; padding: 20px; background: #333; border-radius: 10px; }
        h2 { text-align: center; color: #ff6f61; }
        form { display: flex; flex-direction: column; gap: 15px; }
        input, textarea, select { padding: 10px; border-radius: 5px; border: none; font-size: 16px; }
        button { background: #ff6f61; color: white; padding: 12px; border: none; border-radius: 8px; font-size: 16px; cursor: pointer; }
        button:hover { background: #e64c3c; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<div class="navbar">
    <h1>TrioTrek</h1>
    <div class="nav-links">
        <a href="user_dashboard.php">Dashboard</a>
        <a href="../logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <h2>Submit Your Idea</h2>
    <?php echo $message; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Idea Title:</label>
        <input type="text" name="idea_title" required>
        
        <label>Description:</label>
        <textarea name="idea_description" required></textarea>

        <label>Select Group:</label>
        <select name="group_id" required>
            <option value="">-- Select a Group --</option>
            <?php foreach ($groups as $group): ?>
                <option value="<?php echo $group['id']; ?>">
                    <?php echo htmlspecialchars($group['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Upload File (PDF, DOC, PNG, JPG):</label>
        <input type="file" name="idea_file">

        <button type="submit">Submit Idea</button>
    </form>
</div>

</body>
</html>
