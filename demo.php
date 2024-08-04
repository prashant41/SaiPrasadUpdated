<?php
session_start();
include("db.php"); // Adjust as per your database connection file

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $item_names = $_POST['item_name'];
    $quantities = $_POST['quantity'];
    $amounts = $_POST['amount'];
    $delivery_boy_names = $_POST['delivery_boy_name'];
    $shop_name = $_POST['shop_name']; // Assuming shop_name is selected once for all orders

    try {
        // Begin transaction
        $pdo->beginTransaction();

        // Prepare SQL statement for inserting orders
        $sql = "INSERT INTO orders_demo (item_name, quantity, amount, shop_name, delivery_boy_name, admin_name, order_date)
                VALUES (?, ?, ?, ?, ?, ?, NOW())"; // Assuming admin_name is set in session

        $stmt = $pdo->prepare($sql);

        // Loop through each order and execute the prepared statement
        for ($i = 0; $i < count($item_names); $i++) {
            $item_name = $item_names[$i];
            $quantity = $quantities[$i];
            $amount = $amounts[$i];
            $delivery_boy_name = $delivery_boy_names[$i];
            $admin_name = $_SESSION['username'];

            // Execute the prepared statement with current order data
            $stmt->execute([$item_name, $quantity, $amount, $shop_name, $delivery_boy_name, $admin_name]);
        }

        // Commit the transaction
        $pdo->commit();

        $message = "Orders added successfully";
    } catch (Exception $e) {
        // Rollback the transaction if an error occurs
        $pdo->rollBack();
        $message = "Error inserting orders: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Form</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Your existing CSS styles */
    </style>
</head>
<body>

<nav>
    <a href="admin_dashboard.php" class="logo"><span>SAI PRASAD</span></a>
    <a href="order_form.php">Order Form</a>
    <a href="display_orders.php">Display Orders</a>
    <a href="admin_filter.php">Filter Orders</a>
    <a href="report.php">Report</a>
    <a href="raw_materials.php">Raw Material Data</a>
    <a href="logout.php" onclick="return confirm('Are you sure you want to log out?');">Log Out</a>
    <!-- Add more links as needed -->
</nav><br>

<div class="container">
    <h2>Order Form</h2>

    <?php if (!empty($message)): ?>
        <?php if (strpos($message, 'successfully') !== false): ?>
            <div class="success-message"><?php echo htmlspecialchars($message); ?></div>
        <?php else: ?>
            <div class="error-message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
    <?php endif; ?>

    <form action="process_orders.php" method="POST">
    <div class="order-group">
        <div class="form-group">
            <label for="item_name1">Item Name:</label>
            <select name="item_name[]" id="item_name1">
                <!-- Options for item names -->
                <option value="none">None</option>
                <option value="halftea">Half tea</option>
                <option value="regulartea">Regular Tea</option>
                <option value="regularteaw">Regular Tea(W)</option>
                <option value="supertea">Super Tea</option>
                <option value="superteaw">Super Tea(W)</option>
                <option value="lemontea">Lemon Tea</option>
                <option value="lemonteaw">Lemon Tea(W)</option>
                <option value="blacktea">Black Tea</option>
                <option value="blackteaw">Black Tea(W)</option>
                <option value="ukala">Ukala</option>
                <option value="ukalaw">Ukala(W)</option>
                <option value="kesariukala">Kesari Ukala</option>
                <option value="kesariukalaw">Kesari Ukala(W)</option>
                <option value="coffee">Coffee</option>
                <option value="coffeew">Coffee</option>
                <option value="nescafe">Nescafe</option>
                <option value="nescafew">Nescafe(W)</option>
                <option value="blackcoffee">Black Coffee</option>
                <option value="blackcoffeew">Black Coffee(W)</option>
                <option value="snacks">Snacks</option>
                <option value="coldrink">Coldrinks</option>
            </select>
        </div>
        <div class="form-group">
            <label for="quantity1">Quantity:</label>
            <input type="number" id="quantity1" name="quantity[]" min="1" required>
        </div>
        <div class="form-group">
            <label for="amount1">Amount:</label>
            <input type="number" id="amount1" name="amount[]" required>
        </div>
        <div class="form-group">
            <label for="delivery_boy_name1">Delivery Boy Name:</label>
            <select name="delivery_boy_name[]" id="delivery_boy_name1">
                <!-- Options for delivery boy names -->
                <option value="none">None</option>
                <option value="nithin">Nithin</option>
                <option value="vimlesh">Vimlesh</option>
                <option value="ashish">Ashish</option>
                <option value="sharad">Sharad</option>
                <option value="others">Others</option>
            </select>
        </div>
    </div>

    <!-- Additional order groups can be added dynamically or statically -->

    <div class="form-actions">
        <input type="submit" value="Submit Orders">
        <input type="reset" value="Reset">
    </div>
</form>

</div>

</body>
</html>
