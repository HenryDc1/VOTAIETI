<?php
    session_start();
    include 'db_connection.php';
    require 'log_function.php';

    // Mostrar el mensaje si está presente en la sesión
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];

        // Eliminar el mensaje de la sesión
        unset($_SESSION['message']);
    }

    if (isset($_SESSION['succes'])) {
        echo "<script type='text/javascript'>showSuccesPopup('" . $_SESSION['succes'] . "');</script>";
        unset($_SESSION['succes']);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST["email"];
        $contraseña = $_POST["password"];
        
        $querystr = "SELECT user_name, email, token_accepted, conditions_accepted FROM users WHERE email = :email AND password = SHA2(:contrasena, 256)";

        $query = $pdo->prepare($querystr);

        $query->bindParam(':email', $email);
        $query->bindParam(':contrasena', $contraseña);
        

        $query->execute();
        
        $fila = $query->fetch(PDO::FETCH_ASSOC);
        if ($fila) {
            if ($fila['token_accepted'] == 0) {
                $error_message = "<script type='text/javascript'>$(document).ready(function() { showErrorPopup('Todavía no has validado el email. Revisa la bandeja de entrada'); });</script>";
                custom_log('INCIO DE SESION FALLIDO', "El usuario $email intentó iniciar sesión pero no ha validado el email");

            } else {
                $_SESSION['email'] = $email;
                $_SESSION['user_name'] = $fila['user_name'];
                echo '<script type="text/javascript">window.location = "https://aws21.ieti.site/dashboard.php";</script>';
                custom_log('INCIO DE SESION EXITOSO', "El usuario $email ha iniciado sesión correctamente");

                exit;
            }
        } else {
            $error_message = "<script type='text/javascript'>$(document).ready(function() { showErrorPopup('Correo electrónico o contraseña incorrectos'); });</script>";
            custom_log('INCIO DE SESION FALLIDO', "El usuario $email intentó iniciar sesión pero el correo electrónico o la contraseña son incorrectos");

        }   
        unset($pdo);
        unset($query);
    }
?><!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Portal de votaciones</title>
        <link rel="shortcut icon" href="logosinfondo.png" />
        <link rel="stylesheet" href="styles.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
        <script src="/js/script.js"></script>
    </head>

    <body class="loginBody">
        <?php include 'header.php'; ?>

        <div class="containerLogin">


        

            <form class="iniciasesionLogin" method="post">
                <h1>INICIA SESIÓN</h1>
                <img class="logoLogin" src="imgs/logosinfondo.png" alt="">
                <div class="datosUsuarioLogin">
                    <input class="inputLoginPHP" type="email" id="email" name="email" required>  
                    <label for="email">Correo electrónico</label>  
                </div>

                <div class="datosUsuarioLogin">
                    <input class="inputLoginPHP" type="password" id="password" name="password" required>
                    <label for="password">Contraseña</label>
                </div>

                <a href="https://aws21.ieti.site/send_email_password.php" id="forgotPassword">¿Has olvidado la contraseña?</a>
                <br><br>

                <div class="datosUsuarioLogin">
                    <a href="https://aws21.ieti.site/register.php" id="tienescuentaBotonLogin" type="submit">¿No tienes cuenta?</a>        
                    <button id="siguienteBotonLogin" type="submit">Siguiente</button>        
                </div>
                </form>
                </div>

        <?php include 'footer.php'; ?>
        <?php if (isset($error_message)) echo $error_message; ?>
        <?php if (isset($message)) echo "<script type='text/javascript'>showSuccessPopup('$message');</script>"; ?>

    </body>
</html>