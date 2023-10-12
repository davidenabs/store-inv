<?php
  // DB connect 'config/connect.php'
  include 'config/connect.php';
  $errors = [];
  $success = [];

  if (isset($_POST['user_login'])) {
      $user_password = trim($_POST['user_password']);
      $username = trim($_POST['username']);
      // $user_email = trim($_POST['user_email']);/\

      if (empty($username)) {array_push($errors, 'Username is required'); }
      // if (empty($user_email)) {array_push($errors, 'User\'s email address required'); }
      if (empty($user_password)) {array_push($errors, 'Password is required'); }

      if (count($errors)  === 0 ) {
        $stmt = $conn->prepare("SELECT * FROM user_table WHERE username = :username LIMIT 1");
        $stmt->execute(array(':username' => $username));
        $result = $stmt->fetchAll();
        if (count($result) > 0) {
            foreach ($result as $value) {
              $user_id        = $value['user_id'];
              $user_name      = $value['user_name'];
              $username       = $value['username'];
              $hashedPassword = $value['user_password'];
              $user_role      = $value['user_role'];
            }
            // verify password
            if(password_verify($user_password, $hashedPassword)) {
                $_SESSION['login_user_id']    = $user_id;
                $_SESSION['login_user_name']  = $user_name;
                $_SESSION['login_username']   = $username;
                $_SESSION['login_user_role']  = $user_role;

                $_SESSION['success'] = 'You are logged in!';
                header('location: dashboard.php');
                exit();
            } else {
              array_push($errors, 'Password do not match');
            }
          
        } else {
          array_push($errors, 'No such user with these details');
        }
      }
  }

  include 'inc/header.php';
  define ("TITLE" , "Invoice To");
?>
    <title><?php echo TITLE; ?></title>
</head>
<body>
  <nav class="navbar navbar-light bg-light shadow-sm">
      <ul class="container-fluid navbar-na nav ">
          <li class="navbar-brand nav-item" style="padding-left: 20px;">
          <?php
            $defualt_name ='Invoice Web APP';
            $stmt = $conn->prepare("SELECT * FROM store_table WHERE store_type = 1");
            $stmt->execute();
            $r_brand = $stmt->fetchAll();
            if (count($r_brand) > 0) {
              foreach ($r_brand as $value) {
                // $logo = $value['store_logo'];
                $name = $value['store_name'];
              }
              if(isset($name) && $name !== '') {
                echo $name;
              } else {
                echo $defualt_name;
              }
            }

          ?>
          </li>
          <li > <button class="btn btn-outline-warning shadow" style="border-radius: 20px;">&nbsp;</button> </li >
      </ul>
  </nav>
  <div class="wrapper">
      <section class="container mt-4">
          <div class="row">
              <div class="col-md-6">
                <img src="assets/img/in2.png"  width="500px" alt="" srcset="">
              </div>
              <div class="col-md-6 mt-5 pt-4">
              <?php include 'inc/alert.php'; ?>
                  <div class="head-1 my-3 ">Admin login</div>
                  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                      <div  class=" my-2">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" class="form-control form-1" placeholder="Username" value="<?php if (isset($username)) {echo $username;} ?>" autocomplete="off" />
                      </div>

                      <div  class=" my-2">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="user_password" class="form-control form-1"placeholder="********" autocomplete="off" />
                      </div>
                      

                      <button type="submit"  name="user_login" class="btn btn-success my-2 float-right px-4 shadow">Login</button>
                  </form>
            </div>
          </div>
          
      </section>
<script>
  if ( window.history.replaceState ) {
    window.history.replaceState( null, null, window.location.href );
  }
</script>
  <?php include 'inc/footer.php'; ?>
  </div>
</body>
</html>