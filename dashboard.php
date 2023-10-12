<?php 
    // DB connect 'config/connect.php'
    include 'config/connect.php';
    $errors = [];
    $success = [];
    include 'inc/auth/loginRedirector.php';
    // include 'inc/auth/adminRedirector.php';

    /**
     * 
     * WEEKLY OVERVIEW
     */
    // SALES
    $stmt = $conn->query("SELECT SUM(order_total_amount) AS totalAmount FROM order_table WHERE WEEKOFYEAR(order_datetime)=WEEKOFYEAR(CURDATE())");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $order_total_amount = $result['totalAmount'];

    // PRODUCT
    $stmt = $conn->query("SELECT * FROM product_table WHERE WEEKOFYEAR(product_created_at)=WEEKOFYEAR(CURDATE())");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $added_product = count($result);

    // CUSTOMER
    $stmt = $conn->query("SELECT * FROM customer_table WHERE WEEKOFYEAR(customer_created_at)=WEEKOFYEAR(CURDATE())");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $added_customer = count($result);

    // CUSTOMER
    $stmt = $conn->query("SELECT * FROM store_table WHERE WEEKOFYEAR(store_created_at)=WEEKOFYEAR(CURDATE())");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $added_store = count($result);

    // CUSTOMER
    $stmt = $conn->query("SELECT * FROM order_table WHERE WEEKOFYEAR(order_datetime)=WEEKOFYEAR(CURDATE())");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $invoice_generated = count($result);


    /**
     * 
     * TABLE COUNT
     */
    // SALES
    $stmt = $conn->query("SELECT SUM(order_total_amount) AS totalAmount FROM order_table");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $order_total_amount = $result['totalAmount'];

    // PRODUCT
    $stmt = $conn->query("SELECT * FROM product_table WHERE product_available_quantity != 0");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $available_product = count($result);

    // CUSTOMER
    $stmt = $conn->query("SELECT * FROM customer_table");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $all_customer = count($result);

    // STORE
    $stmt = $conn->query("SELECT * FROM store_table");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $all_store = count($result);

    // USER
    $stmt = $conn->query("SELECT * FROM user_table");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $all_users = count($result);

    // CATEGORY
    $stmt = $conn->query("SELECT * FROM category_table");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_category = count($result);

    // INVOICE
    $stmt = $conn->query("SELECT * FROM order_table");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_invoice_generated = count($result);


    include 'inc/header.php';
    define ("TITLE" , "Dashboard");
?>
    <title><?php echo TITLE; ?></title>
    <link rel="stylesheet" href="assets/custom/dashboard.css">
</head>
<body>
<!-- Navbar -->
<?php include 'inc/navbar.php'; ?>
    <!-- /Navbar -->
    
    
    <div class="d-flex" id="wrapper">

    <!-- Sidebar -->
    <?php include 'inc/sidebar.php'; ?>
    <!-- /#sidebar-wrapper -->


    <!-- Page Content -->
    <div id="page-content-wrapper">
        <div class="d-flex justify-content-between">
            <a class="nav-link m-1" id="menu-toggle"><i class="fa fa-bars"></i></a>
            <div class="head-1 text-shadow"><?php echo TITLE; ?></div>
        </div>
       <div class="container-fluid mt-4">
        <div class="d-flex col-md- justify-content-between">
        </div>
        <?php include 'inc/alert.php'; ?>
        <div class="panel panel-headline shadow-sm my3">
                <div class="panel-heading">
                    <h6 class="panel-title">Weekly Overview</h6>
                        <p class="panel-subtitle">-</a>
                </p>
                </div>
                <div class="panel-body">
                    <div class="row d-flexjustify-content-center">
                        <div class="col-md-3">
                            <div class="metric">
                                <span class="icon"><i class="fa fa-bar-chart"></i></span>
                                <p> <a href="invoice.php" class="nav-link p-0 text-dark">
                                    <span class="number"><?php echo number_format($order_total_amount); ?></span>
                                    <span class="title">Sales</span>
                                    </a>
                            </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric">
                                <span class="icon"><i class="fa fa-exchange"></i></span>
                                <p>
                                    <span class="number"><?php echo number_format($added_product); ?></span>
                                    <span class="title">Products</span>
                                    
                            </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric">
                                <span class="icon"><i class="fa fa-users"></i></span>
                                <p>
                                    <span class="number"><?php echo number_format($added_customer); ?></span>
                                    <span class="title">Customers</span>
                                    
                                    
                            </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric">
                                <span class="icon"><i class="fa fa-list-alt"></i></span>
                                <p>
                                    <span class="number"><?php echo number_format($added_store); ?></span>
                                    <span class="title">Stores</span>
                                    
                            </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric">
                                <span class="icon"><i class="fa fa-money"></i></span>
                                <p>
                                    <span class="number"><?php echo number_format($order_total_amount); ?></span>
                                    <span class="title">Total Sales</span>
                                    
                            </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric">
                                <span class="icon"><i class="fa fa-file"></i></span>
                                <p>
                                    <span class="number"><?php echo number_format($invoice_generated); ?></span>
                                    <span class="title">Total Invoice</span>
                                    
                            </p>
                            </div>
                        </div>
                    </div>
                    <!--  -->
                </div>
            </div>

       
       

       <div class="panel panel-headline shadow-s my-3">
                <div class="panel-body">
                <h6 class="panel-title">Tab</h6>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="metric">
                                <span class="icon"><i class="fa fa-files-o"></i></span>
                                <p> <a href="invoice.php" class="nav-link p-0 text-dark">
                                    <span class="number"><?php echo number_format($total_invoice_generated); ?></span>
                                    <span class="title">Generated Invoice</span>
                                    </a>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric">
                                <span class="icon"><i class="fa fa-product-hunt"></i></span>
                                <p> <a href="product.php" class="nav-link p-0 text-dark">
                                    <span class="number"><?php echo number_format($available_product); ?></span>
                                    <span class="title">Available Products</span>
                                    </a>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric">
                                <span class="icon"><i class="fa fa-users"></i></span>
                                <p> <a href="customer-list.php" class="nav-link p-0 text-dark">
                                    <span class="number"><?php echo number_format($all_customer); ?></span>
                                    <span class="title">All Customers</span>
                                    
                                    </a>
                                </p>
                            </div>
                        </div>
                        <?php if ($_SESSION['login_user_role'] == 'ADM') {?>
                        <div class="col-md-3">
                            <div class="metric">
                                <span class="icon"><i class="fa fa-user-o"></i></span>
                                <p> <a href="user.php" class="nav-link p-0 text-dark">
                                    <span class="number"><?php echo number_format($all_users); ?></span>
                                    <span class="title">Users</span>
                                    </a>
                                </p>
                            </div>
                        </div>
                        <?php } ?>
                        <div class="col-md-3">
                            <div class="metric">
                                <span class="icon"><i class="fa fa-list-alt"></i></span>
                                <p> <a href="store.php" class="nav-link p-0 text-dark">
                                    <span class="number"><?php echo number_format($all_store); ?></span>
                                    <span class="title">Store</span>
                                    </a>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric">
                                <span class="icon"><i class="fa fa-list"></i></span>
                                <p> <a href="category.php" class="nav-link p-0 text-dark">
                                    <span class="number"><?php echo number_format($total_category); ?></span>
                                    <span class="title">Category</span>
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>

       </div>
       
    </div>
    <!-- /#page-content-wrapper -->

  </div>
  <!-- /#wrapper -->
  <?php include 'inc/footer.php'; ?>
  </div>
</body>
</html>