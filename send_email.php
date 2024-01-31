<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "vendor/autoload.php";
include 'db_connection.php'; // Incluir el archivo de conexión

$senderEmail = "amestrevizcaino.cf@iesesteveterradas.cat";
$passwordEmail = "ArnauMestre169";

// Seleccionar los primeros 5 correos electrónicos de la tabla SEND_EMAIL
$query = "SELECT e.*, i.token FROM SEND_EMAIL e INNER JOIN invitation i ON e.email = i.guest_email LIMIT 5";
$stmt = $pdo->prepare($query);
$stmt->execute();
$emails = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach($emails as $email) {
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
    $mail->AddAddress($email['email']);
    $mail->SetFrom($senderEmail, "VOTAIETI");
    $mail->Subject = 'Invitacion para votar en una encuesta';
    $mail->AddEmbeddedImage('votaietilogo.png', 'logo_img');
    $mail->MsgHTML("Has sido invitado a participar en una encuesta en la plataforma VOTAIETI. Para votar, por favor haz clic en el siguiente enlace: <a href='https://aws21.ieti.site/accept_invitation.php?token=" . $email['token'] . "'>Acceder a la encuesta</a>. Tu voto es completamente anónimo. Gracias por tu participación.<br><img src='cid:logo_img'>");

    if($mail->send()) {
        // Eliminar el correo electrónico de la tabla SEND_EMAIL
        $query = "DELETE FROM SEND_EMAIL WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $email['id'], PDO::PARAM_INT);
        $stmt->execute();
    } else {
        echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    }
    header  ('Location: https://aws21.ieti.site/dashboard.php');    
}
?>