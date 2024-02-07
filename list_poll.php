<?php

session_start(); // Iniciar la sesión


if (isset($_SESSION['success'])) {
    echo '<script type="text/javascript">';
    echo 'showSuccessPopup("' . $_SESSION['success'] . '");';
    echo '</script>';
    unset($_SESSION['success']); // Borrar el mensaje de éxito de la sesión
}



include 'log_function.php';
if(!isset($_SESSION['email'])) {
    // Si el usuario no ha iniciado sesión, redirige a la página de error
    header('Location: errores/error403.php');
    custom_log('Error 403', "Se ha intentado acceder a la página de listado de encuestas sin iniciar sesión");

    exit;
}
// Incluir el archivo de conexión
include 'db_connection.php';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="keywords" content="votaieti, votación en línea, votación, encuestas, elecciones, privacidad, seguridad">
    <meta name="description" content="Plataforma de votación en línea comprometida con la privacidad y seguridad de los usuarios. Regístrate ahora y participa en encuestas y elecciones de manera segura.">
    <meta property="og:title" content="Panel de control — Votaieti">
    <meta property="og:description" content="Plataforma de votación en línea comprometida con la privacidad y seguridad de los usuarios. Regístrate ahora y participa en encuestas y elecciones de manera segura.">
    <meta property="og:image" content="../imgs/votaietilogo.png">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="author" content="Arnau Mestre, Alejandro Soldado i Henry Doudo">
    <title>Panel de control — Votaieti</title>
    <link rel="shortcut icon" href="../imgs/logosinfondo.png" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <link rel="stylesheet" href="styles.css">
    <script src="../styles + scripts/script.js"></script> 
    <script src="/js/script.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
</head>

