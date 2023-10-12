<?php
  // DB connect 'config/connect.php'
  include 'config/connect.php';
  include 'inc/auth/loginRedirector.php';
  // include 'inc/auth/adminRedirector.php';
  $errors = [];
  $success = [];

  $stmt = $conn->query("SELECT * FROM product_table ORDER BY product_id DESC");
  $stmt->execute();

  $result = $stmt->fetchAll();

  $total_rows = $stmt->rowCount();

    include 'inc/header.php';
    define ("TITLE" , "Products");
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
       <div class="container-fluid">
        <div class="d-flex col-md- justify-content-between">
            <div class="head-1"><?php echo TITLE; ?></div>
            <a href="create-product.php" class="btn btn-sm btn-liight pull-right my-2"><i class="fa fa-plus"></i> Create New</a>
        </div>
        <div class="clearfix"></div>
        <?php include 'inc/alert.php'; ?>
        
        <div class="card py-3 board table-responsive">
        <table class="table mt-4 table-striped" id="data-table">
            <thead class="thead">
                    <th><input type="checkbox"></th>
                    <th>#</th>
                    <th>IMAGE</th>
                    <th>NAME</th>
                    <th>DESCRIPTION</th>
                    <th>QTY</th>
                    <th>COST</th>
                    <th>R-PRICE</th>
                    <th>W-PRICE</th>
                    <th>SOLD OUT</th>
                    <th>AVAILABLE</th>
                    <th>CATEGORY</th>
                    <th>STATUS</th>
                    <th>STORE</th>
                    <th>EDIT</th>
                    <th>DELETE</th>
                    <th></th>
                </thead>
                <tbody>
                    
                    <?php
                        if ($total_rows > 0) {
                            foreach ($result as $key => $value) {
                                $key = ++$key;
                                // GET CATEGORY NAME BY ID
                                $stmt = $conn->prepare("SELECT category_name FROM category_table WHERE category_id = :product_category_id LIMIT 1");
                                $stmt->execute(array(
                                  ':product_category_id' => $value['product_category_id']
                                ));
                                $cat_result = $stmt->fetchAll();
                                foreach ($cat_result as $v) {
                                  $category_name = $v['category_name'];
                                }

                                // GET STORE NAME BY ID
                                $stmt = $conn->prepare("SELECT store_name FROM store_table WHERE store_id = :product_store_id LIMIT 1");
                                $stmt->execute(array(
                                  ':product_store_id' => $value['product_store_id']
                                ));
                                $cat_result = $stmt->fetchAll();
                                foreach ($cat_result as $v) {
                                  $store_name = $v['store_name'];
                                }

                                $img = '';
                                if ($value['product_image']) {
                                    $img = '<img src="'.$value['product_image'].'" class="rounded-circle border z-depth-0" width="50px" height="50px">';
                                }

                                if ($value['product_available_quantity'] == 0) {
                                  $stock = 'Out-of-stock';
                                } else {
                                  $stock = 'Instock';
                                }
                                echo '
                                <tr>
                                    <td><input type="checkbox"></td>
                                    <td>'.$key.'</td>
                                    <td>'.$img.'</td>
                                    <td>'.$value['product_name'].'</td>
                                    <td>'.$value['product_description'].'</td>
                                    <td>'.$value['product_quantity'].'</td>
                                    <td>₦'.$value['product_cost'].'</td>
                                    <td>₦'.$value['product_retail_price'].'</td>
                                    <td>₦'.$value['product_wholesale_price'].'</td>
                                    <td>'.$value['product_sold_out_quantity'].'</td>
                                    <td>'.$value['product_available_quantity'].'</td>
                                    <td>'.$category_name.'</td>
                                    <td>'.$stock.'</td>
                                    <td>'.$store_name.'</td>
                                    <td>
                                        <a href="#" class="btn-success edit btn btn-sm" id="'.$value['product_id'].'"><i class="fa fa-edit"></i></a>
                                    </td><td>
                                        <a href="#" class="btn-danger delete btn btn-sm" id="'.$value['product_id'].'"><i class="fa fa-trash"></i></a>
                                    </td>
                                    <td></td>
                                </tr>
                                ';
                            }
                        }
                    ?>

                    
                </tbody>
        </table>
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

    $(document).ready(function () {
          var table = $('#data-table').DataTable({
            "order":[],
            "columnDefs":[
              {
                "targets":[4,5,6],
                "orderable":false,
              },
            ],
            "pageLength": 10
          });

          $(document).on('click', '.delete', function () {
            var id =$(this).attr("id");
            if (confirm("Are you sure you want to remove this store?")) {
              window.location.href="create-product.php?delete=1&id="+id;
            } else {
              return false;
            }
          });

          $(document).on('click', '.edit', function () {
            var id =$(this).attr("id");
              window.location.href="create-product.php?edit=1&id="+id;
            
          });
      });
</script>
  <?php include 'inc/footer.php'; ?>
  </div>
</body>
</html>