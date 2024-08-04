<?php
session_start();
include("db.php"); // Adjust as per your database connection file

// Check if the user is logged in as admin
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php"); // Redirect unauthorized users
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate the input for delete confirmation
    if (isset($_POST["confirmation"]) && $_POST["confirmation"] === "Sbvp0342@") {
        // Truncate the orders table
        $sql = "TRUNCATE TABLE orders";
        $stmt = $pdo->prepare($sql);

        try {
            $stmt->execute();
            header("Location: display_orders.php?message=All+orders+deleted");
            exit();
        } catch (Exception $e) {
            echo "Error deleting all orders: " . $e->getMessage();
        }
    } else {
        echo "";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete All Orders</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        label {
            font-weight: bold;
        }
        input[type="text"], input[type="submit"], .btn-go-back {
            padding: 8px;
            margin: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #f44336; /* Red */
            color: white;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #d32f2f; /* Darker red */
        }
        .btn-go-back {
            background-color: #2196F3; /* Blue */
            color: white;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            font-weight: bold;
        }
        .btn-go-back:hover {
            background-color: #1976D2; /* Darker blue */
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Delete All Orders</h2>
    <form method="post">
        <label for="confirmation">Type Password to confirm:</label>
        <input type="text" id="confirmation" name="confirmation">
        <br>
        <input type="submit" value="Delete All Orders">
    </form>
    <a href="display_orders.php" class="btn-go-back">Go Back</a>
</div>
</body>
</html>
