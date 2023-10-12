<?php 
    // DB connect 'config/connect.php'
    include 'config/connect.php';
    $errors = [];
    $success = [];
    include 'inc/auth/loginRedirector.php';
    // include 'inc/auth/adminRedirector.php';

    if (isset($_GET['edit']) && isset($_GET['id'])) {
        if (!$_GET['id'] == '') {
            
            $stmt = $conn->prepare("SELECT * FROM store_table WHERE store_id = :id");
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
     * 
     * not done yet
     */

    if (isset($_GET['delete']) && isset($_GET['id'])) {

        $stmt  = $conn->prepare("SELECT * FROM store_table WHERE store_id = :id");
        $stmt->execute(array(
            ':id' => trim($_GET['id'])
        ));
        $result = $stmt->fetchAll();

        foreach ($result as $key => $value) {
            $store_type = $value['store_type'];
        }

        if ($store_type != 1) {

        // Check if file already exists
        if (file_exists($value['store_logo'])) {
            unlink($value['store_logo']);
        }

        $stmt = $conn->prepare("DELETE FROM store_table WHERE store_id = :id");
        $stmt->execute(array(
            ':id' => trim($_GET['id'])
        ));
  
        $stmt = $conn->prepare("UPDATE product_table 
                SET product_store_id         = :product_store_id
                WHERE product_id                = :id
                ");

                $stmt->execute(array(
                    ':product_store_id'       => 1,
                    ':id'                        => trim($_GET['id'])
                ));
  
        if ($stmt) {
            $_SESSION['success'] = 'Store deleted successfully';
            header('location: store.php');
            exit();
        }

        } else {
            $_SESSION['error'] = 'This is the default/primary store and it cannot be deleted!';
            header('location: store.php');
            exit();
        }
    }

    /**TODO: DELECT AND UPDATE STORE_LOGO
     * 
     * EDIT AN EXISTING STORE
     */
    if (isset($_POST['update_store'])) {
        $store_name = trim($_POST['store_name']);
        $store_address = trim($_POST['store_address']);
        $store_email = trim($_POST['store_email']);
        $store_phone = trim($_POST['store_phone']);
        $store_website = trim($_POST['store_website']);
        if (empty($store_name)) {array_push($errors, 'Store\'s name is required'); }
        if (empty($store_address)) {array_push($errors, 'Store\'s address is required'); }
        if (empty($store_email)) {array_push($errors, 'Store\'s email address required'); } elseif ( filter_var( $store_email, FILTER_VALIDATE_EMAIL ) == false ){array_push($errors, 'Email address inputed is not valid');}
        if (empty($store_phone)) {array_push($errors, 'Store\'s phone number is required'); }
        $store_logo  = $_FILES["store_logo"]["name"];

        if ($store_logo != '') {
            $stmt  = $conn->prepare("SELECT * FROM store_table WHERE store_id = :id");
            $stmt->execute(array(':id' => trim($_GET['id'])));

            $result = $stmt->fetchAll();

            foreach ($result as $key => $value) {
                // Check if file already exists
                if (file_exists($value['store_logo'])) {
                    unlink($value['store_logo']);
                }    
            }

            // upload logo
            $target_dir = "assets/store_logo/";
            $target_file = $target_dir .'SL_'.time(). basename($_FILES["store_logo"]["name"]);
            $uploadOk = 1;
            $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

            // Check if image file is a actual image or fake image
            $check = getimagesize($_FILES["store_logo"]["tmp_name"]);
            if($check !== false) {$uploadOk = 1;} else
            { array_push($errors, 'Store\'s logo must be an images file'); $uploadOk = 0;}
            // Check file size
            if ($_FILES["store_logo"]["size"] > 5000000) {
                array_push($errors, 'Sorry, store\'s logo image is too large');
                $uploadOk = 0;
            }
            // Allow certain file formats
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif" ) {
                array_push($errors, 'Sorry, only JPG, JPEG, PNG & GIF files are allowed');
                $uploadOk = 0;
            }

            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {array_push($errors, 'Sorry, your file was not uploaded');
            // if everything is ok, try to upload file
            } else {
                move_uploaded_file($_FILES["store_logo"]["tmp_name"], $target_file);
            }
            
            $stmt = $conn->prepare("SELECT * FROM store_table WHERE store_logo = :id");
            $stmt->execute(array(':id' => trim($_GET['id'])));
            $stmt = $conn->prepare("UPDATE store_table SET store_logo = :store_logo WHERE store_id = :id");
            $stmt->execute(array(':store_logo' => $target_file, ':id'=> trim($_GET['id'])));

            $stmt = $conn->prepare("UPDATE store_table 
            SET store_name      = :store_name,
                store_address   = :store_address,
                store_email     = :store_email,
                store_phone     = :store_phone,
                store_website   = :store_website
            WHERE store_id      = :id
            ");

            $stmt->execute(array(
                ':store_name'    => $store_name,
                ':store_address' => $store_address,
                ':store_email'   => $store_email,
                ':store_phone'   => $store_phone,
                ':store_website' => $store_website,
                ':id'      => trim($_GET['id'])
            ));

            if ($stmt) {
                $_SESSION['success'] = 'Store profile updated successfully';
                header('location: store.php');
                exit();
            }

        }
        
        if (count($errors) === 0 && $store_logo === '') {          
            
            
            $stmt = $conn->prepare("UPDATE store_table 
            SET store_name      = :store_name,
                store_address   = :store_address,
                store_email     = :store_email,
                store_phone     = :store_phone,
                store_website   = :store_website
            WHERE store_id      = :id
            ");

            $stmt->execute(array(
                ':store_name'    => $store_name,
                ':store_address' => $store_address,
                ':store_email'   => $store_email,
                ':store_phone'   => $store_phone,
                ':store_website' => $store_website,
                ':id'      => trim($_GET['id'])
            ));

            if ($stmt) {
                $_SESSION['success'] = 'Store profile updated successfully';
                header('location: store.php');
                exit();
            }
        }

    }


    /**
     * 
     * CREATE A NEW STORE
     */
    if (isset($_POST['create_store'])) {
        $store_name = trim($_POST['store_name']);
        $store_address = trim($_POST['store_address']);
        $store_email = trim($_POST['store_email']);
        $store_phone = trim($_POST['store_phone']);
        $store_website = trim($_POST['store_website']);
        if (empty($store_name)) {array_push($errors, 'Store\'s name is required'); }
        if (empty($store_address)) {array_push($errors, 'Store\'s address is required'); }
        if (empty($store_email)) {array_push($errors, 'Store\'s email address required'); } elseif ( filter_var( $store_email, FILTER_VALIDATE_EMAIL ) == false ){array_push($errors, 'Email address inputed is not valid');}
        if (empty($store_phone)) {array_push($errors, 'Store\'s phone number is required'); }
        $store_logo  = $_FILES["store_logo"]["name"];

        if (count($errors) === 0) {

            if ($store_logo != '') {
               // upload logo
            $target_dir = "assets/store_logo/";
            $target_file = $target_dir .'SL_'.time(). basename($_FILES["store_logo"]["name"]);
            $uploadOk = 1;
            $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

            // Check if image file is a actual image or fake image
            $check = getimagesize($_FILES["store_logo"]["tmp_name"]);
            if($check !== false) {
                $uploadOk = 1;
            } else {
                array_push($errors, 'Store\'s logo must be an images file');

                $uploadOk = 0;
            }
            // Check file size
            if ($_FILES["store_logo"]["size"] > 5000000) {
                array_push($errors, 'Sorry, store\'s logo image is too large');

                $uploadOk = 0;
            }
            // Allow certain file formats
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif" ) {
                array_push($errors, 'Sorry, only JPG, JPEG, PNG & GIF files are allowed');

                $uploadOk = 0;
            }

            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                array_push($errors, 'Sorry, your file was not uploaded');
            // if everything is ok, try to upload file
            } else {
                if (move_uploaded_file($_FILES["store_logo"]["tmp_name"], $target_file)) {
                    array_push($success, "The file ". basename( $_FILES["store_logo"]["name"]). " has been uploaded");
                } else {
                    array_push($errors, 'Sorry, there was an error uploading your file');
                }
            }
            }
            
            $stmt = $conn->prepare("SELECT store_email FROM store_table WHERE store_email = :store_email LIMIT 1");
            $stmt->execute(array(
                ':store_email' => $store_email
            ));

            $result = $stmt->fetchAll();

            if ($result) {array_push($errors, 'This email address already exist in the database');} else {
                $stmt = $conn->prepare("INSERT INTO store_table (
                    store_name,
                    store_address,
                    store_email,
                    store_phone,
                    store_website,
                    store_logo
                ) VALUES (
                    :store_name,
                    :store_address,
                    :store_email,
                    :store_phone,
                    :store_website,
                    :store_logo
                )");

                $stmt->execute(array(
                    ':store_name' => $store_name,
                    ':store_address' => $store_address,
                    ':store_email' => $store_email,
                    ':store_phone' => $store_phone,
                    ':store_website' => $store_website,
                    ':store_logo' => $target_file
                ));

                if ($stmt) {
                    $_SESSION['success'] = 'New store created successfully';
                    header('location: store.php');
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
    <a class="nav-link m-1" id="menu-toggle"><i class="fa fa-bars"></i></a>
      <div class="container-fluid mt-">
        <div class="d-flex justify-content-center">
            <div class="col-md-12 rounded-0 border-0 shadow my-3 p-3" style="float: cen;">
            <div class="d-flex col-md- justify-content-between">
                <div class="head-1">Store</div>
                <a href="store.php" class="btn btn-sm btn-light pull-right my-2"><i class="fa fa-eye"></i> View Stores </a>
            </div>
            <p class="text-success">Add a new store</p>
            <?php include 'inc/alert.php'; ?>
            <?php
            if (isset($result)) {
                foreach ($result as $key => $row) {
                    $store_name = $row['store_name'];
                    $store_email = $row['store_email'];
                    $store_phone = $row['store_phone'];
                    $store_address = $row['store_address'];
                    $store_website = $row['store_website'];
                }
            }
                
            ?>
            <form action="" method="post" class="form" enctype="multipart/form-data">
                <div  class="my-2 col-md-12">
                    <label for="store_name">Store name</label>
                    <input type="text" id="store_name" name="store_name" class="form-control form-1" placeholder="New store name" value="<?php if(isset($result)) {echo $store_name;} elseif (isset($store_name)) {echo $store_name;} ?>" />
                </div>
                <div  class="my-2 col-md-12">
                    <label for="store_address">Address</label>
                    <input type="text" id="store_address" name="store_address" class="form-control form-1" placeholder="New store address" value="<?php if(isset($result)) {echo $store_address;} elseif (isset($store_address)) {echo $store_address;} ?>"
                     />
                </div>
                <div  class="my-2 col-md-12">
                    <label for="store_email">Email Address</label>
                    <input type="text" id="store_email" name="store_email" class="form-control form-1" placeholder="New store email addess" value="<?php if(isset($result)) {echo $store_email;} elseif (isset($store_email)) {echo $store_email;} ?>" />
                </div>
                <div  class="my-2 col-md-12">
                    <label for="store_phone">Store phone contact</label>
                    <input type="text" id="store_phone" name="store_phone" class="form-control form-1" placeholder="New store phone contact" value="<?php if(isset($result)) {echo $store_phone;} elseif (isset($store_phone)) {echo $store_phone;} ?>" />
                </div>
                <div  class="my-2 col-md-12">
                    <label for="store_website">Website (optional)</label>
                    <input type="text" id="store_website" name="store_website" class="form-control form-1" placeholder="New store website" value="<?php if(isset($result)) {echo $store_website;} elseif (isset($store_website)) {echo $store_website;} ?>" />
                </div>

                <div  class="my-2 col-md-12">
                    <label for="store_logo">Logo</label>
                    <input type="file" id="store_logo" name="store_logo" class="form-control form-1" />
                </div>

                <div class="col-md-12" align="right">
                    <button type="submit" name="<?php if(isset($result)) {echo 'update_store';} else {echo 'create_store';} ?>" class="btn btn-primary my-2 float-right px-4 shadow"><?php if(isset($result)) {echo 'Update store';} else {echo 'Create store';} ?></button>
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