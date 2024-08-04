<?php
session_start();
include("db.php"); // Adjust as per your database connection file

// Check if user is logged in as admin (or implement role-based checks)
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php"); // Redirect unauthorized users
    exit();
}

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $orderId = $_POST['order_id'];
    $orderDate = $_POST['order_date'];
    $itemName = $_POST['item_name'];
    $quantity = $_POST['quantity'];
    $amount = $_POST['amount'];
    $shopName = $_POST['shop_name'];
    $deliveryBoy = $_POST['delivery_boy_name'];
    $adminName = $_SESSION['username']; // Assuming admin name is logged in user

    // Prepare SQL statement to update order details
    $sql = "UPDATE orders SET order_date = ?, item_name = ?, quantity = ?, amount = ?, shop_name = ?, delivery_boy_name = ?, admin_name = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);

    // Bind parameters and execute the statement
    $stmt->execute([$orderDate, $itemName, $quantity, $amount, $shopName, $deliveryBoy, $adminName, $orderId]);

    // Redirect back to display_orders.php after update
    header("Location: display_orders.php");
    exit();
} else {
    // Check if order ID is provided via GET request
    if (isset($_GET['id'])) {
        $orderId = $_GET['id'];

        // Fetch existing order details by ID
        $sql = "SELECT * FROM orders WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        // Display the update form with pre-filled values
        if ($order) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Order</title>
    <link rel="stylesheet" href="style.css"> <!-- Ensure this links to your CSS file -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 100%;
            max-width: 600px;
            position: relative;
        }
        .back-button {
            position: absolute;
            top: 10px;
            left: 10px;
            padding: 10px 15px;
            background-color: #4CAF50; /* Green */
            color: white;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
        }
        h2 {
            text-align: center;
            color: #002147; /* Dark blue */
            margin-top: 0;
        }
        .update-form {
            width: 100%;
        }
        .update-form label {
            display: block;
            margin-top: 10px;
        }
        .update-form input {
            padding: 8px;
            margin: 5px 0;
            width: 100%;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        .update-form input[type="submit"] {
            background-color: #4CAF50; /* Green */
            color: white;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="container">
    <a href="display_orders.php" class="back-button">Go Back</a>
    <h2>Update Order</h2>
    <div class="update-form">
        <form action="update_order.php" method="POST">
            <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['id']); ?>">
            
            <label for="order_date">Order Date:</label>
            <input type="date" id="order_date" name="order_date" value="<?php echo htmlspecialchars($order['order_date']); ?>" required>
            
            <label for="item_name">Item Name:</label>
            <input type="text" id="item_name" name="item_name" value="<?php echo htmlspecialchars($order['item_name']); ?>" required>
            
            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" value="<?php echo htmlspecialchars($order['quantity']); ?>" required>
            
            <label for="amount">Amount:</label>
            <input type="text" id="amount" name="amount" value="<?php echo htmlspecialchars($order['amount']); ?>" required>
            
            <label for="shop_name">Shop Name:</label>
            <input type="text" id="shop_name" name="shop_name" value="<?php echo htmlspecialchars($order['shop_name']); ?>">
            
            <label for="delivery_boy_name">Delivery Boy Name:</label>
            <input type="text" id="delivery_boy_name" name="delivery_boy_name" value="<?php echo htmlspecialchars($order['delivery_boy_name']); ?>">
            
            <input type="submit" value="Update Order">
        </form>
    </div>
</div>

</body>
</html>
<?php
        } else {
            echo "Order not found.";
        }
    } else {
        echo "Order ID not specified.";
    }
}
?>
