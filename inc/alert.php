<?php if (count($errors) > 0) : ?>

<?php foreach ($errors as $error) : ?>
<div class="alert alert-danger shake fade in show" role="alert" style="transition: 0.5s;">
<div class=""><?php echo $error;  ?></div>
</div>
<?php endforeach ?>
<?php endif ?>

<?php if (count($success) > 0) : ?>
<?php foreach ($success as $suc) : ?>
<div class="alert alert-success shake fade in show" role="alert" style="transition: 0.5s;">
<div><?php echo $suc;  ?></div>
</div>
<?php endforeach ?>
<?php endif ?>

<?php if (isset($_SESSION['success'])) 
{ 
?>
<div class="alert alert-success shake fade in show" role="alert" style="transition: 0.5s;">
<div><?php echo $_SESSION['success'];  ?></div>
</div>
<?php 
}
unset($_SESSION['success']);
?>

<?php if (isset($_SESSION['error'])) 
{ 
?>
<div class="alert alert-danger shake fade in show" role="alert" style="transition: 0.5s;">
<div><?php echo $_SESSION['error'];  ?></div>
</div>
<?php 
}
unset($_SESSION['error']);
?>
