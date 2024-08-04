<?php
session_start();
include("db.php");

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $salary = $_POST['salary'];
    $bonus = $_POST['bonus'];
    $extra_money = $_POST['extra_money'];
    $date_of_joining = $_POST['date_of_joining'];

    try {
        $sql = "INSERT INTO employees (name, salary, bonus, extra_money, date_of_joining) VALUES (:name, :salary, :bonus, :extra_money, :date_of_joining)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name' => $name,
            ':salary' => $salary,
            ':bonus' => $bonus,
            ':extra_money' => $extra_money,
            ':date_of_joining' => $date_of_joining
        ]);

        header("Location: manage_employees.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
