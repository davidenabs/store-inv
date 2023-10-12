<?php
  // DB connect 'config/connect.php'
  include 'config/connect.php';
  include 'inc/auth/loginRedirector.php';
  // include 'inc/auth/adminRedirector.php';
  $errors = [];
  $success = [];

  $stmt = $conn->query("SELECT * FROM store_table ORDER BY store_id DESC");
  $stmt->execute();

  $result = $stmt->fetchAll();

  $total_rows = $stmt->rowCount();

    include 'inc/header.php';
    define ("TITLE" , "Store");
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
            <a href="create-store.php" class="btn btn-sm btn-light pull-right my-2"><i class="fa fa-plus"></i> Create New</a>
        </div>
        <div class="clearfix"></div>
        <?php include 'inc/alert.php'; ?>
        
        <div class="card py-3 board">
        <table class="table mt-4" id="data-table">
            <thead class="thead">
                    <th><input type="checkbox"></th>
                    <th>#</th>
                    <th>LOGO</th>
                    <th>NAME</th>
                    <th>EMAIL</th>
                    <th>PHONE</th>
                    <th>TYPE</th>
                    <th>EDIT</th>
                    <th>DELETE</th>
                </thead>
                <tbody>
                    <?php 
                        if ($total_rows > 0) {
                            foreach ($result as $key => $value) {
                                $key = ++$key;
                                if ($value['store_type'] == 1) {
                                      $store_type = '<div class="btn btn-sm btn-success">PRIMARY</div>';
                                    } else {
                                      $store_type = '<div class="btn btn-sm btn-primary">SECONDARY</div>';
                                    }
                                echo '
                                <tr>
                                    <td><input type="checkbox" class=""></td>
                                    <td>'.$key.'</td>
                                    <td><img src="'.$value['store_logo'].'" class="rounded-circle border z-depth-0" width="50px" height="50px"></td>
                                    <td>'.$value['store_name'].'</td>
                                    <td>'.$value['store_email'].'</td>
                                    <td>'.$value['store_phone'].'</td>
                                    <td>'.$store_type.'</td>
                                    <td>
                                        <div class="btn-primary rounded edit btn btn-sm small" id="'.$value['store_id'].'"><span class="fa fa-edit"></span></div>
                                    </td>
                                    <td>
                                        <a class="btn-danger rounded delete btn btn-sm small" id="'.$value['store_id'].'"><span class="fa fa-trash"></span></a>
                                    </td>
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
              window.location.href="create-store.php?delete=1&id="+id;
            } else {
              return false;
            }
          });

          $(document).on('click', '.edit', function () {
            var id =$(this).attr("id");
              window.location.href="create-store.php?edit=1&id="+id;
            
          });
      });
</script>
  <?php include 'inc/footer.php'; ?>
  </div>
</body>
</html>