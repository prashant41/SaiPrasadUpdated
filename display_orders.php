<?php
session_start();
include("db.php"); // Adjust as per your database connection file

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

// Query to fetch all orders
$sql = "SELECT * FROM orders ORDER BY created_at DESC";
$stmt = $pdo->query($sql);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Query to fetch deleted orders from the backup table
$backup_sql = "SELECT * FROM orders_backup ORDER BY created_at DESC";
$backup_stmt = $pdo->query($backup_sql);
$backup_rows = $backup_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Orders</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }
        nav {
            background-color: #002147; /* Dark blue */
            overflow: hidden;
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
        .container {
            max-width: 800px; /* Adjusted for better table visibility */
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
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
        .delete-form {
            text-align: center;
            margin-top: 10px;
        }
        .delete-form input[type="submit"] {
            padding: 8px 16px;
            font-size: 14px;
            cursor: pointer;
            border: none;
            border-radius: 4px;
            background-color: #f44336; /* Red */
            color: white;
        }
        .logo {
            background-color: red;
            font-weight: bold;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }
        .logo:hover {
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
    <h2>All Orders</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Order Date</th>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Amount</th>
                <th>Shop Name</th>
                <th>Delivery Boy</th>
                <th>Admin Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['order_date']); ?></td>
                <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                <td><?php echo htmlspecialchars($row['amount']); ?></td>
                <td><?php echo htmlspecialchars($row['shop_name']); ?></td>
                <td><?php echo htmlspecialchars($row['delivery_boy_name']); ?></td>
                <td><?php echo htmlspecialchars($row['admin_name']); ?></td>
                <td class="action-buttons">
                    <a href="update_order.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="update">Update</a><hr>
                    <a href="delete_order.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="delete" onclick="return confirm('Are you sure you want to delete this order?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="delete-form">
        <form action="delete_all.php" method="post" onsubmit="return confirm('Are you sure you want to delete all orders?');">
            <input type="submit" value="Delete All Orders">
        </form>
    </div>
</div>

</body>
</html>
