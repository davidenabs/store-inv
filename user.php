<?php
  // DB connect 'config/connect.php'
  include 'config/connect.php';
  include 'inc/auth/loginRedirector.php';
  include 'inc/auth/adminRedirector.php';
  $errors = [];
  $success = [];

  $stmt = $conn->query("SELECT * FROM user_table ORDER BY user_id DESC");
  $stmt->execute();

  $result = $stmt->fetchAll();

  $total_rows = $stmt->rowCount();

    include 'inc/header.php';
    define ("TITLE" , "User");
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
            <a href="create-user.php" class="btn btn-sm btn-liight pull-right my-2"><i class="fa fa-plus"></i> Add New</a>
        </div>
        <div class="clearfix"></div>
        <?php include 'inc/alert.php'; ?>
        
        <div class="card py-3 board table-responsive">
        <table class="table mt-4 table-striped" id="data-table">
            <thead class="thead">
                    <th><input type="checkbox"></th>
                    <th>ID</th>
                    <th>NAME</th>
                    <th>EMAIL</th>
                    <th>PHONE</th>
                    <th>ROLE</th>
                    <th>EDIT</th>
                    <th>DELETE</th>
                </thead>
                <tbody>
                    
                    <?php
                        if ($total_rows > 0) {
                            foreach ($result as $key => $value) {
                                $key = ++$key;
                                echo '
                                <tr>
                                    <td><input type="checkbox"></td>
                                    <td>'.$key.'</td>
                                    <td>'.$value['user_name'].'</td>
                                    <td>'.$value['user_email'].'</td>
                                    <td>'.$value['user_phone'].'</td>
                                    <td>'.$value['user_role'].'</td>
                                    <td>
                                        <a href="#" class="btn-success edit btn btn-sm" id="'.$value['user_id'].'"><i class="fa fa-edit"></i></a>
                                    </td>
                                    <td>
                                        <a href="#" class="btn-danger delete btn btn-sm" id="'.$value['user_id'].'"><i class="fa fa-trash"></i></a>
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
              window.location.href="create-user.php?delete=1&id="+id;
            } else {
              return false;
            }
          });

          $(document).on('click', '.edit', function () {
            var id =$(this).attr("id");
              window.location.href="create-user.php?edit=1&id="+id;
            
          });
      });
</script>
  <?php include 'inc/footer.php'; ?>
  </div>
</body>
</html>