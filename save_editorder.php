<?php
// Start output buffering
ob_start();
require 'base.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id'])) {
    $conn = mysqli_connect("localhost", "root", "", "vickydb");

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $order_id = (int)$_POST['order_id'];
    $customer_name = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $mobile_no = mysqli_real_escape_string($conn, $_POST['mobile_no']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $pickup_type = mysqli_real_escape_string($conn, $_POST['pickup_type']);
    $delivery_date = mysqli_real_escape_string($conn, $_POST['delivery_date']);
    $delivery_time = mysqli_real_escape_string($conn, $_POST['delivery_time']);
    $advance_amount = (float)$_POST['advance_amount'];
    $total_amount = (float)$_POST['total_amount'];
    $balance_amount = (float)$_POST['balance_amount'];
    $discount = (float)$_POST['discount'];
    $edited_by = mysqli_real_escape_string($conn, $_POST['edited_by']); // Add this field to your form

    // Start a transaction
    mysqli_begin_transaction($conn);

    try {
        // Update order details
        $sql_update_order = "UPDATE orders SET 
            customer_name = '$customer_name', 
            mobile_no = '$mobile_no', 
            address = '$address', 
            pickup_type = '$pickup_type', 
            delivery_date = '$delivery_date', 
            delivery_time = '$delivery_time', 
            advance_amount = '$advance_amount',
            total_amount = '$total_amount',
            balance_amount = '$balance_amount',
            discount = '$discount'
            WHERE id = '$order_id'";

        if (!mysqli_query($conn, $sql_update_order)) {
            throw new Exception("Error updating order: " . mysqli_error($conn));
        }

        // Delete old order items
        $sql_delete_order_items = "DELETE FROM order_items WHERE order_id = '$order_id'";
        if (!mysqli_query($conn, $sql_delete_order_items)) {
            throw new Exception("Error deleting order items: " . mysqli_error($conn));
        }

        // Insert new order items
        foreach ($_POST['item_id'] as $index => $item_id) {
            $quantity = (int)$_POST['quantity'][$index];
            $price = (float)$_POST['price'][$index];

            $sql_insert_order_item = "INSERT INTO order_items (order_id, item_id, quantity, price) VALUES 
                                      ('$order_id', '$item_id', '$quantity', '$price')";
            if (!mysqli_query($conn, $sql_insert_order_item)) {
                throw new Exception("Error inserting order items: " . mysqli_error($conn));
            }
        }

        // Insert into advance_amount_history table
        $sql_insert_history = "INSERT INTO advance_amount_history (order_id, edited_amount, edited_by, edited_date) VALUES 
                               ('$order_id', '$advance_amount', '$edited_by', NOW())";
        if (!mysqli_query($conn, $sql_insert_history)) {
            throw new Exception("Error inserting advance amount history: " . mysqli_error($conn));
        }

        // Commit the transaction
        mysqli_commit($conn);

        // Redirect to the order list page
        header("Location: orders.php");
        exit();

    } catch (Exception $e) {
        // Rollback the transaction
        mysqli_rollback($conn);
        echo "Failed to update the order: " . $e->getMessage();
    }

    mysqli_close($conn);
} else {
    echo "Invalid request.";
}

// Flush the output buffer and end output buffering
ob_end_flush();
?>
