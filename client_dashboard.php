<?php
include("db.php");
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

// Fetch total cash amount and balance for the logged-in delivery boy
$delivery_boy_name = $_SESSION["username"]; // Assuming the username is the delivery boy's name
$sql = "SELECT SUM(cash) AS total_amount, SUM(balance) AS total_balance FROM delivery_boy_cash WHERE delivery_boy_name = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$delivery_boy_name]);
$row = $stmt->fetch();

$total_amount = $row['total_amount'] ? $row['total_amount'] : 0;
$total_balance = $row['total_balance'] ? $row['total_balance'] : 0;

$commission_rate = 0.16; // 16%
$commission = $total_amount * $commission_rate;
$amount_to_be_paid = $commission - $total_balance; // Adjusted calculation
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard</title>
    <script>
        function confirmLogout() {
            return confirm('Are you sure you want to log out?');
        }
    </script>
    <style>
        /* Basic CSS for navigation */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        nav {
            background-color: #333;
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
        .logo {
            background-color: red;
            font-weight: bold;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }
        .logo:hover {
            background-color: #4CAF50;
        }
        .container {
            padding: 20px;
            max-width: 800px; /* Adjust as needed */
            margin: 0 auto;
        }
        .amounts {
            border: 1px solid #ccc;
            padding: 20px;
            margin-top: 20px;
            border-radius: 4px;
            box-sizing: border-box; /* Ensures padding does not affect width */
        }
        .amounts h3 {
            margin: 0 0 10px 0;
        }
        .amounts .amount-to-be-paid {
            color: green;
        }
        .amounts .total-balance {
            color: red;
        }
    </style>
</head>
<body>
   <nav>
        <a href="client_dashboard.php" class="logo"><span>SAI PRASAD</span></a>
        <a href="client_display_order.php">Display Orders</a>
        <a href="client_filter.php">Filter Orders</a>
        <a href="logout.php" onclick="return confirmLogout();">Log Out</a>
    </nav>
    
    <div class="container">
        <h1>Welcome to Client Dashboard</h1>
        <h2>Hello <?php echo htmlspecialchars($_SESSION["username"]); ?>, welcome back!</h2>
        <p>This is where you check your orders, and commissions.</p>

        <div class="amounts">
            <h3>Total Cash Amount: <?php echo number_format($total_amount, 2); ?> ₹</h3>
            <h3 class="total-balance">Total Balance: <?php echo number_format($total_balance, 2); ?> ₹</h3>
            <h3>Commission (0.16%): <?php echo number_format($commission, 2); ?> ₹</h3>
            <h3 class="amount-to-be-paid">Amount to be Paid: <?php echo number_format($amount_to_be_paid, 2); ?> ₹</h3>
        </div>
    </div>
</body>
</html>
