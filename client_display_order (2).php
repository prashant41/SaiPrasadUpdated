<?php
session_start();
include("db.php"); // Adjust as per your database connection file

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION["username"];

// Query to fetch the 15 latest orders for the logged-in delivery boy (client)
$sql = "SELECT * FROM orders WHERE delivery_boy_name = :username ORDER BY created_at DESC LIMIT 15";
$stmt = $pdo->prepare($sql);
$stmt->execute(['username' => $username]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Orders</title>
    <link rel="stylesheet" href="client_style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table, th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        td button {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 8px 16px;
            cursor: pointer;
            border-radius: 4px;
        }
        td button:hover {
            background-color: #e53935;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            border-radius: 8px;
        }
        .modal-content h3 {
            margin-top: 0;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
        }
        #confirmMistake {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 16px;
            cursor: pointer;
            border-radius: 4px;
        }
        #confirmMistake:hover {
            background-color: #45a049;
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
        function reportMistake(orderId) {
            var modal = document.getElementById('myModal');
            var span = document.getElementsByClassName('close')[0];
            
            modal.style.display = "block";
            
            span.onclick = function() {
                modal.style.display = "none";
            }
            
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
            
            var confirmButton = document.getElementById('confirmMistake');
            confirmButton.onclick = function() {
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        alert("Mistake reported successfully!");
                        modal.style.display = "none";
                        // You can optionally update the UI here if needed
                    }
                };
                xhttp.open("POST", "order_mistake.php", true);
                xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhttp.send("order_id=" + orderId);
            };
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
    <div style="padding: 20px;">
        <h2>Latest Orders</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Order Date</th>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Amount</th>
                    <th>Shop Name</th>
                    <th>Admin Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['order_date']; ?></td>
                    <td><?php echo $row['item_name']; ?></td>
                    <td><?php echo $row['quantity']; ?></td>
                    <td><?php echo $row['amount']; ?></td>
                    <td><?php echo $row['shop_name']; ?></td>
                    <td><?php echo $row['admin_name']; ?></td>
                    <td>
                        <button onclick="reportMistake(<?php echo $row['id']; ?>)">Report Mistake</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Report Mistake</h3>
            <p>Are you sure you want to report a mistake for this order?</p>
            <button id="confirmMistake">Confirm</button>
        </div>
    </div>

</body>
</html>
