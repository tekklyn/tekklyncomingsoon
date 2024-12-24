<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function

include("config.php");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if(!$_POST)
    $_POST= json_decode(file_get_contents('php://input'),true);
$ch=$_GET["type"];
if($ch=="")$ch=$_POST["type"];

$msg=$_POST['msg'];
$subject = $_POST['subject'];

//Load Composer's autoloader
require 'vendor/autoload.php';

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
   // $mail->SMTPDebug = 3;//SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'tekklyn.info@gmail.com';                     //SMTP username
    $mail->Password   = 'cpms dfkk vvmz ptfw';                               //SMTP password
    $mail->SMTPSecure = 'tls'; //PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('tekklyn.info@gmail.com', 'Tekklyn');
     $mail->addAddress($_POST['reciever_email'], $_POST['reciever_full_name']);   
    $mail->addCC($_POST['cc'] ? $_POST['cc'] : 'subhamjena0001@gmail.com');    

    //Content
    $mail->isHTML(true);                                 
    $mail->Subject = $subject;
    $mail->Body    = $msg;

    $mail->send();
    
    echo json_encode([
        'code' => 200,
        'status' => 'success',
        'message' => "Thank you for contacting us."
    ]);
} catch (Exception $e) {
    echo json_encode([
        'code' => 500,
        'status' => 'error',
        'message' => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"
    ]);
    // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>