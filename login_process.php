<?php
session_start();

// Check if user is already logged in
if (isset($_SESSION['username']) && isset($_SESSION['is_admin'])) {
    // Redirect to index page or appropriate page
    header("Location: index.php");
    exit(); // Make sure to exit after redirection
}

// Include database connection
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query database for user with the given username
    $sql = "SELECT * FROM user WHERE username='$username'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        // Verify password
        if (password_verify($password, $row['password'])) {
            // Start session and store user info
            $_SESSION['username'] = $username;
            $_SESSION['is_admin'] = $row['is_admin'];
            // Redirect to appropriate page based on user type
            if ($row['is_admin'] == 1) {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit();
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "User not found!";
    }
}

mysqli_close($conn);
?>
