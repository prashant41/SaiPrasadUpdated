<?php
session_start();
include("db.php");

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$employees = [];

try {
    $sql = "SELECT * FROM employees ORDER BY id ASC";
    $stmt = $pdo->query($sql);
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Employees</title>
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
            background-color: green ; /* Dark blue */
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
            background-color: #002147;
            color: whitesmoke;
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
        .create-form {
            margin-top: 20px;
            max-width: 400px; /* Adjusted for smaller form width */
            border: 1px solid #ddd; /* Add border */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .create-form input, .create-form select {
            padding: 8px;
            margin: 5px 0;
            width: calc(100% - 16px); /* Adjust width to account for padding */
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        .create-form input[type="submit"] {
            background-color: #4CAF50; /* Green */
            color: white;
            cursor: pointer;
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
    <a href="manage_users.php">Manage User</a>
    <a href="manage_items.php">Manage Items</a>
    <a href="manage_shop_names.php">Manage Shop Names</a>
    <a href="manage_delivery_boys.php">Manage Delivery Boys</a>
    <a href="manage_cash.php">Manage Cash</a>
    <a href="manage_raw_materials.php">Manage Raw Materials</a>
    <a href="manage_records.php">Manage Records</a>
    <a href="manage_employees.php">Manage Salary</a>
</div>

<!-- Main Content Area -->
<div class="content">
    <h2>Manage Employees</h2>

    <!-- Create Employee Form -->
    <div class="create-form">
        <h3>Add New Employee</h3>
        <form action="create_employee.php" method="post">
            <label for="name">Employee Name:</label>
            <input type="text" id="name" name="name" required>
            <label for="salary">Salary:</label>
            <input type="number" id="salary" name="salary" step="0.01" required>
            <label for="bonus">Bonus:</label>
            <input type="number" id="bonus" name="bonus" step="0.01">
            <label for="extra_money">Extra Money:</label>
            <input type="number" id="extra_money" name="extra_money" step="0.01">
            <label for="date_of_joining">Date of Joining:</label>
            <input type="date" id="date_of_joining" name="date_of_joining" required>
            <input type="submit" value="Add Employee">
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Salary</th>
                <th>Bonus</th>
                <th>Extra Money</th>
                <th>Date of Joining</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($employees as $employee): ?>
                <tr>
                    <td><?php echo htmlspecialchars($employee['id']); ?></td>
                    <td><?php echo htmlspecialchars($employee['name']); ?></td>
                    <td><?php echo htmlspecialchars($employee['salary']); ?></td>
                    <td><?php echo htmlspecialchars($employee['bonus']); ?></td>
                    <td><?php echo htmlspecialchars($employee['extra_money']); ?></td>
                    <td><?php echo htmlspecialchars($employee['date_of_joining']); ?></td>
                    <td class="action-buttons">
                        <a class="update" href="update_employee.php?id=<?php echo htmlspecialchars($employee['id']); ?>">Update</a>
                        <a class="delete" href="delete_employee.php?id=<?php echo htmlspecialchars($employee['id']); ?>" onclick="return confirm('Are you sure you want to delete this employee?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>