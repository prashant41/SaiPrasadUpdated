<?php
session_start();
include("db.php");

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
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
        $sql = "INSERT INTO raw_materials (name, unit, price, date) VALUES (:name, :unit, :price, :date)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['name' => $name, 'unit' => $unit, 'price' => $price, 'date' => $date]);
        header("Location: manage_raw_materials.php");
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
    <title>Create Raw Material</title>
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
        .create-form input {
            padding: 8px;
            margin: 5px 0;
            width: calc(100% - 16px); /* Adjust width */
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
    <h2>Add New Raw Material</h2>
    <div class="create-form">
        <form action="create_raw_material.php" method="post">
            <label for="name">Material Name:</label>
            <input type="text" id="name" name="name" required>
            <label for="unit">Unit:</label>
            <input type="text" id="unit" name="unit" required>
            <label for="price">Price:</label>
            <input type="number" step="0.01" id="price" name="price" required>
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required>
            <input type="submit" value="Add Raw Material">
        </form>
    </div>
</div>

</body>
</html>
<?php
session_start();
include("db.php");

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
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
        $sql = "INSERT INTO raw_materials (name, unit, price, date) VALUES (:name, :unit, :price, :date)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['name' => $name, 'unit' => $unit, 'price' => $price, 'date' => $date]);
        header("Location: manage_raw_materials.php");
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
    <title>Create Raw Material</title>
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
        .create-form input {
            padding: 8px;
            margin: 5px 0;
            width: calc(100% - 16px); /* Adjust width */
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
    <h2>Add New Raw Material</h2>
    <div class="create-form">
        <form action="create_raw_material.php" method="post">
            <label for="name">Material Name:</label>
            <input type="text" id="name" name="name" required>
            <label for="unit">Unit:</label>
            <input type="text" id="unit" name="unit" required>
            <label for="price">Price:</label>
            <input type="number" step="0.01" id="price" name="price" required>
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required>
            <input type="submit" value="Add Raw Material">
        </form>
    </div>
</div>

</body>
</html>
<?php
session_start();
include("db.php");

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
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
        $sql = "INSERT INTO raw_materials (name, unit, price, date) VALUES (:name, :unit, :price, :date)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['name' => $name, 'unit' => $unit, 'price' => $price, 'date' => $date]);
        header("Location: manage_raw_materials.php");
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
    <title>Create Raw Material</title>
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
        .create-form input {
            padding: 8px;
            margin: 5px 0;
            width: calc(100% - 16px); /* Adjust width */
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
    <h2>Add New Raw Material</h2>
    <div class="create-form">
        <form action="create_raw_material.php" method="post">
            <label for="name">Material Name:</label>
            <input type="text" id="name" name="name" required>
            <label for="unit">Unit:</label>
            <input type="text" id="unit" name="unit" required>
            <label for="price">Price:</label>
            <input type="number" step="0.01" id="price" name="price" required>
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required>
            <input type="submit" value="Add Raw Material">
        </form>
    </div>
</div>

</body>
</html>
<?php
session_start();
include("db.php");

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
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
        $sql = "INSERT INTO raw_materials (name, unit, price, date) VALUES (:name, :unit, :price, :date)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['name' => $name, 'unit' => $unit, 'price' => $price, 'date' => $date]);
        header("Location: manage_raw_materials.php");
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
    <title>Create Raw Material</title>
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
        .create-form input {
            padding: 8px;
            margin: 5px 0;
            width: calc(100% - 16px); /* Adjust width */
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
    <h2>Add New Raw Material</h2>
    <div class="create-form">
        <form action="create_raw_material.php" method="post">
            <label for="name">Material Name:</label>
            <input type="text" id="name" name="name" required>
            <label for="unit">Unit:</label>
            <input type="text" id="unit" name="unit" required>
            <label for="price">Price:</label>
            <input type="number" step="0.01" id="price" name="price" required>
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required>
            <input type="submit" value="Add Raw Material">
        </form>
    </div>
</div>

</body>
</html>
