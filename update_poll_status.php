<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

include 'log_function.php';
include 'db_connection.php'; // Incluye la conexión a la base de datos


$senderEmail = "amestrevizcaino.cf@iesesteveterradas.cat";
$passwordEmail = "";

if (isset($_POST['poll_id']) && isset($_POST['poll_status'])) {
    $pollId = $_POST['poll_id'];
    $status = $_POST['poll_status'] === 'blocked' ? 1 : 0;

    // Actualizar el estado de la encuesta
    $stmt = $pdo->prepare("UPDATE poll SET blocked = ? WHERE poll_id = ?");
    $stmt->execute([$status, $pollId]);

    // Si la encuesta está bloqueada, comprobar si hay algún email que no ha votado
    if ($status === 1) {
        $stmt = $pdo->prepare("SELECT guest_email, token FROM invitation WHERE poll_id = ? AND guest_email NOT IN (SELECT guest_email FROM user_vote WHERE poll_id = ?)");
        $stmt->execute([$pollId, $pollId]);
        $emails = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Enviar un correo electrónico a los usuarios que no han votado
        foreach ($emails as $email) {
            // Crear una nueva instancia de PHPMailer
            $mail = new PHPMailer();
            $mail->IsSMTP();
            $mail->Mailer = "smtp";
            $mail->SMTPDebug  = 0;  
            $mail->SMTPAuth   = TRUE;
            $mail->SMTPSecure = "tls";
            $mail->Port       = 587;
            $mail->Host       = "smtp.gmail.com";
            $mail->Username   = $senderEmail;
            $mail->Password   = $passwordEmail;
            $mail->IsHTML(true);
            $mail->CharSet = 'UTF-8'; 
            $mail->AddAddress($email['guest_email']);
            $mail->SetFrom($senderEmail, "VOTAIETI");
            $mail->Subject = 'Encuesta bloqueada';
            $mail->AddEmbeddedImage('votaietilogo.png', 'logo_img');
            $mail->MsgHTML("La encuesta a la que fuiste invitado ha sido bloqueada por su administrador. No podrás participar en esta encuesta. Para más información, por favor contacta al administrador de la encuesta. Gracias por tu comprensión.<br><img src='cid:logo_img'>");
        
            if(!$mail->send()) {
                echo 'Message could not be sent.';
                echo 'Mailer Error: ' . $mail->ErrorInfo;
            }
        }
    }

    // Redirigir de vuelta a la página de la lista de encuestas
    header("Location: list_poll.php");
    exit;
}
?>