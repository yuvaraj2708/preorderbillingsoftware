<?php
require 'base.php'; 
$conn = mysqli_connect("localhost", "root", "", "vickydb");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$current_date = date("Y-m-d");

// Fetch all orders
$sql_all_orders = "SELECT o.id, o.item_name, o.price 
                    FROM items o"; // Ordering by delivery date descending
$result_all_orders = mysqli_query($conn, $sql_all_orders);

mysqli_close($conn);
?>


</head>
<div class="row">
                            <div class="col-xxl-11 m-b-30">
                                <div class="card card-statistics h-100 mb-0">
                                    <div class="card-header d-flex align-items-center justify-content-between">
                                        <div class="card-heading">
                                            <h4 class="card-title">Item Lists</h4>
                                        </div>
                                       </div>
                                    <div class="card-body scrollbar scroll_dark pt-0" style="max-height: 350px;">
                                        <div class="datatable-wrapper table-responsive">
                                            <table id="datatable" class="table table-borderless table-striped">
                                                <thead>
                                                    <tr>
                                                    <th>Item Name</th>
                    <th>Price</th>
                 
                    <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <?php
if (mysqli_num_rows($result_all_orders) > 0) {
    while($row = mysqli_fetch_assoc($result_all_orders)) {
        echo "<tr>";
        echo "<td class='align-middle'>".$row['item_name']."</td>";
        echo "<td class='align-middle'>".$row['price']."</td>";
       
        echo "<td class='align-middle'>
                <a href='edit_item.php?id=".$row['id']."' class='btn btn-primary btn-sm'>Edit</a> 
                
              </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='12'>No orders</td></tr>";
}
?>

                                             
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                           