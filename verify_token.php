<?php
include 'db_connection.php'; 

if(isset($_GET['token'])) {
    $token = $_GET['token'];

    // Buscar el token en la base de datos
    $sql = "SELECT * FROM users WHERE token = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$token]);
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch();

        // Verificar si el token ya ha sido aceptado
        if ($user['token_accepted']) {
            // Redirigir al usuario a la página de error 404
            header("Location: errores/error404.php");
            exit;
        } else {
            // Marcar el token como aceptado
            $sql = "UPDATE users SET token_accepted = TRUE WHERE token = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$token]);

            // Redirigir al usuario a la página de inicio de sesión
            header("Location: login.php");
            exit;
        }
    } else {
        echo "Token inválido.";
    }
} else {
    echo "Token no proporcionado.";
}
?>