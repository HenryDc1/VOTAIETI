<?php
session_start(); // Iniciar la sesión
if (isset($_POST['poll_id'])) {
    $pollId = $_POST['poll_id'];
    // Ahora puedes usar $pollId en tu código
}
if(!isset($_SESSION['email'])) {
    // Si el usuario no ha iniciado sesión, redirige a la página de error
    header('Location: errores/error403.php');
    exit;
}
// Incluir el archivo de conexión
include 'db_connection.php';

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
    <meta name="author" content="Arnau Mestre, Claudia Moyano i Henry Doudo">
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
        <h2>Invita a votar</h2>
    </div>
 
    <div class="inviteContainer">
            <p>Invita a tus amigos a participar en la encuesta. Solo necesitas introducir sus direcciones de correo electrónico, separadas por comas. Cada destinatario recibirá un enlace para votar en la encuesta seleccionada. Ten en cuenta que los correos electrónicos se enviarán en paquetes de 5 cada 5 minutos para evitar el spam. Nosotros nos encargaremos del resto.</p>        <form action="send_invites.php" method="post">
            <br><br>
            <label for="emails">Correos electrónicos (separados por comas):</label>
            <textarea id="emails" name="emails" rows="10"></textarea>
            <input type="submit" value="Invitar" class="submit-button">
        </form>
    </div>
    

    <div class="contenedorFooter">
        <?php include 'footer.php'; ?>
    </div>
</body>
</html>