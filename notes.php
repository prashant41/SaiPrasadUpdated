<?php
include("db.php"); // Include your database connection file
session_start(); // Start the session

// Handle form submission for inserting new records
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['customerName']) && isset($_POST['itemName'])) {
    $customerName = $_POST['customerName'];
    $itemName = $_POST['itemName'];
    $quantity = $_POST['quantity'];
    $amount = $_POST['amount'];

    // Prepare and execute SQL query to insert new record
    $stmt = $pdo->prepare("INSERT INTO notes_records (customer_name, item_name, quantity, amount, created_at) VALUES (?, ?, ?, ?, NOW())");
    if ($stmt->execute([$customerName, $itemName, $quantity, $amount])) {
        // Data inserted successfully
    } else {
        // Error: Could not insert data.
    }
}

// Handle AJAX request for filtering records
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['filterCustomerName']) && isset($_POST['startDate']) && isset($_POST['endDate'])) {
    $filterCustomerName = $_POST['filterCustomerName'];
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];

    $conditions = [];
    $params = [];

    if (!empty($filterCustomerName)) {
        $conditions[] = "customer_name LIKE ?";
        $params[] = "%$filterCustomerName%";
    }
    if (!empty($startDate)) {
        $conditions[] = "DATE(created_at) >= ?";
        $params[] = $startDate;
    }
    if (!empty($endDate)) {
        $conditions[] = "DATE(created_at) <= ?";
        $params[] = $endDate;
    }

    $whereClause = '';
    if (!empty($conditions)) {
        $whereClause = 'WHERE ' . implode(' AND ', $conditions);
    }

    // Prepare SQL statement
    $sql = "SELECT id, customer_name, item_name, quantity, amount, DATE(created_at) as date_created, created_at, actions FROM notes_records $whereClause ORDER BY created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $filteredData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return JSON response
    echo json_encode($filteredData);
    exit(); // Exit after sending JSON response
}

// Handle AJAX request to mark as paid
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'markPaid' && isset($_POST['recordId'])) {
    $recordId = $_POST['recordId'];
    $stmt = $pdo->prepare("UPDATE notes_records SET actions = 'Paid' WHERE id = ?");
    if ($stmt->execute([$recordId])) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
    exit();
}

// Handle AJAX request to delete the record
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'delete' && isset($_POST['recordId'])) {
    $recordId = $_POST['recordId'];
    $stmt = $pdo->prepare("DELETE FROM notes_records WHERE id = ?");
    if ($stmt->execute([$recordId])) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
    exit();
}

// Check if user is logged in, redirect to login page if not
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notes Page</title>
    <link rel="stylesheet" href="client_style.css">
    <style>
        .logo {
            background-color: red;
            font-weight: bold;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }
        .logo:hover {
            background-color: #4CAF50;
        }
        .container {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            margin-left: 10px;
        }
        .form-container {
            max-width: 48%;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-container label {
            display: block;
            margin-bottom: 8px;
        }
        .form-container input[type="text"],
        .form-container input[type="number"],
        .form-container input[type="date"],
        .form-container input[type="submit"] {
            width: 100%;
            padding: 10px 5px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-container input[type="submit"] {
            margin-top: 10px;
            background-color: green;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        .result-table {
            margin-top: 20px;
            width: 100%;
            border-collapse: collapse;
        }
        .result-table th, .result-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        .result-table th {
            background-color: #f2f2f2;
        }
        .paid-button {
            background-color: red;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
        .paid-text {
            color: green;
            font-weight: bold;
        }
    </style>
    
    <script>
        function filterResults() {
            const formData = new FormData(document.getElementById('filterForm'));
            fetch('notes.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const tableBody = document.getElementById('resultsTableBody');
                tableBody.innerHTML = '';

                if (data.length > 0) {
                    data.forEach(row => {
                        const tr = document.createElement('tr');
                        tr.setAttribute('data-id', row.id);
                        tr.innerHTML = `
                            <td>${row.date_created}</td>
                            <td>${row.customer_name}</td>
                            <td>${row.item_name}</td>
                            <td>${row.quantity}</td>
                            <td>${row.amount}</td>
                            <td>${row.created_at}</td>
                            <td>
                                ${row.actions === 'Paid' ? '<span class="paid-text">Paid</span>' : `<button class="paid-button" onclick="markAsPaid(${row.id})">Mark as Paid</button>`}
                            </td>
                        `;
                        tableBody.appendChild(tr);
                    });
                } else {
                    tableBody.innerHTML = '<tr><td colspan="7">No records found.</td></tr>';
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function markAsPaid(recordId) {
            if (confirm('Are you sure you want to delete this record?')) {
                fetch('notes.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'delete',
                        recordId: recordId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const row = document.querySelector(`tr[data-id='${recordId}']`);
                        if (row) {
                            row.remove(); // Remove the row from the table
                        }
                    } else {
                        alert('Error deleting the record.');
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }
    </script>
</head>
<body>
    <nav>
        <a href="client_dashboard.php" class="logo"><span>SAI PRASAD</span></a>
        <a href="client_display_order.php">Display Orders</a>
        <a href="client_filter.php">Filter Orders</a>
        <a href="notes.php">Notes</a>
        <a href="logout.php" onclick="return confirm('Are you sure you want to log out?');">Log Out</a>
        <!-- Add more links as needed -->
    </nav>

    <div class="container">
        <div class="form-container">
            <h2>Add Orders:</h2>
            <form action="notes.php" method="post">
                <label for="customerName">Customer Name:</label>
                <input type="text" id="customerName" name="customerName" required>

                <label for="itemName">Item Name:</label>
                <input type="text" id="itemName" name="itemName" required>

                <label for="quantity">Quantity:</label>
                <input type="number" id="quantity" name="quantity" required>

                <label for="amount">Amount:</label>
                <input type="number" id="amount" name="amount" required>

                <input type="submit" value="Submit">
            </form>
        </div>

        <div class="form-container">
            <h2>Filter Notes</h2>
            <form id="filterForm" onsubmit="event.preventDefault(); filterResults();">
                <label for="filterCustomerName">Customer Name:</label>
                <input type="text" id="filterCustomerName" name="filterCustomerName">

                <label for="startDate">Start Date:</label>
                <input type="date" id="startDate" name="startDate">

                <label for="endDate">End Date:</label>
                <input type="date" id="endDate" name="endDate">

                <input type="submit" value="Filter">
            </form>
        </div>
    </div>

    <table class="result-table">
        <thead>
            <tr>
                <th>Date Created</th>
                <th>Customer Name</th>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Amount</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="resultsTableBody">
            <!-- Results will be dynamically added here -->
        </tbody>
    </table>
</body>
</html>
