<?php
session_start();
include("db.php"); // Adjust as per your database connection file

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];
$record = null;
$raw_materials = [];

try {
    // Fetch the existing record
    $stmt = $pdo->prepare("SELECT * FROM expenses_material WHERE id = ?");
    $stmt->execute([$id]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch all raw materials for the dropdown
    $stmt = $pdo->query("SELECT * FROM raw_materials");
    $raw_materials = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $raw_material_id = $_POST['raw_material_id'];
    $qty = $_POST['qty'];
    $date = $_POST['date'];
    $amount = $_POST['amount'];

    try {
        // Update the record
        $stmt = $pdo->prepare("UPDATE expenses_material SET raw_material_id = ?, qty = ?, date = ?, amount = ? WHERE id = ?");
        $stmt->execute([$raw_material_id, $qty, $date, $amount, $id]);
        header("Location:manage_expense_material.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Expense Material</title>
    <link rel="stylesheet" href="style.css">
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
            max-width: 500px;
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
        .back-button:hover {
            background-color: #45a049; /* Darker green */
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
        .update-form input, .update-form select {
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
            border: none;
            padding: 10px;
            font-size: 16px;
        }
        .update-form input[type="submit"]:hover {
            background-color: #45a049; /* Darker green */
        }
    </style>
    <script>
        function updateAmount() {
            var qty = parseFloat(document.getElementById('qty').value);
            var price = parseFloat(document.getElementById('raw_material_id').options[document.getElementById('raw_material_id').selectedIndex].getAttribute('data-price'));
            if (!isNaN(qty) && !isNaN(price)) {
                var amount = qty * price;
                document.getElementById('amount').value = amount.toFixed(2);
            } else {
                document.getElementById('amount').value = '';
            }
        }
    </script>
</head>
<body>

<!-- Main Content Area -->
<div class="container">
    <a href="manage_expense_material.php" class="back-button">Go Back</a>
    <br><br>
    <h2>Update Expense Material</h2>
    <div class="update-form">
        <form action="update_expenses_material.php?id=<?php echo htmlspecialchars($id); ?>" method="post">
            <label for="raw_material_id">Raw Material:</label>
            <select id="raw_material_id" name="raw_material_id" onchange="updateAmount()" required>
                <?php foreach ($raw_materials as $material): ?>
                    <option value="<?php echo htmlspecialchars($material['id']); ?>" <?php if ($material['id'] == $record['raw_material_id']) echo 'selected'; ?> data-price="<?php echo htmlspecialchars($material['price']); ?>">
                        <?php echo htmlspecialchars($material['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <label for="qty">Quantity:</label>
            <input type="number" id="qty" name="qty" value="<?php echo htmlspecialchars($record['qty']); ?>" oninput="updateAmount()" required>
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($record['date']); ?>" required>
            <label for="amount">Amount:</label>
            <input type="text" id="amount" name="amount" value="<?php echo htmlspecialchars($record['amount']); ?>" readonly>
            <input type="submit" value="Update Expense Material">
        </form>
    </div>
</div>

</body>
</html>
