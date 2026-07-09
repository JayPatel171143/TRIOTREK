<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

include(__DIR__ . '/../includes/db.php'); // Include the database connection

$user_id = $_SESSION['user_id'];

// Fetch projects associated with the logged-in user
$sql = "SELECT i.id, i.title, i.status 
        FROM ideas i
        JOIN user_groups ug ON i.group_id = ug.group_id
        WHERE ug.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$projects = [];
$seen_titles = []; // Array to track seen project titles to avoid duplicates
while ($row = $result->fetch_assoc()) {
    // Only add the project if its title hasn't been seen before
    if (!in_array($row['title'], $seen_titles)) {
        $projects[] = $row;
        $seen_titles[] = $row['title'];
    }
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Status - TrioTrek</title>
    <style>
        body { 
            background: #000; 
            color: #fff; 
            font-family: 'Poppins', sans-serif; 
            margin: 0;
            padding: 0;
        }
        .navbar {
            background: #1c1c1c;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .navbar h2 {
            color: #fff;
            font-weight: 600;
        }
        .nav-links a {
            margin: 0 15px;
            text-decoration: none;
            color: #bbb;
            font-size: 1rem;
            transition: color 0.3s ease;
        }
        .nav-links a:hover {
            color: #ff6f61;
        }
        .container { 
            max-width: 800px; 
            margin: 50px auto; 
            padding: 20px; 
            text-align: center;
        }
        h1 { 
            font-size: 2rem;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
            text-align: center;
        }
        table, th, td { 
            border: 1px solid #555; 
        }
        th, td { 
            padding: 12px; 
        }
        th { 
            background: #333; 
        }
        .status { 
            font-weight: bold; 
            padding: 5px 10px; 
            border-radius: 5px; 
        }
        .approved { 
            color: #0f0; 
        }
        .pending { 
            color: #ff0; 
        }
        .rejected { 
            color: #f00; 
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <h2>TrioTrek</h2>
        <div class="nav-links">
            <a href="user_dashboard.php">Dashboard</a>
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <h1>Project Status</h1>
        <table>
            <tr>
                <th>Project Title</th>
                <th>Status</th>
            </tr>
            <?php if (!empty($projects)): ?>
                <?php foreach ($projects as $project): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($project['title']); ?></td>
                        <td class="status <?php echo strtolower($project['status']); ?>">
                            <?php echo htmlspecialchars($project['status']); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="2">No projects found</td></tr>
            <?php endif; ?>
        </table>
    </div>

</body>
</html>