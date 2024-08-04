<?php
session_start();
include("db.php"); // Adjust as per your database connection file

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Fetch shop details for the form
    $sql = "SELECT * FROM shop_names WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $shop = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$shop) {
        echo "Shop not found.";
        exit();
    }
} else {
    echo "ID parameter missing.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input
    $shop_name = $_POST['shop_name'];

    try {
        // Update shop details
        $sql = "UPDATE shop_names SET shop_name = :shop_name WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'shop_name' => $shop_name,
            'id' => $id
        ]);

        // Redirect to manage shop names page after update
        header("Location: manage_shop_names.php");
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
    <title>Update Shop Name</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Your existing styles */
    </style>
</head>
<body>

<div class="container">
    <a href="manage_shop_names.php" class="back-button">Go Back</a>
    <h2>Update Shop Name</h2>
    <div class="update-form">
        <form action="update_shop_name.php?id=<?php echo htmlspecialchars($id); ?>" method="post">
            <label for="shop_name">Shop Name:</label>
            <input type="text" id="shop_name" name="shop_name" value="<?php echo htmlspecialchars($shop['shop_name']); ?>" required>
            <input type="submit" value="Update Shop Name">
        </form>
    </div>
</div>

</body>
</html>
