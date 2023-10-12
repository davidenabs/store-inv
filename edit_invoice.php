<?php

    // DB connect 'config/connect.php'
    include 'config/connect.php';
    include 'inc/auth/loginRedirector.php';
    // include 'inc/auth/adminRedirector.php';
    $errors = [];
    $success = [];

    $stmt = $conn->query("SELECT * FROM product_table");
    $stmt->execute();

    $pro_result = $stmt->fetchAll();

    $pro_rows = $stmt->rowCount();

    if (isset($_GET['edit']) && isset($_GET['id'])) {
        $stmt = $conn->prepare("SELECT * FROM order_table WHERE order_id = :order_id LIMIT 1");

        $stmt->execute(array(
            ':order_id' => $_GET['id']
        ));

        $result = $stmt->fetchAll();

    }

    if (isset($_POST['update_invoice'])) {
        $order_id = trim($_POST['order_id']);

        // Check is the quanty product entered is still available
        // for ($i=0; $i < $_POST['total_item']; $i++) {
        //     $stmt = $conn->prepare("SELECT * FROM product_table WHERE product_name = :product_name");
        //     $stmt->execute(array(
        //         ':product_name'             => trim($_POST['item_name'][$i])
        //     ));
        //     $r = $stmt->fetchAll();
        //     foreach ($r as $v) {
        //         $product_sold_out_quantity = $v['product_sold_out_quantity'];
        //         $product_available_quantity = $v['product_available_quantity'];
        //     }

        //     if (trim($_POST['order_item_quantity'][$i]) > $product_available_quantity) {
        //         array_push($errors, '<b>'.trim($_POST['item_name'][$i]).'</b> items are less then the quantity inputed (Available quantity is: '.number_format($product_available_quantity).')');
        //     break;
        //     }

        //     if ($product_available_quantity == 0) {
        //         array_push($errors, 'Insufficent item available for this product: '.$product_available_quantity.' item remaining');
        //     break;
        //     }

        //     if (count($errors) === 0) {
        //         $product_sold_out_quantity = $product_sold_out_quantity + trim($_POST['order_item_quantity'][$i]);
        //         $product_available_quantity = $product_available_quantity - trim($_POST['order_item_quantity'][$i]);

        //         $stmt = $conn->prepare("UPDATE product_table
        //             SET product_sold_out_quantity   = :product_sold_out_quantity,
        //                 product_available_quantity  = :product_available_quantity
        //             WHERE product_name = :product_name
        //         ");
        //         $stmt->execute(array(
        //             ':product_sold_out_quantity'   => $product_sold_out_quantity,
        //             ':product_available_quantity'  => $product_available_quantity,
        //             ':product_name'                => trim($_POST['item_name'][$i])
        //         ));
        //     }
        // }
        if (count($errors) === 0) {

        $stmt =$conn->prepare("DELETE FROM order_item_table WHERE order_id = :order_id");
        $stmt->execute(array(
            ':order_id' => $order_id
        ));

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

        $stmt = $conn->prepare("UPDATE order_table 
            SET order_no                = :order_no,
                order_date              = :order_date,
                order_receiver_name     = :order_receiver_name,
                order_receiver_address  = :order_receiver_address,
                order_receiver_phone    = :order_receiver_phone,
                order_receiver_email    = :order_receiver_email,
                order_total_amount      = :order_total_amount
            WHERE order_id = :order_id
        ");

        $stmt->execute(array(
            ':order_no'                 => trim($_POST["order_no"]),
            ':order_date'               => trim($_POST["order_date"]),
            ':order_receiver_name'      => trim($_POST["order_receiver_name"]),
            ':order_receiver_address'   => trim($_POST["order_receiver_address"]),               ':order_receiver_phone'     => trim($_POST["order_receiver_phone"]),
            ':order_receiver_email'     => trim($_POST["order_receiver_email"]),
            ':order_total_amount'       => $order_total_amount,
            ':order_id'                 => $order_id
        ));

        if ($stmt) {
            $_SESSION['success'] = 'Invoice updated successfully!';
            header('location: invoice.php');
            exit();
        } else {
            $_SESSION['error'] = 'Invoice failed to update!';
            header('location: invoice.php');
            exit();
        }
      } 
    }    

    include 'inc/header.php';
    define ("TITLE" , "Edit Invoice")
?>
    <script>
        $(document).ready(function () {
            $("#order_date").datepicker({
                format: "yyyy-mm-dd",
                autoclose: true
            });


            var table = $('#data-table').DataTable();
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
            <a href="invoice.php" class="btn btn-sm btn-light pull-right my-2"><i class="fa fa-plus"></i> View Invoice </a>
        </div>
        <div class="clearfix"></div>
                        
        <div class="shadow mt-3 p-2 mb-5">
            <?php foreach ($result as $key => $row) {?>
            <script>
                $(document).ready(function(){
                    $('#order_no').val("<?php echo $row["order_no"]; ?>");
                    $('#order_date').val("<?php echo $row["order_date"]; ?>");
                    $('#order_receiver_name').val("<?php echo $row["order_receiver_name"]; ?>");
                    $('#order_receiver_address').val("<?php echo $row["order_receiver_address"]; ?>");
                    $('#order_receiver_phone').val("<?php echo $row["order_receiver_phone"]; ?>");
                    $('#order_receiver_email').val("<?php echo $row["order_receiver_email"]; ?>");

                });
            </script>
            <form id="invoice_form" method="post" >
                <!-- <div class="card board"></div> -->
                <div class="card board table-responsive p-2">
                   
                    <table class="table table-bordered" id="">
                        <tr>
                            <td colspan="2">
                                <div class="row d-flex justify-content-between">
                                    <div class="col-md-4">
                                        Reciever (BILL TO)
                                        <input type="text" name="order_receiver_name" id="order_receiver_name" class="form-control input-s my-1" placeholder="Enter receiver's name" />
                                        Address
                                        <textarea name="order_receiver_address" id="order_receiver_address" cols="10" class="form-control my-1"></textarea>  
                                    </div>
                                    <div class="col-md-4">
                                        Phone no.
                                        <input type="text" name="order_receiver_phone" id="order_receiver_phone"  class="form-control input-s my-1" placeholder="Enter receiver's phone" />
                                        Email
                                        <input type="text" name="order_receiver_email" id="order_receiver_email" class="form-control my-1">
                                        
                                    </div>
                                    <div class="col-md-4">
                                        Reverse Charge<br />
                                        <input type="text" name="order_no" id="order_no" class="form-control input-s my-1" placeholder="Enter invoice number" />

                                        <input type="text" name="order_date" id="order_date" class="form-control input-s my-1" placeholder="Select invoice date" />
                                    </div>
                                </div>
                                <table id="invoice_item_table" class="table table-bordered mt-4">
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
                                    <?php
                                        $stmt = $conn->prepare("SELECT * FROM order_item_table WHERE order_id = :order_id");

                                        $stmt->execute(array(
                                            ':order_id' => $_GET['id']
                                        ));
                                        $item_result = $stmt->fetchAll();
                                        $i = 0;

                                        foreach ($item_result as $key => $sub_row) {
                                        $i = $i + 1;
                                    ?>
                                    <tr>
                                        <td><span id="sr_no"><?php echo $i; ?></span></td>

                                        <td><input type="text" list="product_name" name="item_name[]" id="item_name1<?php echo $i; ?>" class="form-control input-s" autocomplete="off" value="<?php echo $sub_row['item_name']; ?>" />
                                        <datalist id="product_name">
                                        <?php 
                                            if ($pro_rows > 0) {
                                                foreach ($pro_result as $v) {
                                                    echo '
                                                    <option value="'.$v['product_name'].'">
                                                    ';
                                                }
                                            }
                                        ?>  
                                        </datalist>
                                        </td>

                                        <td><input type="text" name="order_item_quantity[]" id="order_item_quantity<?php echo $i; ?>" class="form-control input-sm order_item_quantity" value="<?php echo $sub_row['order_item_quantity']; ?>" /></td>

                                        <td><input type="text" name="order_item_price[]" id="order_item_price<?php echo $i; ?>" class="form-control input-sm order_item_price number_only" data-srno="<?php echo $i; ?>" value="<?php echo $sub_row['order_item_price']; ?>" /></td>

                                        <td><input type="text" name="order_item_actual_amount[]" id="order_item_actual_amount<?php echo $i; ?>" class="form-control input-sm order_item_actual_amount number_only" data-srno="<?php echo $i; ?>" value="<?php echo $sub_row['order_item_actual_amount']; ?>" readonly /></td>
                                        
                                        <td><input type="text" name="order_item_final_amount[]" id="order_item_final_amount<?php echo $i; ?>" class="form-control input-sm order_item_final_amount" data-srno="<?php echo $i; ?>" val<?php echo $sub_row['order_item_final_amount']; ?> readonly /></td>

                                    </tr>
                                    <?php } ?>
                                     
                                </table>
                                <div align="right">
                                    <button type="button" name="add_row" id="add_row" class="btn btn-dark text-right btn-sm">&plus;</button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td align="right">Total</td>
                            <td align="right"><span id="final_total_amt"><b><?php if (isset($row['order_item_final_amount'])) { echo $row['order_item_final_amount']; } ?></b></span></td>
                        </tr>
                        <tr>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <input type="hidden" name="total_item" id="total_item" value="<?php echo $i; ?>" />
                                <div style="text-align: right;">
                                <input type="hidden" name="order_id" id="order_id" value="<?php echo $row['order_id']; ?>" />
                                <div style="text-align: right;">
                                <input type="submit" id="create_invoice" name="update_invoice" class="btn btn-primary shadow" value="Update Invoice" /></div>
                            </td>
                        </tr>
                    </table>
                </div>
            </form>
            <?php } ?>
        </div>
        
       </div>
    </div>
    <!-- /#page-content-wrapper -->

  </div>
  <!-- /#wrapper -->
      <?php include 'inc/footer.php'; ?>
  <script type="text/javascript">;

      $(document).ready(function () {
          var final_total_amt = $('#final_total_amt').text();
          var count = <?php echo $i;?>;

          $(document).on('click', '#add_row', function() {
              count = count + 1;
              $('#total_item').val(count);
              var html_code = '';
              html_code += '<tr id="row_id_'+count+'">';
              html_code += '<td><span id="sr_no">'+count+'</span></td>';
              html_code += '<td><input type="text" list="product_name" name="item_name[]" id="item_name'+count+'" class="form-control input-s" autocomplete="off" /><datalist id="product_name"><?php if ($pro_rows > 0) {foreach ($pro_result as $v) {echo ' <option value="'.$v['product_name'].'">';}} ?> </datalist></td>';
              
              html_code += ' <td><input type="text" name="order_item_quantity[]" id="order_item_quantity'+count+'" class="form-control input-sm order_item_quantity" data-srno="1" /></td>';
              
              html_code += ' <td><input type="text" name="order_item_price[]" id="order_item_price'+count+'" class="form-control input-sm order_item_price number_only" data-srno="'+count+'" /></td>';
              
              html_code += '<td><input type="text" name="order_item_actual_amount[]" id="order_item_actual_amount'+count+'" class="form-control input-sm order_item_actual_amount number_only" data-srno="'+count+'" readonly /></td>';
              html_code += '<td><input type="text" name="order_item_final_amount[]" id="order_item_final_amount'+count+'" class="form-control input-sm order_item_final_amount" data-srno="'+count+'" readonly /></td>';
              
              html_code += '<td><button type="button" class="btn btn-danger btn-sm remove_row" id="'+count+'">&times;</button>';
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

            if ($.trim($('#order_no').val()).length == 0) {
                alert("Please enter invoice number");
                $('#order_no').focus();
                return false;
            }

            if ($.trim($('#order_date').val()).length == 0) {
                alert("Please select invoice date");
                return false;
            }

            // for (var no = 1; no <= count; no++) {
            //   if ($.trim($('#item_name'+no).val()).length == 0) {
            //       alert("Please enter product name");
            //       $('#item_name'+no).focus();
            //       return false;
            //   }
              
            //   if ($.trim($('#order_item_quantity'+no).val()).length == 0) {
            //       alert("Please enter item quanity");
            //       $('#order_item_quantity'+no).focus();
            //       return false;
            //   }

            //   if ($.trim($('#order_item_price'+no).val()).length == 0) {
            //       alert("Please enter item price");
            //       $('#order_item_price'+no).focus();
            //       return false;
            //   }
            // }

            $('#invoice_form').submit();
        });
   
 });
  </script>
</body>
</html>