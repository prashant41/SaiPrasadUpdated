<?php
session_start();
include("db.php");

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$rows = [];

try {
    $sql = "SELECT * FROM expenses_material ORDER BY date DESC, id ASC";
    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Expenses Material</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* CSS Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        nav {
            background-color: #002147; /* Dark blue */
            overflow: hidden;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }
        nav a {
            float: left;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
        }
        nav a:hover {
            background-color: #ddd;
            color: black;
        }
        .side-nav {
            background-color: green; /* Dark blue */
            width: 200px;
            height: calc(100vh - 56px); /* Adjust height to avoid overlap with top nav */
            overflow: auto;
            position: fixed;
            top: 56px; /* Height of the top nav */
            left: 0;
        }
        .side-nav a {
            display: block;
            color: white;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
        }
        .side-nav a:hover {
            background-color:#002147;
            color:whitesmoke;
        }
        .content {
            margin-left: 220px; /* Space for side navigation */
            margin-top: 56px; /* Space for top navigation */
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            flex: 1;
            overflow: auto;
        }
        h2 {
            text-align: left;
            color: #002147; /* Dark blue */
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
            padding: 8px 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2; /* Light gray */
        }
        td {
            vertical-align: middle; /* Center align content vertically */
        }
        .action-buttons {
            text-align: center;
        }
        .action-buttons a {
            display: inline-block;
            padding: 8px 16px;
            margin-right: 5px;
            text-decoration: none;
            border-radius: 4px;
        }
        .action-buttons a.update {
            background-color: #4CAF50; /* Green */
            color: white;
        }
        .action-buttons a.delete {
            background-color: #f44336; /* Red */
            color: white;
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

<!-- Top Navigation Bar -->
<nav>
    <a href="super_admin_dashboard.php" class="logo"><span>SAI PRASAD</span></a>
    <a href="pnl.php">PnL</a>
    <a href="manage_expense_material.php">Raw Materials</a>
    <a href="raw_material_list.php">Filter  Raw Materials</a>
    <a href="manage_users.php">Manage</a>
    <a href="logout.php" onclick="return confirm('Are you sure you want to log out?');">Log Out</a>
    <!-- Add more links as needed -->
</nav>

<!-- Side Navigation -->
<div class="side-nav">
    
</div>

<!-- Main Content Area -->
<div class="content">
    <h2>Manage Expenses Material</h2>

    <h2>Expenses Material List</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Raw Material Name</th>
                <th>Amount</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['date']); ?></td>
                <td><?php echo htmlspecialchars($row['raw_material_name']); ?></td>
                <td><?php echo htmlspecialchars($row['amount']); ?></td>
                <td class="action-buttons">
                    <a href="update_expenses_material.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="update">Update</a>
                    <a href="delete_expenses_material.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="delete" onclick="return confirm('Are you sure you want to delete this expense material?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
