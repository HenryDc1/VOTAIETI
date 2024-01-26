<?php
    session_start();
    include 'db_connection.php';
    

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST["email"];  // Cambiado de "username" a "email"
        $contraseña = $_POST["password"];
        
        // Cambiado "user_name" a "email" y agregado "user_name" a la selección
        $querystr = "SELECT email, user_name FROM users WHERE email = :email AND password = SHA2(:contrasena, 256)";
        $query = $pdo->prepare($querystr);

        $query->bindParam(':email', $email);  // Cambiado de ":usuario" a ":email"
        $query->bindParam(':contrasena', $contraseña);

        $query->execute();
        
        $filas = $query->rowCount();
        if ($filas > 0) {
            // Obtener el nombre de usuario
            $fila = $query->fetch(PDO::FETCH_ASSOC);
            $nombre = $fila['user_name'];

            // Iniciar la sesión y guardar el correo electrónico y el nombre de usuario en la sesión
            $_SESSION['email'] = $email;
            $_SESSION['nombre'] = $nombre;

            // Mostrar el popup de aceptación de condiciones
            echo "<script>
                    function showTermsPopup() {
                        // Crear la ventana flotante
                        var termsPopup = $('<div/>', {
                            id: 'termsPopup',
                            text: 'Acepta las condiciones del portal',
                            style: 'position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: white; color: black; padding: 20px; border-radius: 5px;'
                        });

                        // Crear el checkbox
                        var termsCheckbox = $('<input/>', {
                            type: 'checkbox',
                            id: 'termsCheckbox',
                            name: 'termsCheckbox'
                        });

                        // Crear la etiqueta para el checkbox
                        var termsLabel = $('<label/>', {
                            for: 'termsCheckbox',
                            text: ' Acepto las condiciones'
                        });

                        // Crear el botón 'Siguiente'
                        var nextButton = $('<button/>', {
                            text: 'Siguiente',
                            style: 'display: block; margin-top: 20px;',
                            click: function () {
                                if ($('#termsCheckbox').is(':checked')) {
                                    // Si el checkbox está marcado, redirigir al dashboard
                                    window.location.href = 'dashboard.php';
                                } else {
                                    alert('Debes aceptar las condiciones para continuar.');
                                }
                            }
                        });

                        // Añadir el checkbox, la etiqueta y el botón a la ventana flotante
                        termsPopup.append(termsCheckbox, termsLabel, nextButton);

                        // Añadir la ventana flotante al cuerpo del documento
                        $('body').append(termsPopup);
                    }
                    window.onload = function () {
                        showTermsPopup();
                    };
                  </script>";
            exit;
        } else {
            $error_message = "<script type='text/javascript'>$(document).ready(function() { showErrorPopup('Correo electrónico o contraseña incorrectos'); });</script>";
        }
        unset($pdo);
        unset($query);
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
                

                <a href="register.php" id="tienescuentaBotonLogin" type="submit">¿No tienes cuenta?</a>        
                <button id="siguienteBotonLogin" type="submit">Siguiente</button>        
            </form>
        </div>

        <?php include 'footer.php'; ?>
        <?php if (isset($error_message)) echo $error_message; ?>
    </body>
</html>