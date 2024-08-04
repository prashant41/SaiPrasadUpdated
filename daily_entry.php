<?php
include("db.php");
session_start();

if (!isset($_SESSION['username']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'super_admin')) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['cash_submit'])) {
        $date = date('Y-m-d');
        $counter_cash = $_POST['counter_cash'];
        $delivery_boy_cash = $_POST['delivery_boy_cash'];
        $book_cash = $_POST['book_cash'];

        $sql = "INSERT INTO revenue(date, counter_cash, delivery_boy_cash, book_cash) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$date, $counter_cash, $delivery_boy_cash, $book_cash]);

        $successMessage = "Cash entry added successfully.";
    }

    if (isset($_POST['material_submit'])) {
        $date = date('Y-m-d');
        $raw_material_id = $_POST['raw_material_id'];
        $raw_material_name = $_POST['raw_material_name'];
        $qty = $_POST['qty'];
        $unit = $_POST['unit'];
        $amount = $_POST['amount'];

        $sql = "INSERT INTO expenses_material(date, raw_material_id, raw_material_name, qty, unit, amount) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$date, $raw_material_id, $raw_material_name, $qty, $unit, $amount]);

        $successMessage = "Raw material entry added successfully.";
    }
}

// Fetch raw materials for the dropdown
$rawMaterials = $pdo->query("SELECT id, name, unit, price FROM raw_materials")->fetchAll();

// Fetch the two latest entries for cash and raw material
$latestCashEntries = $pdo->query("SELECT * FROM revenue ORDER BY id DESC LIMIT 2")->fetchAll();
$latestMaterialEntries = $pdo->query("SELECT * FROM expenses_material ORDER BY id DESC LIMIT 2")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Entry</title>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        nav {
            background-color: #002147;
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
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            padding: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            margin: 20px;
            width: 300px;
        }
        label {
            margin-top: 10px;
        }
        input[type="text"], input[type="number"], select {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            width: 100%;
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px 12px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        .success-message {
            padding: 10px;
            margin: 10px 0;
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
            border-radius: 4px;
            opacity: 1;
            transition: opacity 2s ease-out;
        }
        .logo {
            background-color: red;
            font-weight: bold;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }
        .logo:hover {
            background-color: #4CAF50;
        }
        .raw-material-container {
            position: relative;
            padding: 20px; /* Adjust padding as needed */
        }
        .barwala-button {
            position: absolute;
            top: 20px; /* Adjust to fit your design */
            right: 20px; /* Adjust to fit your design */
        }
        .barwala-button button {
            background-color: #4CAF50; /* Green background */
            color: white; /* White text */
            padding: 10px 20px; /* Padding inside the button */
            border: none; /* Remove default border */
            border-radius: 5px; /* Rounded corners */
            font-size: 16px; /* Larger font size */
            cursor: pointer; /* Pointer cursor on hover */
            transition: background-color 0.3s; /* Smooth background color change */
        }
        .barwala-button button:hover {
            background-color: #45a049; /* Darker green on hover */
        }
    </style>
    <script>
        function confirmLogout() {
            return confirm('Are you sure you want to log out?');
        }

        function calculateAmount() {
            var qty = document.getElementById('qty').value;
            var price = document.getElementById('raw_material_id').selectedOptions[0].getAttribute('data-price');
            var amount = qty * price;
            document.getElementById('amount').value = amount.toFixed(2);

            var unit = document.getElementById('raw_material_id').selectedOptions[0].getAttribute('data-unit');
            document.getElementById('unit').value = unit;

            var raw_material_name = document.getElementById('raw_material_id').selectedOptions[0].text;
            document.getElementById('raw_material_name').value = raw_material_name;
        }

        window.onload = function() {
            setTimeout(function() {
                var message = document.querySelector('.success-message');
                if (message) {
                    message.style.opacity = '0';
                }
            }, 2000);
        }
    </script>
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
    </nav>

    <div class="container">
        <div>
            <h1>Cash Entry:</h1>
            <?php if (isset($successMessage) && isset($_POST['cash_submit'])): ?>
                <p class="success-message"><?php echo $successMessage; ?></p>
            <?php endif; ?>
            <form action="daily_entry.php" method="POST">
                <label for="counter_cash">Counter Cash:</label>
                <input type="number" step="0.01" id="counter_cash" name="counter_cash" required>
                
                <label for="delivery_boy_cash">Delivery Boy Cash:</label>
                <input type="number" step="0.01" id="delivery_boy_cash" name="delivery_boy_cash" required>
                
                <label for="book_cash">Book Cash:</label>
                <input type="number" step="0.01" id="book_cash" name="book_cash" required>
                
                <input type="submit" name="cash_submit" value="Submit">
            </form>
            
            <h2>Latest Cash Entries:</h2>
            <table>
                <tr>
                    <th>Date</th>
                    <th>Counter Cash</th>
                    <th>Delivery Boy Cash</th>
                    <th>Book Cash</th>
                </tr>
                <?php foreach ($latestCashEntries as $entry): ?>
                <tr>
                    <td><?php echo $entry['date']; ?></td>
                    <td><?php echo $entry['counter_cash']; ?></td>
                    <td><?php echo $entry['delivery_boy_cash']; ?></td>
                    <td><?php echo $entry['book_cash']; ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div class="raw-material-container">
            <a href="delivery_boy_data.php" class="barwala-button">
                <button>Barwala Cash</button>
            </a>
            <h1>Raw Material Entry:</h1>
            <?php if (isset($successMessage) && isset($_POST['material_submit'])): ?>
                <p class="success-message"><?php echo $successMessage; ?></p>
            <?php endif; ?>
            <form action="daily_entry.php" method="POST">
                <label for="raw_material_id">Raw Material:</label>
                <select id="raw_material_id" name="raw_material_id" onchange="calculateAmount()" required>
                    <option value="">Select Raw Material</option>
                    <?php foreach ($rawMaterials as $material): ?>
                        <option value="<?php echo $material['id']; ?>" data-price="<?php echo $material['price']; ?>" data-unit="<?php echo $material['unit']; ?>">
                            <?php echo $material['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <label for="raw_material_name">Name:</label>
                <input type="text" id="raw_material_name" name="raw_material_name" readonly>
                
                <label for="qty">Quantity:</label>
                <input type="number" id="qty" name="qty" min="1" onchange="calculateAmount()" required>
                
                <label for="unit">Unit:</label>
                <input type="text" id="unit" name="unit" readonly>
                
                <label for="amount">Amount:</label>
                <input type="number" step="0.01" id="amount" name="amount" readonly>
                
                <input type="submit" name="material_submit" value="Submit">
            </form>
            
            <h2>Latest Raw Material Entries:</h2>
            <table>
                <tr>
                    <th>Date</th>
                    <th>Raw Material Name</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Amount</th>
                </tr>
                <?php foreach ($latestMaterialEntries as $entry): ?>
                <tr>
                    <td><?php echo $entry['date']; ?></td>
                    <td><?php echo $entry['raw_material_name']; ?></td>
                    <td><?php echo $entry['qty']; ?></td>
                    <td><?php echo $entry['unit']; ?></td>
                    <td><?php echo $entry['amount']; ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>
