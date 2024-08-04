
<?php

session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}
include("db.php"); // Adjust as per your database connection file

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve form data
    $item_name = $_POST['item_name'];
    $quantity = $_POST['quantity'];
    $amount = $_POST['amount'];
    $shop_name = $_POST['shop_name'];
    $delivery_boy_name = $_POST['delivery_boy_name'];

    // Example validation (you can add more based on your requirements)
    if (empty($item_name) || empty($quantity) || empty($amount) || empty($shop_name) || empty($delivery_boy_name)) {
        $message = "All fields are required.";
    } else {
        // Insert into orders table
        $sql = "INSERT INTO orders (item_name, quantity, amount, shop_name, delivery_boy_name, admin_name, order_date)
                VALUES (?, ?, ?, ?, ?, ?, NOW())"; // Assuming admin_name is set in session

        $stmt = $pdo->prepare($sql);

        try {
            $stmt->execute([$item_name, $quantity, $amount, $shop_name, $delivery_boy_name, $_SESSION['username']]);
            $message = "Order added successfully";
        } catch (Exception $e) {
            $message = "Error inserting order: " . $e->getMessage();
        }
    }
}

// Fetch delivery boy names for the dropdown
$delivery_boy_query = $pdo->query("SELECT name FROM delivery_boys");
$delivery_boys = $delivery_boy_query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Form</title>
    <link rel="stylesheet" href="style.css">
    <style>
    /* General styles for body and navigation */
    /* General Styles */
    body {
        font-family: Arial, sans-serif;
        background-color: #f0f0f0;
        margin: 0;
        padding: 0;
    }

    nav {
        background-color: #002147; /* Dark blue */
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
        max-width: 600px;
        margin: 20px auto;
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    /* Form Styles */
    form {
        position: relative; /* Ensure the suggestions are positioned relative to the form */
    }

    .form-group {
        margin-bottom: 10px;
        position: relative; /* Ensure suggestions appear relative to form group */
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    .form-group input, .form-group select {
        width: calc(100% - 40px);
        padding: 8px ;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    /* Button Styles */
    .form-actions {
        display: flex;
        justify-content: center; /* Center the buttons horizontally */
        gap: 10px; /* Space between buttons */
        margin-top: 20px;
    }

    .form-actions input {
        padding: 10px 20px;
        font-size: 16px;
        cursor: pointer;
        background-color: #002147; /* Dark blue */
        color: white;
        border: none;
        border-radius: 4px;
        margin: 0; /* Remove margin to ensure buttons are side by side */
    }

    .form-actions input:hover {
        background-color: #004e96; /* Darker blue */
    }

    /* Responsive Styles */
    @media (max-width: 768px) {
        .container {
            width: 90%; /* Adjust container width on smaller screens */
            padding: 15px; /* Adjust padding on smaller screens */
        }

        .form-group input, .form-group select {
            width: 100%; /* Full width for inputs on smaller screens */
        }

        .form-actions {
            flex-direction: column; /* Stack buttons vertically on smaller screens */
        }

        .form-actions input {
            font-size: 14px; /* Adjust font size */
            padding: 8px 16px; /* Adjust padding */
            margin-bottom: 10px; /* Space between stacked buttons */
        }
    }

    /* Error and Success Messages */
    .error-message {
        color: red;
        margin-bottom: 10px;
    }

    .success-message {
        color: green;
        margin-bottom: 10px;
    }

    .fade-out {
        animation: fadeOut 2s forwards;
    }

    @keyframes fadeOut {
        0% {
            opacity: 1;
        }
        100% {
            opacity: 0;
        }
    }

    /* Logo Style */
    .logo {
        background-color: red;
        font-weight: bold;
        font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
    }

    .logo:hover {
        background-color: #4CAF50;
    }

    /* Autocomplete Suggestions */
    .autocomplete-suggestions {
        border: 0.5px solid #002147;
        background-color: #fff;
        max-height: 200px;
        overflow-y: auto;
        position: absolute;
        z-index: 1000;
        margin-top: 5px; /* Space between input and suggestions */
        width: calc(100% - 40px); /* Match input width minus padding */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Optional: shadow for better visibility */
        border-radius: 4px; /* Optional: rounded corners */
    }

    .autocomplete-suggestion {
        padding: 10px;
        cursor: pointer;
        border-bottom: 1px solid #ddd; /* Optional: border between items */
    }

    .autocomplete-suggestion:hover {
        background-color: #ddd;
    }

    /* Remove the default outline on focus for inputs */
    input:focus, select:focus {
        outline: none;
    }


    </style>
    <script>
        function updateAmount() {
            const itemInput = document.getElementById('item_name');
            const quantityInput = document.getElementById('quantity');
            const amountInput = document.getElementById('amount');

            const itemPrice = itemInput.dataset.price;
            const quantity = quantityInput.value;

            if (itemPrice) {
                amountInput.value = (itemPrice * quantity).toFixed(2);
            } else {
                amountInput.value = '';
            }
        }

        function fetchSuggestions(inputId, fetchUrl, suggestionsId) {
            const input = document.getElementById(inputId).value;
            const suggestionsBox = document.getElementById(suggestionsId);

            if (input.length === 0) {
                suggestionsBox.innerHTML = '';
                return;
            }

            fetch(`${fetchUrl}?term=${input}`)
                .then(response => response.json())
                .then(data => {
                    suggestionsBox.innerHTML = '';
                    data.forEach(item => {
                        const suggestion = document.createElement('div');
                        suggestion.className = 'autocomplete-suggestion';
                        suggestion.innerText = item.name;
                        suggestion.onclick = function () {
                            document.getElementById(inputId).value = item.name;
                            document.getElementById(inputId).dataset.price = item.price;
                            suggestionsBox.innerHTML = '';
                            if (inputId === 'item_name') {
                                updateAmount(); // Update amount based on selected item
                            }
                        };
                        suggestionsBox.appendChild(suggestion);
                    });
                });
        }

        document.addEventListener("DOMContentLoaded", function() {
            const successMessage = document.getElementById("success-message");
            if (successMessage) {
                setTimeout(function() {
                    successMessage.classList.add("fade-out");
                }, 2000); // Delay of 2 seconds before fading out
            }
        });
    </script>
</head>
<body>

<nav>
    <a href="admin_dashboard.php" class="logo"><span>SAI PRASAD</span></a>
    <a href="order_form.php">Order Form</a>
    <a href="display_orders.php">Display Orders</a>
    <a href="admin_filter.php">Filter Orders</a>
    <a href="report.php">Report</a>
    <a href="raw_materials.php">Raw Material Data</a>
    <a href="logout.php" onclick="return confirm('Are you sure you want to log out?');">Log Out</a>
    <!-- Add more links as needed -->
</nav><br>

<div class="container">
    <h2>Order Form</h2>

    <?php if (!empty($message)): ?>
        <?php if (strpos($message, 'successfully') !== false): ?>
            <div id="success-message" class="success-message"><?php echo htmlspecialchars($message); ?></div>
        <?php else: ?>
            <div class="error-message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
    <?php endif; ?>

    <form method="POST" action="order_form.php">
        <div class="form-group">
            <label for="item_name">Item Name:</label>
            <input type="text" id="item_name" name="item_name" oninput="fetchSuggestions('item_name', 'fetch_items.php', 'item-suggestions')">
            <div id="item-suggestions" class="autocomplete-suggestions"></div>
        </div>
        <div class="form-group">
            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" min="1" oninput="updateAmount()">
        </div>
        <div class="form-group">
            <label for="amount">Amount:</label>
            <input type="text" id="amount" name="amount" >
        </div>
        <div class="form-group">
            <label for="shop_name">Shop Name:</label>
            <input type="text" id="shop_name" name="shop_name" oninput="fetchSuggestions('shop_name', 'fetch_shops.php', 'shop-suggestions')">
            <div id="shop-suggestions" class="autocomplete-suggestions"></div>
        </div>
        <div class="form-group">
            <label for="delivery_boy_name">Delivery Boy Name:</label>
            <select id="delivery_boy_name" name="delivery_boy_name">
                <?php foreach ($delivery_boys as $delivery_boy): ?>
                    <option value="<?php echo htmlspecialchars($delivery_boy['name']); ?>"><?php echo htmlspecialchars($delivery_boy['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-actions">
            <input type="submit" value="Submit">
            <input type="reset" value="Reset">
        </div>
    </form>
</div>

<script>
    function confirmLogout() {
        return confirm("Are you sure you want to log out?");
    }
</script>

</body>
</html>
