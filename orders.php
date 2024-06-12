<?php
require 'base.php'; 
$conn = mysqli_connect("localhost", "root", "", "vickydb");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$current_date = date("Y-m-d");

// Fetch all orders with order items
$sql_all_orders = "SELECT o.id, o.customer_name, o.mobile_no, o.address, o.pickup_type, o.delivery_date, o.delivery_time, o.advance_amount, o.balance_amount, o.discount, o.total_amount
                    FROM orders o
                    ORDER BY o.delivery_date DESC"; // Ordering by delivery date descending
$result_all_orders = mysqli_query($conn, $sql_all_orders);

// Fetch unpaid orders (where balance amount > 0 and delivery date is before today)
$sql_unpaid_orders = "SELECT o.id, o.customer_name, o.mobile_no, o.address, o.pickup_type, o.delivery_date, o.delivery_time, o.advance_amount, o.discount, o.balance_amount, o.total_amount
                        FROM orders o
                        WHERE o.balance_amount > 0 AND o.delivery_date < '$current_date'
                        ORDER BY o.delivery_date DESC";


$result_unpaid_orders = mysqli_query($conn, $sql_unpaid_orders);

// Check if order_id is provided in the URL
if(isset($_GET['id'])) {
    $order_id = $_GET['id'];
    $sql_history = "SELECT * FROM advance_amount_history WHERE order_id = $order_id";
    // Execute this query and process the history data as needed
} else {
    echo "No order ID provided.";
}

mysqli_close($conn);
?>


<!DOCTYPE html>
<html>
<head>
    <title>Orders</title>
    <!-- Include CSS or any other resources -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
      
    </style>
    <script>
        function printSection(sectionId) {
            var printContents = document.getElementById(sectionId).innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = "<html><head><title>Orders</title></head><body>" + printContents + "</body>";
            window.print();
            document.body.innerHTML = originalContents;
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="text-center my-4">Orders Report</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <button class="btn btn-success btn-print" onclick="printSection('allOrdersSection')">Print All Orders</button>
                <button class="btn btn-success btn-print" onclick="printSection('unpaidOrdersSection')">Print Unpaid Orders</button>
            </div>
        </div>
        <div class="row" id="allOrdersSection">
            <div class="col-12 mb-4">
                <div class="card card-statistics h-100 mb-0">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="card-heading">
                            <h4 class="card-title">All Orders</h4>
                        </div>
                        <div class="dropdown">
                            <a class="btn btn-xs" href="#!">Export <i class="zmdi zmdi-download pl-1"></i> </a>
                        </div>
                    </div>
                    <div class="card-body scrollbar scroll_dark pt-0">
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
                                        <th>Advance Amount</th>
                                        <th>Balance Amount</th>
                                        <th>Discount</th>
                                        <th>Total Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (mysqli_num_rows($result_all_orders) > 0) {
                                        while($row = mysqli_fetch_assoc($result_all_orders)) {
                                            $formatted_date = date("d F Y", strtotime($row['delivery_date']));
                                            echo "<tr>";
                                            echo "<td>".$row['customer_name']."</td>";
                                            echo "<td>".$row['mobile_no']."</td>";
                                            echo "<td>".$row['address']."</td>";
                                            echo "<td>".$row['pickup_type']."</td>";
                                            echo "<td>".$formatted_date."</td>";
                                            echo "<td>".$row['delivery_time']."</td>";
                                            echo "<td>".$row['advance_amount']."</td>";
                                            echo "<td>".$row['balance_amount']."</td>";
                                            echo "<td>".$row['discount']."</td>";
                                            echo "<td>".$row['total_amount']."</td>";
                                            echo "<td>";
                                            echo "<a href='view_advance_history.php?id=".$row['id']."' class='btn btn-info btn-sm'>View</a>";
                                            echo "<a href='edit_order.php?id=".$row['id']."' class='btn btn-primary btn-sm'>Edit</a>";
                                            // Check if the user is logged in and is an admin
                                            if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {
                                                echo "<a href='delete_order.php?id=".$row['id']."' class='btn btn-danger btn-sm'>Delete</a>";
                                            }
                                            echo "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='11'>No orders</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" id="unpaidOrdersSection">
            <div class="col-12">
                <div class="card card-statistics h-100 mb-0">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="card-heading">
                            <h4 class="card-title">Unpaid Orders</h4>
                        </div>
                        <div class="dropdown">
                            <a class="btn btn-xs" href="#!">Export <i class="zmdi zmdi-download pl-1"></i> </a>
                        </div>
                    </div>
                    <div class="card-body scrollbar scroll_dark pt-0">
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
                                        <th>Advance Amount</th>
                                        <th>Balance Amount</th>
                                        <th>Discount</th>
                                        <th>Total Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (mysqli_num_rows($result_unpaid_orders) > 0) {
                                        while($row = mysqli_fetch_assoc($result_unpaid_orders)) {
                                            $formatted_date = date("d F Y", strtotime($row['delivery_date']));
                                            echo "<tr>";
                                            echo "<td>".$row['customer_name']."</td>";
                                            echo "<td>".$row['mobile_no']."</td>";
                                            echo "<td>".$row['address']."</td>";
                                            echo "<td>".$row['pickup_type']."</td>";
                                            echo "<td>".$formatted_date."</td>";
                                            echo "<td>".$row['delivery_time']."</td>";
                                            echo "<td>".$row['advance_amount']."</td>";
                                            echo "<td>".$row['balance_amount']."</td>";
                                            echo "<td>".$row['discount']."</td>";
                                            echo "<td>".$row['total_amount']."</td>";
                                            echo "<td>";
                                            echo "<a href='view_advance_history.php?id=".$row['id']."' class='btn btn-info btn-sm'>View</a>";
                                            echo "<a href='edit_order.php?id=".$row['id']."' class='btn btn-primary btn-sm'>Edit</a>";
                                            // Check if the user is logged in and is an admin
                                            if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {
                                                echo "<a href='delete_order.php?id=".$row['id']."' class='btn btn-danger btn-sm'>Delete</a>";
                                            }
                                            echo "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='11'>No unpaid orders</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
