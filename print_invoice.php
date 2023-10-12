<?php include 'inc/header.php'; ?>
<title>Print Invoice</title>


</head>
<body>
<script>
// $(document).ready(function () {
//         $('#printInvoice').click(function(){
//             Popup($('.invoice')[0].outerHTML);
//             function Popup(data) 
//             {
//                 window.print();
//                 return true;
//             }
//         }); 
//     });
    function printContent(print) {
		var restorepage = document.body.innerHTML;
		var printcontent = document.getElementById("invoice").innerHTML;
		document.body.innerHTML = printcontent;
		window.print();
		document.body.innerHTML = restorepage;
	}
</script>
<?php

// require_once 'dompdf/autoload.inc.php';;
// use Dompdf\Dompdf;

if (isset($_GET['pdf']) && isset($_GET['id'])) {
    include 'config/connect.php';

    $output = '';
    $stmt = $conn->prepare("SELECT * FROM order_table WHERE order_id = :id");
    $stmt->execute(array(
        ':id' => $_GET['id']
    ));

    $result = $stmt->fetchAll();

    foreach ($result as $key => $row) {
    $r_address = '';$r_email = '';$r_phone = '';$r_web = '';$p_desc ='';

    if (isset($row['order_receiver_address'])) {$r_address = $row['order_receiver_address'];}
    if (isset($row['order_receiver_phone'])) {$r_phone = $row['order_receiver_phone'];}
    if (isset($row['order_receiver_email'])) {$r_email = $row['order_receiver_email'];}
    
    $stmt = $conn->prepare("SELECT * FROM store_table WHERE store_id = :id");
    $stmt->execute(array(':id' => $row['order_store_id']));
    $s_result = $stmt->fetchAll();
    foreach ($s_result as $key => $v) {
        $store_name = $v['store_name'];
        $store_phone = $v['store_phone'];
        $store_email = $v['store_email'];
        $store_website = $v['store_website'];
        $store_logo = $v['store_logo'];
        $store_address = $v['store_address'];
    }
    if (isset($store_logo)) { $store_logo = '<img src="'.$store_logo.'" data-holder-rendered="true" height="100" width="100" />'; } else { $store_logo = ''; }

        $output .= ' 
    <div class="toolbar hidden-print">
        <div align="right" class="px-4 py-3">
            <button type="button" id="printInvoice" onclick="printContent(print)" class="btn btn-dark mb-3"><i class="fa fa-print"></i> Print</button>
        </div>
    </div>  
    <div id="invoice">
    <div class="invoice overflow-auto" id="printPDF">
    <hr>
        <div style="min-width: 600px">
            <header>
                <div class="row">
                    <div class="col">
                        <a target="_blank" class="navbar-brand" href="">
                            '.$store_logo.'
                            </a>
                    </div>
                    <div class="col company-details">
                        <h2 class="name">
                            <a target="_blank" href="">
                            '. $store_name.'
                            </a>
                        </h2>
                        <div>'. $store_address.'</div>
                        <div>'. $store_phone.'</div>
                        <div>'. $store_email.'</div>
                        <div>'. $store_website.'</div>
                    </div>
                </div>
            </header>
            <main>
                <div class="row contacts">
                    <div class="col invoice-to">
                        <div class="text-gray-light">INVOICE TO:</div>
                        <h2 class="to">'.$row['order_receiver_name'].'</h2>
                        <div class="address">'.$r_address.'</div>
                        <div class="email"><a href="mailto:'.$r_email.'">'.$r_email.'</a></div>
                        <div class="phone">'.$r_phone.'</div>
                    </div>
                    <div class="col invoice-details">
                        <h1 class="invoice-id">INVOICE '.$row['order_no'].'</h1>
                        <div class="date">Date of Invoice: '.$row['order_date'].'</div>
                    </div>
                </div>

                <table border="0" cellspacing="0" cellpadding="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th class="text-left">DESCRIPTION</th>
                            <th class="text-right">QUANTITY</th>
                            <th class="text-right">PRICE</th>
                            <th class="text-right">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>';

                    $stmt = $conn->prepare("SELECT * FROM order_item_table WHERE order_id = :order_id");

                    $stmt->execute(array(
                        ':order_id' => $_GET['id']
                    ));
                    $item_result = $stmt->fetchAll();
                    $i = 0;

                    foreach ($item_result as $key => $sub_row) {
                    $i = $i + 1;

                    // Get item description
                    $stmt = $conn->prepare("SELECT * FROM product_table WHERE product_name = :item_name");

                    $stmt->execute(array(
                        ':item_name' => $sub_row['item_name']
                    ));
                    $item_name_result = $stmt->fetchAll();
                    foreach ($item_name_result as $key => $val) {
                        $item_desc = $val['product_description'];
                    }
                    if (isset($item_desc)) { $item_desc = $item_desc; } else { $item_desc = ''; }
                        $output .= '
                        <tr>
                            <td class="no">'.$i.'</td>
                            <td class="text-left"><h3>'.$sub_row['item_name'].'</h3>'.$item_desc.'</td>
                            <td class="unit">'.number_format($sub_row['order_item_quantity']).'</td>
                            <td class="qty">₦'.number_format($sub_row['order_item_price']).'</td>
                            <td class="total text-dark">₦'.number_format($sub_row['order_item_actual_amount']).'</td>
                        </tr>
                        
                        ';
                    }
          

          $output .= '   
                    </tbody>
                
            ';

            $output .= '
            <tfoot>
                
                <tr>
                    <td colspan="2"></td>
                    <td colspan="2">GRAND TOTAL</td>
                    <td>₦'.number_format($row['order_total_amount']).'</td>
                </tr>
            </tfoot>
            ';
        
            $output .= '
            </table>
            <div class="thanks pt-4">Thank you!</div>
            
        </main>
            ';
        
    }
//     
    echo $output;

    // $pdf = new Dompdf();
    // $file_name = 'Invoice-'.$row["order_no"].'.pdf';
    // $pdf->loadHtml($output);
    // $pdf->render();
    // $pdf->stream($file_name, array("Attachment" => false));

}

?>

        <div>

        </div>
    </div>
</div>
</body>
</html>

