<?php
include("db.php");
session_start();

if (!isset($_SESSION['username']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'super_admin')) {
    header("Location: login.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['cash_submit'])) {
        $delivery_boy_name = $_POST['delivery_boy_name'];
        $cash = $_POST['cash'];
        $paid = $_POST['paid'];
        $date = $_POST['date'];

        $sql = "INSERT INTO delivery_boy_cash (delivery_boy_name, cash, paid, date) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$delivery_boy_name, $cash, $paid, $date]);

        $successMessage = "Cash entry added successfully.";
    }
    if (isset($_POST['delete_record'])) {
        $id = $_POST['id'];

        $sql = "DELETE FROM delivery_boy_cash WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);

        $successMessage = "Cash entry deleted successfully.";
    }
}

// Fetch delivery boys for the dropdown
$deliveryBoys = $pdo->query("SELECT name FROM delivery_boys")->fetchAll();

// Fetch the latest calculations
$latestCalculations = $pdo->query("
    SELECT delivery_boy_name, SUM(cash) as total_cash, SUM(paid) as total_paid, SUM(cash - paid) as total_balance
    FROM delivery_boy_cash
    GROUP BY delivery_boy_name
    ORDER BY MAX(id) DESC
    LIMIT 3
")->fetchAll();

// Fetch the latest three records for the table
$allRecords = $pdo->query("SELECT * FROM delivery_boy_cash ORDER BY id DESC LIMIT 6")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Boy Cash Entry</title>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .back-button {
            background-color: #007BFF; /* Blue background */
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
            margin: 20px;
        }
        .back-button:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }
        .container {
            display: flex;
            padding: 20px;
        }
        .form-container {
            flex: 1;
            margin-right: 20px;
            border: 2px solid black;
            border-radius: 8px;
            padding: 20px;
            max-width: 400px; /* Adjust the max width as needed */
        }
        .data-container {
            flex: 2;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            margin: 0;
            width: 100%;
        }
        label {
            margin-top: 10px;
        }
        input[type="text"], input[type="number"], select, input[type="date"] {
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
        .button-container {
            display: flex;
            gap: 10px; /* Space between buttons */
        }

        button {
            background-color: #4CAF50; /* Green background */
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        button:hover {
            background-color: #45a049; /* Darker green on hover */
        }

        .delete-button {
            background-color: #f44336; /* Red background */
        }

        .delete-button:hover {
            background-color: #da190b; /* Darker red on hover */
        }
    </style>
    <script>
        function confirmDelete() {
            return confirm("Are you sure you want to delete this record?");
        }
    </script>
</head>
<body>
    <a href="daily_entry.php" class="back-button">Back</a>

    <div class="container">
        <div class="form-container">
            <h1>Delivery Boy Cash Entry:</h1>
            <?php if (isset($successMessage)): ?>
                <p class="success-message"><?php echo $successMessage; ?></p>
            <?php endif; ?>
            <form action="delivery_boy_data.php" method="POST">
                <label for="delivery_boy_name">Delivery Boy Name:</label>
                <select id="delivery_boy_name" name="delivery_boy_name" required>
                    <?php foreach ($deliveryBoys as $boy): ?>
                        <option value="<?php echo $boy['name']; ?>"><?php echo $boy['name']; ?></option>
                    <?php endforeach; ?>
                </select>
                
                <label for="cash">Cash:</label>
                <input type="number" step="0.01" id="cash" name="cash" required>

                <label for="paid">Paid:</label>
                <input type="number" step="0.01" id="paid" name="paid" required>
                
                <label for="date">Date:</label>
                <input type="date" id="date" name="date" required>
                
                <input type="submit" name="cash_submit" value="Submit">
            </form>
        </div>
        
        <div class="data-container">
            <h2>Latest Calculations:</h2>
            <table>
                <tr>
                    <th>Delivery Boy Name</th>
                    <th>Total Cash</th>
                    <th>Total Paid</th>
                    <th>Total Balance</th>
                </tr>
                <?php foreach ($latestCalculations as $calculation): ?>
                <tr>
                    <td><?php echo $calculation['delivery_boy_name']; ?></td>
                    <td><?php echo $calculation['total_cash']; ?></td>
                    <td><?php echo $calculation['total_paid']; ?></td>
                    <td><?php echo $calculation['total_balance']; ?></td>
                </tr>
                <?php endforeach; ?>
            </table>

            <h2>All Records:</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Delivery Boy Name</th>
                    <th>Cash</th>
                    <th>Paid</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($allRecords as $record): ?>
                <tr>
                    <td><?php echo $record['id']; ?></td>
                    <td><?php echo $record['delivery_boy_name']; ?></td>
                    <td><?php echo $record['cash']; ?></td>
                    <td><?php echo $record['paid']; ?></td>
                    <td><?php echo $record['date']; ?></td>
                    <td>
                        <form action="delivery_boy_data.php" method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $record['id']; ?>">
                            <button type="submit" name="delete_record" class="delete-button" onclick="return confirmDelete();">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>
