<?php
include("db.php");
session_start();
if(!isset($_SESSION["username"])){
    header("Location:login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    
    <style>
        /* Basic CSS for navigation */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
        }
        /* Top Navigation Bar */
        nav.top-nav {
            background-color: #002147;
            overflow: hidden;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1;
        }
        nav.top-nav a {
            float: left;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
        }
        nav.top-nav a:hover {
            background-color: #ddd;
            color: black;
        }
        /* Side Navigation Bar */
        nav.side-nav {
            background-color: #008000; /* Green color */
            width: 200px;
            height: 100vh;
            position: fixed;
            top: 50px; /* Adjust based on the height of the top-nav */
            left: 0;
            overflow: auto;
            z-index: 1;
        }
        nav.side-nav a {
            display: block;
            color: white;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
        }
        nav.side-nav a:hover {
            background-color: #ddd;
            color: black;
        }
        .main-content {
            margin-left: 220px; /* Adjust to make space for the side-nav */
            padding: 20px;
            margin-top: 50px; /* Adjust based on the height of the top-nav */
            flex: 1;
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
    <script>
        function confirmLogout() {
            return confirm('Are you sure you want to log out?');
        }
    </script>
</head>
<body>
    <!-- Top Navigation Bar -->
    <nav class="top-nav">
        <a href="admin_dashboard.php" class="logo"><span>SAI PRASAD</span></a>
        <a href="order_form.php">Order Form</a>
        <a href="display_orders.php">Display Orders</a>
        <a href="admin_filter.php">Filter Orders</a>
        <a href="manage_users.php">Manage</a>
        <a href="report.php">Report</a>
        <a href="pnl.php">PnL</a>
        <a href="logout.php" onclick="return confirmLogout()";>Log Out</a>
        <!-- Add more links as needed -->
    </nav>

    <!-- Side Navigation Bar -->
    <nav class="side-nav">
        <a href="manage_users.php">Manage User</a>
        <a href="manage_items.php">Manage Items</a>
        <a href="manage_shop_names.php">Manage Shop Names</a>
        <a href="manage_delivery_boys.php">Manage Delivery Boys</a>
    </nav>

    <!-- Main Content Area -->
    <div class="main-content">
        <h1>Welcome to Admin Dashboard</h1>
        <h2>Hello <?php echo htmlspecialchars($_SESSION["username"]); ?>, welcome back!!!</h2>
        <p>This is where you check your orders, and more.</p>
    </div>
</body>
</html>
<?php
include("db.php");
session_start();
if(!isset($_SESSION["username"])){
    header("Location:login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    
    <style>
        /* Basic CSS for navigation */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
        }
        /* Top Navigation Bar */
        nav.top-nav {
            background-color: #002147;
            overflow: hidden;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1;
        }
        nav.top-nav a {
            float: left;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
        }
        nav.top-nav a:hover {
            background-color: #ddd;
            color: black;
        }
        /* Side Navigation Bar */
        nav.side-nav {
            background-color: #008000; /* Green color */
            width: 200px;
            height: 100vh;
            position: fixed;
            top: 50px; /* Adjust based on the height of the top-nav */
            left: 0;
            overflow: auto;
            z-index: 1;
        }
        nav.side-nav a {
            display: block;
            color: white;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
        }
        nav.side-nav a:hover {
            background-color: #ddd;
            color: black;
        }
        .main-content {
            margin-left: 220px; /* Adjust to make space for the side-nav */
            padding: 20px;
            margin-top: 50px; /* Adjust based on the height of the top-nav */
            flex: 1;
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
    <script>
        function confirmLogout() {
            return confirm('Are you sure you want to log out?');
        }
    </script>
</head>
<body>
    <!-- Top Navigation Bar -->
    <nav class="top-nav">
        <a href="admin_dashboard.php" class="logo"><span>SAI PRASAD</span></a>
        <a href="order_form.php">Order Form</a>
        <a href="display_orders.php">Display Orders</a>
        <a href="admin_filter.php">Filter Orders</a>
        <a href="manage_users.php">Manage</a>
        <a href="report.php">Report</a>
        <a href="pnl.php">PnL</a>
        <a href="logout.php" onclick="return confirmLogout()";>Log Out</a>
        <!-- Add more links as needed -->
    </nav>

    <!-- Side Navigation Bar -->
    <nav class="side-nav">
        <a href="manage_users.php">Manage User</a>
        <a href="manage_items.php">Manage Items</a>
        <a href="manage_shop_names.php">Manage Shop Names</a>
        <a href="manage_delivery_boys.php">Manage Delivery Boys</a>
    </nav>

    <!-- Main Content Area -->
    <div class="main-content">
        <h1>Welcome to Admin Dashboard</h1>
        <h2>Hello <?php echo htmlspecialchars($_SESSION["username"]); ?>, welcome back!!!</h2>
        <p>This is where you check your orders, and more.</p>
    </div>
</body>
</html>
<?php
include("db.php");
session_start();
if(!isset($_SESSION["username"])){
    header("Location:login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    
    <style>
        /* Basic CSS for navigation */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
        }
        /* Top Navigation Bar */
        nav.top-nav {
            background-color: #002147;
            overflow: hidden;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1;
        }
        nav.top-nav a {
            float: left;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
        }
        nav.top-nav a:hover {
            background-color: #ddd;
            color: black;
        }
        /* Side Navigation Bar */
        nav.side-nav {
            background-color: #008000; /* Green color */
            width: 200px;
            height: 100vh;
            position: fixed;
            top: 50px; /* Adjust based on the height of the top-nav */
            left: 0;
            overflow: auto;
            z-index: 1;
        }
        nav.side-nav a {
            display: block;
            color: white;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
        }
        nav.side-nav a:hover {
            background-color: #ddd;
            color: black;
        }
        .main-content {
            margin-left: 220px; /* Adjust to make space for the side-nav */
            padding: 20px;
            margin-top: 50px; /* Adjust based on the height of the top-nav */
            flex: 1;
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
    <script>
        function confirmLogout() {
            return confirm('Are you sure you want to log out?');
        }
    </script>
</head>
<body>
    <!-- Top Navigation Bar -->
    <nav class="top-nav">
        <a href="admin_dashboard.php" class="logo"><span>SAI PRASAD</span></a>
        <a href="order_form.php">Order Form</a>
        <a href="display_orders.php">Display Orders</a>
        <a href="admin_filter.php">Filter Orders</a>
        <a href="manage_users.php">Manage</a>
        <a href="report.php">Report</a>
        <a href="pnl.php">PnL</a>
        <a href="logout.php" onclick="return confirmLogout()";>Log Out</a>
        <!-- Add more links as needed -->
    </nav>

    <!-- Side Navigation Bar -->
    <nav class="side-nav">
        <a href="manage_users.php">Manage User</a>
        <a href="manage_items.php">Manage Items</a>
        <a href="manage_shop_names.php">Manage Shop Names</a>
        <a href="manage_delivery_boys.php">Manage Delivery Boys</a>
    </nav>

    <!-- Main Content Area -->
    <div class="main-content">
        <h1>Welcome to Admin Dashboard</h1>
        <h2>Hello <?php echo htmlspecialchars($_SESSION["username"]); ?>, welcome back!!!</h2>
        <p>This is where you check your orders, and more.</p>
    </div>
</body>
</html>
