<?php

  // DB connect 'config/connect.php'
  include 'config/connect.php';
  include 'inc/auth/loginRedirector.php';
  // include 'inc/auth/adminRedirector.php';
  $errors = [];
  $success = [];

  $stmt = $conn->query("SELECT * FROM order_table ORDER BY order_id DESC");
  $stmt->execute();

  $result = $stmt->fetchAll();

  $total_rows = $stmt->rowCount();


  if (isset($_GET['delete']) && isset($_GET['id'])) {
      $stmt = $conn->prepare("DELETE FROM order_table WHERE order_id = :id");
      $stmt->execute(array(
          ':id' =>$_GET['id']
      ));

      $stmt = $conn->prepare("DELETE FROM order_item_table WHERE order_id = :id");
      $stmt->execute(array(
          ':id' =>$_GET['id']
      ));

      header("Location:invoice.php");
  }


  include 'inc/header.php';
  define ("TITLE" , "Invoice List");
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
            <a href="invoice-to.php" class="btn btn-sm btn-light pull-right my-2"><i class="fa fa-plus"></i> Create Invoice </a>
        </div>
        <div class="clearfix"></div>
        <?php include 'inc/alert.php'; ?>
        <div class="card py-3 board">
        <table class="table mt-4  table-striped" id="data-table">
            <thead class="thead">
                    <th><input type="checkbox" class=""></th>
                    <th>INVOICE NO.</th>
                    <th>INVOICE DATE</th>
                    <th>RECEIVER NAME</th>
                    <th>INVOICE TOTAL</th>
                    <th>CREATED BY</th>
                    <th>PRINT</th>
                    <th>EDIT</th>
                    <th>DELETE</th>
                </thead>
                <tbody>
                    <?php
                      if ($total_rows > 0) {
                        foreach ($result as $key => $row) {
                          $stmt =$conn->prepare("SELECT user_name FROM user_table WHERE user_id = :user_id");
                          $stmt->execute(array(':user_id' => $row['order_user_id']));
                          $u_r = $stmt->fetchAll();
                          
                          foreach ($u_r as $r) {
                            $user_name = $r['user_name'];
                          }

                          echo '
                            <tr>
                              <td></td>
                              <td>'.$row["order_no"].'</td>
                              <td>'.$row["order_date"].'</td>
                              <td>'.$row["order_receiver_name"].'</td>
                              <td>'.$row["order_total_amount"].'</td>
                              <td>'.$user_name.'</td>
                              <td><a href="print_invoice.php?pdf=1&id='.$row["order_id"].'" class="btn btn-sm btn-primary"><i class="fa fa-print"></i></a></td>
                              <td><a href="edit_invoice.php?edit=1&id='.$row["order_id"].'" class="btn btn-success btn-sm"><span class="fa fa-edit"></span></a></td>
                              <td><a href="#" id="'.$row["order_id"].'" class="delete btn btn-danger btn-sm"><span class="fa fa-trash"></span></a></td>
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
            if (confirm("Are you sure you want to remove this?")) {
              window.location.href="invoice.php?delete=1&id="+id;
            } else {
              return false;
            }
          });
      });
  </script>
  <?php include 'inc/footer.php'; ?>
  </div>
</body>
</html>