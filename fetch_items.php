<?php
include("db.php");

$search = $_GET['term'] ?? '';

if ($search !== '') {
    $stmt = $pdo->prepare("SELECT name, price FROM items WHERE name LIKE ?");
    $stmt->execute(["%$search%"]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results);
}
?>