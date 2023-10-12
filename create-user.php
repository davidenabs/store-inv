<?php 
    // DB connect 'config/connect.php'
    include 'config/connect.php';
    $errors = [];
    $success = [];
    include 'inc/auth/loginRedirector.php';
    include 'inc/auth/adminRedirector.php';

    if (isset($_GET['edit']) && isset($_GET['id'])) {
        if (!$_GET['id'] == '') {
            
            $stmt = $conn->prepare("SELECT * FROM user_table WHERE user_id = :id");
            $stmt->execute(array(
                ':id' => trim($_GET['id'])
            ));
            $result = $stmt->fetchAll();
            $exist =  $stmt->rowCount();

            foreach ($result as $key => $row) {
                $user_name = $row['user_name'];
                $username = $row['username'];
                $user_email = $row['user_email'];
                $user_phone = $row['user_phone'];
                $user_role = $row['user_role'];
            }
        }
    }

    /**
     * 
     * DELETE EXISTING CUSTOMER
     * 
     * not done yet
     */

    if (isset($_GET['delete']) && isset($_GET['id'])) {
        $stmt  = $conn->prepare("SELECT * FROM user_table WHERE user_id = :id");
        $stmt->execute(array(
            ':id' => trim($_GET['id'])
        ));

        $result = $stmt->fetchAll();

        foreach ($result as $key => $value) {
            // Check if file already exists
            if (file_exists($value['user_logo'])) {
                unlink($value['user_logo']);
            }
            
        }

        $stmt = $conn->prepare("DELETE FROM user_table WHERE user_id = :id");
        $stmt->execute(array(
            ':id' => trim($_GET['id'])
        ));
  
        // $stmt = $conn->prepare("DELETE FROM user_item_table WHERE user_id = :id");
        // $stmt->execute(array(
        //     ':id' => trim($_GET['id'])
        // ));
  
        if ($stmt) {
            $_SESSION['success'] = 'Custormer\'s deleted successfully';
            header('location: user.php');
            exit();
        }
    }

    /**TODO: DELECT AND UPDATE user_LOGO
     * 
     * EDIT AN EXISTING user
     */
    if (isset($_POST['update_user'])) {
        $user_name = trim($_POST['user_name']);
        $username = trim($_POST['username']);
        $user_email = trim($_POST['user_email']);
        $user_phone = trim($_POST['user_phone']);
        $user_role = trim($_POST['user_role']);
        $user_password = trim($_POST['user_password']);
        if (empty($user_name)) {array_push($errors, 'User\'s name is required'); }
        if (empty($username)) {array_push($errors, 'User\'s username is required'); }
        if (empty($user_email)) {array_push($errors, 'User\'s email address required'); } elseif ( filter_var( $user_email, FILTER_VALIDATE_EMAIL ) == false ){array_push($errors, 'Email address inputed is not valid');}
        if (empty($user_password)) {array_push($errors, 'User\'s password is required'); }
        
        if( strlen( $user_password ) < 4 ) {array_push($errors, 'Password must be at least 4 characters');}      
        if (empty($user_role)) {array_push($errors, 'Please choose user\'s role'); }
        $stmt = $conn->prepare("SELECT user_email, username FROM user_table WHERE user_email = :user_email AND username = :username AND user_id != :id  LIMIT 1");
        $stmt->execute(array(':user_email' => $user_email, ':username' => $username, ':id' => trim($_GET['id'])));
        $result = $stmt->fetchAll();
        if ($result) {array_push($errors, 'This email address / username already exist in the database');}
        
        if (count($errors) === 0) {
            // hash the passowrd
            $user_password = password_hash( $user_password, PASSWORD_DEFAULT );
            
            $stmt = $conn->prepare("UPDATE user_table 
            SET user_name       = :user_name,
                username        = :username,
                user_email      = :user_email,
                user_phone      = :user_phone,
                user_role       = :user_role,
                user_password   = :user_password
            WHERE user_id       = :id
            ");

            $stmt->execute(array(
                ':user_name'        => $user_name,
                ':username'         => $username,
                ':user_email'       => $user_email,
                ':user_phone'       => $user_phone,
                ':user_role'        => $user_role,
                ':user_password'    => $user_password,
                ':id'               => trim($_GET['id'])
            ));

            if ($stmt) {
                $_SESSION['success'] = 'User profile updated successfully';
                header('location: user.php');
                exit();
            }
        }

    }


    /**
     * 
     * CREATE A NEW user
     */
    if (isset($_POST['create_user'])) {
        $user_name = trim($_POST['user_name']);
        $username = trim($_POST['username']);
        $user_email = trim($_POST['user_email']);
        $user_phone = trim($_POST['user_phone']);
        $user_role = trim($_POST['user_role']);
        $user_password = trim($_POST['user_password']);
        if (empty($user_name)) {array_push($errors, 'User\'s name is required'); }
        if (empty($username)) {array_push($errors, 'User\'s username is required'); }
        if (empty($user_email)) {array_push($errors, 'User\'s email address required'); } elseif ( filter_var( $user_email, FILTER_VALIDATE_EMAIL ) == false ){array_push($errors, 'Email address inputed is not valid');}
        if (empty($user_password)) {array_push($errors, 'User\'s password is required'); }
        
        if( strlen( $user_password ) < 4 ) {array_push($errors, 'Password must be at least 4 characters');} 
        if (empty($user_role)) {array_push($errors, 'Please choose user\'s role'); }
        $stmt = $conn->prepare("SELECT user_email, username FROM user_table WHERE user_email = :user_email AND username = :username LIMIT 1");
        $stmt->execute(array(':user_email' => $user_email, ':username' => $username));
        $result = $stmt->fetchAll();
        if ($result) {array_push($errors, 'This email address / username already exist in the database');}

        if (count($errors) === 0) {
            // hash the passowrd
            $user_password = password_hash( $user_password, PASSWORD_DEFAULT );
        
            $stmt = $conn->prepare("INSERT INTO user_table (
                user_name,
                username,
                user_email,
                user_phone,
                user_role,
                user_password 
            ) VALUES (
                :user_name,
                :username,
                :user_email,
                :user_phone,
                :user_role,
                :user_password
            )");

            $stmt = $stmt->execute(array(
                ':user_name'        => $user_name,
                ':username'         => $username,
                ':user_email'       => $user_email,
                ':user_phone'       => $user_phone,
                ':user_role'        => $user_role,
                ':user_password'    => $user_password,
            ));

            if ($stmt) {
                $_SESSION['success'] = 'New user added successfully';
                header('location: user.php');
                exit();
            }
            
                
            
        }
        
    }


    include 'inc/header.php';
    define ("TITLE" , "Add User");
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
                <div class="head-1">user</div>
                <a href="user.php" class="btn btn-sm btn-light pull-right my-2"><i class="fa fa-eye"></i> View users </a>
            </div>
            <?php include 'inc/alert.php'; ?>
            <form action="" method="post" class="form" enctype="multipart/form-data">
                <div class="my-2 col-md-12">
                    <label for="user_name">Full Name</label>                    <input type="text" id="user_name" name="user_name" class="form-control form-1" placeholder="New user name" value="<?php if(isset($result)) {echo $user_name;} elseif (isset($user_name)) {echo $user_name;} ?>" />
                </div>
                <div class="my-2 col-md-12">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control form-1" placeholder="New user name" value="<?php if(isset($result)) {echo $username;} elseif (isset($username)) {echo $username;} ?>" />
                </div>
                <div  class="my-2 col-md-12">
                    <label for="user_email">Email Address</label>
                    <input type="text" id="user_email" name="user_email" class="form-control form-1" placeholder="New user email addess" value="<?php if(isset($result)) {echo $user_email;} elseif (isset($user_email)) {echo $user_email;} ?>" />
                </div>
                <div  class="my-2 col-md-12">
                    <label for="user_phone">User Phone No.</label>
                    <input type="text" id="user_phone" name="user_phone" class="form-control form-1" placeholder="New user phone contact" value="<?php if(isset($result)) {echo $user_phone;} elseif (isset($user_phone)) {echo $user_phone;} ?>" />
                </div>
                <?php if(isset($result)) {?>
                <div  class="my-2 col-md-12">
                    <label for="user_role">Role</label>
                    <input type="text" id="user_phone" name="user_phone" class="form-control form-1" disabled value="<?php echo $user_role; ?>" />
                </div>
                <?php } ?>
                <div  class="my-2 col-md-12">
                    <label for="user_role">Role</label>
                    <select name="user_role" class="form-control form-1" id="user_role" value="">
                        <option>Change role</option>
                        <option value="USR">USER</option>
                        <option value="ADM">ADMIN</option>
                    </select>
                </div>
                <div  class="my-2 col-md-12">
                    <label for="user_password">Password</label>
                    <input type="password" id="user_password" name="user_password" class="form-control form-1" placeholder="**********" />
                </div>

                <div class="col-md-12" align="right">
                    <button type="submit" name="<?php if(isset($result)) {echo 'update_user';} else {echo 'create_user';} ?>" class="btn btn-primary my-2 float-right px-4 shadow"><?php if(isset($result)) {echo 'Update user';} else {echo 'Create user';} ?></button>
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