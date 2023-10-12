<div class=" border-right" id="sidebar-wrapper" >
      <div class="list-group list-group-flush" style="overflow: scroll; height:100vh">
        <a href="dashboard.php" class="list-group-item list-group-item-action"><i class="fa fa-dashboard"></i> Dashboard</a>

        <a href="#" class="list-group-item list-group-item-action disabled"> <i class="fa fa-history"></i> Sales Record</a>
          <!-- <a href="create-sales.php" class="list-group-item list-group-item-action"><span style="margin-left: 10px;"><i class="fa fa-plus-circle" style="font-size: 11px;"></i> Create Sales Record</span> </a> -->
          <a href="sales.php" class="list-group-item list-group-item-action"><span style="margin-left: 10px;"><i class="fa fa-eye" style="font-size: 11px;"></i> View Sales Record</span> </a>

        <a href="#" class="list-group-item list-group-item-action disabled"><i class="fa fa-files-o"></i> Invoice</a>
          <a href="invoice-to.php" class="list-group-item list-group-item-action"><span style="margin-left: 10px;"><i class="fa fa-plus-circle" style="font-size: 11px;"></i> Create invoice</span> </a>
          <a href="invoice.php" class="list-group-item list-group-item-action"><span style="margin-left: 10px;"><i class="fa fa-eye" style="font-size: 11px;"></i> View all Invoice</span> </a>
        
        <a href="#" class="list-group-item list-group-item-action disabled"> <i class="fa fa-list-alt"></i> Store</a>
          <a href="create-store.php" class="list-group-item list-group-item-action"><span style="margin-left: 10px;"><i class="fa fa-plus-circle" style="font-size: 11px;"></i> Create Store</span> </a>
          <a href="store.php" class="list-group-item list-group-item-action"><span style="margin-left: 10px;"><i class="fa fa-eye" style="font-size: 11px;"></i> View all Stores</span> </a>
        
        <a href="#" class="list-group-item list-group-item-action disabled"> <i class="fa fa-product-hunt"></i> Products</a>
          <a href="create-product.php" class="list-group-item list-group-item-action"><span style="margin-left: 10px;"><i class="fa fa-plus-circle" style="font-size: 11px;"></i> Create Product</span> </a>
          <a href="product.php" class="list-group-item list-group-item-action"><span style="margin-left: 10px;"><i class="fa fa-eye" style="font-size: 11px;"></i> View all Products</span> </a>

        <a href="#" class="list-group-item list-group-item-action disabled"> <i class="fa fa-user-o"></i> Customers</a>
          <a href="add-customer.php" class="list-group-item list-group-item-action"><span style="margin-left: 10px;"><i class="fa fa-plus-circle" style="font-size: 11px;"></i> Add Customer</span> </a>
          <a href="customer-list.php" class="list-group-item list-group-item-action"><span style="margin-left: 10px;"><i class="fa fa-eye" style="font-size: 11px;"></i> View all Customers</span> </a>
        
        <a href="#" class="list-group-item list-group-item-action disabled"> <i class="fa fa-list-alt"></i> Categories</a>
          <a href="create-category.php" class="list-group-item list-group-item-action"><span style="margin-left: 10px;"><i class="fa fa-plus-circle" style="font-size: 11px;"></i> Create Category</span> </a>
          <a href="category.php" class="list-group-item list-group-item-action"><span style="margin-left: 10px;"><i class="fa fa-eye" style="font-size: 11px;"></i> View all Categories</span> </a>

     <?php if ($_SESSION['login_user_role'] == 'ADM') {?>
     <a href="#" class="list-group-item list-group-item-action disabled"> <i class="fa fa-users"></i> Users</a>
          <a href="create-user.php" class="list-group-item list-group-item-action"><span style="margin-left: 10px;"><i class="fa fa-plus-circle" style="font-size: 11px;"></i> Add User</span> </a>
          <a href="user.php" class="list-group-item list-group-item-action"><span style="margin-left: 10px;"><i class="fa fa-eye" style="font-size: 11px;"></i> View all Users</span> </a>

          <a href="settings.php" class="list-group-item list-group-item-action pb-5 mb-5"><i class="fa fa-gear"></i> Settings</a>
     <?php } ?>
      </div>
    </div>