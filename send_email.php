<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "vendor/autoload.php";
include 'db_connection.php'; // Incluir el archivo de conexión

$senderEmail = "amestrevizcaino.cf@iesesteveterradas.cat";
$passwordEmail = "ArnauMestre169";

// Seleccionar los primeros 5 correos electrónicos de la tabla SEND_EMAIL
$query = "SELECT * FROM SEND_EMAIL LIMIT 5";
$stmt = $pdo->prepare($query);
$stmt->execute();
$emails = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach($emails as $email) {
    // Crear una nueva instancia de PHPMailer
    $mail = new PHPMailer();
    // ... (código anterior)

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
}

// Redirigir al usuario a index.php
header('Location: index.php');
exit;
?>