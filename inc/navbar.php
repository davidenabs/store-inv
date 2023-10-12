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
          <li class="nav-item">
            <a class="nav-link text-dark" href="#"> 
              <?php if (isset($_SESSION['login_user_name'])) {echo ' <i class="fa fa-user-circle"></i> Hi, '.$_SESSION['login_user_name'];}?>
              (<?php if (isset($_SESSION['login_user_role'])) {echo $_SESSION['login_user_role'];}?>)
            </a>
          </li>
          <li class="nav-item text-danger">
            <a class="nav-link text-dark" href="logout.php"> 
              <i class="fa fa-sign-out"></i> Logout
            </a>
          </li>
      </ul>
  </nav>
  <div class="tab">
      <!-- Hello word -->
  </div>