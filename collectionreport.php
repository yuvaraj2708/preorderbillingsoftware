<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            max-height: 400px;
            overflow-y: scroll;
            /* display: block; */
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
        .btn {
            margin: 10px 0;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #45a049;
        }
    </style>
    <script>
        function printReport() {
            var printContents = document.getElementById('reportTable').outerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = "<html><head><title>Collection Report</title></head><body>" + printContents + "</body>";
            window.print();
            document.body.innerHTML = originalContents;
        }

        function deleteRow(row) {
            var table = document.getElementById('reportTable');
            table.deleteRow(row.rowIndex);
        }
    </script>
</head>
<body>
    <?php
    require 'base.php';

    $conn = mysqli_connect("localhost", "root", "", "vickydb");

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Process filters
    $filter_customer = isset($_GET['customer']) ? $_GET['customer'] : '';
    $filter_date = isset($_GET['date']) ? $_GET['date'] : '';

    // Construct SQL query based on filters
    $sql = "SELECT o.*, oi.quantity, i.item_name 
            FROM orders o 
            LEFT JOIN order_items oi ON o.id = oi.order_id 
            LEFT JOIN items i ON oi.item_id = i.id 
            WHERE 1=1";
    if (!empty($filter_customer)) {
        $sql .= " AND o.customer_name LIKE '%$filter_customer%'";
    }
    if (!empty($filter_date)) {
        $sql .= " AND o.delivery_date = '$filter_date'";
    }

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        echo "<h2>Collection Report</h2>";

        // Filter form
        echo "<form method='get'>";
        echo "<label for='customer'>Customer Name:</label>";
        echo "<input type='text' id='customer' name='customer' value='$filter_customer'>";
        echo "<label for='date'>Date:</label>";
        echo "<input type='date' id='date' name='date' value='$filter_date'>";
        echo "<input type='submit' value='Filter'>";
        echo "</form>";

        // Print and Delete buttons
        echo "<button class='btn' onclick='printReport()'>Print Report</button>";

        // Table
        echo "<table id='reportTable'>";
        echo "<tr><th>Customer Name</th><th>Item Name</th><th>Quantity</th><th>Total Amount</th><th>Status</th><th>Delivery Date</th></tr>";

        while ($row = mysqli_fetch_assoc($result)) {
            $customer_name = $row['customer_name'];
            $item_name = $row['item_name'];
            $qty = $row['quantity']; // Fetching quantity from order_items
            $total_amount = $row['total_amount'];
            $advance_amount = $row['advance_amount'];
            $balance_amount = $row['balance_amount'];
            $formatted_date = date("d F Y", strtotime($row['delivery_date']));

            // Determine status
            if ($balance_amount == 0) {
                $status = "Fully Paid (Credited)";
            } else {
                $status = "Partially Paid: Balance Amount: $balance_amount";
            }

            echo "<tr><td>$customer_name</td><td>$item_name</td><td>$qty</td><td>$total_amount</td><td>$status</td><td>$formatted_date</td></tr>";
        }

        echo "</table>";
    } else {
        echo "No Report found.";
    }

    mysqli_close($conn);
    ?>
</body>
</html>
