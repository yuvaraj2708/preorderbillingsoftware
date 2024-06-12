<?php
// Check if from_date and to_date are set in the URL
if(isset($_GET['from_date']) && isset($_GET['to_date'])) {
    // Database connection
    $conn = mysqli_connect("localhost", "root", "", "vickydb");
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Extract from_date and to_date from the URL parameters
    $from_date = $_GET['from_date'];
    $to_date = $_GET['to_date'];
    $item_id = isset($_GET['item_id']) ? $_GET['item_id'] : ''; // Check if item_id is set

    // Prepare the SQL query
    $sql = "SELECT o.customer_name, o.mobile_no, o.address, o.pickup_type, o.delivery_date, o.delivery_time, i.item_name, i.price, oi.quantity,o.discount, o.advance_amount, o.balance_amount 
            FROM orders o
            INNER JOIN order_items oi ON o.id = oi.order_id
            INNER JOIN items i ON oi.item_id = i.id
            WHERE DATE(o.delivery_date) BETWEEN '$from_date' AND '$to_date'"; // Base query

    // Add item filtering if item_id is not empty
    if (!empty($item_id)) {
        $sql .= " AND oi.item_id = '$item_id'"; // Append item_id condition
    }

    // Execute the SQL query
    $result = mysqli_query($conn, $sql);

    // Set headers for CSV file download
    header('Content-Type: text/csv');
    header("Content-Disposition: attachment; filename=\"sales_report_${from_date}_to_${to_date}.csv\"");

    // Create a file pointer connected to the output stream
    $output = fopen('php://output', 'w');

    // Write the CSV header
    fputcsv($output, ['Customer Name', 'Mobile No', 'Address', 'Pickup Type', 'Delivery Date', 'Delivery Time', 'Item', 'Price', 'Quantity', 'Discount','Advance Amount', 'Balance Amount']);

    // Write the CSV data
    while($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, $row);
    }

    // Close the file pointer
    fclose($output);

    // Close database connection
    mysqli_close($conn);

    exit; // Exit to prevent further execution
}
?>
