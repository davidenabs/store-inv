<?php 
    // DB connect 'config/connect.php'
    include 'config/connect.php';
    $errors = [];
    $success = [];
    include 'inc/auth/loginRedirector.php';
    // include 'inc/auth/adminRedirector.php';
    
    $category_name ='';

    if (isset($_GET['edit']) && isset($_GET['id'])) {
        if (!$_GET['id'] == '') {
            
            $stmt = $conn->prepare("SELECT * FROM category_table WHERE category_id = :id");
            $stmt->execute(array(
                ':id' => trim($_GET['id'])
            ));
            $result = $stmt->fetchAll();
            $exist =  $stmt->rowCount();

        }
    }

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
                $_SESSION['success'] = 'Category deleted successfully';
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
    if (isset($_POST['update_category'])) {
        $category_name = trim($_POST['category_name']);

        if (empty($category_name)) {array_push($errors, 'Category name is required'); }
        
        if (count($errors) === 0) {
            
            
            $stmt = $conn->prepare("UPDATE category_table 
            SET category_name                = :category_name
            WHERE category_id                = :id
            ");

            $stmt->execute(array(
                
                ':category_name'                 => $category_name,
                
                ':id'                           => trim($_GET['id'])
            ));

            if ($stmt) {
                $_SESSION['success'] = 'Category updated successfully';
                header('location: category.php');
                exit();
            }
        }

    }

    /**
     * 
     * CREATE A NEW category
     */
    if (isset($_POST['create_category'])) {

        $category_name = trim($_POST['category_name']);

        if (empty($category_name)) {array_push($errors, 'category\'s name is required'); }

        
        if (count($errors) === 0) {

            
            $stmt = $conn->prepare("SELECT category_name FROM category_table WHERE category_name = :category_name LIMIT 1");
            $stmt->execute(array(
                ':category_name' => $category_name
            ));

            $result_name = $stmt->fetchAll();

            if ($result_name) {array_push($errors, 'This category name already exist in the database:'.$category_name);} else {
                
                $stmt = $conn->prepare("INSERT INTO category_table (                  
                    
                    category_name
                ) VALUES (
                    
                    :category_name
                )");

                $stmt->execute(array(
                    
                    ':category_name'  =>  $category_name
                ));

                if ($stmt) {
                    $_SESSION['success'] = 'New category created successfully';
                    header('location: category.php');
                    exit();
                }
            }
                
            
        }
        
    }


    include 'inc/header.php';
    define ("TITLE" , "Create category");
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
                <a href="category.php" class="btn btn-sm btn-light pull-right my-2"><i class="fa fa-plus"></i> View Categories </a>
            </div>
            
            <?php include 'inc/alert.php'; ?>
            <?php
            if (isset($result)) {
                foreach ($result as $key => $row) {
                    $category_name = $row['category_name'];
                }
            }
                
            ?>
            
                <form action="" method="post" class="form" enctype="multipart/form-data">

                <div  class="my-2 col-md-12">
                    <label for="category_name">Category name</label>
                    <input type="text" id="category_name" name="category_name" class="form-control form-1" placeholder="New category name" value="<?php if(isset($result)) {echo $category_name;} elseif (isset($category_name)) {echo $category_name;} ?>" />
                </div>
                        
                <div class="col-md-12" align="right">
                    <button type="submit" name="<?php if(isset($result)) {echo 'update_category';} else {echo 'create_category';} ?>" class="btn btn-primary my-2 float-right px-4 shadow"><?php if(isset($result)) {echo 'Update category';} else {echo 'Create category';} ?></button>
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