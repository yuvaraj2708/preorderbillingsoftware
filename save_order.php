<?php
require 'base.php';

// Establish a database connection
$conn = mysqli_connect("localhost", "root", "", "vickydb");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get the values from the POST request
$customer_name = $_POST['customer_name'];
$mobile_no = $_POST['mobile_no'];
$address = $_POST['address'];
$pickup_type = $_POST['pickup_type'];
$delivery_date = $_POST['delivery_date'];
$delivery_time = $_POST['delivery_time'];
$advance_amount = $_POST['advance_amount'];
$discount = $_POST['discount'];

// Calculate total amount
$total_amount = 0;

// Loop through each item and calculate total amount
foreach ($_POST['item_id'] as $index => $item_id) {
    $qty = $_POST['qty'][$index];
    $price = $_POST['price'][$index];
    $total_amount += ($price * $qty);
}

// Calculate balance amount
$balance_amount = $total_amount - $advance_amount - $discount;

// Insert data into the orders table
$sql = "INSERT INTO orders (customer_name, mobile_no, address, pickup_type, delivery_date, delivery_time, advance_amount,discount, balance_amount, total_amount) 
        VALUES ('$customer_name', '$mobile_no', '$address', '$pickup_type', '$delivery_date', '$delivery_time', '$advance_amount', '$discount','$balance_amount', '$total_amount')";

if (mysqli_query($conn, $sql)) {
    // Get the ID of the inserted order
    $order_id = mysqli_insert_id($conn);

    // Loop through each item and insert into order_items table
    foreach ($_POST['item_id'] as $index => $item_id) {
        $qty = $_POST['qty'][$index];
        $price = $_POST['price'][$index];

        // Insert each item individually
        $sql = "INSERT INTO order_items (order_id, item_id, quantity, price) 
                VALUES ('$order_id', '$item_id', '$qty', '$price')";
        
        if (mysqli_query($conn, $sql)) {
            echo "Item added successfully<br>";
        } else {
            echo "Error adding item: " . mysqli_error($conn) . "<br>";
        }
    }

    echo "Order saved successfully";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

// Close the database connection
mysqli_close($conn);
?>
