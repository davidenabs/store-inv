<?php 
    // DB connect 'config/connect.php'
    include 'config/connect.php';
    $errors = [];
    $success = [];
    include 'inc/auth/loginRedirector.php';
    // include 'inc/auth/adminRedirector.php';

    $stmt = $conn->query("SELECT * FROM customer_table ORDER BY customer_id DESC");
    $stmt->execute();

    $result = $stmt->fetchAll();

    $total_rows = $stmt->rowCount();

    $stmt = $conn->query("SELECT * FROM store_table ORDER BY store_id ASC");
    $stmt->execute();

    $s_result = $stmt->fetchAll();

    $s_total_rows = $stmt->rowCount();

    if (isset($_POST['select_customer'])) {
        $customer_id = trim($_POST['customer_id']);
        $store_id = trim($_POST['store_id']);
        if (empty($customer_id)) {array_push($errors, 'Select a customer');}
        if (count($errors) === 0) {
            
            $_SESSION['customer_id'] = $customer_id;
            $_SESSION['store_id'] = $store_id;
            header('location: create-invoice.php');
            exit();

        }
    }

    if (isset($_POST['no_customer'])) {
        $store_id = trim($_POST['store_id']);
        unset($_SESSION['customer_id']);
        $_SESSION['store_id'] = $store_id;
        header('location: create-invoice.php');
        exit();
    }

    include 'inc/header.php';
    define ("TITLE" , "Invoice To");
?>
    <title><?php echo TITLE; ?></title>
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
        <a class="nav-link m-1" id="menu-toggle"><i class="fa fa-bars"></i></a>
      <div class="container-fluid mt-">
        <div class="d-flex justify-content-center">
            <div class="col-md-12 rounded-0 border-0 shadow my-3 p-3" style="float: cen;">
            <div class="d-flex col-md- mb-3 justify-content-between">
                <div class="head-1"><?php echo TITLE; ?></div>
            </div>
            
            <?php include 'inc/alert.php'; ?>
            
            
                <form action="" method="post" class="form" enctype="multipart/form-data">

                <div for="category_name">Select store and customer</div>
                <div  class="my-2 col-md- input-group">
                <select name="store_id" id="store_id" class="form-control">
                        <option value="" disabled>Select Store</option>
                        
                    <?php
                    if (isset($s_result)) {
                        foreach ($s_result as $key => $row) {
                           echo '<option value="'.$row['store_id'].'">'.$row['store_name'].'</option>';
                        }
                    }
                        
                    ?>
                        
                    </select>
                    <select name="customer_id" id="customer_id" class="form-control">
                        <option value="">Select customer</option>
                        
                    <?php
                    if (isset($result)) {
                        foreach ($result as $key => $row) {
                           echo '<option value="'.$row['customer_id'].'">'.$row['customer_name'].'</option>';
                        }
                    }
                        
                    ?>
                        
                    </select>
                    <button type="submit" name="select_customer" class="btn btn-dark">Select</button>
                   
                </div>
                        
                <div class="col-md-12 mt-3 text-right">
                    <form action="" method="post">
                        <button type="submit" name="no_customer" class="btn-none btn">&raquo; Click here create blank invoice</button>
                    </form>
                </div>
            </form>
            
            </div>
            </div>
      </div>
    </div>
    <!-- /#page-content-wrapper -->

  </div>
  <!-- /#wrapper -->
  <script>
    if ( window.history.replaceState ) {
    window.history.replaceState( null, null, window.location.href );
    }
</script>
  <?php include 'inc/footer.php'; ?>
  </div>
</body>
</html>