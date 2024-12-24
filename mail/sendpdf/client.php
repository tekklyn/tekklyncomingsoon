<?php

//index.php
include("../config.php");
$message = '';

// if(!$_POST)
//     $_POST= json_decode(file_get_contents('php://input'),true);
//      $_REQUEST= json_decode(file_get_contents('php://input'),true);

    //$data =$db->getRow("SELECT * FROM ".SALES." where status=1 and id=".$_REQUEST['sid']."  ORDER BY id asc");
    
    
    $data =$db->getRows("SELECT * FROM ".SALES." where status=1 and cid=".$_REQUEST['cid']."  ");
    
    $client=$db->getRow("select * from ".CLIENT." where id=".$_REQUEST['cid']." ");
    
    $total_amount=$db->getVal("SELECT SUM(total_amount) FROM ".SALES." where status=1 and cid=".$_REQUEST['cid']."  ");
    
    $billing_amount=$db->getVal("SELECT SUM(billing_amount) FROM ".SALES." where status=1 and cid=".$_REQUEST['cid']."  ");
    
    $non_billing_amount=$db->getVal("SELECT SUM(non_billing_amount) FROM ".SALES." where status=1 and cid=".$_REQUEST['cid']."  ");
    
    $other_charge_amount=$db->getVal("SELECT SUM(other_charge_amount) FROM ".SALES." where status=1 and cid=".$_REQUEST['cid']."  ");
    
    $grand_total=$db->getVal("SELECT SUM(grand_total) FROM ".SALES." where status=1 and cid=".$_REQUEST['cid']."  ");
    
    $paid_amount=$db->getVal("SELECT SUM(paid_amount) FROM ".SALES." where status=1 and cid=".$_REQUEST['cid']."  ");
    
    $due_amount=$db->getVal("SELECT SUM(due_amount) FROM ".SALES." where status=1 and cid=".$_REQUEST['cid']."  ");


     if($total_amount==null || $total_amount==''){
         $total_amount=0;
     } else if($billing_amount==null || $billing_amount==''){
         $billing_amount=0;
     } else if($non_billing_amount==null || $non_billing_amount==''){
         $non_billing_amount=0;
     } else if($other_charge_amount==null || $other_charge_amount==''){
         $other_charge_amount=0;
     } else if($grand_total==null || $grand_total==''){
         $grand_total=0;
     } else if($paid_amount==null || $paid_amount==''){
         $paid_amount=0;
     } else if($due_amount==null || $due_amount==''){
         $due_amount=0;
     }
    
    
    $output="  <h3 style='text-align:center;'>Sales Details</h3>";
    $output.="<table id='customers'  width='100%'>
                <tr>
                     <th>Client Name</th>
                     <th>Total Amount</th>
                     <th>Billing Amount</th>
                     <th>Non-Billing Amount</th>
                     <th>Delivery Charge</th>
                     <th>Grand Total</th>
                     <th>Paid Amount</th>
                     <th>Due Amount</th>
                </tr>
                
                <tr>
                    <td>".$client['name']."</td>
                    <td>".$total_amount."</td>
                    <td>".$billing_amount."</td>
                    <td>".$non_billing_amount."</td>
                    <td>".$other_charge_amount."</td>
                    <td>".$grand_total."</td>
                    <td>".$paid_amount."</td>
                    <td>".$due_amount."</td>
                </tr>
            
            </table>
            <br /><br />
            ";
            
         


     //echo $output;



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

		//$message = '<label class="text-success">Customer Details has been send successfully...</label>';
		
		//echo json_encode(array("status"=>"success",));
		
		redirect($root_url."clients");
	}
	




?>




