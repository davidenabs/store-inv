<?php 
    // DB connect 'config/connect.php'
    include 'config/connect.php';
    include 'inc/auth/loginRedirector.php';
    // include 'inc/auth/adminRedirector.php';
    $errors = [];
    $success = [];
    $product_category_id ='';
    $product_name ='';
    $product_description ='';
    $product_quantity ='';
    $product_cost ='';
    $product_retail_price ='';
    $product_wholesale_price ='';
    $product_sold_out_quantity ='';
    $product_available_quantity ='';
    $product_store_id ='';
    $product_instock_status ='';
    $product_type ='';
    $product_image ='';

    if (isset($_GET['edit']) && isset($_GET['id'])) {
        if (!$_GET['id'] == '') {
            
            $stmt = $conn->prepare("SELECT * FROM product_table WHERE product_id = :id");
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
        $stmt  = $conn->prepare("SELECT * FROM product_table WHERE product_id = :id");
        $stmt->execute(array(
            ':id' => trim($_GET['id'])
        ));

        $result = $stmt->fetchAll();

        foreach ($result as $key => $value) {
            // Check if file already exists
            if (file_exists($value['product_image'])) {
                unlink($value['product_logo']);
            }
            
        }

        $stmt = $conn->prepare("DELETE FROM product_table WHERE product_id = :id");
        $stmt->execute(array(
            ':id' => trim($_GET['id'])
        ));
  
    //     // $stmt = $conn->prepare("DELETE FROM store_item_table WHERE store_id = :id");
    //     // $stmt->execute(array(
    //     //     ':id' => trim($_GET['id'])
    //     // ));
  
        if ($stmt) {
            $_SESSION['success'] = 'Product deleted successfully';
            header('location: product.php');
            exit();
        }
    }

    /**TODO: DELECT AND UPDATE product_image
     * 
     * EDIT AN EXISTING STORE
     */
    if (isset($_POST['update_product'])) {
        $product_name = trim($_POST['product_name']);
        $product_description = trim($_POST['product_description']);
        $product_category_id = trim($_POST['product_category_id']);
        $product_quantity = trim($_POST['product_quantity']);
        $product_cost = trim($_POST['product_cost']);
        $product_retail_price = trim($_POST['product_retail_price']);
        $product_wholesale_price = trim($_POST['product_wholesale_price']);
        $product_store_id = trim($_POST['product_store_id']);
        $product_type = trim($_POST['product_type']);
        $product_instock_status = trim($_POST['product_instock_status']);

        if (empty($product_name)) {array_push($errors, 'Product name is required'); }
        if (empty($product_description)) {array_push($errors, 'Product description is required'); } elseif (strlen($product_description) > 100) {array_push($errors, 'Product description must not be more that 100 characters');}
        if (empty($product_category_id)) {array_push($errors, 'Product category required'); }
        if (empty($product_cost)) {array_push($errors, 'Cost of product is required'); }
        if (empty($product_retail_price)) {array_push($errors, 'Retail price of product is required'); }
        if (empty($product_wholesale_price)) {array_push($errors, 'Wholesale price product is required'); }
        if (empty($product_store_id)) {array_push($errors, 'product store location is required'); }
        if (empty($product_type)) {array_push($errors, 'Please select the type of product you are creating'); }

        $product_image  = $_FILES["product_image"]["name"];
        $product_available_quantity =$product_quantity;

        if ($product_image != '') {
            $stmt  = $conn->prepare("SELECT * FROM product_table WHERE product_id = :id");
            $stmt->execute(array(':id' => trim($_GET['id'])));

            $result = $stmt->fetchAll();

            foreach ($result as $key => $value) {
                // Check if file already exists
                if (file_exists($value['product_image'])) {
                    unlink($value['product_image']);
                }    
            }

            // upload logo
            $target_dir = "assets/product_image/";
            $target_file = $target_dir .'PI_'.time(). basename($_FILES["product_image"]["name"]);
            $uploadOk = 1;
            $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

            // Check if image file is a actual image or fake image
            $check = getimagesize($_FILES["product_image"]["tmp_name"]);
            if($check !== false) {$uploadOk = 1;} else
            { array_push($errors, 'product logo must be an images file'); $uploadOk = 0;}
            // Check file size
            if ($_FILES["product_image"]["size"] > 5000000) {
                array_push($errors, 'Sorry, product logo image is too large');
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
                move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file);
            }
            
            $stmt = $conn->prepare("SELECT * FROM product_table WHERE product_image = :id");
            $stmt->execute(array(':id' => trim($_GET['id'])));
            $stmt = $conn->prepare("UPDATE product_table SET product_image = :product_image WHERE product_id = :id");
            $stmt->execute(array(':product_image' => $target_file, ':id'=> trim($_GET['id'])));

            $stmt = $conn->prepare("UPDATE product_table 
            SET product_category_id         = :product_category_id,
                product_user_id             = :product_user_id,
                product_name                = :product_name,
                product_description         = :product_description,
                product_quantity            = :product_quantity,
                product_cost                = :product_cost,
                product_retail_price        = :product_retail_price,
                product_wholesale_price     = :product_wholesale_price,
                product_sold_out_quantity   = :product_sold_out_quantity,
                product_available_quantity  = :product_available_quantity,
                product_store_id            = :product_store_id,
                product_instock_status      = :product_instock_status,
                product_type                = :product_type,
                product_image               = :product_image
            WHERE product_id                = :id
            ");

            $stmt->execute(array(
                ':product_category_id'          => $product_category_id,
                ':product_user_id'              => $_SESSION['login_user_id'],
                ':product_name'                 => $product_name,
                ':product_description'          => $product_description,
                ':product_quantity'             => $product_quantity,
                ':product_cost'                 => $product_cost,
                ':product_retail_price'         => $product_retail_price,
                ':product_wholesale_price'      => $product_wholesale_price,
                ':product_sold_out_quantity'    => $product_sold_out_quantity,
                ':product_available_quantity'   => $product_available_quantity,
                ':product_store_id'             => $product_store_id,
                ':product_instock_status'       => $product_instock_status,
                ':product_type'                 => $product_type,
                ':product_image'                => $target_file,
                ':id'                           => trim($_GET['id'])
            ));

            if ($stmt) {
                $_SESSION['success'] = 'Product updated successfully';
                header('location: product.php');
                exit();
            }
        }
        
        if (count($errors) === 0 && $product_image === '') {
            
            $stmt = $conn->prepare("UPDATE product_table 
            SET product_category_id         = :product_category_id,
                product_user_id             = :product_user_id,
                product_name                = :product_name,
                product_description         = :product_description,
                product_quantity            = :product_quantity,
                product_cost                = :product_cost,
                product_retail_price        = :product_retail_price,
                product_wholesale_price     = :product_wholesale_price,
                product_sold_out_quantity   = :product_sold_out_quantity,
                product_available_quantity  = :product_available_quantity,
                product_store_id            = :product_store_id,
                product_instock_status      = :product_instock_status,
                product_type                = :product_type
            WHERE product_id                = :id
            ");

            $stmt->execute(array(
                ':product_category_id'          => $product_category_id,
                ':product_user_id'              => $_SESSION['login_user_id'],
                ':product_name'                 => $product_name,
                ':product_description'          => $product_description,
                ':product_quantity'             => $product_quantity,
                ':product_cost'                 => $product_cost,
                ':product_retail_price'         => $product_retail_price,
                ':product_wholesale_price'      => $product_wholesale_price,
                ':product_sold_out_quantity'    => $product_sold_out_quantity,
                ':product_available_quantity'   => $product_available_quantity,
                ':product_store_id'             => $product_store_id,
                ':product_instock_status'       => $product_instock_status,
                ':product_type'                 => $product_type,
                ':id'                           => trim($_GET['id'])
            ));

            if ($stmt) {
                $_SESSION['success'] = 'Product updated successfully';
                header('location: product.php');
                exit();
            }
        }

    }

    /**
     * 
     * CREATE A NEW PRODUCT
     */
    if (isset($_POST['create_product'])) {

        $product_name = trim($_POST['product_name']);
        $product_description = trim($_POST['product_description']);
        $product_category_id = trim($_POST['product_category_id']);
        $product_quantity = trim($_POST['product_quantity']);
        $product_cost = trim($_POST['product_cost']);
        $product_retail_price = trim($_POST['product_retail_price']);
        $product_wholesale_price = trim($_POST['product_wholesale_price']);
        $product_store_id = trim($_POST['product_store_id']);
        $product_type = trim($_POST['product_type']);
        $product_instock_status = 'Instock';

        if (empty($product_name)) {array_push($errors, 'Product name is required'); }
        if (empty($product_description)) {array_push($errors, 'Product description is required'); } elseif (strlen($product_description) > 100) {array_push($errors, 'Product description must not be more that 100 characters');}
        if (empty($product_category_id)) {array_push($errors, 'Product category required'); }
        if (empty($product_cost)) {array_push($errors, 'Cost of product is required'); }
        if (empty($product_retail_price)) {array_push($errors, 'Retail price of product is required'); }
        if (empty($product_wholesale_price)) {array_push($errors, 'Wholesale price product is required'); }
        if (empty($product_store_id)) {array_push($errors, 'product store location is required'); }
        if (empty($product_type)) {array_push($errors, 'Please select the type of product you are creating'); }

        
        $product_sold_out_quantity =0;
        $product_available_quantity =$product_quantity;
        
        if (count($errors) === 0) {


            // upload logo
            $target_dir = "assets/product_image/";
            $target_file = $target_dir .'PI_'.time(). basename($_FILES["product_image"]["name"]);
            $uploadOk = 1;
            $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

            // Check if image file is a actual image or fake image
            $check = getimagesize($_FILES["product_image"]["tmp_name"]);
            if($check !== false) {
                $uploadOk = 1;
            } else {
                array_push($errors, 'Store logo must be an images file');

                $uploadOk = 0;
            }
            // Check file size
            if ($_FILES["product_image"]["size"] > 5000000) {
                array_push($errors, 'Sorry, store logo image is too large');
                $uploadOk = 0;
            }
            // Allow certain file formats
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif" ) {
                array_push($errors, 'Sorry, only JPG, JPEG, PNG & GIF files are allowed');
                $uploadOk = 0;
            }

            
            
            $stmt = $conn->prepare("SELECT product_name FROM product_table WHERE product_name = :product_name LIMIT 1");
            $stmt->execute(array(
                ':product_name' => $product_name
            ));

            $result_name = $stmt->fetchAll();

            if ($result_name) {array_push($errors, 'This product name already exist in the database:'.$product_name);} else {
                
                // Check if $uploadOk is set to 0 by an error
                if ($uploadOk === 0) {
                    array_push($errors, 'Sorry, your file was not uploaded');
                // if everything is ok, try to upload file
                } elseif (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
                array_push($success, "The file ". basename( $_FILES["product_image"]["name"]). " has been uploaded");
                } else {
                    array_push($errors, 'Sorry, there was an error uploading your file');
                }  

                $stmt = $conn->prepare("INSERT INTO product_table (                  
                    product_category_id,
                    product_user_id,
                    product_name,
                    product_description,
                    product_quantity,
                    product_cost,
                    product_retail_price,
                    product_wholesale_price,
                    product_sold_out_quantity,
                    product_available_quantity,
                    product_store_id,
                    product_instock_status,
                    product_type,
                    product_image
                ) VALUES (
                    :product_category_id,
                    :product_user_id,
                    :product_name,
                    :product_description,
                    :product_quantity,
                    :product_cost,
                    :product_retail_price,
                    :product_wholesale_price,
                    :product_sold_out_quantity,
                    :product_available_quantity,
                    :product_store_id,
                    :product_instock_status,
                    :product_type,
                    :product_image
                )");

                $stmt->execute(array(
                    ':product_category_id'          =>  $product_category_id,
                    ':product_user_id'              =>  $_SESSION['login_user_id'],
                    ':product_name'                 =>  $product_name,
                    ':product_description'          =>  $product_description,
                    ':product_quantity'             =>  $product_quantity,
                    ':product_cost'                 =>  $product_cost,
                    ':product_retail_price'         =>  $product_retail_price,
                    ':product_wholesale_price'      =>  $product_wholesale_price,
                    ':product_sold_out_quantity'    =>  $product_sold_out_quantity,
                    ':product_available_quantity'   =>  $product_available_quantity,
                    ':product_store_id'             =>  $product_store_id,
                    ':product_instock_status'       =>  $product_instock_status,
                    ':product_type'                 =>  $product_type,
                    ':product_image'                =>  $target_file
                ));

                if ($stmt) {
                    $_SESSION['success'] = 'New product created successfully';
                    header('location: product.php');
                    exit();
                }
            }
                
            
        }
        
    }

    // CATEGORY
    $stmt = $conn->query("SELECT * FROM category_table");
    $stmt->execute();
    $category_result = $stmt->fetchAll();

    // STORE
    $stmt = $conn->query("SELECT * FROM store_table");
    $stmt->execute();
    $store_result = $stmt->fetchAll();

    include 'inc/header.php';
    define ("TITLE" , "Create Products");
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
                <a href="product.php" class="btn btn-sm btn-light pull-right my-2"><i class="fa fa-eye"></i> View Products </a>
            </div>
            
            <?php include 'inc/alert.php'; ?>
            <?php
            if (isset($result)) {
                foreach ($result as $key => $row) {
                    $product_name = $row['product_name'];
                    $product_description = $row['product_description'];
                    $product_category_id = $row['product_category_id'];
                    $product_quantity = $row['product_quantity'];
                    $product_cost = $row['product_cost'];
                    $product_retail_price = $row['product_retail_price'];
                    $product_wholesale_price = $row['product_wholesale_price'];
                    $product_store_id = $row['product_store_id'];
                    $product_type = $row['product_type'];
                    $product_instock_status = $row['product_instock_status'];
                }
            }
                
            ?>
            
                <form action="" method="post" class="form" enctype="multipart/form-data">
                <table class="table">
                <tr>
                    <td colspan="3">
                        <div  class="my-2 col-md-12">
                            <label for="product_name">Product name</label>
                            <input type="text" id="product_name" name="product_name" class="form-control form-1" autocomplete="off" placeholder="New product name" value="<?php if(isset($result)) {echo $product_name;} elseif (isset($product_name)) {echo $product_name;} ?>" />
                        </div>
                        <div  class="my-2 col-md-12">
                            <label for="product_description">Product description</label>
                            <textarea id="product_description" name="product_description" class="form-control form-1" autocomplete="off" placeholder="New product description" value=""><?php if(isset($result)) {echo $product_description;} elseif (isset($product_description)) {echo $product_description;} ?></textarea>
                        </div>
                    </td>
                    <td>
                    <div  class="my-2 col-md-12">
                            <label for="product_category_id">Product category</label>
                            <select name="product_category_id" id="product_category_id" class="form-control form-1">
                                <option value="" class="disabled" disabled>Select a product category</option>
                                <?php
                                    foreach ($category_result as $key => $value) {
                                        echo ' <option value="'.$value['category_id'].'">'.$value['category_name'].'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                        <div  class="my-2 col-md-12">
                            <label for="product_quantity">Quantity</label>
                            <input type="number" autocomplete="off" id="product_quantity" name="product_quantity" class="form-control form-1" placeholder="Product quantity" value="<?php if(isset($result)) {echo $product_quantity;} elseif (isset($product_quantity)) {echo $product_quantity;} ?>"
                            />
                        </div><br />
                <br />
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="my-2 col-md-12">
                            <label for="product_cost">Cost price</label>
                            <input type="number" id="product_cost" autocomplete="off" name="product_cost" class="form-control form-1" placeholder="Cost price (₦)" step=".01" value="<?php if(isset($result)) {echo $product_cost;} elseif (isset($product_cost)) {echo $product_cost;} ?>"
                            />
                        </div>
                    </td>

                    <td>
                        <div class="my-2 col-md-12">
                            <label for="product_retail_price">Retail price</label>
                            <input type="number" id="product_retail_price" name="product_retail_price" autocomplete="off" class="form-control form-1" placeholder="Retail price (₦)" step=".01" value="<?php if(isset($result)) {echo $product_retail_price;} elseif (isset($product_retail_price)) {echo $product_retail_price;} ?>"
                            />
                        </div>
                    </td>

                    <td>
                        <div class="my-2 col-md-12">
                            <label for="product_wholesale_price">Wholesale price</label>
                            <input type="number" id="product_wholesale_price" name="product_wholesale_price" autocomplete="off" class="form-control form-1" placeholder="Wholesale price (₦)" step=".01" value="<?php if(isset($result)) {echo $product_wholesale_price;} elseif (isset($product_wholesale_price)) {echo $product_wholesale_price;} ?>"
                            />
                        </div>
                    </td>
                    <td>
                        <div class="my-2 col-md-12">
                            <label for="product_store_id">Product store</label>
                            <select name="product_store_id" id="product_store_id" class="form-control form-1">
                            <?php
                                foreach ($store_result as $key => $value) {
                                    echo ' <option value="'.$value['store_id'].'">'.$value['store_name'].'</option>';
                                }
                            ?>
                            </select>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                    <div class="my-2 col-md-12">
                            <label for="product_type">Product type</label>
                            <select name="product_type" id="product_type" class="form-control form-1">
                                <option value="" class="disabled" disabled>Select a product</option>
                                <option value="PHY">Physical</option>
                                <option value="DIGI">Digital </option>
                            </select>
                        </div>
                    </td>
                    <td>
                    <td>
                    <!-- <div class="my-2 col-md-12">
                            <label for="product_instock_status">Product Status</label>
                            <select name="product_instock_status" id="product_instock_status" class="form-control form-1">
                                <option value="" class="disabled" disabled>Select a product</option>
                                <option value="In-stock">In-stock </option>
                                <option value="Out-of-stock">Out-of-stock </option>
                            </select>
                        </div> -->
                    </td>
                    <td>
                    <div class="my-2 col-md-12">
                            <label for="product_image">Product image</label>
                            <input type="file" id="product_image" name="product_image" class="form-control form-1" placeholder="Product quantity" value="<?php if(isset($result)) {echo $product_image;} elseif (isset($product_image)) {echo $product_image;} ?>"
                            />
                        </div>
                    </td>
                    <!-- <td></td> -->
                    <tr>
                        <td colspan="4" align="right"><div class="col-md-12">
                            <button type="submit" name="<?php if(isset($result)) {echo 'update_product';} else {echo 'create_product';} ?>" class="btn btn-primary my-2 float-right px-4 shadow"><?php if(isset($result)) {echo 'Update product';} else {echo 'Create product';} ?></button>
                        </td></div>
                    </tr>
                </tr>
            </form>
            </table>
            
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