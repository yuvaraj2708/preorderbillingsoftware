<?php
require 'base.php';

$conn = mysqli_connect("localhost", "root", "", "vickydb");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Validate input data
$item_id = (int)$_POST['item_id'];
$item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
$price = mysqli_real_escape_string($conn, $_POST['price']);


// Check if required fields are not empty
if (empty($item_name) || empty($price)) {
    die("Please fill in all the required fields.");
}


// Update the order in the database


$sql = "UPDATE items SET 
        item_name = '$item_name', 
        price = '$price' 
        WHERE id = '$item_id'";



if (mysqli_query($conn, $sql)) {
    echo "Item updated successfully";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
?>
