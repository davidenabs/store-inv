<?php
    // DB connect 'config/connect.php'
    include 'config/connect.php';
    $errors = [];
    $success = [];
    include 'inc/auth/loginRedirector.php';
    // include 'inc/auth/adminRedirector.php';

    if (isset($_GET['edit']) && isset($_GET['id'])) {
        if (!$_GET['id'] == '') {
            
            $stmt = $conn->prepare("SELECT * FROM customer_table WHERE customer_id = :id");
            $stmt->execute(array(
                ':id' => trim($_GET['id'])
            ));
            $result = $stmt->fetchAll();
            $exist =  $stmt->rowCount();

        }
    }

    /**
     * 
     * DELETE EXISTING CUSTOMER
     */

    if (isset($_GET['delete']) && isset($_GET['id'])) {
        $stmt = $conn->prepare("DELETE FROM customer_table WHERE customer_id = :id");
        $stmt->execute(array(
            ':id' => trim($_GET['id'])
        ));
  
        // $stmt = $conn->prepare("DELETE FROM customer_item_table WHERE customer_id = :id");
        // $stmt->execute(array(
        //     ':id' => trim($_GET['id'])
        // ));
  
        if ($stmt) {
            $_SESSION['success'] = 'Custormer\'s deleted successfully';
            header('location: customer-list.php');
            exit();
        }
    }

    /**
     * 
     * EDIT AN EXISTING CUSTOMER
     */
    if (isset($_POST['update_customer'])) {
        $customer_name = trim($_POST['name']);
        $customer_address = trim($_POST['address']);
        $customer_email = trim($_POST['email']);
        $customer_phone = trim($_POST['phone']);
        $customer_website = trim($_POST['website']);
        if (empty($customer_name)) {array_push($errors, 'Custormer\'s full name is required'); }
        if (empty($customer_address)) {array_push($errors, 'Custormer\'s address is required'); }
        if (empty($customer_email)) {array_push($errors, 'Custormer\'s email address required'); } elseif ( filter_var( $customer_email, FILTER_VALIDATE_EMAIL ) == false ){array_push($errors, 'Email address inputed is not valid');}
        if (empty($customer_phone)) {array_push($errors, 'Custormer\'s phone number is required'); }
        
        if (count($errors) === 0) {
            $stmt = $conn->prepare("UPDATE customer_table 
            SET customer_name = :customer_name,
                customer_address = :customer_address,
                customer_email = :customer_email,
                customer_phone = :customer_phone,
                customer_website = :customer_website
            WHERE customer_id = :id
            ");

            $stmt->execute(array(
                ':customer_name'    => $customer_name,
                ':customer_address' => $customer_address,
                ':customer_email'   => $customer_email,
                ':customer_phone'   => $customer_phone,
                ':customer_website' => $customer_website,
                ':id'      => trim($_GET['id'])
            ));

            if ($stmt) {
                $_SESSION['success'] = 'Custormer\'s profile updated successfully';
                header('location: customer-list.php');
                exit();
            }
        }

    }


    /**
     * 
     * CREATE A NEW CUSTOMER
     */
    if (isset($_POST['save_customer'])) {
       
        $customer_name = trim($_POST['name']);
        $customer_address = trim($_POST['address']);
        $customer_email = trim($_POST['email']);
        $customer_phone = trim($_POST['phone']);
        $customer_website = trim($_POST['website']);
        if (empty($customer_name)) {array_push($errors, 'Custormer\'s full name is required'); }
        if (empty($customer_address)) {array_push($errors, 'Custormer\'s address is required'); }
        if (empty($customer_email)) {array_push($errors, 'Custormer\'s email address required'); } elseif ( filter_var( $customer_email, FILTER_VALIDATE_EMAIL ) == false ){array_push($errors, 'Email address inputed is not valid');}
        if (empty($customer_phone)) {array_push($errors, 'Custormer\'s phone number is required'); }

        if (count($errors) === 0) {
            $stmt = $conn->prepare("SELECT customer_email FROM customer_table WHERE customer_email = :customer_email LIMIT 1");
            $stmt->execute(array(
                ':customer_email' => $customer_email
            ));

            $result = $stmt->fetchAll();

            if ($result) {array_push($errors, 'This email address already exist in the database');} else {
                $stmt = $conn->prepare("INSERT INTO customer_table (
                    customer_name,
                    customer_address,
                    customer_email,
                    customer_phone,
                    customer_website
                ) VALUES (
                    :customer_name,
                    :customer_address,
                    :customer_email,
                    :customer_phone,
                    :customer_website
                )");

                $stmt->execute(array(
                    ':customer_name' => $customer_name,
                    ':customer_address' => $customer_address,
                    ':customer_email' => $customer_email,
                    ':customer_phone' => $customer_phone,
                    ':customer_website' => $customer_website
                ));

                if ($stmt) {
                    $_SESSION['success'] = 'New customer created successfully';
                    header('location: customer-list.php');
                    exit();
                }
            }
        }
    }

    include 'inc/header.php';
    define ("TITLE" , "Add Customer");
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
        <!-- <button class="btn btn-primary m-1" id="menu-toggle">&map; </button> -->

        <!-- <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button> -->
    
      <div class="container-fluid mt-">
        <div class="d-flex justify-content-center">
            <div class="col-md-12 rounded-0 border-0 shadow my-3 p-3" style="float: cen;">
            <div class="d-flex col-md- justify-content-between">
                <div class="head-1">Add customer</div>
                <a href="customer-list.php" class="btn btn-sm btn-light pull-right my-2"><i class="fa fa-plus"></i> View All Customers </a>
            </div>
            <?php include 'inc/alert.php'; ?>
            <?php
            if (isset($result)) {
                foreach ($result as $key => $row) {
                    $customer_name = $row['customer_name'];
                    $customer_email = $row['customer_email'];
                    $customer_phone = $row['customer_phone'];
                    $customer_address = $row['customer_address'];
                    $customer_website = $row['customer_website'];
                }
            }
                
            ?>
            <form action="" method="post" class="form">
                <div  class="my-2 col-md-12">
                    <label for="name">Customer full name</label>
                    <input type="text" id="name" name="name" class="form-control form-1" placeholder="New store name" value="<?php if(isset($result)) {echo $customer_name;} elseif (isset($customer_name)) {echo $customer_name;} ?>" />
                </div>
                <div  class="my-2 col-md-12">
                    <label for="addess">Address</label>
                    <input type="text" id="address" name="address" class="form-control form-1" placeholder="New store address" value="<?php if(isset($result)) {echo $customer_address;} elseif (isset($customer_address)) {echo $customer_address;} ?>" />
                </div>
                <div  class="my-2 col-md-12">
                    <label for="email">Email Address</label>
                    <input type="text" id="email" name="email" class="form-control form-1" placeholder="New store email addess" value="<?php if(isset($result)) {echo $customer_email;} elseif (isset($customer_email)) {echo $customer_email;} ?>" />
                </div>
                <div  class="my-2 col-md-12">
                    <label for="phone">Phone contact</label>
                    <input type="text" id="phone" name="phone" class="form-control form-1" placeholder="New store phone contact" value="<?php if(isset($result)) {echo $customer_phone;} elseif (isset($customer_phone)) {echo $customer_phone;} ?>" />
                </div>
                <div  class="my-2 col-md-12">
                    <label for="website">Website (optional)</label>
                    <input type="text" id="website" name="website" class="form-control form-1" placeholder="New store website" value="<?php if(isset($result)) {echo $customer_website;} elseif (isset($customer_website)) {echo $customer_website;} ?>" />
                </div>

                <div class="col-md-12" align="right">
                    <button type="submit" name="<?php if(isset($result)) {echo 'update_customer';} else  {echo 'save_customer';} ?>" class="btn btn-primary my-2 float-right px-4 shadow"><?php if(isset($result)) {echo 'Update customer';} else  {echo 'Save';} ?></button>
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