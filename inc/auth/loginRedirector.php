<?php
	if (!isset($_SESSION['login_username'])) {
        $_SESSION['error'] = 'You are not logged in!';
		header('location: index.php');
		exit();
	  } else {
		  $expiring_date = '5/1/2021';
		  $now = date('d/m/Y');
		  if ($now > $expiring_date) {
			echo "<div class='text-center text-danger small bg-dark'>This software expires ".$now."</div>";
			session_unset();
			$_SESSION['error'] = 'The demo version of this software has expired!';
			header('location: index.php');
			
			exit();
		  }
		//   echo "<div class='text-center text-danger small bg-dark'>This software expires ".$now."</div>";
	  }	  
?>