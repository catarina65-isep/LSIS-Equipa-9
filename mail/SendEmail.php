<?php
// require_once('scripts/phpmailer/class.phpmailer.php');

// $mail = new PHPMailer();

// $mail->IsSMTP();                       // telling the class to use SMTP

// $mail->SMTPDebug = 0;                  
// // 0 = no output, 1 = errors and messages, 2 = messages only.

// $mail->SMTPAuth = true;                // enable SMTP authentication
// $mail->SMTPSecure = "tls";              // sets the prefix to the servier
// $mail->Host = "smtp.gmail.com";        // sets Gmail as the SMTP server
// $mail->Port = 587;                     // set the SMTP port for the GMAIL

// //$mail->Username = "info@example.com";  // Gmail username
// //$mail->Password = "yourpassword";      // Gmail password
// $mail->Username = "paulorproenca@gmail.com";  // Gmail username
// $mail->Password = "pmuadib";      // Gmail password

// $mail->CharSet = 'windows-1250';
// $mail->SetFrom ('paulorproenca@gmail.com', 'Paulo Proenca');
// //$mail->AddBCC ( 'sales@example.com', 'Example.com Sales Dep.');
// $mail->Subject = 'assunto teste';
// $mail->ContentType = 'text/plain';
// $mail->IsHTML(false);

// $mail->Body = 'Isto é o corpo do email teste'; 
// // you may also use $mail->Body = file_get_contents('your_mail_template.html');

// $mail->AddAddress ('paulorproenca@gmail.com', 'Paulo Proenca');     
// // you may also use this format $mail->AddAddress ($recipient);

// if(!$mail->Send())
// {
//         $error_message = "Mailer Error: " . $mail->ErrorInfo;
// } else 
// {
//         $error_message = "Successfully sent!";
// }
?>

<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/PHPMailer-master/src/SMTP.php';


//Load Composer's autoloader (created by composer, not included with PHPMailer)
//require 'vendor/autoload.php';

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'paulorproenca@gmail.com';                     //SMTP username
    $mail->Password   = 'app_password';                               //App password definida em https://support.google.com/accounts/answer/185833?visit_id=638871478621138283-140957664&p=InvalidSecondFactor&rd=1
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('paulorproenca@gmail.com', 'Paulo Proença');
    $mail->addAddress('paulorproenca@gmail.com', 'Paulo Proenca');     //Add a recipient
    // $mail->addAddress('ellen@example.com');               //Name is optional
    // $mail->addReplyTo('info@example.com', 'Information');
    // $mail->addCC('cc@example.com');
    // $mail->addBCC('bcc@example.com');

    //Attachments
    // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Here is the subject';
    $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
