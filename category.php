<?php
  // DB connect 'config/connect.php'
  include 'config/connect.php';
  $errors = [];
  $success = [];
  include 'inc/auth/loginRedirector.php';
  // include 'inc/auth/adminRedirector.php';

  $stmt = $conn->query("SELECT * FROM category_table ORDER BY category_id DESC");
  $stmt->execute();

  $result = $stmt->fetchAll();

  $total_rows = $stmt->rowCount();

    include 'inc/header.php';
    define ("TITLE" , "Categories");
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
            <a href="create-category.php" class="btn btn-sm btn-light pull-right my-2"><i class="fa fa-plus"></i> Create New</a>
        </div>
        <div class="clearfix"></div>
        <?php include 'inc/alert.php'; ?>
        
        <div class="card py-3 board">
        <table class="table mt-4  table-striped" id="data-table">
            <thead class="thead">
                    <th><input type="checkbox" class=""></th>
                    <th>#</th>
                    <th>NAME</th>
                    <th></th>
                    <th>EDIT</th>
                    <th>DELETE</th>
                    <th></th>
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
                              <td>'.$value['category_name'].'</td>
                              <td></td>
                              <td>
                                  <a class="btn-primary rounded edit btn btn-sm" id="'.$value['category_id'].'"><span class="fa fa-edit"></span></a>
                              </td>
                              <td>
                                  <a class="btn-danger rounded delete btn btn-sm" id="'.$value['category_id'].'"><span class="fa fa-trash"></span></a>
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
            if (confirm("Are you sure you want to remove this category?")) {
              window.location.href="create-category.php?delete=1&id="+id;
            } else {
              return false;
            }
          });

          $(document).on('click', '.edit', function () {
            var id =$(this).attr("id");
              window.location.href="create-category.php?edit=1&id="+id;
            
          });
      });
</script>
  <?php include 'inc/footer.php'; ?>
  </div>
</body>07041897662
</html>