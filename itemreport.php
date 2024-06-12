<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Item Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #dddddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<?php
require 'base.php';

$conn = mysqli_connect("localhost", "root", "", "vickydb");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Process filters
$filter_from_date = isset($_GET['from_date']) ? $_GET['from_date'] : '';
$filter_to_date = isset($_GET['to_date']) ? $_GET['to_date'] : '';
$filter_item = isset($_GET['item']) ? $_GET['item'] : '';

// Construct SQL query based on filters
$sql = "SELECT items.item_name, 
               SUM(order_items.quantity) AS total_qty, 
               SUM(order_items.quantity * order_items.price) AS total_amount 
        FROM items 
        LEFT JOIN order_items ON items.id = order_items.item_id 
        LEFT JOIN orders ON order_items.order_id = orders.id
        WHERE 1=1";

if (!empty($filter_from_date) && !empty($filter_to_date)) {
    $sql .= " AND orders.order_date BETWEEN '$filter_from_date' AND '$filter_to_date'";
}
if (!empty($filter_item)) {
    $sql .= " AND items.item_name LIKE '%$filter_item%'";
}
$sql .= " GROUP BY items.item_name";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    echo "<h2>Item Report</h2>";

    // Filter form
    echo "<form method='get'>";
    echo "<label for='from_date'>From Date:</label>";
    echo "<input type='date' id='from_date' name='from_date' value='$filter_from_date'>";
    echo "<label for='to_date'>To Date:</label>";
    echo "<input type='date' id='to_date' name='to_date' value='$filter_to_date'>";
    echo "<label for='item'>Item Name:</label>";
    echo "<input type='text' id='item' name='item' value='$filter_item'>";
    echo "<input type='submit' value='Filter'>";
    echo "</form>";

    // Table
    echo "<table>";
    echo "<tr><th>Item Name</th><th>Total Quantity</th><th>Total Amount</th></tr>";
    
    while ($row = mysqli_fetch_assoc($result)) {
        $item_name = $row['item_name'];
        $total_qty = $row['total_qty'];
        $total_amount = $row['total_amount'];

        echo "<tr><td>$item_name</td><td>$total_qty</td><td>$total_amount</td></tr>";
    }
    
    echo "</table>";
} else {
    echo "No items found.";
}

mysqli_close($conn);
?>

</body>
</html>
