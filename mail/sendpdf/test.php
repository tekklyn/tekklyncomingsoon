<?php

//index.php
include("../config.php");
$message = '';

$data =$db->getRow("SELECT * FROM ".SALES." where status=1 and id=".$_REQUEST['sid']."  ORDER BY id asc");
    echo "<pre>";print_r($data);
    $payment_type="";
    if($data['payment_type']==1){
    	    $payment_type='Full Payment';
    	}else if($data['payment_type']==2){
    	    $payment_type='Partially Payment';
    	}else if($data['payment_type']==3){
    	    $payment_type='Without GST';
    	}
    
    $output="  <h3 style='text-align:center;'>Sales Details</h3>";
    $output.="<table id='customers'  width='100%'>
                <tr>
                    
                     <th>Invoice No.</th>
                     <th>Client Name</th>
                     <th>Payment Type</th>
                     <th>Billing Address</th>
                     <th>Delivery Address</th>
                     <th>Billing Date</th>
                      <th>Total Amount</th>
                </tr>
                
                <tr>
                    <td>".$data['invoice_no']."</td>
                    <td>".$data['client_name']."</td>
                    <td>".$payment_type."</td>
                    <td>".$data['billing_address']."</td>
                    <td>".$data['delivery_address']."</td>
                    <td>".$data['billing_date']."</td>
                    <td>".$data['total_amount']."</td>
                    
                    
                </tr>
            
            </table>
            <br /><br />
            ";
            
             $output.="<table id='customers'  width='100%'>
            
                            <tr>
                               
                                 <th>Billing Amount</th>
                                 <th>Non-Billing Amount</th>
                                 <th>Other Charge</th>
                                 <th>Other Charge Amount</th>
                                 <th>Grand Total</th>
                                 <th>Paid Amount</th>
                                 <th>Due Amount</th>
                            </tr>
                            
                            <tr>
                                <td>".$data['billing_amount']."</td>
                                <td>".$data['non_billing_amount']."</td>
                                <td>".$data['other_charge']."</td>
                                <td>".$data['other_charge_amount']."</td>
                                <td>".$data['grand_total']."</td>
                                <td>".$data['paid_amount']."</td>
                                <td>".$data['due_amount']."</td>
                                
                                
                            </tr>
                      </table>
                      <br /><br />
                      
                      <h3>Product Details</h3>
                      <br />
            
            ";
           
            $product_details=json_decode($data['product_details'],true);
           
            
             $output.="<table id='customers'  width='100%'>
            
                            <tr>
                               
                                 <th>Product Name</th>
                                 <th>Size</th>
                                 <th>Thickness</th>
                                 <th>Per Sqft. Price</th>
                                 <th>Quantity</th>
                                 <th>Total</th>
                                 
                            </tr>";
                            
                 foreach($product_details as $dt){
    	            $pname=$db->getVal("select name from ".ITEM." where id=".$dt['pid']."  ");            
                    $output.="<tr>
                                <td>".$pname."</td>
                                <td>".$dt['size']."</td>
                                <td>".$dt['thickness']."</td>
                                <td>".$dt['price']."</td>
                                <td>".$dt['quantity']."</td>
                                <td>".$dt['total']."</td>
                                
                                
                            </tr>";
                 }            
                            
                     $output.="</table>
                      <br /><br />
                      
                      <h3>Payment Details</h3>
            
            ";
            
            $paymentDetails=$db->getRows("select * from ".PAYMENT."  where sid=".$_REQUEST['sid']." and status=2  ");
            
            
             $output.="<table id='customers'  width='100%'>
            
                            <tr>
                               
                                 <th>Payment Date</th>
                                 <th>Amount</th>
                                 <th>Payment Mode</th>
                                 
                                 
                            </tr>";
                            
                 foreach($paymentDetails as $dt2){
    	            $pname=$db->getVal("select name from ".ITEM." where id=".$dt['pid']."  ");            
                    $output.="<tr>
                                <td>".$dt2['p_date']."</td>
                                <td>".$dt2['amount']."</td>
                                <td>".$dt2['payment_mode']."</td>
                                
                                
                            </tr>";
                 }            
                            
                     $output.="</table>
                      <br /><br />";
    
    $output.="";


