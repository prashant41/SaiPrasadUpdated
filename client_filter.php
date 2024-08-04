<?php
include("db.php");
session_start();

// Check if user is logged in, redirect to login page if not
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

// Fetch delivery boy names
try {
    $delivery_boy_query = $pdo->query("SELECT name FROM delivery_boys");
    $delivery_boys = $delivery_boy_query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching delivery boy names: " . $e->getMessage();
    exit();
}

// Fetch shop names
try {
    $shop_query = $pdo->query("SELECT DISTINCT shop_name FROM orders");
    $shops = $shop_query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching shop names: " . $e->getMessage();
    exit();
}

// Handle AJAX request for filtering orders
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Fetch POST data (JSON format)
    $data = json_decode(file_get_contents("php://input"), true);

    // Extract filter parameters
    $shopName = isset($data['shop_name']) ? $data['shop_name'] : null;
    $startDate = isset($data['start_date']) ? $data['start_date'] : null;
    $endDate = isset($data['end_date']) ? $data['end_date'] : null;
    $deliveryBoyName = isset($data['delivery_boy_name']) ? $data['delivery_boy_name'] : null;

    // Prepare SQL query with placeholders
    $sql = "SELECT id, DATE(order_date) as order_date, TIME(created_at) as order_time, item_name, quantity, amount, shop_name, delivery_boy_name, admin_name FROM orders WHERE 1";

    // Array to hold parameters for prepared statement
    $params = array();

    // Build WHERE clause dynamically based on selected filters
    if ($shopName && $shopName !== 'none') {
        $sql .= " AND shop_name = ?";
        $params[] = $shopName;
    }
    if ($startDate && $endDate) {
        $sql .= " AND DATE(order_date) BETWEEN ? AND ?";
        $params[] = $startDate;
        $params[] = $endDate;
    }
    if ($deliveryBoyName && $deliveryBoyName !== 'none') {
        $sql .= " AND delivery_boy_name = ?";
        $params[] = $deliveryBoyName;
    }

    $sql .= " ORDER BY order_date DESC";

    try {
        // Prepare and execute the query
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $filteredOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculate total amount
        $totalAmount = 0;
        foreach ($filteredOrders as $order) {
            $totalAmount += $order['amount'];
        }

        // Prepare response in JSON format
        $response = array(
            'orders' => $filteredOrders,
            'totalAmount' => $totalAmount
        );

        // Send JSON response back to JavaScript
        header('Content-Type: application/json');
        echo json_encode($response);
        exit(); // Exit to prevent further output
    } catch (PDOException $e) {
        echo "Error fetching filtered orders: " . $e->getMessage();
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filter Page</title>
    <link rel="stylesheet" href="client_style.css">
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

    .form-container {
        margin-bottom: 20px;
        padding: 10px;
        background-color: #f2f2f2;
        border-radius: 8px;
    }

    .form-container label {
        font-weight: bold;
        margin-right: 10px;
    }

    .form-container select, 
    .form-container input[type="submit"], 
    .form-container input[type="date"] {
        padding: 10px;
        margin: 5px;
        border: 2px solid #ddd;
        border-radius: 4px;
        background: #fff;
        font-size: 16px;
        transition: all 0.3s ease;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    }

    .form-container select:hover, 
    .form-container select:focus,
    .form-container input[type="submit"]:hover, 
    .form-container input[type="submit"]:focus,
    .form-container input[type="date"]:hover, 
    .form-container input[type="date"]:focus {
        border-color: #4CAF50;
        box-shadow: 0 0 8px rgba(0, 0, 0, 0.2);
    }

    .form-container input[type="submit"] {
        background: linear-gradient(90deg, #4CAF50 0%, #45a049 100%);
        color: white;
        cursor: pointer;
        font-weight: bold;
    }

    .form-container input[type="submit"]:hover {
        background: linear-gradient(90deg, #45a049 0%, #4CAF50 100%);
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

    <script>
        // Function to handle form submission and AJAX request
        function filterOrders() {
            var shopName = document.getElementById('shop_name').value;
            var startDate = document.getElementById('start_date').value;
            var endDate = document.getElementById('end_date').value;
            var deliveryBoyName = document.getElementById('delivery_boy_name').value;

            var data = {
                shop_name: shopName,
                start_date: startDate,
                end_date: endDate,
                delivery_boy_name: deliveryBoyName
            };

            var xhr = new XMLHttpRequest();
            xhr.open('POST', '<?php echo $_SERVER["PHP_SELF"]; ?>', true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    displayFilteredOrders(response);
                }
            };
            xhr.send(JSON.stringify(data));
        }

        // Function to display filtered orders and total amount
        function displayFilteredOrders(data) {
            var ordersTableBody = document.getElementById('orders_table_body');
            var totalAmountElement = document.getElementById('total_amount');
            
            ordersTableBody.innerHTML = ''; // Clear previous rows

            data.orders.forEach(function (order) {
                var row = `<tr>
                               <td>${order.id}</td>
                               <td>${order.order_date}</td>
                               <td>${order.order_time}</td>
                               <td>${order.item_name}</td>
                               <td>${order.quantity}</td>
                               <td>${order.amount}</td>
                               <td>${order.shop_name}</td>
                               <td>${order.delivery_boy_name}</td>
                               <td>${order.admin_name}</td>
                           </tr>`;
                ordersTableBody.innerHTML += row;
            });

            totalAmountElement.textContent = 'Total Amount: ' + data.totalAmount.toFixed(2); // Display total amount formatted
        }
    </script>
</head>
<body>
    <nav>
        <a href="client_dashboard.php" class="logo"><span>SAI PRASAD</span></a>
        <a href="client_display_order.php">Display Orders</a>
        <a href="client_filter.php">Filter Orders</a>
        <a href="logout.php" onclick="return confirm('Are you sure you want to log out?');">Log Out</a>
        <!-- Add more links as needed -->
    </nav>
    <div class="container">
        <div class="form-container">
            <form onsubmit="event.preventDefault(); filterOrders();">
                <label for="shop_name">Filter by Shop Name:</label>
                <select id="shop_name">
                    <option value="none">Select Shop Name</option>
                    <?php foreach ($shops as $shop): ?>
                        <option value="<?php echo htmlspecialchars($shop['shop_name']); ?>">
                            <?php echo htmlspecialchars($shop['shop_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <br>
                <label for="start_date">Filter by Start Date:</label>
                <input type="date" id="start_date">
                <br>
                <label for="end_date">Filter by End Date:</label>
                <input type="date" id="end_date">
                <br>
                <label for="delivery_boy_name">Filter by Delivery Boy:</label>
                <select id="delivery_boy_name">
                    <option value="none">Select Delivery Boy</option>
                    <?php foreach ($delivery_boys as $boy): ?>
                        <option value="<?php echo htmlspecialchars($boy['name']); ?>">
                            <?php echo htmlspecialchars($boy['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <br>
                <input type="submit" value="Filter">
            </form>
        </div>
        <div id="results">
            <h2>Filtered Orders:</h2>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Order Date</th>
                        <th>Order Time</th>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Amount</th>
                        <th>Shop Name</th>
                        <th>Delivery Boy Name</th>
                        <th>Admin Name</th>
                    </tr>
                </thead>
                <tbody id="orders_table_body">
                    <!-- Filtered orders will be displayed here -->
                </tbody>
            </table>
            <p id="total_amount">Total Amount: 0.00</p>
        </div>
    </div>
</body>
</html>
