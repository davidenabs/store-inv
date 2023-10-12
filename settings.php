<?php 
    // DB connect 'config/connect.php'
    include 'config/connect.php';
    $errors = [];
    $success = [];
    include 'inc/auth/loginRedirector.php';
    // include 'inc/auth/adminRedirector.php';
    
    $category_name ='';

            
    $stmt = $conn->prepare("SELECT * FROM store_table WHERE store_type = 1");
    $stmt->execute();
    $res = $stmt->fetchAll();

    /**
     * 
     * DELETE CATEGORY
     * IF THE CATEGORY IS IN USE IN THE PRODUCT_TABLE, SET THE ID TO 1
     * 1 IS THE DEFUALT CATEGORY AND THE IS CATEGORY NAME IS UNCATEGORIZED
     */
    if (isset($_GET['delete']) && isset($_GET['id'])) {
        

        if (trim($_GET['id']) != 1) {
            $stmt  = $conn->prepare("SELECT * FROM category_table WHERE category_id = :id LIMIT 1");
            $stmt->execute(array(
                ':id' => trim($_GET['id'])
            ));

            $result = $stmt->fetchAll();

            $stmt = $conn->prepare("DELETE FROM category_table WHERE category_id = :id ");
            $stmt->execute(array(
                ':id' => trim($_GET['id'])
            ));

            $stmt = $conn->prepare("UPDATE product_table 
                SET product_category_id         = :product_category_id
                WHERE product_id                = :id
                ");

                $stmt->execute(array(
                    ':product_category_id'       => 1,
                    ':id'                        => trim($_GET['id'])
                ));
            
            if ($stmt) {
                $_SESSION['success'] = 'Category deleted succefully';
                header('location: category.php');
                exit();
            }

        } else {
            $_SESSION['error'] = 'Uncategorized category is the default category and it cannot be deleted';
            header('location: category.php');
            exit();
        }

    }
    /**
     * 
     * EDIT AN EXISTING STORE
     */
    if (isset($_POST['save_setting'])) {
        $new_store_id = trim($_POST['store_id']);

        if (empty($new_store_id)) {array_push($errors, 'Store name is required'); }

        if (count($errors) === 0 ) {
            $stmt = $conn->prepare("SELECT * FROM store_table WHERE store_type = :one");
            $stmt->execute(array(
                ':one' => 1
            ));
            $re = $stmt->fetchAll();

            // if (count($re) > 0) {
                    foreach ($re as $val) {
                        $old_store_id = $val['store_id'];
                    }
                    $stmt = $conn->prepare("UPDATE store_table
                        SET store_type = :store_type
                        WHERE store_id = :store_id
                    ");
                    $stmt->execute(array(
                        ':store_type'       => 0,
                        ':store_id'         => $old_store_id
                    ));
                // }

            $stmt = $conn->prepare("UPDATE store_table
                SET store_type = :store_type
                WHERE store_id = :store_id
            ");
            $stmt->execute(array(
                ':store_type'       => 1,
                ':store_id'         => $new_store_id
            ));

            if ($stmt) {
                $_SESSION['success'] = 'Updated succefully';
                header('location: settings.php');
                exit();
            }
        } else {
            $_SESSION['error'] = 'something went wrong';
        }
        

    }



    include 'inc/header.php';
    define ("TITLE" , "Settings");
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
            <div class="col-md-8 rounded-0 border-0 shadow my-3 p-3" style="float: cen;">
            <div class="d-flex col-md- mb-3 justify-content-between">
                <div class="head-1"><?php echo TITLE; ?></div>
            </div>
            
            <?php
            $store_name ='';

            if (isset($res)) {
                foreach ($res as $row) {
                    $store_name = $row['store_name'];
                }
            }
                
            ?>
            
            <?php include 'inc/alert.php'; ?>
                <form action="" method="post" class="form" enctype="multipart/form-data">
                <div  class="my-2 col-md-12">
                    <label for="category_name">Primary Store</label>
                    <input type="text" class="form-control form-1" value="<?php echo $store_name; ?>" readonly />
                </div>
                <div class="my-3 py-1"><hr></div>
                <div  class="my-2 col-md-12">
                    <label for="store_id">Change primary Store</label>
                   <select name="store_id" id="store_id" class="form-control form-1" >
                       <?php
                            $stmt = $conn->prepare("SELECT * FROM store_table");
                            $stmt->execute();
                            $s_result = $stmt->fetchAll();
                            foreach ($s_result as $value) {
                                echo '<option value="'.$value['store_id'].'">'.$value['store_name'].'</option>';
                            }
                       ?>
                   </select>
                </div>
                        
                <div class="col-md-12" align="right">
                    <button type="submit" name="save_setting" class="btn btn-primary my-2 float-right px-4 shadow">Save</button>
                </div>
            </form>
            <p class="bg-light p-1 border mt-3">
                Note: Any store that is set to default/primary, will be used for application and invoice branding. 
            </p>
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