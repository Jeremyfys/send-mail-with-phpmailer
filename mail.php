
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';

switch($_SERVER['REQUEST_METHOD']) {
    case("OPTIONS"): 
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Allow-Headers: *");
        exit;
    case("POST"):
        $json = file_get_contents('php://input');

        $params = json_decode($json);

        $name = $params->name;
        $email = $params->email;
        $phone = $params->phone;
        $table = $params->data;

        $resource = base64_decode(str_replace(" ", "+", substr($table, strpos($table, ","))));
        
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();                                            
            $mail->SMTPDebug = 0;
            $mail->Host = 's64-202-188-159.secureserver.net';
            $mail->SMTPAuth = true;                                   
            $mail->Username = 'support@cotamotor.com';                     
            $mail->Password = 'QUt$}CRFzYQ4';                         
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;         
            $mail->Port = 465;                                    
        
            $mail->setFrom('support@cotamotor.com', 'Cota Motor');
            $mail->addAddress($email, $name);    
            $mail->addAddress('support@cotamotor.com');
            
            $mail->isHTML(true);                                  
            $mail->Subject = 'Informe detallado de vehiculo';
            $mail->Body = 'Nombre: '.$name.'<br>';
            $mail->Body .= 'Email: '.$email.'<br>';
            $mail->Body .= 'Telefono: '.$phone.'<br>';
            $mail->addStringAttachment($resource, "Datos.png");
            $mail->AddStringEmbeddedImage($resource, 'datos', 'datos.png', 'base64', 'image/png');
            $mail->Body .= '<img style="width:100%;height:auto;" src="cid:datos" alt="Table" /><br>';
            $mail->smtpConnect([
               'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ]);
            
            if (!$mail->send()) {
                echo 'Internal Error';
            } else {
                echo json_encode(array());
            }
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
        break;
    default:
        header("Allow: POST", true, 405);
        exit;
}

?>