<?php
include 'log_function.php';
// Inicia la sesión (necesario para cerrarla)
session_start();

// Elimina todas las variables de sesión
session_unset();

// Destruye la sesión
session_destroy();
custom_log('SESION CERRADA', "El usuario con correo electrónico: {$_SESSION['guest_email']} ha cerrado la sesión con éxito");

// Redirige a la página de inicio (o a donde desees)
header('Location: https://aws21.ieti.site/index.php');
exit;
?>
