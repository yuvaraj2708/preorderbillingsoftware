<?php
session_start();

// Check if user is not logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['is_admin'])) {
    // Redirect to login page
    header("Location: login.php");
    exit();
}

// If user is logged in, you can continue with your index.php content
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Billing</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="Admin template that can be used to build dashboards for CRM, CMS, etc." />
    <meta name="author" content="Potenza Global Solutions" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- app favicon -->
    <link rel="shortcut icon" href="assets/img/favcon.ico">
    <!-- google fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">
    <!-- plugin stylesheets -->
    <link rel="stylesheet" type="text/css" href="assets/css/vendors.css" />
    <!-- app style -->
    <link rel="stylesheet" type="text/css" href="assets/css/style.css" />
</head>

<body>
    <!-- begin app -->
    <div class="app">
        <!-- begin app-wrap -->
        <div class="app-wrap">
            <!-- begin pre-loader -->
            <div class="loader">
                <div class="h-100 d-flex justify-content-center">
                    <div class="align-self-center">
                        <img src="assets/img/loader/loader.svg" alt="loader">
                    </div>
                </div>
            </div>
            <!-- end pre-loader -->
            <!-- begin app-header -->
            <header class="app-header top-bar">
                <!-- begin navbar -->
                <nav class="navbar navbar-expand-md">

                    <!-- begin navbar-header -->
                    <div class="navbar-header d-flex align-items-center">
                        <a href="javascript:void:(0)" class="mobile-toggle"><i class="ti ti-align-right"></i></a>
                        <a class="navbar-brand" href="index.html">
                            <img src="assets/img/ogo.png" class="img-fluid logo-desktop" alt="logo" />
                            <img src="assets/img/loo-icon.png" class="img-fluid logo-mobile" alt="logo" />
                        </a>
                    </div>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="ti ti-align-left"></i>
                    </button>
                    <!-- end navbar-header -->
                    <!-- begin navigation -->
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <div class="navigation d-flex">
                            <ul class="navbar-nav nav-left">
                                <li class="nav-item">
                                    <a href="javascript:void(0)" class="nav-link sidebar-toggle">
                                        <i class="ti ti-align-right"></i>
                                    </a>
                                </li>
                               
                                <li class="nav-item full-screen d-none d-lg-block" id="btnFullscreen">
                                    <a href="javascript:void(0)" class="nav-link expand">
                                        <i class="icon-size-fullscreen"></i>
                                    </a>
                                </li>
                            </ul>
                          
                        </div>
                    </div>
                    <!-- end navigation -->
                </nav>
                <!-- end navbar -->
            </header>
            <div class="app-container">
                <!-- begin app-nabar -->
                <aside class="app-navbar">
                    <!-- begin sidebar-nav -->
                    <div class="sidebar-nav scrollbar scroll_light">
                        <ul class="metismenu " id="sidebarNav">
                            <li class="nav-static-title">Personal</li>
                            <li class="active">
                                <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                                    <i class="nav-icon ti ti-rocket"></i>
                                    <span class="nav-title">Orders</span>
                                    <span class="nav-label label label-danger"></span>
                                </a>
                                <ul aria-expanded="false">
                                    <li class="active"> <a href='index.php'>Place Order</a> </li>
                                    <li> <a href='orders.php'>Order List</a> </li>
                                   
                                </ul>
                            </li>
                           
                            <li><a href="additem.php" aria-expanded="false"><i class="nav-icon ti ti-comment"></i><span class="nav-title">Add Item</span></a> </li>
                            <li><a href="itemlist.php" aria-expanded="false"><i class="nav-icon ti ti-comment"></i><span class="nav-title">Item List</span></a> </li>
                            <?php if ($_SESSION['is_admin'] == 1): ?>
                                <li><a href="salesreport.php" aria-expanded="false"><i class="nav-icon ti ti-comment"></i><span class="nav-title">Sales Report</span></a> </li>    
                            <li><a href="itemreport.php" aria-expanded="false"><i class="nav-icon ti ti-comment"></i><span class="nav-title">Item Report</span></a> </li>
                            <li><a href="collectionreport.php" aria-expanded="false"><i class="nav-icon ti ti-comment"></i><span class="nav-title">Collection Report</span></a> </li>
                            <?php endif; ?>
                           
                            <li><a href="Logout.php" aria-expanded="false"><i class="nav-icon ti ti-comment"></i><span class="nav-title">Logout</span></a> </li>
                          
                                </ul>
                    </div>
                    <!-- end sidebar-nav -->
                </aside>
                <!-- end app-navbar -->
                <!-- begin app-main -->
                <div class="app-main" id="main">
                    <!-- begin container-fluid -->
                    <div class="container-fluid">
                        <!-- begin row -->
                        <div class="row">
                            <div class="col-md-12 m-b-30">
                                <!-- begin page title -->
                                <div class="d-block d-lg-flex flex-nowrap align-items-center">
                                    <div class="page-title mr-4 pr-4 border-right">
                                        <h1>Dashboard</h1>
                                    </div>
                                    <div class="breadcrumb-bar align-items-center">
                                        <nav>
                                            <ol class="breadcrumb p-0 m-b-0">
                                                <li class="breadcrumb-item">
                                                    <a href="index.php"><i class="ti ti-home"></i></a>
                                                </li>
                                                <li class="breadcrumb-item">
                                                    Dashboard
                                                </li>
                                                <li class="breadcrumb-item active text-primary" aria-current="page">Default</li>
                                            </ol>
                                        </nav>
                                    </div>
                                  
                                </div>
                                <!-- end page title -->
                            </div>
                        </div>
                        <!-- Notification -->
                       
                        <script src="assets/js/vendors.js"></script>

                        <!-- custom app -->
                        <script src="assets/js/app.js"></script>
