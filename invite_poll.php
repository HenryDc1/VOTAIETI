<?php
session_start(); // Iniciar la sesión

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "vendor/autoload.php";
include 'db_connection.php'; // Incluir el archivo de conexión
include 'log_function.php';

if(!isset($_SESSION['email'])) {
    // Si el usuario no ha iniciado sesión, redirige a la página de error
    header('Location: errores/error403.php');
    custom_log('PERMISO DENEGADO', "Se ha intentado acceder a la página de invitación sin iniciar sesión");

    exit;
}

if (isset($_POST['poll_id'])) {
    $pollId = $_POST['poll_id'];
    $_SESSION['pollId'] = $pollId; // Guardar el pollId en la sesión

    error_log("Poll ID: " . $pollId); // Debug line
} else {
    error_log("Poll ID not set in POST data");
    exit; // Salir del script si poll_id no está establecido
}

// Incluir el archivo de conexión
include 'db_connection.php';

if(isset($_POST['emails'])) {
    $emails = array_unique(array_map('trim', explode(',', $_POST['emails']))); // Divide los correos en un array y elimina duplicados y espacios en blanco

    // Validar los correos electrónicos
    $emails = array_filter($emails, function($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    });

    foreach($emails as $email) {
        // Generar un token único
        $token = bin2hex(random_bytes(16));

        // Insertar el correo electrónico del invitado en la tabla user_guest
       // $query = "INSERT IGNORE INTO user_guest (guest_email) VALUES (:email)";
        //$stmt = $pdo->prepare($query);
        //$stmt->bindParam(':email', $email, PDO::PARAM_STR);
        //$stmt->execute();

        // Insertar el token en la tabla de invitaciones
        $query = "INSERT INTO invitation (poll_id, guest_email, sent_date, token, token_accepted, blocked) VALUES (:pollId, :email, NOW(), :token, 0, 0)";
        
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':pollId', $_SESSION['pollId'], PDO::PARAM_INT);

        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        custom_log('INSERCIÓN DE DATOS', "Se han insertado los datos de la invitación en la base de datos");

        // Insertar el correo electrónico en la tabla SEND_EMAIL
        $query = "INSERT INTO SEND_EMAIL (email) VALUES (:email)";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        
    }
    header('Location: send_email.php');
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

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
           
        <p>Invita a tus amigos a participar en la encuesta. Solo necesitas introducir sus direcciones de correo electrónico, separadas por comas. Cada destinatario recibirá un enlace para votar en la encuesta seleccionada. Ten en cuenta que los correos electrónicos se enviarán en paquetes de 5 cada 5 minutos para evitar el spam. Nosotros nos encargaremos del resto.</p>     
        <form action="invite_poll.php" method="post">
                <input type="hidden" name="poll_id" value="<?php echo $_SESSION['pollId']; ?>">
                <label for="emails">Correos electrónicos (separados por comas):</label>
                <textarea id="emails" name="emails" rows="10"></textarea>
                <input type="submit" value="Invitar" class="submit-button">
            </form>

       
       
    </div>
    

    <script>
        function showSuccesPopup(message) {
            // Crear la ventana flotante
            var successPopup = $('<div/>', {
                id: 'successPopup',
                text: message,
                style: 'position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: green; color: white; padding: 20px; border-radius: 5px;'
            });

            // Crear el botón 'X'
            var closeButton = $('<button/>', {
                text: 'X',
                style: 'position: absolute; top: 0; right: 0; background-color: transparent; color: white; border: none; font-size: 20px; cursor: pointer;'
            });

            // Añadir el botón 'X' a la ventana flotante
            successPopup.append(closeButton);

            // Añadir la ventana flotante al cuerpo del documento
            $('body').append(successPopup);

            // Manejador de eventos para el botón 'X'
            closeButton.click(function () {
                successPopup.remove();
            });
        }
        <?php if(isset($feedback)): ?>
        window.onload = function () {
            showSuccesPopup('La encuesta ha sido enviada con éxito');
        };
    <?php endif; ?>
            
    </script>
    <div class="contenedorFooter">
        <?php include 'footer.php'; ?>
    </div>
</body>
</html>