<body class="bodyDashboard">
    <!-- HEADER -->
    <div class="contenedorHeader">
        <?php include 'header.php'; ?>
    </div>

    <div class="imagenCabecera">
        <h1>VOTAIETI</h1>
        <h2>Listado de preguntas</h2>
    </div>

    <div class="dashboardContenedor">
    <div class="listPollContainer">
    <?php   
    if (isset($_SESSION['email'])) {
        $email = $_SESSION['email'];
        custom_log('ENCUESTAS LISTADAS', "Se ha listado las encuestas del usuario $email");


        // Consulta para obtener el user_id
        $selectStmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
        $selectStmt->execute([$email]);
        $userId = $selectStmt->fetchColumn();
        
        if ($userId) {
            // Consulta para recuperar preguntas basadas en el user_id
            $pollStmt = $pdo->prepare("SELECT poll_id, question, start_date, end_date, poll_state, question_visibility, results_visibility FROM poll WHERE user_id = ?");


            $pollStmt->execute([$userId]);

            // Mostrar las preguntas y el estado de la encuesta
            echo "<h1>Mis encuestas</h1>";
            echo "<table>";
            echo "<thead><tr><th class='question-column'>Pregunta</th><th class='state-column'>Estado</th><th class='visibility-column'>Visibilidad Pregunta</th><th class='options-column'>Visibilidad Opciones</th><th class='block-column'>Bloquear/Desbloquear</th><th class='invite-column'>Invitar</th><th class='details-column'>Detalles</th></tr></thead>";


            echo "<tbody>";
            while ($row = $pollStmt->fetch(PDO::FETCH_ASSOC)) {
                $question = $row['question'];
                $pollState = $row['poll_state'];
                $questionVisibility = $row['question_visibility'];
                $resultsVisibility = $row['results_visibility'];
                $pollId = $row['poll_id'];



                // Añadir clases CSS basadas en el valor de pollState
                $class = '';
                switch ($pollState) {
                    case 'not_started':
                        $class = 'not-started';
                        break;
                    case 'finished':
                        $class = 'finished';
                        break;
                    case 'active':
                        $class = 'active';
                        break;
                }

                // Mapear los valores de estado a sus correspondientes 
            
                $stateTexts = array(
                    'not_started' => 'No Iniciada',
                    'finished' => 'Finalizada',
                    'active' => 'Activa',
                );

                // Mapear los valores de visibilidad a sus correspondientes 
                $visibilityTexts = array(
                    'public' => 'Publica',
                    'private' => 'Privada',
                    'hidden' => 'Oculta',
                );

                // Obtener el texto correspondiente al estado actual
                $stateText = isset($stateTexts[$pollState]) ? $stateTexts[$pollState] : $pollState;

               // Obtener el texto correspondiente a la visibilidad actual
             $visibilityText = isset($visibilityTexts[$questionVisibility]) ? $visibilityTexts[$questionVisibility] : $questionVisibility;

             echo "<tr>
    <td>$question</td>
    <td><span class='poll-state $class'>$stateText</span></td>
    <td>
        <select class='question-visibility' onchange='updateSelects(this)'>
            <option value='public'".($questionVisibility=='public'?'selected':'').">Publica</option>
            <option value='private'".($questionVisibility=='private'?'selected':'').">Privada</option>
            <option value='hidden'".($questionVisibility=='hidden'?'selected':'').">Oculta</option>
        </select>
    </td>
    <td>
        <select class='options-visibility' onchange='updateSelects(this)'>
            <option value='public'>Publica</option>
            <option value='private'>Privada</option>
            <option value='hidden'>Oculta</option>
        </select>
    </td>
    <td>
        <form method='POST' action='update_poll_status.php'>
            <input type='hidden' name='poll_id' value='$pollId'>
            <select name='status'>
                <option value='block'>Bloquear</option>
                <option value='unblock'>Desbloquear</option>
            </select>
            <input type='submit' value='Actualizar'>
        </form>
    </td>
    <td>
        <form method='POST' action='invite_poll.php'>
            <input type='hidden' name='poll_id' value='$pollId'>
            <button type='submit'>Invitar</button>
        </form>
    </td>
    <td>
        <form method='POST' action='details_page.php'>
            <input type='hidden' name='poll_id' value='$pollId'>
            <button type='submit'>Detalles</button>
        </form>
    </td>
</tr>";

echo "<script>
    function updateSelects(select) {
        var row = select.closest('tr');
        var questionVisibility = row.querySelector('.question-visibility').value;
        var optionsVisibility = row.querySelector('.options-visibility');

        optionsVisibility.disabled = false;

        if (questionVisibility === 'private') {
            row.querySelectorAll('.options-visibility option').forEach(option => {
                if (option.value !== 'private' && option.value !== 'hidden') {
                    option.style.display = 'none';
                } else {
                    option.style.display = 'block';
                }
            });
        } else if (questionVisibility === 'hidden') {
            row.querySelectorAll('.options-visibility option').forEach(option => {
                if (option.value !== 'hidden') {
                    option.style.display = 'none';
                } else {
                    option.style.display = 'block';
                }
            });
        } else {
            row.querySelectorAll('.options-visibility option').forEach(option => {
                option.style.display = 'block';
            });
        }
    }
</script>";

            }
            echo "</tbody>";
            echo "</table>";

            // Cerrar la consulta preparada
            $pollStmt->closeCursor();
        } else {
            echo "No se encontró el user_id para el correo electrónico proporcionado.";
            custom_log('ERROR LISTAR ENCUESTAS', "No se encontró el user_id para el correo electrónico proporcionado");

            header('Location: dashboard.php');
        }
    } else {
        echo "La variable de sesión 'email' no está definida.";
    }
    ?>
    </div>
</div>

    <div class="contenedorFooter">
        <?php include 'footer.php'; ?>
    </div>

    <?php if(isset($_SESSION['success'])): ?>
    <script>
        $(document).ready(function() {
            showSuccesPopup("<?php echo $_SESSION['success']; ?>");
        });
    </script>
        <?php 
            unset($_SESSION['success']); // Limpiar la variable de sesión después de mostrar el mensaje
        endif; 
        ?>
</body>
</html>
