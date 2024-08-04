<?php
session_start();
include("db.php");

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: manage_employees.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $salary = trim($_POST['salary']);
    $bonus = trim($_POST['bonus']);
    $extra_money = trim($_POST['extra_money']);
    $date_of_joining = trim($_POST['date_of_joining']);

    if (empty($name) || empty($salary) || empty($bonus) || empty($extra_money) || empty($date_of_joining)) {
        echo "All fields are required!";
        exit();
    }

    try {
        $sql = "UPDATE employees SET name = :name, salary = :salary, bonus = :bonus, extra_money = :extra_money, date_of_joining = :date_of_joining WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'name' => $name, 
            'salary' => $salary, 
            'bonus' => $bonus, 
            'extra_money' => $extra_money, 
            'date_of_joining' => $date_of_joining, 
            'id' => $id
        ]);
        header("Location: manage_employees.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

try {
    $sql = "SELECT * FROM employees WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$record) {
        header("Location: manage_employees.php");
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
    <title>Update Employee</title>
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
</head>
<body>

<!-- Main Content Area -->
<div class="container">
    <a href="manage_employees.php" class="back-button">Go Back</a>
    <br><br>
    <h2>Update Employee</h2>
    <div class="update-form">
        <form action="update_employee.php?id=<?php echo htmlspecialchars($id); ?>" method="post">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($record['name']); ?>" required>
            <label for="salary">Salary:</label>
            <input type="number" id="salary" name="salary" step="0.01" value="<?php echo htmlspecialchars($record['salary']); ?>" required>
            <label for="bonus">Bonus:</label>
            <input type="number" id="bonus" name="bonus" step="0.01" value="<?php echo htmlspecialchars($record['bonus']); ?>" required>
            <label for="extra_money">Extra Money:</label>
            <input type="number" id="extra_money" name="extra_money" step="0.01" value="<?php echo htmlspecialchars($record['extra_money']); ?>" required>
            <label for="date_of_joining">Date of Joining:</label>
            <input type="date" id="date_of_joining" name="date_of_joining" value="<?php echo htmlspecialchars($record['date_of_joining']); ?>" required>
            <input type="submit" value="Update Employee">
        </form>
    </div>
</div>

</body>
</html>
