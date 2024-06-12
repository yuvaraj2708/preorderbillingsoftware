<?php require 'base.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Include necessary CSS and JS libraries -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>
    <div class="container">
        <!-- begin row -->
        <div class="row">
            <div class="col-xxl-7 m-b-30">
                <div class="card card-statistics h-100 mb-0 apexchart-tool-force-top">
                    <div class="card-header d-flex justify-content-between">
                        <div class="card-heading">
                            <h4 class="card-title">Site activity</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <form action="save_order.php" method="post">
                                    <div class="mb-3">
                                        <label for="customer_name" class="form-label">Customer Name:</label>
                                        <input type="text" name="customer_name" id="customer_name" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="mobile_no" class="form-label">Mobile No:</label>
                                        <input type="text" name="mobile_no" id="mobile_no" class="form-control" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)" title="Mobile number should not have more than 10 digits and only decimal values" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Address:</label>
                                        <textarea name="address" id="address" class="form-control" required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="pickup_type" class="form-label">Pickup Type:</label>
                                        <select name="pickup_type" id="pickup_type" class="form-select">
                                            <option value="Self Pickup">Self Pickup</option>
                                            <option value="Delivery">Delivery</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="delivery_date" class="form-label">Delivery Date:</label>
                                        <input type="date" name="delivery_date" id="delivery_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="delivery_time" class="form-label">Delivery Time:</label>
                                        <input type="time" name="delivery_time" id="delivery_time" class="form-control" value="<?php echo date('H:i'); ?>" required>
                                    </div>

                                    <div class="mb-3" id="itemsContainer">
                                        <label for="item_id" class="form-label">Items:</label>
                                        <select name="item_id" id="item_id" class="form-select">
                                            <option value="">Select Item</option>
                                            <?php
                                            // Fetch items from the database
                                            $conn = mysqli_connect("localhost", "root", "", "vickydb");
                                            $sql = "SELECT id, item_name, price FROM items";
                                            $result = mysqli_query($conn, $sql);

                                            if (mysqli_num_rows($result) > 0) {
                                                while ($row = mysqli_fetch_assoc($result)) {
                                                    echo "<option value='" . $row['id'] . "' data-price='" . $row['price'] . "'>" . $row['item_name'] . "</option>";
                                                }
                                            }
                                            mysqli_close($conn);
                                            ?>
                                        </select>
                                    </div>

                                    <div id="itemDetailsContainer"></div>

                                    <button type="button" class="btn btn-primary" onclick="addItem()">Add Item</button>

                                    <div class="mb-3">
                                        <label for="advance_amount" class="form-label">Advance Amount:</label>
                                        <input type="number" name="advance_amount" id="advance_amount" class="form-control">
                                    </div>
                                    <div class="mb-3">
    <label for="discount" class="form-label">Discount:</label>
    <input type="number" name="discount" id="discount" class="form-control" oninput="updateTotalAmount()" value="0">
</div>
                                    <div class="mb-3">
                                        <label for="total_amount" class="form-label">Total Amount:</label>
                                        <input type="number" name="total_amount" id="total_amount" class="form-control" readonly>
                                    </div>
                                    <input type="submit" value="Save Order" class="btn btn-primary">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <footer class="footer">
            <div class="row">
                <div class="col-12 col-sm-6 text-center text-sm-left">
                    <p>&copy; Copyright 2024. All rights reserved.</p>
                </div>
                <div class="col col-sm-6 ml-sm-auto text-center text-sm-right">
                    <p><a target="_blank" href=#></a></p>
                </div>
            </div>
        </footer>
        <!-- end footer -->
    </div>
    <!-- end app-wrap -->
    </div>
    <!-- end app -->

    <!-- plugins -->
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
        itemQuantityInput.name = "qty[]";
        itemQuantityInput.classList.add("form-control", "mb-2", "mr-2");
        itemQuantityInput.placeholder = "Quantity";
        itemQuantityInput.oninput = updateTotalAmount;

        const itemPriceInput = document.createElement("input");
        itemPriceInput.type = "text"; // Change input type to text for editable price
        itemPriceInput.name = "price[]";
        itemPriceInput.value = itemPrice.toFixed(2);
        itemPriceInput.classList.add("form-control", "mr-2");
        itemPriceInput.oninput = updateTotalAmount;

        // Append hidden input for item_id
        const itemIdInput = document.createElement("input");
        itemIdInput.type = "hidden";
        itemIdInput.name = "item_id[]";
        itemIdInput.value = itemId;

        const discardButton = document.createElement("button");
        discardButton.type = "button";
        discardButton.textContent = "Discard";
        discardButton.classList.add("btn", "btn-danger", "mr-2");
        discardButton.onclick = function() {
            itemDetailRow.remove();
            updateTotalAmount();
        };

        itemDetailRow.appendChild(itemNameLabel);
        itemDetailRow.appendChild(itemNameDisplay);
        itemDetailRow.appendChild(itemQuantityInput);
        itemDetailRow.appendChild(itemPriceInput);
        itemDetailRow.appendChild(itemIdInput);
        itemDetailRow.appendChild(discardButton);

        itemDetailsContainer.appendChild(itemDetailRow);

        // Reset the item dropdown
        itemDropdown.selectedIndex = 0;

        // Update total amount whenever an item is added
        updateTotalAmount();
    }

    function updateTotalAmount() {
    let totalAmount = 0;
    const itemDetailRows = document.querySelectorAll(".item-detail-row");

    itemDetailRows.forEach(row => {
        const quantity = parseFloat(row.querySelector("input[name='qty[]']").value) || 0;
        const price = parseFloat(row.querySelector("input[name='price[]']").value) || 0;
        totalAmount += quantity * price;
    });

    const discount = parseFloat(document.getElementById("discount").value) || 0;
    totalAmount -= discount; // Subtract discount from total amount

    document.getElementById("total_amount").value = totalAmount.toFixed(2);
}
</script>

</body>

</html>
