<?php
session_start();
include("db.php");

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: manage_cash.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $counter_cash = trim($_POST['counter_cash']);
    $delivery_boy_cash = trim($_POST['delivery_boy_cash']);
    $book_cash = trim($_POST['book_cash']);
    $date = trim($_POST['date']);

    if (empty($counter_cash) || empty($delivery_boy_cash) || empty($book_cash) || empty($date)) {
        echo "All fields are required!";
        exit();
    }

    try {
        $sql = "UPDATE revenue SET counter_cash = :counter_cash, delivery_boy_cash = :delivery_boy_cash, book_cash = :book_cash, date = :date WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['counter_cash' => $counter_cash, 'delivery_boy_cash' => $delivery_boy_cash, 'book_cash' => $book_cash, 'date' => $date, 'id' => $id]);
        header("Location: manage_cash.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

try {
    $sql = "SELECT * FROM revenue WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $cash_entry = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$cash_entry) {
        header("Location: manage_cash.php");
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
    <title>Update Cash Entry</title>
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
        h2 {
            text-align: center;
            color: #002147; /* Dark blue */
            margin-top: 0;
        }
        .create-form {
            width: 100%;
        }
        .create-form label {
            display: block;
            margin-top: 10px;
        }
        .create-form input, .create-form select {
            padding: 8px;
            margin: 5px 0;
            width: 100%;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        .create-form input[type="submit"] {
            background-color: #4CAF50; /* Green */
            color: white;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="container">
    <a href="manage_cash.php" class="back-button">Go Back</a>
    <h2>Update Cash Entry</h2>
    <div class="create-form">
        <form action="update_cash.php?id=<?php echo htmlspecialchars($id); ?>" method="post">
            <label for="counter_cash">Counter Cash:</label>
            <input type="number" step="0.01" id="counter_cash" name="counter_cash" value="<?php echo htmlspecialchars($cash_entry['counter_cash']); ?>" required>
            <label for="delivery_boy_cash">Delivery Boy Cash:</label>
            <input type="number" step="0.01" id="delivery_boy_cash" name="delivery_boy_cash" value="<?php echo htmlspecialchars($cash_entry['delivery_boy_cash']); ?>" required>
            <label for="book_cash">Book Cash:</label>
            <input type="number" step="0.01" id="book_cash" name="book_cash" value="<?php echo htmlspecialchars($cash_entry['book_cash']); ?>" required>
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($cash_entry['date']); ?>" required>
            <input type="submit" value="Update Cash Entry">
        </form>
    </div>
</div>

</body>
</html>
