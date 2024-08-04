<?php
include("db.php");
session_start();

// Check if user is logged in, redirect to login page if not
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

// Fetch raw material names
$material_query = $pdo->query("SELECT DISTINCT raw_material_name FROM expenses_material");
$materials = $material_query->fetchAll(PDO::FETCH_ASSOC);

// Fetch units
$unit_query = $pdo->query("SELECT DISTINCT unit FROM expenses_material");
$units = $unit_query->fetchAll(PDO::FETCH_ASSOC);

// Handle AJAX request for filtering orders
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Fetch POST data (JSON format)
    $data = json_decode(file_get_contents("php://input"), true);

    // Extract filter parameters
    $materialName = isset($data['raw_material_name']) ? $data['raw_material_name'] : null;
    $unit = isset($data['unit']) ? $data['unit'] : null;
    $startDate = isset($data['start_date']) ? $data['start_date'] : null;
    $endDate = isset($data['end_date']) ? $data['end_date'] : null;

    // Prepare SQL query with placeholders
    $sql = "SELECT id, date, raw_material_id, qty, amount, unit, raw_material_name FROM expenses_material WHERE 1";

    // Array to hold parameters for prepared statement
    $params = array();

    // Build WHERE clause dynamically based on selected filters
    if ($materialName && $materialName !== 'none') {
        $sql .= " AND raw_material_name = ?";
        $params[] = $materialName;
    }
    if ($unit && $unit !== 'none') {
        $sql .= " AND unit = ?";
        $params[] = $unit;
    }
    if ($startDate && $endDate) {
        $sql .= " AND DATE(date) BETWEEN ? AND ?";
        $params[] = $startDate;
        $params[] = $endDate;
    }

    $sql .= " ORDER BY date DESC";

    // Prepare and execute the query
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $filteredMaterials = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate total amount
    $totalAmount = 0;
    foreach ($filteredMaterials as $material) {
        $totalAmount += $material['amount'];
    }

    // Prepare response in JSON format
    $response = array(
        'materials' => $filteredMaterials,
        'totalAmount' => $totalAmount
    );

    // Send JSON response back to JavaScript
    header('Content-Type: application/json');
    echo json_encode($response);
    exit(); // Exit to prevent further output
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expenses Filter Page</title>
    <style>
        body {
    font-family: Arial, sans-serif;
    background-color: #f0f0f0;
    margin: 0;
    padding: 0;
    }

    nav {
        background-color: #002147;
        overflow: hidden;
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

    .container {
        max-width: 1000px; /* Increased max-width */
        margin: 40px auto; /* Adjusted margin */
        padding: 30px; /* Increased padding */
        border: 1px solid #ccc;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1); /* Increased box-shadow size */
        background-color: #fff;
    }

    .form-container {
        margin-bottom: 30px; /* Increased margin-bottom */
        padding: 20px; /* Increased padding */
        background-color: #f2f2f2;
        border-radius: 8px;
    }

    .form-container label {
        font-weight: bold;
        font-size: 18px; /* Increased font-size */
    }

    .form-container select, 
    .form-container input[type="submit"], 
    .form-container input[type="date"] {
        padding: 15px; /* Increased padding */
        margin: 10px 0; /* Adjusted margin */
        border: 1px solid #ccc;
        border-radius: 6px; /* Increased border-radius */
        font-size: 18px; /* Increased font-size */
    }

    .form-container input[type="submit"] {
        background-color: #4CAF50;
        color: white;
        cursor: pointer;
    }

    .form-container input[type="submit"]:hover {
        background-color: #45a049;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    table th, 
    table td {
        border: 1px solid #ddd;
        padding: 15px; /* Increased padding */
        text-align: left;
        font-size: 16px; /* Increased font-size */
    }

    table th {
        background-color: #f2f2f2;
    }

    table tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    table tbody tr:hover {
        background-color: #e0e0e0;
    }

    #total_amount {
        font-size: 18px; /* Increased font-size */
    }
    .logo{
        background-color: red;
        font-weight: bold;
        font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
    }
    .logo:hover{
        background-color: #4CAF50;
    }

    </style>
    <script>
        // Function to handle form submission and AJAX request
        function filterMaterials() {
            var materialName = document.getElementById('raw_material_name').value;
            var unit = document.getElementById('unit').value;
            var startDate = document.getElementById('start_date').value;
            var endDate = document.getElementById('end_date').value;

            var data = {
                raw_material_name: materialName,
                unit: unit,
                start_date: startDate,
                end_date: endDate
            };

            var xhr = new XMLHttpRequest();
            xhr.open('POST', '<?php echo $_SERVER["PHP_SELF"]; ?>', true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    displayFilteredMaterials(response);
                }
            };
            xhr.send(JSON.stringify(data));
        }

        // Function to display filtered materials and total amount
        function displayFilteredMaterials(data) {
            var materialsTableBody = document.getElementById('materials_table_body');
            var totalAmountElement = document.getElementById('total_amount');
            
            materialsTableBody.innerHTML = ''; // Clear previous rows

            data.materials.forEach(function (material) {
                var row = `<tr>
                               <td>${material.id}</td>
                               <td>${material.date}</td>
                               <td>${material.raw_material_name}</td>
                               <td>${material.unit}</td>
                               <td>${material.qty}</td>
                               <td>${material.amount}</td>
                               <td>${material.raw_material_id}</td>
                           </tr>`;
                materialsTableBody.innerHTML += row;
            });

            totalAmountElement.textContent = 'Total Amount: ' + data.totalAmount.toFixed(2); // Display total amount formatted
        }
    </script>
</head>
<body>
<nav>
    <a href="super_admin_dashboard.php" class="logo"><span>SAI PRASAD</span></a>
    <a href="pnl.php">PnL</a>
    <a href="manage_expense_material.php">Raw Materials</a>
    <a href="raw_material_list.php">Filter Raw Material</a>
    <a href="manage_users.php">Manage</a>
    <a href="logout.php" onclick="return confirm('Are you sure you want to log out?');">Log Out</a>
    <!-- Add more links as needed -->
</nav>
<br>



    <div class="container">
        <div class="form-container">
            <form onsubmit="event.preventDefault(); filterMaterials();">
                <label for="raw_material_name">Filter by Raw Material Name:</label>
                <select id="raw_material_name">
                    <option value="none">Select Raw Material Name</option>
                    <?php foreach ($materials as $material): ?>
                        <option value="<?php echo htmlspecialchars($material['raw_material_name']); ?>"><?php echo htmlspecialchars($material['raw_material_name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <br>
                <label for="unit">Filter by Unit:</label>
                <select id="unit">
                    <option value="none">Select Unit</option>
                    <?php foreach ($units as $unit): ?>
                        <option value="<?php echo htmlspecialchars($unit['unit']); ?>"><?php echo htmlspecialchars($unit['unit']); ?></option>
                    <?php endforeach; ?>
                </select>
                <br>
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date">
                <br>
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date">
                <br>
                <input type="submit" value="Filter Materials">
            </form>
        </div>

        <h2>Filtered Materials</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Raw Material Name</th>
                    <th>Unit</th>
                    <th>Quantity</th>
                    <th>Amount</th>
                    <th>Raw Material ID</th>
                </tr>
            </thead>
            <tbody id="materials_table_body">
                <!-- Table rows will be populated dynamically -->
            </tbody>
        </table>

        <p id="total_amount"></p>
    </div>
</body>
</html>
