<?php 
require 'base.php';

if (isset($_GET['id'])) {
    $order_id = $_GET['id'];

    // Fetch order details from the database based on the order ID
    $conn = mysqli_connect("localhost", "root", "", "vickydb");

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $sql = "SELECT * FROM orders WHERE id = '$order_id'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $order = mysqli_fetch_assoc($result);
        // Fetch order items
        $sql_items = "SELECT oi.*, i.item_name, i.price FROM order_items oi 
                      JOIN items i ON oi.item_id = i.id WHERE oi.order_id = '$order_id'";
        $result_items = mysqli_query($conn, $sql_items);
        $order_items = [];
        while ($row = mysqli_fetch_assoc($result_items)) {
            $order_items[] = $row;
        }
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Edit Order</title>
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
            <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        </head>
        <body>
        <div class="container">
            <h2 class="mt-4">Edit Order</h2>
            <form action="save_editorder.php" method="post">
                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                <div class="mb-3">
                    <label for="customer_name" class="form-label">Customer Name:</label>
                    <input type="text" name="customer_name" id="customer_name" class="form-control" value="<?php echo $order['customer_name']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="mobile_no" class="form-label">Mobile No:</label>
                    <input type="text" name="mobile_no" id="mobile_no" class="form-control" maxlength="10" value="<?php echo $order['mobile_no']; ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)" title="Mobile number should not have more than 10 digits and only decimal values" required>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Address:</label>
                    <textarea name="address" id="address" class="form-control" required><?php echo $order['address']; ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="pickup_type" class="form-label">Pickup Type:</label>
                    <select name="pickup_type" id="pickup_type" class="form-select">
                        <option value="Self Pickup" <?php if ($order['pickup_type'] == 'Self Pickup') echo 'selected'; ?>>Self Pickup</option>
                        <option value="Delivery" <?php if ($order['pickup_type'] == 'Delivery') echo 'selected'; ?>>Delivery</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="delivery_date" class="form-label">Delivery Date:</label>
                    <input type="date" name="delivery_date" id="delivery_date" class="form-control" value="<?php echo $order['delivery_date']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="delivery_time" class="form-label">Delivery Time:</label>
                    <input type="time" name="delivery_time" id="delivery_time" class="form-control" value="<?php echo $order['delivery_time']; ?>" required>
                </div>

                <div class="mb-3" id="itemsContainer">
                    <label for="item_id" class="form-label">Items:</label>
                    <select id="item_id" class="form-select">
                        <option value="">Select Item</option>
                        <?php
                        // Fetch items from the database
                        $sql_items = "SELECT id, item_name, price FROM items";
                        $result_items = mysqli_query($conn, $sql_items);

                        if (mysqli_num_rows($result_items) > 0) {
                            while ($row = mysqli_fetch_assoc($result_items)) {
                                echo "<option value='" . $row['id'] . "' data-price='" . $row['price'] . "'>" . $row['item_name'] . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <div id="itemDetailsContainer">
                    <?php foreach ($order_items as $item) { ?>
                        <div class="item-detail-row d-flex align-items-center mb-2">
    <label class="mr-2">Item:</label>
    <span class="mr-4 font-weight-bold"><?php echo $item['item_name']; ?></span>
    <input type="number" name="quantity[]" class="form-control mb-2 mr-2" placeholder="Quantity" value="<?php echo $item['quantity']; ?>" oninput="updateTotalAmount()">
    <input type="text" name="price[]" class="form-control mr-2" value="<?php echo $item['price']; ?>" oninput="updateTotalAmount()">
    <input type="hidden" name="item_id[]" value="<?php echo $item['item_id']; ?>">
    <button type="button" class="btn btn-danger" onclick="discardItem(this)">Discard</button>
</div>

                    <?php } ?>
                </div>

                <button type="button" class="btn btn-primary" onclick="addItem()">Add Item</button>

                <div class="mb-3">
                    <label for="advance_amount" class="form-label">Advance Amount:</label>
                    <input type="number" name="advance_amount" id="advance_amount" class="form-control" value="<?php echo $order['advance_amount']; ?>" oninput="calculateBalance()">
                </div>
                <div class="mb-3">
    <label for="discount" class="form-label">Discount:</label>
    <input type="number" name="discount" id="discount" class="form-control" value="<?php echo $order['discount']; ?>" required>
</div>
                <div class="mb-3">
                    <label for="total_amount" class="form-label">Total Amount:</label>
                    <input type="number" name="total_amount" id="total_amount" class="form-control" value="<?php echo $order['total_amount']; ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="balance_amount" class="form-label">Balance Amount:</label>
                    <input type="number" name="balance_amount" id="balance_amount" class="form-control" value="<?php echo number_format((float)$order['total_amount'] - (float)$order['advance_amount'] - (float)$order['discount'], 2, '.', ''); ?>" readonly>
                </div>
                <input type="submit" value="Save Order" class="btn btn-primary">
            </form>
        </div>
        <script>
    function addItem() {
        const itemDropdown = document.getElementById("item_id");
        const selectedItem = itemDropdown.options[itemDropdown.selectedIndex];
        const itemName = selectedItem.text;
        const itemId = selectedItem.value;
        const itemPrice = parseFloat(selectedItem.getAttribute("data-price"));

        if (!itemId) {
            alert("Please select an item.");
            return;
        }

        function discardItem(button) {
    // Get the parent div of the item
    const itemRow = button.parentElement;
    // Remove the item's parent div from the DOM
    itemRow.remove();
    // Update total amount after discarding the item
    updateTotalAmount();
    // If the item has an ID (indicating it's from the database), remove it from the database
    const itemId = itemRow.querySelector("input[name='item_id[]']").value;
    if (itemId) {
        // Make an AJAX request to delete the item from the database
        // Replace 'delete_item.php' with the appropriate URL for your delete script
        $.post('delete_item.php', { item_id: itemId }, function(response) {
            if (response.success) {
                // Item successfully deleted from the database
                console.log("Item deleted from the database.");
            } else {
                // Error deleting item from the database
                console.error("Error deleting item from the database.");
            }
        });
    }
}



        const itemDetailsContainer = document.getElementById("itemDetailsContainer");

        const itemDetailRow = document.createElement("div");
        itemDetailRow.classList.add("item-detail-row", "d-flex", "align-items-center", "mb-2");

        const itemNameLabel = document.createElement("label");
        itemNameLabel.textContent = "Item:";
        itemNameLabel.classList.add("mr-2");

        const itemNameDisplay = document.createElement("span");
        itemNameDisplay.textContent = itemName;
        itemNameDisplay.classList.add("mr-4", "font-weight-bold");

        const itemQuantityInput = document.createElement("input");
        itemQuantityInput.type = "number";
        itemQuantityInput.name = "quantity[]";
        itemQuantityInput.classList.add("form-control", "mb-2", "mr-2");
        itemQuantityInput.placeholder = "Quantity";
        itemQuantityInput.oninput = updateTotalAmount;

        const itemPriceInput = document.createElement("input");
        itemPriceInput.type = "text"; // Change input type to text for editable price
        itemPriceInput.name = "price[]";
        itemPriceInput.value = itemPrice.toFixed(2);
        itemPriceInput.classList.add("form-control", "mr-2");
        itemPriceInput.oninput = updateTotalAmount;

        const itemIdInput = document.createElement("input");
        itemIdInput.type = "hidden";
        itemIdInput.name = "item_id[]";
        itemIdInput.value = itemId;
        
        


        itemDetailRow.appendChild(itemNameLabel);
        itemDetailRow.appendChild(itemNameDisplay);
        itemDetailRow.appendChild(itemQuantityInput);
        itemDetailRow.appendChild(itemPriceInput);
        itemDetailRow.appendChild(itemIdInput);

        itemDetailsContainer.appendChild(itemDetailRow);

        itemDropdown.selectedIndex = 0;
        updateTotalAmount();
    }

    function updateTotalAmount() {
        let totalAmount = 0;
        const itemDetailRows = document.querySelectorAll(".item-detail-row");

        itemDetailRows.forEach(row => {
            const quantity = parseFloat(row.querySelector("input[name='quantity[]']").value) || 0;
            const price = parseFloat(row.querySelector("input[name='price[]']").value) || 0;
            totalAmount += quantity * price;
        });

        document.getElementById("total_amount").value = totalAmount.toFixed(2);
        calculateBalance();
    }

    function calculateBalance() {
    const totalAmount = parseFloat(document.getElementById('total_amount').value) || 0;
    const advanceAmount = parseFloat(document.getElementById('advance_amount').value) || 0;
    const discount = parseFloat(document.getElementById('discount').value) || 0;
    const balanceAmount = totalAmount - advanceAmount - discount;
    document.getElementById('balance_amount').value = balanceAmount.toFixed(2);
}
</script>

        </body>
        </html>
        <?php
    } else {
        echo "Order not found.";
    }

    mysqli_close($conn);
} else {
    echo "Order ID not specified.";
}
?>
