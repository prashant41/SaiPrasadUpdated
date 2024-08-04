<?php
include("db.php");
session_start();

// Redirect to login page if user is not logged in
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

// Handle marking a report as solved
if (isset($_GET['action']) && $_GET['action'] == 'mark_solved' && isset($_GET['id'])) {
    $reportId = $_GET['id'];
    
    // Prepare SQL statement to delete the notification (mark as solved)
    $sql = "DELETE FROM notifications WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $reportId]);
    
    // Redirect back to the report page after deletion
    header("Location: report.php");
    exit();
}

// Fetch notifications from database
$sql = "SELECT * FROM notifications ORDER BY created_at DESC";
$stmt = $pdo->query($sql);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Page</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1, h2, p {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        table th {
            background-color: #f2f2f2;
        }
        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        table tbody tr:hover {
            background-color: #e0e0e0;
        }
        .action-btn {
            display: inline-block;
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            text-decoration: none;
            cursor: pointer;
            border-radius: 4px;
        }
        .logo{
            background-color: red;
            font-weight: bold;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }
        .logo:hover{
            background-color: #4CAF50;
        }
    </style>
</head>
<body>
    <nav>
    <a href="admin_dashboard.php" class="logo"><span>SAI PRASAD</span></a>
        <a href="order_form.php">Order Form</a>
        <a href="display_orders.php">Display Orders</a>
        <a href="admin_filter.php">Filter Orders</a>
        <a href="daily_entry.php">Daily Entry</a>
        <a href="report.php">Report</a>
        <a href="logout.php" onclick="return confirm('Are you sure you want to log out?');">Log Out</a>
    <!-- Add more links as needed -->
    </nav><br>

    <div class="container">
        
        <h2>Report Notifications</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Message</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($notifications as $notification): ?>
                    <tr>
                        <td><?php echo $notification['id']; ?></td>
                        <td><?php echo $notification['message']; ?></td>
                        <td><?php echo $notification['created_at']; ?></td>
                        <td>
                            <a class="action-btn" href="report.php?action=mark_solved&id=<?php echo $notification['id']; ?>">Report Solved</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
