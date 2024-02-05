<?php
session_start();
include 'db_connection.php'; // Incluye tu script de conexión a la base de datos

include 'log_function.php';
// Verifica si la sesión de correo electrónico está iniciada
if (!isset($_SESSION['guest_email']) || empty($_SESSION['guest_email'])) {
    // Redirige al usuario a la página de error
    header("Location: errores/error403.php");
    custom_log('Permiso Denegado', "Se ha intentado acceder a la página de procesar los votos");

    exit;
}

$pollId = $_POST['poll_id']; // Obtiene el id de la encuesta
$pollOption = $_POST['pollOption']; // Obtiene la opción seleccionada de la encuesta
$guestEmail = $_SESSION['guest_email']; // Obtiene el correo electrónico del invitado de la sesión

if (isset($_POST['password'])){
    $pwd = $_POST['password'];
} else {
    $pwd = $_POST['config'];
}

// Comprueba si el correo electrónico del invitado existe en la tabla de usuarios
$stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
$stmt->execute([$guestEmail]);
$user = $stmt->fetch();

$stmtCount = $pdo->prepare("SELECT COUNT(*) FROM user_vote");
$stmtCount->execute([$guestEmail]);
$count = $stmt->fetch();

if ($user) {
    // El usuario está registrado, guarda su voto como 'registered'
    $stmt = $pdo->prepare("INSERT INTO user_vote (user_id, poll_id, user_type, guest_email, hash_id) VALUES (?,?,'registered',?,?)");
    $stmt->execute([$user['user_id'],$pollId,$guestEmail,$count+1]);

    //Encriptacion de la contraseña e insercion en la tabla voted_option
    $hash = openssl_encrypt($count, 'AES-128-CBC', $pwd);
    $stmt = $pdo->prepare("INSERT INTO voted_option (option_id,hash) VALUES (?,?)");
    $stmt->execute([$pollOption,$hash]);

} else {
    // El usuario es un invitado, guarda su voto como 'guest'
    $stmt = $pdo->prepare("INSERT INTO user_vote (guest_email, poll_id, user_type, hash_id) VALUES (?, ?, 'guest', ?)");
    $stmt->execute([$guestEmail,$pollId,$count+1]);

    //Encriptacion de la contraseña e insercion en la tabla voted_option
    $hash = openssl_encrypt($count, 'AES-128-CBC', $pwd);
    $stmt = $pdo->prepare("INSERT INTO voted_option (option_id,hash) VALUES (?,?)");
    $stmt->execute([$pollOption,$hash]);
}

// Almacena el mensaje de éxito en una variable de sesión
$_SESSION['message'] = "Su voto ha sido enviado con éxito.";
custom_log('ESTADO VOTO', "Se ha enviado el voto con éxito");


// Redirige al usuario a index.php
header("Location: index.php");
exit;
?>
