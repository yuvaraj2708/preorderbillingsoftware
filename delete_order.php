<?php
require 'base.php';

if(isset($_GET['id'])) {
    $order_id = $_GET['id'];

    // Establish database connection
    $conn = mysqli_connect("localhost", "root", "", "vickydb");

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Start transaction
    mysqli_begin_transaction($conn);

    try {
        // Delete related records from advance_amount_history table
        $sql_delete_history = "DELETE FROM advance_amount_history WHERE order_id = '$order_id'";
        if (!mysqli_query($conn, $sql_delete_history)) {
            throw new Exception("Error deleting advance amount history: " . mysqli_error($conn));
        }

        // Delete related records from order_items table
        $sql_delete_order_items = "DELETE FROM order_items WHERE order_id = '$order_id'";
        if (!mysqli_query($conn, $sql_delete_order_items)) {
            throw new Exception("Error deleting order items: " . mysqli_error($conn));
        }

        // Now delete the order
        $sql_delete_order = "DELETE FROM orders WHERE id = '$order_id'";
        if (!mysqli_query($conn, $sql_delete_order)) {
            throw new Exception("Error deleting order: " . mysqli_error($conn));
        }

        // Commit transaction
        mysqli_commit($conn);

        echo "Order and related records deleted successfully.";
    } catch (Exception $e) {
        // Rollback transaction if any error occurs
        mysqli_rollback($conn);
        echo $e->getMessage();
    }

    // Close the database connection
    mysqli_close($conn);
} else {
    echo "No order ID provided.";
}
?>
