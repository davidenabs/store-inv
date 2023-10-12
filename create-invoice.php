<?php

    // DB connect 'config/connect.php'
    include 'config/connect.php';
    include 'inc/auth/loginRedirector.php';
    // include 'inc/auth/adminRedirector.php';
    $errors = [];
    $success = [];

    $stmt = $conn->query("SELECT * FROM product_table WHERE product_available_quantity != 0");
    $stmt->execute();

    $result = $stmt->fetchAll();

    $total_rows = $stmt->rowCount();

    if (isset( $_SESSION['customer_id'])) {
        $cus_id = $_SESSION['customer_id'];

        $stmt = $conn->prepare("SELECT * FROM customer_table WHERE customer_id = :cus_id LIMIT 1");
        $stmt->execute(array(
            ':cus_id' => $cus_id
        ));

        $cus_result = $stmt->fetchAll();

        foreach ($cus_result as $value) {
            $customer_name = $value['customer_name'];
            $customer_email = $value['customer_email'];
            $customer_phone = $value['customer_phone'];
            $customer_address = $value['customer_address'];
        }
    } else {
        unset($_SESSION['customer_id']);
    }
    
    
    

    if (isset($_POST['create_invoice'])) {
        
        // Check is the quanty product entered is still available
        for ($i=0; $i < $_POST['total_item']; $i++) {
            $stmt = $conn->prepare("SELECT * FROM product_table WHERE product_name = :product_name");
            $stmt->execute(array(
                ':product_name'             => trim($_POST['item_name'][$i])
            ));
            $r = $stmt->fetchAll();
            foreach ($r as $v) {
                $product_sold_out_quantity = $v['product_sold_out_quantity'];
                $product_available_quantity = $v['product_available_quantity'];
            }

            if (trim($_POST['order_item_quantity'][$i]) > $product_available_quantity) {
                array_push($errors, '<b>'.trim($_POST['item_name'][$i]).'</b> items are less then the quantity inputed (Available quantity is: '.number_format($product_available_quantity).')');
            break;
            }

            if ($product_available_quantity == 0) {
                array_push($errors, 'Insufficent item available for this product: '.$product_available_quantity.' item remaining');
            break;
            }

            if (count($errors) === 0) {
                $product_sold_out_quantity = $product_sold_out_quantity + trim($_POST['order_item_quantity'][$i]);
                $product_available_quantity = $product_available_quantity - trim($_POST['order_item_quantity'][$i]);

                $stmt = $conn->prepare("UPDATE product_table
                    SET product_sold_out_quantity   = :product_sold_out_quantity,
                        product_available_quantity  = :product_available_quantity
                    WHERE product_name = :product_name
                ");
                $stmt->execute(array(
                    ':product_sold_out_quantity'   => $product_sold_out_quantity,
                    ':product_available_quantity'  => $product_available_quantity,
                    ':product_name'                => trim($_POST['item_name'][$i])
                ));
            }
        }
        if (count($errors) === 0) {
        $stmt =$conn->prepare("INSERT INTO order_table (
        order_user_id,
        order_store_id,
        order_no,
        order_date,
        order_receiver_name,
        order_receiver_address,
        order_receiver_phone,
        order_receiver_email,
        order_total_amount
        )
        VALUES (
        :order_user_id,
        :order_store_id,
        :order_no,
        :order_date,
        :order_receiver_name,
        :order_receiver_address,
        :order_receiver_phone,
        :order_receiver_email,
        :order_total_amount
        )");

        $stmt->execute(array(
            ':order_user_id'              => $_SESSION['login_user_id'],
            ':order_store_id'             => $_SESSION['store_id'],
            ':order_no'                   => 0,
            ':order_date'                 => trim($_POST["order_date"]),
            ':order_receiver_name'        => trim($_POST["order_receiver_name"]),
            ':order_receiver_address'     => trim($_POST["order_receiver_address"]),':order_receiver_phone'       => trim($_POST["order_receiver_phone"]),
            ':order_receiver_email'       => trim($_POST["order_receiver_email"]),
            ':order_total_amount'         => 0
        ));

        $stmt = $conn->query("SELECT LAST_INSERT_ID()");
        $order_id = $stmt->fetchColumn();

        for ($i=0; $i < $_POST['total_item']; $i++) {

            $stmt = $conn->prepare("INSERT INTO order_item_table (
                order_id,
                item_name,
                order_item_quantity,
                order_item_price,
                order_item_actual_amount,
                order_item_final_amount
            )
            VALUES (
                :order_id,
                :item_name,
                :order_item_quantity,
                :order_item_price,
                :order_item_actual_amount,
                :order_item_final_amount
            )");

            $stmt->execute(array(
                ':order_id' => $order_id,
                ':item_name' => trim($_POST['item_name'][$i]),
                ':order_item_quantity' => trim($_POST['order_item_quantity'][$i]),
                ':order_item_price' => trim($_POST['order_item_price'][$i]),
                ':order_item_actual_amount' => trim($_POST['order_item_actual_amount'][$i]),
                ':order_item_final_amount' => trim($_POST['order_item_final_amount'][$i])
            ));
            
        }
        $stmt = $conn->prepare("SELECT SUM(order_item_final_amount) AS totalAmount FROM order_item_table WHERE order_id = :order_id");
        $stmt->execute(array(
            ':order_id'             => $order_id
        ));
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $order_total_amount = $result['totalAmount'];

        $order_no = str_pad($order_id, 4, "0", STR_PAD_LEFT);
        $stmt = $conn->prepare("UPDATE order_table 
            SET order_total_amount      = :order_total_amount, 
                order_no                = :order_no
            WHERE order_id              = :order_id
        ");

        $stmt->execute(array(
            ':order_total_amount'   => $order_total_amount,
            ':order_no'             => $order_no,
            ':order_id'             => $order_id
        ));

        if ($stmt) {
            $_SESSION['success'] = 'Invoice Generated and ready to print!';
            header('location: invoice.php');
            exit();
        } else {
            $_SESSION['error'] = 'Invoice Generation failed!';
            header('location: create-invoice.php');
            exit();
        }
      } 
    }

    include 'inc/header.php';
    define ("TITLE" , "Create Invoice")
?>
    <script>
        $(document).ready(function () {
            $("#order_date").datepicker({
                format: "yyyy-mm-dd",
                autoclose: true
            });
        });
    </script>
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
            <a href="invoice.php" class="btn btn-sm btn-light pull-right my-2"><i class="fa fa-eye"></i> View Invoice </a>
        </div>
        <div class="clearfix"></div>
        <?php include 'inc/alert.php'; ?>
        <div class="shadow mt-3 p-2 mb-5">
            
            <form id="invoice_form" method="post" >
                
                <!-- <div class="card board"></div> -->
                <div class="card board table-responsive p-2">
                    <table class="table table-bordered">
                        <tr>
                            <td colspan="2">
                                <div class="row d-flex justify-content-between">
                                    <div class="col-md-4">
                                
                                        Reciever (BILL TO)
                                        <input type="text" name="order_receiver_name" id="order_receiver_name" value="<?php if (isset( $cus_result)){echo $customer_name;} ?>" class="form-control input-s my-2 " placeholder="Enter receiver's name" />
                                        <Address></Address>
                                        <textarea name="order_receiver_address" id="order_receiver_address" value="" cols="10" class="form-control my-2"><?php if (isset($cus_result)){echo $customer_address;} ?></textarea>  
                                    </div>
                                    <div class="col-md-4" id="h">
                                        Phone no.
                                        <input type="text" name="order_receiver_phone" id="order_receiver_phone" value="<?php if (isset($cus_result)){echo $customer_phone;} ?>" class="form-control input-s my-2" placeholder="Enter receiver's phone" />
                                        Email
                                        <input type="text" name="order_receiver_email" id="order_receiver_email" cols="10" class="form-control my-2" class="form-control input-s my-2" placeholder="Enter receiver's email" value="<?php if (isset($cus_result)){echo $customer_email;} ?>" />
                                    </div>
                                    <div class="col-md-4">
                                        <span class="text-success"></span> Invoice number / Date Charge<br />
                                        <input type="text" name="order_no" id="order_no" class="form-control validate input-s my-2" placeholder="AUTO INCREMENT" disabled autocomplete="off" />

                                        <input type="text" name="order_date" id="order_date" class="form-control input-s my-2" placeholder="Select invoice date" autocomplete="off" />
                                    </div>
                                </div>
                                <table id="invoice_item_table" class="table table-bordered">
                                    <tr>
                                        <th>Srn NO.</th>
                                        <th>ITEM NAME</th>
                                        <th>QUANTITY</th>
                                        <th>PRICE</th>
                                        <th>ACTUAL AMT.</th>
                                        <th colspan="2">TOTAL</th>
                                        <!-- <th colspan="2"></th> -->
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <td><span id="sr_no">1</span></td>

                                        <td><input type="text" list="product_name" name="item_name[]" id="item_name1" class="form-control input-s" autocomplete="off" />
                                        <datalist id="product_name">
                                        <?php 
                                            if ($total_rows > 0) {
                                                foreach ($result as $value) {
                                                    echo '
                                                    <option value="'.$value['product_name'].'">('.$value['product_available_quantity'].') Remaining
                                                    ';
                                                }
                                            }
                                        ?>  
                                        </datalist>
                                        </td>

                                        <td><input type="text" name="order_item_quantity[]" id="order_item_quantity1" class="form-control input-sm order_item_quantity" /></td>

                                        <td><input type="text" name="order_item_price[]" id="order_item_price1" class="form-control input-sm order_item_price number_only" data-srno="1" /></td>

                                        <td><input type="text" name="order_item_actual_amount[]" id="order_item_actual_amount1" class="form-control input-sm order_item_actual_amount number_only" data-srno="1" readonly /></td>
                                        
                                        <td><input type="text" name="order_item_final_amount[]" id="order_item_final_amount1" class="form-control input-sm order_item_final_amount" data-srno="1" readonly /></td>

                                    </tr>
                                     
                                </table>
                                <div align="right">
                                        <button type="button" name="add_row" id="add_row" class="btn btn-dark text-right btn-sm">&plus;</button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td align="right">Total</td>
                            <td align="right"><span id="final_total_amt"></span></td>
                        </tr>
                        <tr>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <input type="hidden" name="total_item" id="total_item" value="1" />
                                <div style="text-align: right;">
                                <input type="submit" id="create_invoice" name="create_invoice" class="btn btn-primary" value="Create" /></div>
                            </td>
                        </tr>
                    </table>
                </div>
            </form>
            <?php //} ?>
        </div>
        
       </div>
    </div>
    <!-- /#page-content-wrapper -->

  </div>
  <!-- /#wrapper -->
      <?php include 'inc/footer.php'; ?>
  <script type="text/javascript">
  
        $(document).ready(function () {
            var table = $('#data-table').DataTable();
        });

      $(document).ready(function () {
          var final_total_amt = $('#final_total_amt').text();
          var count = 1;

          $(document).on('click', '#add_row', function() {
              count = count + 1;
              $('#total_item').val(count);
              var html_code = '';
              html_code += '<tr id="row_id_'+count+'">';
              html_code += '<td><span id="sr_no">'+count+'</span></td>';
              html_code += '<td><input type="text" list="product_name" name="item_name[]" id="item_name'+count+'" class="form-control input-s" autocomplete="off" /><datalist id="product_name"><?php if ($total_rows > 0) {foreach ($result as $value) {echo ' <option value="'.$value['product_name'].'">('.$value['product_available_quantity'].') Remaining';}} ?> </datalist></td>';

              html_code += ' <td><input type="text" name="order_item_quantity[]" id="order_item_quantity'+count+'" class="form-control input-sm order_item_quantity" data-srno="1" /></td>';

              html_code += ' <td><input type="text" name="order_item_price[]" id="order_item_price'+count+'" class="form-control input-sm order_item_price number_only" data-srno="'+count+'" /></td>';
              
              html_code += '<td><input type="text" name="order_item_actual_amount[]" id="order_item_actual_amount'+count+'" class="form-control input-sm order_item_actual_amount number_only" data-srno="'+count+'" readonly /></td>';

              html_code += '<td><input type="text" name="order_item_final_amount[]" id="order_item_final_amount'+count+'" class="form-control input-sm order_item_final_amount" data-srno="'+count+'" readonly /></td>';
              
              html_code += '<td><button type="button" class="btn btn-danger btn-sm remove_row" id="'+count+'">&times;</button></td>';
              $('#invoice_item_table').append(html_code);
          });

          $(document).on('click', '.remove_row', function() {
              
              var row_id = $(this).attr("id");
              var total_item_amount = $('#order_item_final_amount'+row_id).val();
              var final_amount = $('#final_total_amt').text();
              var result_amount = parseFloat(final_amount) - parseFloat(total_item_amount);
              $('#final_total_amt').text(result_amount);
              $('#row_id_'+row_id).remove();
              count = count - 1;
              $('#total_item').val(count);
          });

          function cal_final_total(count) {
              var final_item_total = 0;
              for (index=0; index<=count; index++) {
                  var quantity = 0;
                  var price = 0;
                  var actual_amount = 0;         
                  var item_total = 0;

                  quantity = $('#order_item_quantity'+index).val();

                  if (quantity > 0) {

                      price = $('#order_item_price'+index).val();
                    //   alert(price);

                      if (price > 0) {
                          actual_amount = parseFloat(quantity) * parseFloat(price);
                          $('#order_item_actual_amount'+index).val(actual_amount);

                          item_total = parseFloat(actual_amount);

                          final_item_total = parseFloat(final_item_total) + parseFloat(item_total);

                          $('#order_item_final_amount'+index).val(item_total);
                      }
                  }         
              }
              $('#final_total_amt').text(final_item_total);
          }
          $(document).on('blur', '.order_item_price', function(){
              cal_final_total(count);
          });
          
          $('#create_invoice').click(function() {
            
              if ($.trim($('#order_receiver_name').val()).length == 0) {
                  alert("Please enter reciever's name");
                  $('#order_no').focus();
                  return false;
              }

            //   if ($.trim($('#order_no').val()).length == 0) {
            //       alert("Please enter invoice number");
            //       $('#order_no').focus();
            //       return false;
            //   }

              if ($.trim($('#order_date').val()).length == 0) {
                  alert("Please select invoice date");
                  return false;
              }

              for (var no = 1; no <= count; no++) {
                if ($.trim($('#item_name'+no).val()).length == 0) {
                    alert("Please enter product name");
                    $('#item_name'+no).focus();
                    return false;
                }
                
                if ($.trim($('#order_item_quantity'+no).val()).length == 0) {
                    alert("Please enter item quanity");
                    $('#order_item_quantity'+no).focus();
                    return false;
                }

                if ($.trim($('#order_item_price'+no).val()).length == 0) {
                    alert("Please enter item price");
                    $('#order_item_price'+no).focus();
                    return false;
                }
              }

              $('#invoice_form').submit();
          });
     
 });
  </script>
</body>
</html>