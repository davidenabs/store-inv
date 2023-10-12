<?php
  // DB connect 'config/connect.php'
  include 'config/connect.php';
  include 'inc/auth/loginRedirector.php';
  // include 'inc/auth/adminRedirector.php';
  $errors = [];
  $success = [];

  $stmt = $conn->query("SELECT * FROM order_item_table ORDER BY order_item_id DESC");
  $stmt->execute();

  $result = $stmt->fetchAll();

  $total_rows = $stmt->rowCount();

    include 'inc/header.php';
    define ("TITLE" , "Sales Record");
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
                    <th>ITEM</th>
                    <th>DESCRIPTION</th>
                    <th>QTY</th>
                    <th>SOLD</th>
                    <th>TOTAL</th>
                    <th>AVAILABLE ITEM</th>
                    <th>EDIT</th>
                    <th>DELETE</th>
                </thead>
                <tbody>
                    
                    <?php
                        if ($total_rows > 0) {
                            foreach ($result as $key => $value) {
                                $key = ++$key;
                                // GET PRODUCT DESC NAME BY NAME
                                $stmt = $conn->prepare("SELECT * FROM product_table WHERE product_name = :product_name");
                                $stmt->execute(array(
                                  ':product_name' => $value['item_name']
                                ));
                                $p_result = $stmt->fetchAll();
                                if (count($p_result) > 0) {
                                    $item_description ='';
                                    foreach ($p_result as $val) {	
                                        $item_description  = $val['product_description'];
                                        $product_available_quantity  = $val['product_available_quantity'];
                                    }
                                }
                                echo '
                                <tr>
                                    <td><input type="checkbox"></td>
                                    <td>'.$key.'</td>
                                    <td>'.$value['item_name'].'</td>
                                    <td>'.$item_description.'</td>
                                    <td>'.$value['order_item_quantity'].'</td>
                                    <td>₦'.$value['order_item_price'].'</td>
                                    <td>₦'.$value['order_item_final_amount'].'</td>
                                    <td>'.$product_available_quantity.'</td>
                                    <td><a href="edit_invoice.php?edit=1&id='.$value["order_id"].'" class="btn btn-success btn-sm"><span class="fa fa-edit"></span></a></td>
                                    <td><a href="#" id="'.$value["order_id"].'" class="delete btn btn-danger btn-sm"><span class="fa fa-trash"></span></a></td>
                    
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