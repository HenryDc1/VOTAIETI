<?php
session_start(); // Iniciar la sesión
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "vendor/autoload.php";

if(!isset($_SESSION['email'])) {
    // Si el usuario no ha iniciado sesión, redirige a la página de error
    header('Location: errores/error403.php');
    exit;
}

if (isset($_POST['poll_id'])) {
    $pollId = $_POST['poll_id'];
    error_log("Poll ID: " . $pollId); // Debug line
} else {
    error_log("Poll ID not set in POST data");
}

// Incluir el archivo de conexión
include 'db_connection.php';
$pollToken = null; // Define $pollToken before the query

// Obtener el poll_token de la encuesta seleccionada
$query = "SELECT poll_token FROM poll WHERE poll_id = :pollId";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':pollId', $pollId, PDO::PARAM_INT);

$stmt->execute();
if ($stmt->rowCount() > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $pollToken = $row['poll_token'];
    error_log("Poll token: " . $pollToken); // Debug line
} else {
    error_log("No poll found with the provided ID");
}


$senderEmail = "amestrevizcaino.cf@iesesteveterradas.cat";
$passwordEmail = "";

if(isset($_POST['emails'])) {
    $emails = array_unique(array_map('trim', explode(',', $_POST['emails']))); // Divide los correos en un array y elimina duplicados y espacios en blanco

    // Validar los correos electrónicos
    $emails = array_filter($emails, function($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    });

    // Dividir los correos electrónicos en paquetes de 5
    $emailChunks = array_chunk($emails, 5);

    foreach($emailChunks as $chunk) {
        foreach($chunk as $email) {
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
            $mail->AddAddress($email);
            $mail->SetFrom($senderEmail, "VOTAIETI");
            $mail->Subject = 'Invitacion para votar en una encuesta';
            $mail->AddEmbeddedImage('votaietilogo.png', 'logo_img');
            error_log("Poll token before sending mail: " . $pollToken); // Debug line
            $mail->MsgHTML("Has sido invitado a participar en una encuesta en la plataforma VOTAIETI. Para votar, por favor haz clic en el siguiente enlace: <a href='http://localhost:3000/accept_invitation.php?poll_token=" . $pollToken . "'>Acceder a la encuesta</a>. Tu voto es completamente anónimo. Gracias por tu participación.<br><img src='cid:logo_img'>");
            if(!$mail->send()) {
                echo 'Message could not be sent.';
                echo 'Mailer Error: ' . $mail->ErrorInfo;
            } else {
                echo 'Message has been sent';
            }
        }

        // Esperar 5 minutos antes de enviar el próximo paquete
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="keywords" content="votaieti, votación en línea, votación, encuestas, elecciones, privacidad, seguridad">
    <meta name="description" content="Plataforma de votación en línea comprometida con la privacidad y seguridad de los usuarios. Regístrate ahora y participa en encuestas y elecciones de manera segura.">
    <meta property="og:title" content="Panel de control — Votaieti">
    <meta property="og:description" content="Plataforma de votación en línea comprometida con la privacidad y seguridad de los usuarios. Regístrate ahora y participa en encuestas y elecciones de manera segura.">
    <meta property="og:image" content="../imgs/votaietilogo.png">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="author" content="Arnau Mestre, Alejandro Soldado y Henry Doudo">
    <title>Panel de Invitación — Votaieti</title>
    <link rel="shortcut icon" href="../imgs/logosinfondo.png" />
    <link rel="stylesheet" href="styles.css">
    <script src="../styles + scripts/script.js"></script> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
</head>

<body class="bodyIndex">    <!-- HEADER -->
    <div class="contenedorHeader">
        <?php include 'header.php'; ?>
    </div>

    <div class="imagenCabecera">
        <h1>VOTAIETI</h1>
        <h2>Panel de Invitación</h2>
    </div>
 
    <div class="inviteContainer">
            <p>Invita a tus amigos a participar en la encuesta. Solo necesitas introducir sus direcciones de correo electrónico, separadas por comas. Cada destinatario recibirá un enlace para votar en la encuesta seleccionada. Ten en cuenta que los correos electrónicos se enviarán en paquetes de 5 cada 5 minutos para evitar el spam. Nosotros nos encargaremos del resto.</p>        <form action="invite_poll.php" method="post">
            <br><br>
            <label for="emails">Correos electrónicos (separados por comas):</label>
            <textarea id="emails" name="emails" rows="10"></textarea>
            <input type="submit" value="Invitar" class="submit-button">
        </form>

        </form>
        <?php
            // Assuming $pollId and $pollToken are available in this scope
            echo "Poll ID: " . $pollId . "<br>";
            echo "Poll Token: " . $pollToken . "<br>";
        ?>
    </div>
    

    

    <div class="contenedorFooter">
        <?php include 'footer.php'; ?>
    </div>
</body>
</html>