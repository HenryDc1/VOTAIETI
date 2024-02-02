<?php

session_start(); // Iniciar la sesión
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
    <meta name="author" content="Arnau Mestre, Claudia Moyano i Henry Doudo">
    <title>Listado de encuestas — Votaieti</title>
    <link rel="shortcut icon" href="../imgs/logosinfondo.png" />
    <link rel="stylesheet" href="styles.css">
    <script src="/js/script.js"></script> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

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
            echo "<thead><tr><th class='question-column'>Pregunta</th><th class='state-column'>Estado</th><th class='visibility-column'>Visibilidad Pregunta</th><th class='options-column'>Visibilidad Opciones</th><th class='invite-column'>Invitar</th><th class='details-column'>Detalles</th></tr></thead>";


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

                $visibilityText = isset($visibilityTexts[$questionVisibility]) ? $visibilityTexts[$questionVisibility] : $questionVisibility;

                // Mostrar la pregunta, el estado y la visibilidad de la encuesta en una fila de la tabla
                
                echo "<tr>
                <td>$question</td>
                <td><span class='poll-state $class'>$stateText</span></td>
                <td>
                    <select class='question-visibility' onchange='updateSelects(this)'>
                        <option value='public' " . ($questionVisibility == 'public' ? 'selected' : '') . ">Publica</option>
                        <option value='private' " . ($questionVisibility == 'private' ? 'selected' : '') . ">Privada</option>
                        <option value='hidden' " . ($questionVisibility == 'hidden' ? 'selected' : '') . ">Oculta</option>
                    </select>
                </td>
                <td>
                    <select class='options-visibility' onchange='updateSelects(this)' " . ($questionVisibility == 'private' ? 'disabled' : '') . ">
                        <option value='public' " . ($optionsVisibility == 'public' ? 'selected' : '') . ">Publica</option>
                        <option value='private' " . ($optionsVisibility == 'private' ? 'selected' : '') . ">Privada</option>
                        <option value='hidden' " . ($optionsVisibility == 'hidden' ? 'selected' : '') . ">Oculta</option>
                    </select>
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
        //Script para controlar la visibilidad de la pregunta y la respuesta
        echo "<script>
        function updateSelects(select) {
            var questionVisibility = document.querySelector('.question-visibility');
            var optionsVisibility = document.querySelector('.options-visibility');
    
            if (questionVisibility.value === 'public') {
                optionsVisibility.disabled = false;
            } else if (questionVisibility.value === 'private' && optionsVisibility.value !== 'private' && optionsVisibility.value !== 'hidden') {
                optionsVisibility.disabled = true;
                showErrorPopup('Si la visibilidad de la  pregunta és privada, la visibilidad de la  respuesta solo puede ser privada u oculta. Selecciona una de esas opciones.');
            } else if (questionVisibility.value === 'hidden' && optionsVisibility.value !== 'hidden') {
                optionsVisibility.disabled = true;
                showErrorPopup('Una pregunta con visibilidad oculta solo puede tener respuestas con visibilidad oculta. Por favor, selecciona Oculta para la respuesta.');
            } else {
                optionsVisibility.disabled = false;
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
</body>
</html>
