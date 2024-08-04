<?php
include("db.php");
session_start();

// Check if user is logged in, redirect to login page if not
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

// Handle AJAX request for filtering orders
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Fetch POST data (JSON format)
    $data = json_decode(file_get_contents("php://input"), true);

    // Extract filter parameters
    $deliveryBoy = isset($data['delivery_boy']) ? $data['delivery_boy'] : null;
    $shopName = isset($data['shop_name']) ? $data['shop_name'] : null;
    $itemName = isset($data['item_name']) ? $data['item_name'] : null;
    $startDate = isset($data['start_date']) ? $data['start_date'] : null;
    $endDate = isset($data['end_date']) ? $data['end_date'] : null;

    // Prepare SQL query with placeholders
    $sql = "SELECT id, DATE(order_date) as order_date, TIME(created_at) as order_time, item_name, quantity, amount, shop_name, delivery_boy_name, admin_name FROM orders WHERE 1";

    // Array to hold parameters for prepared statement
    $params = array();

    // Build WHERE clause dynamically based on selected filters
    if ($deliveryBoy && $deliveryBoy !== 'none') {
        $sql .= " AND delivery_boy_name = ?";
        $params[] = $deliveryBoy;
    }
    if ($shopName && $shopName !== 'none') {
        $sql .= " AND shop_name = ?";
        $params[] = $shopName;
    }
    if ($itemName && $itemName !== 'none') {
        $sql .= " AND item_name = ?";
        $params[] = $itemName;
    }
    if ($startDate && $endDate) {
        $sql .= " AND DATE(order_date) BETWEEN ? AND ?";
        $params[] = $startDate;
        $params[] = $endDate;
    }

    $sql .= " ORDER BY order_date DESC";

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
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filter Page</title>
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
        }
        .form-container select, .form-container input[type="submit"], .form-container input[type="date"] {
            padding: 8px;
            margin: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-container input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }
        .form-container input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
    <script>
        // Function to handle form submission and AJAX request
        function filterOrders() {
            var deliveryBoy = document.getElementById('delivery_boy').value;
            var shopName = document.getElementById('shop_name').value;
            var itemName = document.getElementById('item_name').value;
            var startDate = document.getElementById('start_date').value;
            var endDate = document.getElementById('end_date').value;

            var data = {
                delivery_boy: deliveryBoy,
                shop_name: shopName,
                item_name: itemName,
                start_date: startDate,
                end_date: endDate
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

            totalAmountElement.textContent = 'Total Amount: ' + data.totalAmount;
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <form onsubmit="event.preventDefault(); filterOrders();">
                <label for="delivery_boy">Filter by Delivery Boy Name:</label>
                <select id="delivery_boy">
                    <option value="none">Select Delivery Boy</option>
                    <option value="nithin">Nithin</option>
                    <option value="sharad">Sharad</option>
                    <option value="ashish">Ashish</option>
                    <option value="vimlesh">Vimlesh</option>
                </select>
                <br>
                <label for="shop_name">Filter by Shop Name:</label>
                <select id="shop_name">
                    <option value="none">Select Shop Name</option>
                    <option value="sakari">Sakari</option>
                    <option value="bata">Bata</option>
                    <option value="premson">Premson</option>
                </select>
                <br>
                <label for="item_name">Filter by Item Name:</label>
                <select id="item_name">
                    <option value="none">Select Item Name</option>
                    <option value="super tea">Super Tea</option>
                    <option value="lemon tea">Lemon Tea</option>
                    <option value="black tea">Black Tea</option>
                </select>
                <br>
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date">
                <br>
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date">
                <br>
                <input type="submit" value="Filter Orders">
            </form>
        </div>

        <h2>Filtered Orders</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Order Date</th>
                    <th>Order Time</th>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Amount</th>
                    <th>Shop Name</th>
                    <th>Delivery Boy</th>
                    <th>Admin Name</th>
                </tr>
            </thead>
            <tbody id="orders_table_body">
                <!-- Table rows will be populated dynamically -->
            </tbody>
        </table>

        <p id="total_amount"></p>
    </div>
</body>
</html>