echo $output;



    include('pdf.php');
	$file_name = md5(rand()) . '.pdf';
	$html_code = '<link rel="stylesheet" href="style.css">';
	$html_code .= $output;
	$pdf = new Pdf();
	$pdf->load_html($html_code);
	$pdf->render();
	$file = $pdf->output();
	file_put_contents($file_name, $file);
	
	
		require 'class/class.phpmailer.php';
	$mail = new PHPMailer(true);
	 //$mail->SMTPDebug = 3;
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
	$mail->WordWrap = 50;							//Sets word wrapping on the body of the message to a given number of characters
	$mail->IsHTML(true);							//Sets message type to HTML				
	$mail->AddAttachment($file_name);     				//Adds an attachment from a path on the filesystem
	$mail->Subject = 'Customer Details';			//Sets the Subject of the message
	$mail->Body = 'Please Find Customer details in attach PDF File.';				//An HTML or plain text message body
	if($mail->Send())								//Send an Email. Return true on success or false on error
	{
		$message = '<label class="text-success">Customer Details has been send successfully...</label>';
	}
	unlink($file_name);


if(isset($_POST["action"]))
{
    
    //echo fetch_customer_data();
    
// 	include('pdf.php');
// 	$file_name = md5(rand()) . '.pdf';
// 	$html_code = '<link rel="stylesheet" href="bootstrap.min.css">';
// 	$html_code .= fetch_customer_data();
// 	$pdf = new Pdf();
// 	$pdf->load_html($html_code);
// 	$pdf->render();
// 	$file = $pdf->output();
// 	file_put_contents($file_name, $file);
	
// 	require 'class/class.phpmailer.php';
// 	$mail = new PHPMailer(true);
// 	 $mail->SMTPDebug = 3;
// 	$mail->IsSMTP();								//Sets Mailer to send message using SMTP
// 	$mail->Host = 'mail.jurysoftprojects.com';		//Sets the SMTP hosts of your Email hosting, this for Godaddy
// 	$mail->Port = '587';								//Sets the default SMTP server port
// 	$mail->SMTPAuth = true;							//Sets SMTP authentication. Utilizes the Username and Password variables
// 	$mail->Username = 'agroshare@jurysoftprojects.com';					//Sets SMTP username
// 	$mail->Password = 'India@2022';					//Sets SMTP password
// 	$mail->SMTPSecure = 'tls';							//Sets connection prefix. Options are "", "ssl" or "tls"
// 	$mail->From = 'socialmedia@wedezinestudio.com';			//Sets the From email address for the message
// 	$mail->FromName = 'Admin';			//Sets the From name of the message
// 	$mail->AddAddress('manojkhatarkar890@gmail.com', 'Manoj');		//Adds a "To" address
// 	$mail->WordWrap = 50;							//Sets word wrapping on the body of the message to a given number of characters
// 	$mail->IsHTML(true);							//Sets message type to HTML				
// 	$mail->AddAttachment($file_name);     				//Adds an attachment from a path on the filesystem
// 	$mail->Subject = 'Customer Details';			//Sets the Subject of the message
// 	$mail->Body = 'Please Find Customer details in attach PDF File.';				//An HTML or plain text message body
// 	if($mail->Send())								//Send an Email. Return true on success or false on error
// 	{
// 		$message = '<label class="text-success">Customer Details has been send successfully...</label>';
// 	}
// 	unlink($file_name);
}

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Create Dynamic PDF Send As Attachment with Email in PHP</title>
		<script src="jquery.min.js"></script>
		<link rel="stylesheet" href="bootstrap.min.css" />
		<script src="bootstrap.min.js"></script>
	</head>
	<body>
		<br />
		<div class="container">
			<h3 align="center">Create Dynamic PDF Send As Attachment with Email in PHP</h3>
			<br />
			<form method="post">
				<input type="submit" name="action" class="btn btn-danger" value="PDF Send" /><?php echo $message; ?>
			</form>
			<br />
			<?php
			echo fetch_customer_data($connect);
			?>			
		</div>
		<br />
		<br />
	</body>
</html>





