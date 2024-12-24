<?php

//index.php
include("../config.php");
$message = '';

$data =$db->getRow("SELECT * FROM ".SALES." where status=1 and id=".$_REQUEST['sid']." ");

 $client=$db->getRow("select * from ".CLIENT." where id=".$data['cid']." ");
 
    //echo "<pre>";print_r($data);
    $payment_type="";
    if($data['payment_type']==1){
    	    $payment_type='Full Payment';
    	}else if($data['payment_type']==2){
    	    $payment_type='Partially Payment';
    	}else if($data['payment_type']==3){
    	    $payment_type='Without GST';
    	}
    
    $output.="
                <div class='row' style='margin-bottom:-120px !important;padding-bottom:-120px !important;'>
                    <div class='col-6 w-50 d-inline'>
                        <h2 style='color:green;'>ECOGEN INDUSTRIES</h2>
                        <p style='color:#943a2c;font-size:18px;'>Manufacturer of CALIBRE & DVONN PLYWOOD</p>
                        <p>Sy.No 39/1, Pattanagere Road, 
                            Near R.V Engineering College, R.V.Post, Mysore Road, Bangalore - 560059.
                        <p>  
                        
                       
                    </div>
                    <div class='col-6 w-50 d-inline' style='text-align:right;margin-top: 15px;'>
                        <h4>GSTIN : 29AAGFE0730R1Z2</h4>
                        <h4>Email: Hello@calibreply.com</h4>
                        <h4>Web: www.calibreply.com</h4>
                          
                    </div>
                </div>
            ";
    
    $output.="  <h3 style='text-align:center;'>SALES DETAILS</h3>";
    $output.="<table id='customers'  width='100%'>
                <tr>
                    
                     <th>Billing Date</th>
                     <th>Client Name</th>
                     <th>Billing Address</th>
                     <th>Delivery Address</th>
                     <th>Total Amount</th>
                </tr>
                
                <tr>
                     <td>".$data['billing_date']."</td>
                    <td>".$data['client_name']."</td>
                    <td>".$data['billing_address']."</td>
                    <td>".$data['delivery_address']."</td>
                    <td>".$data['total_amount']."</td>
                    
                    
                </tr>
            
            </table>
            <br />
            ";
            
                      
                      $output.="<h3>PRODUCT DETAILS</h3>
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
                      <br />
                      
                      
                      <table id='customers'  width='100%'>
            
                            <tr>
                               
                                 
                                 <th>Other Charge</th>
                                 <th>Other Charge Amount</th>
                                 <th>Grand Total</th>
                                 <th>Paid Amount</th>
                                 <th>Due Amount</th>
                            </tr>
                            
                            <tr>
                                
                                <td>".$data['other_charge']."</td>
                                <td>".$data['other_charge_amount']."</td>
                                <td>".$data['grand_total']."</td>
                                <td>".$data['paid_amount']."</td>
                                <td>".$data['due_amount']."</td>
                                
                                
                            </tr>
                      </table>
                       <br />
             
                      <h3>PAYMENT DETAILS</h3>
            
            ";
            
            $paymentDetails=$db->getRows("select * from ".PAYMENT."  where sid=".$_REQUEST['sid']." and status=2  ");
            
            if(count($paymentDetails)==1){
                $show="Sale given in Credit ";
                
                $output.="<br /> <div style='border: 1px solid #000; padding:10px;'>".$show."</div> <br />";
                
            }else{
                $show="";
           
            
             $output.="<table id='customers'  width='100%'>
            
                            <tr>
                               
                                 <th>Payment Date</th>
                                 <th>Amount</th>
                                 <th>Payment Mode</th>
                                 
                                 
                            </tr>";
                            
                 foreach($paymentDetails as $dt2){
    	            $pname=$db->getVal("select name from ".ITEM." where id=".$dt['pid']."  ");    
    	            if($dt2['amount']>0){
    	                 $output.="<tr>
                                <td>".$dt2['p_date']."</td>
                                <td>".$dt2['amount']."</td>
                                <td>".$dt2['payment_mode']."</td>
                                
                                
                            </tr>";
    	            }
                   
                 }            
                            
                     $output.="</table>
                                <br />";
                      
                  }          
                       $output.="<h3>REMARKS</h3>
                      
                      <div style='border: 1px solid #000; padding:10px;'>".$data['remark']."</div>
                      
                      ";
    
    $output.="";

// $html_code = '<link rel="stylesheet" href="style.css"> <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" >';
// 	$html_code .= $output;
// echo  $html_code;



    include('pdf.php');
	$file_name = md5(rand()) . '.pdf';
	$html_code = '<link rel="stylesheet" href="style.css"> <link rel="stylesheet" href="bootstrap.min.css" >';
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
	$mail->Username = 'info_calibreply@jurysoftprojects.com';					//Sets SMTP username
	$mail->Password = 'India@2023';					//Sets SMTP password
	$mail->SMTPSecure = 'tls';							//Sets connection prefix. Options are "", "ssl" or "tls"
	$mail->From = 'info_calibreply@jurysoftprojects.com';			//Sets the From email address for the message
	$mail->FromName = 'Calibreply';			//Sets the From name of the message
	$mail->AddAddress($client['email'], $client['name']);		//Adds a "To" address
	$mail->WordWrap = 50;							//Sets word wrapping on the body of the message to a given number of characters
	$mail->IsHTML(true);							//Sets message type to HTML				
	$mail->AddAttachment($file_name);     				//Adds an attachment from a path on the filesystem
	$mail->Subject = 'Calibreply Sales Details';			//Sets the Subject of the message
	$mail->Body = 'Please Find Sales details in attach PDF File.';				//An HTML or plain text message body
	if($mail->Send())								//Send an Email. Return true on success or false on error
	{  
	    unlink($file_name);
// 		$message = '<label class="text-success">Customer Details has been send successfully...</label>';
        	redirect($root_url."sales");
	}
	





