<?php
    session_start();
    include 'db_connection.php';
    require 'log_function.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if ($password == $confirm_password) {
            // Las contraseñas coinciden
            $token = $_GET['token'];

            // Buscar el usuario con el token
            $sql = "SELECT * FROM users WHERE token = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$token]);
            $user = $stmt->fetch();

            if ($user) {
                // El usuario existe, actualizar la contraseña
                $hashed_password = hash('sha256', $password);
                $sql = "UPDATE users SET password = ? WHERE token = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$hashed_password, $token]);
                $_SESSION['message'] = "Contraseña actualizada con éxito.";
                custom_log('CONTRASEÑA RESTABLECIDA', "El usuario $email ha restablecido la contraseña.");


                 // Redirigir a login.php
                 header("Location: https://aws21.ieti.site/login.php");
                 exit;
            } else {
                // El usuario no existe
                $message = "Token inválido.";
            }
        } else {
            // Las contraseñas no coinciden
            $message = "Las contraseñas no coinciden.";
        }
    }
?>
<!DOCTYPE html>
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
            <h1>Restablecer Contraseña</h1>
            <img class="logoLogin" src="imgs/logosinfondo.png" alt="">
            <div class="datosUsuarioLogin">
                <input class="inputLoginPHP" type="password" id="password" name="password" required>  
                <label for="password">Nueva Contraseña</label>  
            </div>

            <div class="datosUsuarioLogin" id="confirm_password_div"></div>

            <br><br>

            <div class="datosUsuarioLogin" id="submit_button_div">
            </div>
        </form>
    </div>
        <?php if (isset($message)) echo $message; ?>

        <?php include 'footer.php'; ?>

        <script>
          $(document).ready(function() {
            // Función para verificar la contraseña
            function checkPassword(password) {
                // Debe tener al menos 8 caracteres, una letra mayúscula, una letra minúscula y un carácter especial
                var regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
                return regex.test(password);
            }
        
            // Cuando se presiona la tecla Tab o Enter en el campo de contraseña
            $('#password').on('keydown', function(e) {
                if (e.keyCode == 9 || e.keyCode == 13) { // 9 es Tab, 13 es Enter
                    if (checkPassword($(this).val())) {
                        // Si la contraseña es válida, agregar el campo de confirmación de contraseña
                        $('#confirm_password_div').html('<input class="inputLoginPHP" type="password" id="confirm_password" name="confirm_password" required><label for="confirm_password">Confirmar Contraseña</label>');
                    } else {
                        // Si la contraseña no es válida, mostrar un mensaje de error
                        showErrorPopup('La contraseña debe tener al menos 8 caracteres, una letra mayúscula, una letra minúscula y un carácter especial.');
                    }
                }
            });
        
            // Cuando se presiona la tecla Tab o Enter en el campo de confirmación de contraseña
            $(document).on('keydown', '#confirm_password', function(e) {
                if (e.keyCode == 9 || e.keyCode == 13) { // 9 es Tab, 13 es Enter
                    if ($(this).val() == $('#password').val()) {
                        // Si las contraseñas coinciden, agregar el botón de envío
                        $('#submit_button_div').html('<button id="siguienteBotonLogin" type="submit">Restablecer Contraseña</button>');
                    } else {
                        // Si las contraseñas no coinciden, mostrar un mensaje de error
                        showErrorPopup('Las contraseñas no coinciden.');
                    }
                }
            });
        });
        </script>
    </body>
</html>
