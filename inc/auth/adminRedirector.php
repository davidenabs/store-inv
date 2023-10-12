<?php
	if ($_SESSION['login_user_role'] !== 'ADM') {
        $_SESSION['error'] = 'You are not an admin!';
        header('location: dashboard.php');
        exit();
      }	  
?>