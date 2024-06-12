<?php 
require 'base.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sales Report</title>
    <style>
        
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 20px;
        }
        .sales-report {
            width: 100%;
            max-width: 800px;
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
        }
        .sales-report h2 {
            margin-top: 0;
            color: #333;
        }
        form {
            margin-bottom: 20px;
        }
        form input[type="date"],
        form input[type="submit"] {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
            margin-right: 10px;
            font-size: 14px;
        }
        form input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .button {
            display: inline-block;
            padding: 8px 16px;
            text-decoration: none;
            background-color: #4CAF50;
            color: white;
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }
        .button:hover {
            background-color: #45a049;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        @media only screen and (max-width: 600px) {
            .container {
                margin-top: 10px;
            }
            .sales-report {
                padding: 10px;
            }
            form input[type="date"],
            form input[type="submit"],
            .button {
                width: 100%;
                margin-right: 0;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <h2>Sales Report</h2>
    <form action="" method="get">
        From Date: <input type="date" name="from_date" value="<?php echo isset($_GET['from_date']) ? $_GET['from_date'] : ''; ?>">
        To Date: <input type="date" name="to_date" value="<?php echo isset($_GET['to_date']) ? $_GET['to_date'] : ''; ?>">
        <select name="item_id">
            <option value="">Select Item</option>
            <?php
            // Connect to the database
            $conn = mysqli_connect("localhost", "root", "", "vickydb");
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }

            // Fetch items from the database
            $sql_items = "SELECT id, item_name FROM items";
            $result_items = mysqli_query($conn, $sql_items);
            if (mysqli_num_rows($result_items) > 0) {
                while($row = mysqli_fetch_assoc($result_items)) {
                    echo "<option value='".$row['id']."'>".$row['item_name']."</option>";
                }
            }
            mysqli_close($conn);
            ?>
        </select>
        <input type="submit" value="Generate Report">
        <?php if(isset($_GET['from_date']) && isset($_GET['to_date'])): ?>
            <?php
            // Get the submitted form data
            $from_date = $_GET['from_date'];
            $to_date = $_GET['to_date'];
            $item_id = isset($_GET['item_id']) ? $_GET['item_id'] : '';

            // Connect to the database
            $conn = mysqli_connect("localhost", "root", "", "vickydb");
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }

            // Prepare the SQL query with item filter
            $sql = "SELECT o.customer_name, o.mobile_no, o.address, o.pickup_type, o.delivery_date, o.delivery_time,o.discount, i.item_name, oi.price, oi.quantity, o.advance_amount, o.balance_amount 
                    FROM orders o
                    INNER JOIN order_items oi ON o.id = oi.order_id
                    INNER JOIN items i ON oi.item_id = i.id
                    WHERE DATE(o.delivery_date) BETWEEN '$from_date' AND '$to_date'";
            // Add item filter if item is selected
            if (!empty($item_id)) {
                $sql .= " AND oi.item_id = '$item_id'";
            }

            // Execute the SQL query
            $result = mysqli_query($conn, $sql);

            // Display the sales report
            if (mysqli_num_rows($result) > 0) {
                echo "<a href='download_sales_report.php?from_date=$from_date&to_date=$to_date&item_id=$item_id' class='button'>Download as Excel</a>";
                echo "<h3>Sales Report from $from_date to $to_date</h3>";
                echo "<table>";
                echo "<tr><th>Customer Name</th><th>Mobile No</th><th>Address</th><th>Pickup Type</th><th>Delivery Date</th><th>Delivery Time</th><th>Item</th><th>Price</th><th>Quantity</th><th>Discount</th><th>Advance Amount</th><th>Balance Amount</th></tr>";
                while($row = mysqli_fetch_assoc($result)) {
                    $formatted_date = date("d F Y", strtotime($row['delivery_date']));
                    echo "<tr>";
                    echo "<td>".$row['customer_name']."</td>";
                    echo "<td>".$row['mobile_no']."</td>";
                    echo "<td>".$row['address']."</td>";
                    echo "<td>".$row['pickup_type']."</td>";
                    echo "<td>".$formatted_date."</td>";
                    echo "<td>".$row['delivery_time']."</td>";
                    echo "<td>".$row['item_name']."</td>";
                    echo "<td>".$row['price']."</td>";
                    echo "<td>".$row['quantity']."</td>";
                    echo "<td>".$row['discount']."</td>";
                    echo "<td>".$row['advance_amount']."</td>";
                    echo "<td>".$row['balance_amount']."</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "No sales found between the selected dates.";
            }

            // Close the database connection
            mysqli_close($conn);
            ?>
        <?php endif; ?>
    </form>

    <div class="row">
        <div class="col-xxl-11 m-b-30">
            <div class="card card-statistics h-100 mb-0">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="card-heading">
                        <h4 class="card-title">Today Orders</h4>
                    </div>
                    <div class="dropdown">
                        <a class="btn btn-xs" href="#!">Export <i class="zmdi zmdi-download pl-1"></i> </a>
                    </div>
                </div>
                <div class="card-body scrollbar scroll_dark pt-0" style="max-height: 350px;">
                    <div class="datatable-wrapper table-responsive">
                        <table id="datatable" class="table table-borderless table-striped">
                            <thead>
                                <tr>
                                    <th>Customer Name</th>
                                    <th>Mobile No</th>
                                    <th>Address</th>
                                    <th>Pickup Type</th>
                                    <th>Delivery Date</th>
                                    <th>Delivery Time</th>
                                    <th>Item</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Discount</th>
                                    <th>Advance Amount</th>
                                    <th>Balance Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            // Connect to the database
                            $conn = mysqli_connect("localhost", "root", "", "vickydb");
                            if (!$conn) {
                                die("Connection failed: " . mysqli_connect_error());
                            }

                            // Fetch today's orders from the database
                            $sql = "SELECT o.customer_name, o.mobile_no, o.address, o.pickup_type, o.delivery_date, o.delivery_time, i.item_name, oi.price, oi.quantity,o.discount, o.advance_amount, o.balance_amount 
                                    FROM orders o
                                    INNER JOIN order_items oi ON o.id = oi.order_id
                                    INNER JOIN items i ON oi.item_id = i.id
                                    WHERE DATE(o.delivery_date) = CURDATE()";
                            $result = mysqli_query($conn, $sql);

                            if (mysqli_num_rows($result) > 0) {
                                while($row = mysqli_fetch_assoc($result)) {
                                    $formatted_date = date("d F Y", strtotime($row['delivery_date']));
                                    echo "<tr>";
                                    echo "<td class='align-middle'>".$row['customer_name']."</td>";
                                    echo "<td class='align-middle'>".$row['mobile_no']."</td>";
                                    echo "<td class='align-middle'>".$row['address']."</td>";
                                    echo "<td class='align-middle'>".$row['pickup_type']."</td>";
                                    echo "<td class='align-middle'>".$formatted_date."</td>";
                                    echo "<td class='align-middle'>".$row['delivery_time']."</td>";
                                    echo "<td class='align-middle'>".$row['item_name']."</td>";
                                    echo "<td class='align-middle'>".$row['price']."</td>";
                                    echo "<td class='align-middle'>".$row['quantity']."</td>";
                                    echo "<td class='align-middle'>".$row['discount']."</td>";
                                    echo "<td class='align-middle'>".$row['advance_amount']."</td>";
                                    echo "<td class='align-middle'>".$row['balance_amount']."</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='11'>No orders today</td></tr>";
                            }

                            mysqli_close($conn);
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="right">
        <h3>All Orders</h3>
        <table>
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Mobile No</th>
                    <th>Address</th>
                    <th>Pickup Type</th>
                    <th>Delivery Date</th>
                    <th>Delivery Time</th>
                    <th>Item</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Discount</th>
                    <th>Advance Amount</th>
                    <th>Balance Amount</th>
                </tr>
            </thead>
            <tbody>
            <?php
            // Connect to the database
            $conn = mysqli_connect("localhost", "root", "", "vickydb");
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }

            // Fetch all orders from the database
            $sql = "SELECT o.customer_name, o.mobile_no, o.address, o.pickup_type, o.delivery_date, o.delivery_time, i.item_name, oi.price, oi.quantity,o.discount, o.advance_amount, o.balance_amount 
                    FROM orders o
                    INNER JOIN order_items oi ON o.id = oi.order_id
                    INNER JOIN items i ON oi.item_id = i.id";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    $formatted_date = date("d F Y", strtotime($row['delivery_date']));
                    echo "<tr>";
                    echo "<td>".$row['customer_name']."</td>";
                    echo "<td>".$row['mobile_no']."</td>";
                    echo "<td>".$row['address']."</td>";
                    echo "<td>".$row['pickup_type']."</td>";
                    echo "<td>".$formatted_date."</td>";
                    echo "<td>".$row['delivery_time']."</td>";
                    echo "<td>".$row['item_name']."</td>";
                    echo "<td>".$row['price']."</td>";
                    echo "<td>".$row['quantity']."</td>";
                    echo "<td>".$row['discount']."</td>";
                    echo "<td>".$row['advance_amount']."</td>";
                    echo "<td>".$row['balance_amount']."</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='11'>No orders</td></tr>";
            }

            mysqli_close($conn);
            ?>
            </tbody>
        </table>
    </div>
</body>
</html>
