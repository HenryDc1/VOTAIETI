<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    include 'log_function.php';
    require "vendor/autoload.php";
    include 'db_connection.php'; // Incluir el archivo de conexión

    $senderEmail = "amestrevizcaino.cf@iesesteveterradas.cat";
    $passwordEmail = "ArnauMestre169";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST['email'];

        // Comprobar si el correo electrónico existe en la base de datos
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $result = $stmt->fetchAll();
        if (count($result) > 0) {
            // El correo electrónico existe en la base de datos
            // Generar un token aleatorio
            $token = bin2hex(openssl_random_pseudo_bytes(16));
        
            // Guardar el token en la base de datos
            $sql = "UPDATE users SET token = ? WHERE email = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$token, $email]);
        
            // Crear una nueva instancia de PHPMailer
            $mail = new PHPMailer();
            $mail->IsSMTP();
            $mail->Mailer = "smtp";
            $mail->SMTPDebug  = 0;  
            $mail->SMTPAuth   = TRUE;
            $mail->SMTPSecure = "tls";
            $mail->Port       = 587;
            $mail->Host       = "smtp.gmail.com";
            $mail->Username   = $senderEmail;
            $mail->Password   = $passwordEmail;
            $mail->IsHTML(true);
            $mail->CharSet = 'UTF-8'; 
            $mail->AddAddress($email);
            $mail->SetFrom($senderEmail, "VOTAIETI");
            $mail->Subject = 'Recuperación de contraseña';
            $mail->MsgHTML("Por favor, haga clic en el siguiente enlace para recuperar su contraseña: <a href='https://aws21.ieti.site/forgot_password.php?token=" . $token . "'>Restablecer contraseña</a>");
            $message = '';

            if($mail->send()) {
                $message = "<script>showSuccesPopup('Se ha enviado un correo electrónico para restablecer la contraseña.');</script>";
                custom_log('RESTABLECER CONTRASEÑA', "El usuario con el correo electrónico $email ha solicitado restablecer la contraseña. Se ha enviado un correo electrónico con un enlace para restablecer la contraseña.");


            } else {
                echo 'Message could not be sent.';
                echo 'Mailer Error: ' . $mail->ErrorInfo;
            }
        } else {
            // El correo electrónico no existe en la base de datos
            // Redirigir al usuario a la página de registro
            header("Location: https://aws21.ieti.site/register.php");
            exit;
        }
    }
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Recuperar Contraseña - Votatieti</title>
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
                <h1>RECUPERAR CONTRASEÑA</h1>
                <img class="logoLogin" src="imgs/logosinfondo.png" alt="">
                <div class="datosUsuarioLogin">
                    <input class="inputLoginPHP" type="email" id="email" name="email" required>  
                    <label for="email">Correo electrónico</label>  
                </div>

              
                <div class="datosUsuarioLogin">
                    <button id="siguienteBotonLogin" type="submit">Siguiente</button>        
                </div>
                </form>
                </div>

        <?php include 'footer.php'; ?>
        <?php echo $message; ?>

        <?php if (isset($error_message)) echo $error_message; ?>
    </body>
</html>