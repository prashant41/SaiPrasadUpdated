<?php
session_start();
include("db.php");

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: manage_raw_materials.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $unit = trim($_POST['unit']);
    $price = trim($_POST['price']);
    $date = trim($_POST['date']);

    if (empty($name) || empty($unit) || empty($price) || empty($date)) {
        echo "All fields are required!";
        exit();
    }

    try {
        $sql = "UPDATE raw_materials SET name = :name, unit = :unit, price = :price, date = :date WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['name' => $name, 'unit' => $unit, 'price' => $price, 'date' => $date, 'id' => $id]);
        header("Location: manage_raw_materials.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

try {
    $sql = "SELECT * FROM raw_materials WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$record) {
        header("Location: manage_raw_materials.php");
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
    <title>Update Raw Material</title>
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
        nav, .side-nav {
            position: fixed;
            z-index: 1000;
            width: 100%;
        }
        nav {
            background-color: #002147; /* Dark blue */
            overflow: hidden;
            top: 0;
            left: 0;
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
        .side-nav {
            background-color: green;
            width: 200px;
            height: calc(100vh - 56px);
            overflow: auto;
            top: 56px;
            left: 0;
        }
        .side-nav a {
            display: block;
            color: white;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
        }
        .side-nav a:hover {
            background-color: #002147;
            color: whitesmoke;
        }
        .content {
            margin-left: 220px;
            margin-top: 56px;
            padding: 20px;
        }
    </style>
</head>
<body>

<!-- Top Navigation Bar -->
<nav>
    <a href="super_admin_dashboard.php" class="logo"><span>SAI PRASAD</span></a>
    <a href="pnl.php">PnL</a>
    <a href="raw_material_list.php">Raw Material</a>
    <a href="manage_users.php">Manage</a>
    <a href="logout.php" onclick="return confirm('Are you sure you want to log out?');">Log Out</a>
</nav>

<!-- Side Navigation -->
<div class="side-nav">
    <a href="manage_users.php">Manage User</a>
    <a href="manage_items.php">Manage Items</a>
    <a href="manage_shop_names.php">Manage Shop Names</a>
    <a href="manage_delivery_boys.php">Manage Delivery Boys</a>
    <a href="manage_cash.php">Manage Cash</a>
    <a href="manage_raw_materials.php">Manage Raw Materials</a>
    <a href="manage_expenses_material.php">Manage Expenses Material</a>
</div>

<!-- Main Content Area -->
<div class="content">
    <div class="container">
        <a href="manage_raw_materials.php" class="back-button">Go Back</a>
        <br><br>
        <h2>Update Raw Material</h2>
        <div class="update-form">
            <form action="update_raw_material.php?id=<?php echo htmlspecialchars($id); ?>" method="post">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($record['name']); ?>" required>
                <label for="unit">Unit:</label>
                <input type="text" id="unit" name="unit" value="<?php echo htmlspecialchars($record['unit']); ?>" required>
                <label for="price">Price:</label>
                <input type="number" id="price" name="price" step="0.01" value="<?php echo htmlspecialchars($record['price']); ?>" required>
                <label for="date">Date:</label>
                <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($record['date']); ?>" required>
                <input type="submit" value="Update Raw Material">
            </form>
        </div>
    </div>
</div>

</body>
</html>
