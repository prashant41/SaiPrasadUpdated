<?php
session_start();
include("db.php");

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: manage_records.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $utility_name = trim($_POST['utility_name']);
    $month = trim($_POST['month']);
    $amount = trim($_POST['amount']);
    $date_paid = trim($_POST['date_paid']);

    if (empty($utility_name) || empty($month) || empty($amount) || empty($date_paid)) {
        echo "All fields are required!";
        exit();
    }

    try {
        $sql = "UPDATE records SET utility_name = :utility_name, month = :month, amount = :amount, date_paid = :date_paid WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'utility_name' => $utility_name,
            'month' => $month,
            'amount' => $amount,
            'date_paid' => $date_paid,
            'id' => $id
        ]);
        header("Location: manage_records.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

try {
    $sql = "SELECT * FROM records WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$record) {
        header("Location: manage_records.php");
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Record</title>
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
            border: none;
            padding: 10px;
            font-size: 16px;
        }
        .update-form input[type="submit"]:hover {
            background-color: #45a049; /* Darker green */
        }
    </style>
</head>
<body>

<div class="container">
    <a href="manage_records.php" class="back-button">Go Back</a>
    <h2>Update Record</h2>
    <div class="update-form">
        <form action="update_record.php?id=<?php echo htmlspecialchars($id); ?>" method="post">
            <label for="utility_name">Utility Name:</label>
            <input type="text" id="utility_name" name="utility_name" value="<?php echo htmlspecialchars($record['utility_name'] ?? ''); ?>" required>
            <label for="month">Month:</label>
            <input type="text" id="month" name="month" value="<?php echo htmlspecialchars($record['month'] ?? ''); ?>" required>
            <label for="amount">Amount:</label>
            <input type="number" step="0.01" id="amount" name="amount" value="<?php echo htmlspecialchars($record['amount'] ?? ''); ?>" required>
            <label for="date_paid">Date Paid:</label>
            <input type="date" id="date_paid" name="date_paid" value="<?php echo htmlspecialchars($record['date_paid'] ?? ''); ?>" required>
            <input type="submit" value="Update Record">
        </form>
    </div>
</div>

</body>
</html>
