<?php
require 'base.php';
$conn = mysqli_connect("localhost", "root", "", "vickydb");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if order_id is provided in the URL
if(isset($_GET['id'])) {
    $order_id = mysqli_real_escape_string($conn, $_GET['id']);

    // Fetch order details along with all items for the given order ID
    $sql_order = "SELECT o.*, i.item_name, oi.price, oi.quantity FROM orders o
                  INNER JOIN order_items oi ON o.id = oi.order_id
                  INNER JOIN items i ON oi.item_id = i.id
                  WHERE o.id = $order_id";

    $result_order = mysqli_query($conn, $sql_order);

    if(mysqli_num_rows($result_order) > 0) {
        // Display order details
        $order = mysqli_fetch_assoc($result_order);
        echo "<h2>Order Details</h2>";
        echo "<p><strong>Customer Name:</strong> " . htmlspecialchars($order['customer_name']) . "</p>";
        echo "<p><strong>Mobile No:</strong> " . htmlspecialchars($order['mobile_no']) . "</p>";
        echo "<p><strong>Address:</strong> " . htmlspecialchars($order['address']) . "</p>";
        echo "<p><strong>Pickup Type:</strong> " . htmlspecialchars($order['pickup_type']) . "</p>";
        echo "<p><strong>Delivery Date:</strong> " . htmlspecialchars($order['delivery_date']) . "</p>";
        echo "<p><strong>Delivery Time:</strong> " . htmlspecialchars($order['delivery_time']) . "</p>";
        echo"<br>";
        // Display all items associated with the order
        echo "<h3>Ordered Items</h3>";
        echo "<table class='table table-bordered'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>Item Name</th>";
        echo "<th>Price</th>";
        echo "<th>Quantity</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        
        // Fetch ordered items again to display them
        $result_order = mysqli_query($conn, $sql_order);
        while($row = mysqli_fetch_assoc($result_order)) {
            echo "<tr>";
            echo "<td>".htmlspecialchars($row['item_name'])."</td>";
            echo "<td>".htmlspecialchars($row['price'])."</td>";
            echo "<td>".htmlspecialchars($row['quantity'])."</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";

        // Display advance amount
        echo "<h3>Advance Amount</h3>";
        echo "<p><strong>Previous Advance Amount:</strong> " . htmlspecialchars($order['advance_amount']) . "</p>";

        // Fetch advance amount history for the given order_id
        $sql_history = "SELECT * FROM advance_amount_history WHERE order_id = $order_id";
        $result_history = mysqli_query($conn, $sql_history);
        
        if(mysqli_num_rows($result_history) > 0) {
            // Display the history in a table
            echo "<h4>Advance Amount History</h4>";
            echo "<table class='table table-bordered'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>Edited Amount</th>";
            echo "<th>Edited By</th>";
            echo "<th>Edited Date</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
            while($row = mysqli_fetch_assoc($result_history)) {
                echo "<tr>";
                echo "<td>".htmlspecialchars($row['edited_amount'])."</td>";
                echo "<td>".htmlspecialchars($row['edited_by'])."</td>";
                echo "<td>".htmlspecialchars($row['edited_date'])."</td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";
        } else {
            echo "No history found for this order.";
        }
          echo"<br><br>";
        // Display total amount and balance amount
        echo "<h3>Total Amount and Balance Amount</h3>";
        echo "<p><strong>Discount:</strong> " . htmlspecialchars($order['discount']) . "</p>";
        echo "<p><strong>Balance Amount:</strong> " . htmlspecialchars($order['balance_amount']) . "</p>";
        echo "<p><strong>Total Amount:</strong> " . htmlspecialchars($order['total_amount']) . "</p>";

    } else {
        echo "Order not found.";
    }
} else {
    echo "Invalid request.";
}

mysqli_close($conn);
?>
