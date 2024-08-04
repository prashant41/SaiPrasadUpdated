<?php
include("db.php");

$search = $_GET['term'] ?? '';

if ($search !== '') {
    $stmt = $pdo->prepare("SELECT name FROM shop_names WHERE name LIKE ?");
    $stmt->execute(["%$search%"]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results);
}
?>