<?php

//index.php
include("../config.php");
$message = '';

         $client=$db->getRow("select * from ".CLIENT." where id=".$_REQUEST['cid']." ");
        $data=$db->getRows("select *,client_name as c_name from ".SALES." where status=1 and cid=".$_REQUEST["cid"]."  order by id desc   ");
        $i=0;
        $tgrand_total=0;$tpaid_amount-0;$tdue_amount=0;$tbilling_amount=0;$tnon_billing_amount=0;
        $tpaid_billing_amount=0;$tpaid_non_billing_amount=0;$tdue_billing_amount=0;$tdue_non_billing_amount=0;
        $twithout_gst_amount=0;$twithout_gst_paid=0;$twithout_gst_due=0;$tother_charge_amount=0;$tdue_total_amount=0;
        
        $due_other_charge=0;
        
        foreach($data as $dt){
            
            $tgrand_total=$tgrand_total+ $data[$i]['grand_total'];
            $tpaid_amount=$tpaid_amount+ $data[$i]['paid_amount'];
            $tdue_amount=$tdue_amount+ $data[$i]['due_amount'];
            
           
            
            $data[$i]['without_gst']=0;
            $data[$i]['without_gst_paid']=0;
            $data[$i]['without_gst_due']=0;
            
            if($data[$i]['payment_type']==3){
                $data[$i]['without_gst']=$data[$i]['grand_total']-$data[$i]['other_charge_amount'];
                $twithout_gst_amount=$twithout_gst_amount+$data[$i]['without_gst'];
                // $wpayment=$db->getVal("select SUM(amount) from ".PAYMENT." where sid=".$dt['id']." and status=2  ");
                
                $data[$i]['without_gst_paid']=$data[$i]['paid_amount']-$data[$i]['other_charge_amount'];
                $twithout_gst_paid=$twithout_gst_paid+$data[$i]['without_gst_paid'];
                
                $data[$i]['without_gst_due']=$data[$i]['without_gst']-$data[$i]['without_gst_paid'];
                $twithout_gst_due=$twithout_gst_due+$data[$i]['without_gst_due'];
            }
            
            //$tother_charge_amount=$tother_charge_amount+$data[$i]['other_charge_amount'];
            
            $bill=0;
            $non_bill=0;
            $bill=$db->getVal("select SUM(amount) from ".PAYMENT." where sid=".$dt['id']." and status=2 and payment_mode='cheque'  ");
            $non_bill=$db->getVal("select SUM(amount) from ".PAYMENT." where sid=".$dt['id']." and status=2 and payment_mode='cash'  ");
            
            $total_other=$db->getVal("select SUM(amount) from ".PAYMENT." where sid=".$dt['id']." and status=2 and payment_mode='other'  ");
            
            $due_other=$data[$i]['other_charge_amount']-$total_other;
            $data[$i]['due_other']=$due_other;
            
            $tother_charge_amount=$tother_charge_amount+$due_other;
            
            $tbilling_amount=$tbilling_amount+$data[$i]['billing_amount'];
            
            $tnon_billing_amount=$tnon_billing_amount+$data[$i]['non_billing_amount'];
            
            
            $totalBillingDue=$dt['billing_amount']-$bill;
           
            
            $totalNonBillingDue=$dt['non_billing_amount']-$non_bill;
            
            
            if($totalBillingDue>0){
                $data[$i]['billing_due']=$totalBillingDue;
                 $tdue_billing_amount=$tdue_billing_amount+$totalBillingDue;
                 
            }else if($totalBillingDue<0){
                $data[$i]['billing_due']=0;
                 $tdue_billing_amount=$tdue_billing_amount+0;
            }else{
                $data[$i]['billing_due']=0;
                 $tdue_billing_amount=$tdue_billing_amount+0;
            }
            
            if($totalNonBillingDue>0){
                $data[$i]['non_billing_due']=$totalNonBillingDue;
                $tdue_non_billing_amount=$tdue_non_billing_amount+$totalNonBillingDue;
            }else if($totalNonBillingDue<0){
                $data[$i]['non_billing_due']=0;
                $tdue_non_billing_amount=$tdue_non_billing_amount+0;
            }else{
                $data[$i]['non_billing_due']=0;
                $tdue_non_billing_amount=$tdue_non_billing_amount+0;
            }
            
            
            $data[$i]['paid_billing_amount']=$dt['billing_amount']-$data[$i]['billing_due'];
            
            $tpaid_billing_amount=$tpaid_billing_amount+$data[$i]['paid_billing_amount'];
            
            $data[$i]['paid_non_billing_amount']=$dt['non_billing_amount']-$data[$i]['non_billing_due'];
            
            if($data[$i]['paid_non_billing_amount']<0){
                $data[$i]['paid_non_billing_amount']=0;
            }
            
            $tpaid_non_billing_amount=$tpaid_non_billing_amount+$data[$i]['paid_non_billing_amount'];
            
            
            if($dt['pay_by']=='client'){
                if($dt['other_charge_amount']<$dt['paid_amount']){
                    $due_other_charge=$due_other_charge+0;
                    
                    
                }else{
                    $due_other_charge=$due_other_charge+($dt['other_charge_amount']-$dt['paid_amount']);
                     
                }
                
                
            }
            
            // echo "due->".$due_other_charge."<br>";
            // echo "other_charge_amount->".$dt['other_charge_amount']."<br>";
            // echo "paid_amount->".$dt['paid_amount']."<br>";
            // echo "pay_by->".$dt['pay_by']."<br> --> <br>";
            
            
            // $data[$i]['non_billing_due']=$non_bill;
            $i++;
        }
        
        $totalAry=array(
                    "grand_total"=>$tgrand_total,
                    "paid_amount"=>$tpaid_amount,
                    "due_amount"=>$tdue_amount,
                    "billing_amount"=>$tbilling_amount,
                    "paid_billing_amount"=>$tpaid_billing_amount,
                    "due_billing_amount"=>$tdue_billing_amount,
                    "non_billing_amount"=>$tnon_billing_amount,
                    "paid_non_billing_amount"=>$tpaid_non_billing_amount,
                    "due_non_billing_amount"=>$tdue_non_billing_amount,
                    "twithout_gst_amount"=>$twithout_gst_amount,
                    "twithout_gst_paid"=>$twithout_gst_paid,
                    "twithout_gst_due"=>$twithout_gst_due,
                    "tother_charge_amount"=>$tother_charge_amount,
                    "due_other_charge"=>$due_other_charge,
                    
                );
        
        
   
    
    
    $output="  <h3 style='text-align:center;'>Sales Details</h3>";
    $output.="<table id='customers'  width='100%'>
                <tr>
                     <th>Client Name</th>
                     <th>Total Amount Due</th>
                     <th>Billing Amount Due</th>
                     <th>Non-Billing Amount Due</th>
                     <th>Delivery Charge Due</th>
                     
                </tr>
                
                <tr>
                    <td>".$client['name']."</td>
                    <td>".$totalAry['due_amount']."</td>
                    <td>".$totalAry['due_billing_amount']."</td>
                    <td>".$totalAry['due_non_billing_amount']."</td>
                    <td>".$totalAry['tother_charge_amount']."</td>
                    
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
		
		//redirect($root_url."clients");
	}
	




?>




