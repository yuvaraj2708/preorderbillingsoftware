<?php
require 'base.php';

if(isset($_GET['id'])) {
    $item_id = $_GET['id'];

    // Fetch order details from the database based on the order ID
    $conn = mysqli_connect("localhost", "root", "", "vickydb");

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $sql = "SELECT * FROM items WHERE id = '$item_id'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $order = mysqli_fetch_assoc($result);
        // Display form to edit order
?>
<!DOCTYPE html>
<html>
<head>
<style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
            margin-top: 0;
        }
        form {
            max-width: 400px;
            margin: 0 auto;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }
        input[type="text"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
            box-sizing: border-box;
            font-size: 16px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        select {
            width: 100%;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
            box-sizing: border-box;
            font-size: 16px;
        }
        textarea {
            resize: vertical;
        }
    </style>
    <title>Edit Item</title>
</head>
<body>
    <h2>Edit Item</h2>
    <form action="save_edititem.php" method="post">
        <input type="hidden" name="item_id" value="<?php echo $order['id']; ?>">
        <label for="item_name">Item Name:</label>
        <input type="text" name="item_name" id="item_name" value="<?php echo $order['item_name']; ?>">
        <label for="price">Price:</label>
        <input type="text" name="price" id="price" value="<?php echo $order['price']; ?>">
       
        <input type="submit" value="Save Item">
    </form>
</body>
</html>
<?php
    } else {
        echo "Item not found.";
    }

    mysqli_close($conn);
}
?>
