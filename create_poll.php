<?php
session_start(); // Iniciar la sesión
$conn = new mysqli('localhost', 'root', 'Kecuwa53', 'VOTE');

// Verificar la conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $question = $_POST['question'];
    $numOptions = $_POST['numOptions'];
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];

    // Obtener el email de la sesión
    if (isset($_SESSION['email'])) {
        $email = $_SESSION['email'];

        // Consulta para obtener el user_id
        $selectStmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $selectStmt->bind_param("s", $email);
        $selectStmt->execute();
        $selectStmt->bind_result($userId);

        // Obtener el resultado
        if ($selectStmt->fetch()) {
            // Cerrar la consulta preparada
            $selectStmt->close();

            // Determinar el estado de la encuesta
            $currentDate = date("Y-m-d H:i:s");
            $pollState = "";

            if ($currentDate < $startDate) {
                $pollState = "not_started";
            } elseif ($startDate <= $currentDate && $currentDate <= $endDate) {
                $pollState = "active";
            } else {
                $pollState = "finished";
            }

            // Insertar la pregunta en la tabla de encuestas con el user_id
            $stmt = $conn->prepare("INSERT INTO poll (question, user_id, start_date, end_date, poll_state, question_visibility, results_visibility, poll_link, path_image) 
                                    VALUES (?, ?, ?, ?, ?, NULL, NULL, NULL, NULL)");
            $stmt->bind_param("sssss", $question, $userId, $startDate, $endDate, $pollState);
            $stmt->execute();

            // Obtener el ID de la encuesta que acabamos de insertar
            $pollId = $stmt->insert_id;

            // Cerrar la primera consulta preparada
            $stmt->close();

            // Preparar la consulta para insertar opciones
            $stmt = $conn->prepare("INSERT INTO poll_options (poll_id, option_text, start_date, end_date, path_image) VALUES (?, ?, ?, ?, NULL)");

            for ($i = 1; $i <= $numOptions; $i++) {
                $option = $_POST["option$i"];
                if (!empty($option)) {
                    $stmt->bind_param("isss", $pollId, $option, $startDate, $endDate);
                    $stmt->execute();
                }
            }

            // Cerrar la consulta preparada
            $stmt->close();

            echo "<script>
            function showSuccesPopup(message) {
                // Crear la ventana flotante
                var successPopup = $('<div/>', {
                    id: 'successPopup',
                    text: message,
                    style: 'position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: green; color: white; padding: 20px; border-radius: 5px;'
                });

                // Crear el botón 'X'
                var closeButton = $('<button/>', {
                    text: 'X',
                    style: 'position: absolute; top: 0; right: 0; background-color: transparent; color: white; border: none; font-size: 20px; cursor: pointer;'
                });

                // Añadir el botón 'X' a la ventana flotante
                successPopup.append(closeButton);

                // Añadir la ventana flotante al cuerpo del documento
                $('body').append(successPopup);

                // Manejador de eventos para el botón 'X'
                closeButton.click(function () {
                    successPopup.remove();
                });
            }
            window.onload = function () {
                showSuccesPopup('Encuesta creada con éxito');
            };
          </script>";
        } else {
            echo "No se encontró el user_id para el correo electrónico proporcionado.";
        }
    } else {
        echo "La variable de sesión 'email' no está definida.";
    }

    // Cerrar la conexión
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Encuesta</title>
    <!-- Asegúrate de incluir la biblioteca jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="createPollBody">
    <?php include 'header.php'; ?>

    <div class="containerCreatePoll">
    <form class="createPoll"id="pollForm" method="post" action="create_poll.php">
    <h1 class="tituloCreatePoll">Crear Encuesta</h1>
    <div class="datosCreatePoll">
        <input type="text" id="question" name="question" required>
        <label for="question">Pregunta:</label>
    </div>
    <div class="datosCreatePoll">
        <label id="numeroOpciones"for="numOptions">Número de opciones:</label>
        <select id="numOptions" name="numOptions">
            <?php for($i=1; $i<=100; $i++) echo "<option value='$i'>$i</option>"; ?>
        </select>
        <div id="optionInputs"></div>
    </div>
  
    <div class="datosCreatePoll">
        <input type="date" id="startDate" name="startDate" required>
        <label for="startDate">Fecha de Inicio:</label>
    </div>
    <div class="datosCreatePoll">
        <input type="date" id="endDate" name="endDate" required>
        <label for="endDate">Fecha de Finalización:</label>
    </div>

    <button class="btnCreatePoll"type="submit">Crear Encuesta</button>
</form>
</div>
<div class="contenedorFooter">
            <?php include 'footer.php'; ?>
        </div>
    <script>
    $(document).ready(function() {
        $('#numOptions').change(function() {
            var numOptions = $(this).val();
            $('#optionInputs').empty();
            for(var i=1; i<=numOptions; i++) {
                $('#optionInputs').append('<label for="option'+i+'"></label><input placeholder="Option '+i+'" type="text" id="option'+i+'" name="option'+i+'" required>');
            }
        });
    });
    </script>
</body>
</html>