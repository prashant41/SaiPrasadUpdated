<?php
include("db.php"); // Include your database connection logic

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $filterCustomerName = $_POST['filterCustomerName'] ?? null;
    $startDate = $_POST['startDate'] ?? null;
    $endDate = $_POST['endDate'] ?? null;

    $conditions = [];
    $params = [];

    if (!empty($filterCustomerName)) {
        $conditions[] = "customer_name LIKE ?";
        $params[] = "%$filterCustomerName%";
    }
    if (!empty($startDate) && !empty($endDate)) {
        // Convert PHP dates to MySQL-compatible format for comparison
        $startDate = date('Y-m-d H:i:s', strtotime($startDate));
        $endDate = date('Y-m-d H:i:s', strtotime($endDate));

        // Adjust the condition to use UNIX_TIMESTAMP for date comparison
        $conditions[] = "UNIX_TIMESTAMP(created_at) BETWEEN UNIX_TIMESTAMP(?) AND UNIX_TIMESTAMP(?)";
        $params[] = $startDate;
        $params[] = $endDate;
    }

    $whereClause = '';
    if (!empty($conditions)) {
        $whereClause = ' WHERE ' . implode(' AND ', $conditions);
    }

    // Prepare SQL statement
    $sql = "SELECT customer_name, item_name, quantity, amount, created_at FROM notes_records $whereClause ORDER BY created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $filteredData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return JSON response
    echo json_encode($filteredData);
    exit(); // Ensure no further output interferes with JSON response
}
?>
ChatGPT