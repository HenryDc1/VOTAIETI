<?php
session_start();
include 'db_connection.php'; // Incluye tu script de conexión a la base de datos

$pollId = $_POST['poll_id']; // Obtiene el id de la encuesta
$pollOption = $_POST['pollOption']; // Obtiene la opción seleccionada de la encuesta
$guestEmail = $_SESSION['guest_email']; // Obtiene el correo electrónico del invitado de la sesión

// Comprueba si el correo electrónico del invitado existe en la tabla de usuarios
$stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
$stmt->execute([$guestEmail]);
$user = $stmt->fetch();

if ($user) {
    // El usuario está registrado, guarda su voto como 'registered'
    $stmt = $pdo->prepare("INSERT INTO user_vote (user_id, poll_id, option_id, user_type, guest_email) VALUES (?, ?, ?, 'registered',?)");
    $stmt->execute([$user['user_id'], $pollId, $pollOption,$guestEmail]);
} else {
    // El usuario es un invitado, guarda su voto como 'guest'
    $stmt = $pdo->prepare("INSERT INTO user_vote (guest_email, poll_id, option_id, user_type) VALUES (?, ?, ?, 'guest')");
    $stmt->execute([$guestEmail, $pollId, $pollOption]);
}

// Almacena el mensaje de éxito en una variable de sesión
$_SESSION['message'] = "Su voto ha sido enviado con éxito.";

// Redirige al usuario a index.php
header("Location: index.php");
exit;
?>