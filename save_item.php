<?php

require 'base.php';

$conn = mysqli_connect("localhost", "root", "", "vickydb");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$item_name = $_POST['item_name'];
$price = $_POST['price'];




$sql = "INSERT INTO items (item_name, price)
        VALUES ('$item_name', '$price')";

if (mysqli_query($conn, $sql)) {
    echo "Item saved successfully";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
?>
