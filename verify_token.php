<?php
include 'db_connection.php'; 

if(isset($_GET['token'])) {
    $token = $_GET['token'];

    // Buscar el token en la base de datos
    $sql = "SELECT * FROM users WHERE token = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$token]);
    if ($stmt->rowCount() > 0) {
        // Marcar el correo electrónico como verificado
        $sql = "UPDATE users SET email_verified = 1 WHERE token = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$token]);
        
        // Redirigir al usuario a la página de inicio de sesión
        header("Location: login.php");
        exit;
    } else {
        echo "Token inválido.";
    }
} else {
    echo "Token no proporcionado.";
}
?>