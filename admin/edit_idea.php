<?php
// Start session
session_start();

// Include database connection
include('../db.php');

// Redirect if not admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// Check if idea ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage_ideas.php");
    exit;
}

$ideaId = $_GET['id'];

// Fetch existing idea details
$stmt = $conn->prepare("SELECT title, description, status FROM ideas WHERE id = ?");
$stmt->bind_param("i", $ideaId);
$stmt->execute();
$result = $stmt->get_result();
$idea = $result->fetch_assoc();

// Handle update form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $status = $_POST['status'];

    $updateStmt = $conn->prepare("UPDATE ideas SET title = ?, description = ?, status = ? WHERE id = ?");
    $updateStmt->bind_param("sssi", $title, $description, $status, $ideaId);

    if ($updateStmt->execute()) {
        header("Location: manage_ideas.php?message=Idea Updated Successfully");
        exit;
    } else {
        $error = "Error updating idea.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Idea - TrioTrek</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #000;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            width: 400px;
            background: #222;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            text-align: left;
            margin-top: 10px;
        }

        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            background: #333;
            border: 1px solid #555;
            color: white;
            border-radius: 5px;
        }

        button {
            margin-top: 15px;
            padding: 10px;
            background: #ff6f61;
            border: none;
            color: white;
            font-size: 1rem;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #e64c3c;
        }

        .back-link {
            display: block;
            margin-top: 15px;
            text-decoration: none;
            color: #ff6f61;
        }

        .back-link:hover {
            color: #e64c3c;
        }

        .error {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Edit Idea</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($idea['title']); ?>" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($idea['description']); ?></textarea>

            <label for="status">Status:</label>
            <select id="status" name="status">
                <option value="pending" <?php if ($idea['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                <option value="approved" <?php if ($idea['status'] == 'approved') echo 'selected'; ?>>Approved</option>
                <option value="rejected" <?php if ($idea['status'] == 'rejected') echo 'selected'; ?>>Rejected</option>
            </select>

            <button type="submit">Update Idea</button>
        </form>
        <a href="manage_ideas.php" class="back-link">Back to Manage Ideas</a>
    </div>

</body>
</html>
