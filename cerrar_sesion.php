<?php
include 'log_function.php';
// Inicia la sesión (necesario para cerrarla)
session_start();

// Elimina todas las variables de sesión
session_unset();

// Destruye la sesión
session_destroy();
custom_log('Sessión Cerrada', "El usuario ha cerrado la sesión con exito");

// Redirige a la página de inicio (o a donde desees)
header('Location: index.php');
exit;
?>
