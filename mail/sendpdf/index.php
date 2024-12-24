<?php

include("../config.php");

//$data =$db->getRows("SELECT * FROM ".SALES." where status=1 and id=".$_REQUEST['sid']."  ORDER BY id asc");



//index.php

$message = '';

// $connect = new PDO("mysql:host=localhost;dbname=testing", "root", "");

function fetch_customer_data()
{


    $data =$db->getRows("SELECT * FROM ".SALES." where status=1 and id=".$_REQUEST['sid']."  ORDER BY id asc");
   
   
   //echo "<pre>";print_r($data);
   
	$output = "
    	    <table border='1' style='border-collapse:collapse;'>
                <tr>
                 <th>S.no.</th>
                 <th>Invoice No.</th>
                 <th>Client Name</th>
                 <th>Payment Type</th>
                 <th>Billing Address</th>
                 <th>Delivery Address</th>
                 <th>Billing Date</th>
                 <th>Product Details</th>
                 <th>Total Amount</th>
                 <th>Billing Amount</th>
                 <th>Non-Billing Amount</th>
                 <th>Other Charge</th>
                 <th>Other Charge Amount</th>
                 <th>Grand Total</th>
                 <th>Paid Amount</th>
                 <th>Due Amount</th>
                 
                </tr>";
                
//                  $i=0;
//      foreach($data as $row){
// 		 $i++;
// 	    $sno=$i;
//     	$name = $row['client_name'];
//     	if($row['payment_type']==1){
//     	    $payment_type='Full Payment';
//     	}else if($row['payment_type']==2){
//     	    $payment_type='Partially Payment';
//     	}else if($row['payment_type']==3){
//     	    $payment_type='Without GST';
//     	}
//     	$invoice_no=$row['invoice_no'];
//     	$billing_address=$row['billing_address'];
//     	$delivery_address=$row['delivery_address'];
//     	$billing_date=$row['billing_date'];
//     	$product_details=json_decode($row['product_details'],true);
//     	$total_amount=$row['total_amount'];
//     	$billing_amount=$row['billing_amount'];
//     	$non_billing_amount=$row['non_billing_amount'];
//     	$other_charge_amount=$row['other_charge_amount'];
//     	$grand_total=$row['grand_total'];
//     	$paid_amount=$row['paid_amount'];
//     	$due_amount=$row['due_amount'];
//     	$other_charge=$row['other_charge'];
    	
    	
//     	$output.="<tr>
//             <td>".$sno."</td>
//             <td>".$invoice_no."</td>
//             <td>".$name."</td>
//             <td>".$payment_type."</td>
//             <td>".$billing_address."</td>
//             <td>".$delivery_address."</td>
//             <td>".$billing_date."</td>
//             <td>".$total_amount."</td>
//             <td>".$billing_amount."</td>
//           <td>".$non_billing_amount."</td>
//           <td>".$other_charge."</td>
//           <td>".$other_charge_amount."</td>
//           <td>".$grand_total."</td>
//           <td>".$paid_amount."</td>
//           <td>".$due_amount."</td>
           
//       </tr>
//       <tr>
//             <th>Product Details</th>
//             <th>Product Name</th>
//             <th>Size</th>
//             <th>Thickness</th>
//             <th>Per Sq ft Price</th>
//             <th>Quantity</th>
//             <th>Total</th>
//       </tr>
//       <tr>";
      
//             foreach($product_details as $dt){
//                 $pname=$db->getVal("select name from ".ITEM." where id=".$dt['pid']."  ");
               
              
//                 $output.="  <td></td>
//                             <td>".$pname."</td>
//                             <td>".$dt["size"]."</td>
//                             <td>".$dt["thickness"]."</td>
//                             <td>".$dt["quantity"]."</td>
//                             <td>".$dt["price"]."</td>
//                             <td>".$dt["total"]."</td>";
          
                
//             }
          
//           $output.=" </tr>";
    	     
//     	$output .= '
//     		</table>
    	
//     	';
	

//     }
    
    	return $output;
}


// function fetch_customer_data(){
    
//     return '<h1>dsadasdadadadada</h1>';
// }



if(isset($_POST["action"]))
{
	include('pdf.php');
	$file_name = md5(rand()) . '.pdf';
	$html_code = '<link rel="stylesheet" href="bootstrap.min.css">';
	$html_code .= fetch_customer_data();
	$pdf = new Pdf();
	$pdf->load_html($html_code);
	$pdf->render();
	$file = $pdf->output();
	file_put_contents($file_name, $file);
	
	require 'class/class.phpmailer.php';
	$mail = new PHPMailer(true);
	 $mail->SMTPDebug = 3;
	$mail->IsSMTP();								//Sets Mailer to send message using SMTP
	$mail->Host = 'mail.jurysoftprojects.com';		//Sets the SMTP hosts of your Email hosting, this for Godaddy
	$mail->Port = '587';								//Sets the default SMTP server port
	$mail->SMTPAuth = true;							//Sets SMTP authentication. Utilizes the Username and Password variables
	$mail->Username = 'agroshare@jurysoftprojects.com';					//Sets SMTP username
	$mail->Password = 'India@2022';					//Sets SMTP password
	$mail->SMTPSecure = 'tls';							//Sets connection prefix. Options are "", "ssl" or "tls"
	$mail->From = 'socialmedia@wedezinestudio.com';			//Sets the From email address for the message
	$mail->FromName = 'Admin';			//Sets the From name of the message
	$mail->AddAddress('manojkhatarkar890@gmail.com', 'Manoj');		//Adds a "To" address
	$mail->WordWrap = 500;							//Sets word wrapping on the body of the message to a given number of characters
	$mail->IsHTML(true);							//Sets message type to HTML				
	$mail->AddAttachment($file_name);     				//Adds an attachment from a path on the filesystem
	$mail->Subject = 'Customer Details';			//Sets the Subject of the message
	$mail->Body = 'Please Find Customer details in attach PDF File.';				//An HTML or plain text message body
	if($mail->Send())								//Send an Email. Return true on success or false on error
	{
		$message = '<label class="text-success">Customer Details has been send successfully...</label>';
	}
	unlink($file_name);
}

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Send this data in mail</title>
		<script src="jquery.min.js"></script>
		<link rel="stylesheet" href="bootstrap.min.css" />
		<script src="bootstrap.min.js"></script>
	</head>
	<body>
		<br />
		<div class="container">
			<h3 align="center">Send this data in mail</h3>
			<br />
			<form method="post">
				<input type="submit" name="action" class="btn btn-danger" value="PDF Send" />
				<br />
				<?php echo $message; ?>
			</form>
			<br />
			<?php
			echo fetch_customer_data();
			?>			
		</div>
		<br />
		<br />
	</body>
</html>





