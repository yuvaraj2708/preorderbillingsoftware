<?php
// Include database connection
include 'db_connection.php';

// Retrieve form data
$username = $_POST['username'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$is_admin = isset($_POST['is_admin']) ? 1 : 0;

// Insert into database
$sql = "INSERT INTO user (username, password, is_admin) VALUES ('$username', '$password', '$is_admin')";
if (mysqli_query($conn, $sql)) {
    echo "Registration successful!";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

// Close connection
mysqli_close($conn);
?>
