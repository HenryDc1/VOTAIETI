<?php
session_start(); // Iniciar la sesión
if (!isset($_SESSION['email'])) {
    // Si el usuario no ha iniciado sesión, redirige a la página de error
    header('Location: errores/error403.php');
    exit;
}

// Incluir el archivo de conexión
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $question = $_POST['question'];
    $numOptions = $_POST['numOptions'];
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];

    // Obtener el email de la sesión
    if (isset($_SESSION['email'])) {
        $email = $_SESSION['email'];

        // Consulta para obtener el user_id
        $selectStmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
        $selectStmt->execute([$email]);
        $userId = $selectStmt->fetchColumn();

        if ($userId) {

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
            $stmt = $pdo->prepare("INSERT INTO poll (question, user_id, start_date, end_date, poll_state, question_visibility, results_visibility, poll_link, path_image) 
                                    VALUES (?, ?, ?, ?, ?, NULL, NULL, NULL, NULL)");
            $stmt->execute([$question, $userId, $startDate, $endDate, $pollState]);

            // Obtener el ID de la encuesta que acabamos de insertar
            $pollId = $pdo->lastInsertId();

            // Preparar la consulta para insertar opciones
            $stmt = $pdo->prepare("INSERT INTO poll_options (poll_id, option_text, start_date, end_date, path_image) VALUES (?, ?, ?, ?, NULL)");

            for ($i = 2; $i <= $numOptions; $i++) {
                $option = $_POST["option$i"];
                if (!empty($option)) {
                    $stmt->execute([$pollId, $option, $startDate, $endDate]);
                }
            }

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
    $pdo = null;
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
        <form class="createPoll" id="pollForm" method="post" action="create_poll.php" enctype="multipart/form-data">
            <h1 class="tituloCreatePoll">Crear Encuesta</h1>
            <div class="datosCreatePoll" id="questionContainer">
                <input type="text" id="question" name="question" required>
                <label for="question">Pregunta:</label>
                <input type="file" id="questionImage" name="questionImage" accept="image/*">
            </div>
        </form>
    </div>
    <div class="contenedorFooter">
        <?php include 'footer.php'; ?>
    </div>
    <script>
        $(document).ready(function() {
            // Variable para verificar si el elemento newOptionSet ya se creó
            var isNewOptionSetCreated = false;
            // Variable para verificar si el elemento newDateSet ya se creó
            var isNewDateSetCreated = false;

            var isNewButtonCreated = false;

            // Configurar eventos para el conjunto inicial de campos de opciones
            setupOptionEvents();

            // Manejar cambios en el campo de pregunta para borrar #optionSet si está vacío
            $('#question').on('input', function() {
                if ($('#question').val().trim() === '') {
                    $('#optionSet').remove();
                    $('#dateStartSet').remove();
                    $('#dateEndSet').remove();
                    $('#btnCratePoll').remove();
                    isNewOptionSetCreated = false; // Restablecer la bandera
                    isNewDateSetCreated = false; // Restablecer la bandera
                    isNewButtonCreated = false;

                }
            });

            // Manejar la pulsación de tecla en el campo de pregunta
            $('#question').on('keydown', function(e) {
                if (e.which === 9 || e.which === 13 && !isNewOptionSetCreated) { // TAB key code y verificar si aún no se ha creado
                    e.preventDefault();

                    // Obtener el valor del campo de pregunta
                    var questionValue = $('#question').val();

                    // Verificar si el campo de pregunta está vacío
                    if (questionValue.trim() === '') {
                        return;
                    }

                    // Crear el elemento y agregarlo al contenedor
                    const newOptionSet = $(
                        '<div class="datosCreatePoll" id="optionSet">' +
                        '<label id="numeroOpciones">Número de opciones:</label>' +
                        '<button type="button" id="addOption">+</button>' +
                        '<button type="button" id="removeOption" style="display: none;">-</button>' +
                        '<div id="optionInputs"></div>' +
                        '</div>'
                    );

                    $('#pollForm').append(newOptionSet);

                    // Configurar eventos para el nuevo conjunto de campos de opciones
                    setupOptionEvents();

                    // Marcar que el elemento newOptionSet ya se creó
                    isNewOptionSetCreated = true;
                }
            });

            function setupOptionEvents() {
                var numOptions = 2;

                $('#optionInputs').empty(); // Limpiar los campos de opciones existentes

                for (var i = 1; i <= numOptions; i++) {
                    $('#optionInputs').append(
                        '<label for="option' + i + '"></label>' +
                        '<input placeholder="Option ' + i + '" type="text" id="option' + i + '" name="option' + i + '" required>' +
                        '<input type="file" id="optionImage' + i + '" name="optionImage' + i + '" accept="image/*">'
                    );
                }

                $('#addOption').click(function() {
                    numOptions++;
                    $('#optionInputs').append(
                        '<label for="option' + numOptions + '"></label>' +
                        '<input placeholder="Option ' + numOptions + '" type="text" id="option' + numOptions + '" name="option' + numOptions + '" required>' +
                        '<input type="file" id="optionImage' + numOptions + '" name="optionImage' + numOptions + '" accept="image/*">'
                    );
                    if (numOptions > 2) {
                        $('#removeOption').show();
                    }
                });

                $('#removeOption').click(function() {
                    if (numOptions > 2) {
                        $('#option' + numOptions).remove();
                        $('#optionImage' + numOptions).remove();
                        $('label[for="option' + numOptions + '"]').remove();
                        numOptions--;
                        if (numOptions <= 2) {
                            $('#removeOption').hide();
                        }
                    }
                });
            }

            $(document).on('keydown', '#optionInputs input[type="text"]', function(e) {
                if (e.which === 9 || e.which === 13 &&  !isNewDateSetCreated) { // TAB key code y verificar si aún no se ha creado
                    e.preventDefault();

                    // Obtener el valor de los campos de opciones
                    var option1Value = $('#option1').val();
                    var option2Value = $('#option2').val();

                    // Verificar si ambos campos de opciones están llenos
                    if (option1Value.trim() !== '' && option2Value.trim() !== '') {
                        // Crear el elemento de fecha y agregarlo al contenedor
                        const newDateSet = $(
                            '<div class="datosCreatePoll" id="dateStartSet">' +
                            '<input type="date" id="startDate" name="startDate" required>' +
                            '<label for="startDate">Fecha de Inicio:</label>' +
                            '</div>' +
                            '<div class="datosCreatePoll" id="dateEndSet">' +
                            '<input type="date" id="endDate" name="endDate" required>' +
                            '<label for="endDate">Fecha de Finalización:</label>' +
                            '</div>'
                        );

                        $('#pollForm').append(newDateSet);

                        // Marcar que el elemento newDateSet ya se creó
                        isNewDateSetCreated = true;
                    }
                }
            });

            $(document).on('input change', '#optionInputs input[type="text"], #startDate, #endDate', function() {
                var option1Value = $('#option1').val();
                var option2Value = $('#option2').val();
                var startDateValue = $('#startDate').val();
                var endDateValue = $('#endDate').val();

                if (option1Value.trim() === '' && option2Value.trim() === '' && startDateValue.trim() === '' && endDateValue.trim() === '') {
                    $('#dateStartSet').remove();
                    $('#dateEndSet').remove();
                    $('#btnCreatePoll').remove();
                    isNewDateSetCreated = false;
                    isNewButtonCreated = false;
                } else if (startDateValue.trim() === '' || endDateValue.trim() === '') {
                    $('#btnCreatePoll').remove();
                    isNewButtonCreated = false;
                }
            });

            // Crear el botón al cambiar el valor de endDate
            $(document).on('change', '#startDate', function() {
                var startDateValue = $('#startDate').val();
                var endDateValue = $('#endDate').val();

                // Verificar si el campo de fecha de inicio está vacío
                if (startDateValue.trim() !== '' && endDateValue.trim() !== '' && !isNewButtonCreated) {
                    
                    // Crear el elemento del botón y agregarlo al contenedor
                    const newButton = $('<button class="btnCreatePoll" id="btnCreatePoll" type="submit">Crear Encuesta</button>');

                    $('#pollForm').append(newButton);

                    // Marcar que el elemento newButton ya se creó
                    isNewButtonCreated = true;
                    /*
                    if(endDateValue<startDateValue){
                        alert('Error la fecha de finalizacion es mayor que la de inicio')
                        $('#btnCreatePoll').remove();
                        return;
                    }
                    */
                }
                
            });

            // Crear el botón al cambiar el valor de endDate
            $(document).on('change', '#endDate', function() {
                if (!isNewButtonCreated) {
                    // Obtener el valor del campo de fecha de finalización
                    var endDateValue = $('#endDate').val();
                    var startDateValue = $('#startDate').val();
                    console.log('-----> '+ endDate)

                if (startDateValue.trim() !== '' && endDateValue.trim() !== '' && !isNewButtonCreated) {
                    
                    // Crear el elemento del botón y agregarlo al contenedor
                    const newButton = $('<button class="btnCreatePoll" id="btnCreatePoll" type="submit">Crear Encuesta</button>');

                    $('#pollForm').append(newButton);

                    // Marcar que el elemento newButton ya se creó
                    isNewButtonCreated = true;
                    /*
                    if(endDateValue<startDateValue){
                        alert('Error la fecha de finalizacion es mayor que la de inicio')
                        $('#btnCreatePoll').remove();
                        return;
                    }
                    */
                }
                    /*
                    else if(endDateValue<startDateValue){
                        alert('Error la fecha de finalizacion es mayor que la de inicio')
                        $('#btnCreatePoll').remove();
                        return;
                    }
                    */
                    // Crear el elemento del botón y agregarlo al contenedor
                
                }
            });
        });
    </script>
</body>

</html>