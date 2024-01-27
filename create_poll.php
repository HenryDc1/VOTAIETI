<?php
session_start(); // Iniciar la sesión
$conn = new mysqli('localhost', 'root', 'root', 'VOTE');
echo '<script src="js/script.js"></script>';

// Verificar la conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $question = $_POST['question'];
    $numOptions = $_POST['numOptions'];
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];

    // Verificar si se subió un archivo
    $imagePath = NULL;
    if (isset($_FILES['questionImage']) && $_FILES['questionImage']['error'] == 0) {
        // Definir el directorio de destino
        $uploadDir = 'uploads/';

        // Obtener la extensión del archivo
        $extension = strtolower(pathinfo($_FILES['questionImage']['name'], PATHINFO_EXTENSION));

        // Verificar si la extensión del archivo es válida
        $validExtensions = array('jpg', 'jpeg', 'png', 'gif');
        if (!in_array($extension, $validExtensions)) {
            echo 'El archivo subido no es una imagen válida. Solo se permiten archivos JPG, JPEG, PNG y GIF.';
            exit();
        }

        // Crear un nombre único para el archivo
        $filename = uniqid() . '.' . $extension;

        // Mover el archivo al directorio de destino
        if (move_uploaded_file($_FILES['questionImage']['tmp_name'], $uploadDir . $filename)) {
            // Si el archivo se movió con éxito, guardar la ruta en la base de datos
            $imagePath = $uploadDir . $filename;
        } else {
            echo 'Hubo un error al mover el archivo al directorio de destino.';
        }
    } 

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
            $currentDate = date("Y-m-d");
            $pollState = "";
        
            // Validar que la fecha de inicio no sea anterior a la fecha actual
            if ($startDate < $currentDate) {
                echo "La fecha de inicio de la encuesta no puede ser anterior a la fecha actual.";
                exit();
                
            }

            if ($currentDate < $startDate) {
                $pollState = "not_started";
            } elseif ($startDate <= $currentDate && $currentDate <= $endDate) {
                $pollState = "active";
            } else {
                $pollState = "finished";
            }

            // Insertar la pregunta en la tabla de encuestas con el user_id
            $stmt = $conn->prepare("INSERT INTO poll (question, user_id, start_date, end_date, poll_state, question_visibility, results_visibility, poll_link, path_image) 
            VALUES (?, ?, ?, ?, ?, NULL, NULL, NULL, ?)");
            $stmt->bind_param("ssssss", $question, $userId, $startDate, $endDate, $pollState, $imagePath);
            $stmt->execute();

            // Obtener el ID de la encuesta que acabamos de insertar
            $pollId = $stmt->insert_id;

            // Preparar la consulta para insertar las opciones en la tabla poll_options
            $stmt = $conn->prepare("INSERT INTO poll_options (poll_id, option_text, start_date, end_date, path_image) VALUES (?, ?, ?, ?, ?)");

            

            for ($i = 1; $i <= $numOptions; $i++) {
                $option = $_POST["option$i"];
                if (!empty($option)) {
                    $target_file = NULL;
                    // Manejar la carga de la imagen
                    if(isset($_FILES["optionImage$i"]) && $_FILES["optionImage$i"]["error"] == 0){
                        $target_dir = "uploads/";
                        $target_file = $target_dir . basename($_FILES["optionImage$i"]["name"]);
                        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
            
                        // Verificar si el archivo ya existe
                        if (!file_exists($target_file)) {
                            // Verificar el tamaño del archivo
                            if ($_FILES["optionImage$i"]["size"] < 500000) {
                                // Permitir ciertos formatos de archivo
                                if($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif" ) {
                                    // Intentar mover el archivo subido al directorio de destino
                                    if (!move_uploaded_file($_FILES["optionImage$i"]["tmp_name"], $target_file)) {
                                        echo "Hubo un error al subir el archivo.";
                                        $target_file = NULL;
                                    }
                                } else {
                                    echo "Solo se permiten archivos JPG, JPEG, PNG y GIF.";
                                    $target_file = NULL;
                                }
                            } else {
                                echo "El archivo es demasiado grande.";
                                $target_file = NULL;
                            }
                        } else {
                            echo "El archivo ya existe.";
                            $target_file = NULL;
                        }
                    }
            
                    $stmt->bind_param("issss", $pollId, $option, $startDate, $endDate, $target_file);
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
                        style: 'position: fixed; top: 20%; left: 50%; transform: translate(-50%, -50%); background-color: green; color: white; padding: 20px; border-radius: 5px;'
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
                    showSuccesPopup('La encuesta ha sido registrada con éxito');
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
?><!DOCTYPE html>
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
    <!-- Campo oculto para almacenar el número de opciones -->
    <input type="hidden" id="numOptions" name="numOptions" value="0">
    </form>
</div>
<div class="contenedorFooter">
            <?php include 'footer.php'; ?>
        </div>
    <script>
    $(document).ready(function() {
        var optionCount = 0;

        // Generar el campo de "Pregunta" dinámicamente
        $('#pollForm').append('<div class="datosCreatePoll"><input type="text" id="question" name="question" required><label for="question">Pregunta:</label><input type="file" id="questionImage" name="questionImage"></div>');

        $('#question').on('keydown', function(e) {
            if(e.which == 13 || e.which == 9) {
                if($(this).val().trim() !== '') {
                    if(optionCount == 0) {
                        $('#pollForm').append('<div class="datosCreatePoll" id="optionsDiv"><label id="numeroOpciones">Opciones:</label><button type="button" id="removeOption" style="display: none;">-</button><button type="button" id="addOption">+</button><div id="optionInputs"></div></div>');
                        $('#pollForm').append('<div class="datosCreatePoll" id="datesDiv"><input type="date" id="startDate" name="startDate" required><label for="startDate">Fecha de Inicio y Finalización:</label><input type="date" id="endDate" name="endDate" required><label for="endDate"></label></div>');
                        $('#pollForm').append('<button class="btnCreatePoll"type="submit" id="submitBtn">Crear Encuesta</button>');
                        addOption();
                        addOption();
                        $('#addOption').click(function() {
                            addOption();
                        });
                        $('#removeOption').click(function() {
                            if(optionCount > 2) {
                                $('#option'+optionCount).remove();
                                optionCount--;
                                if(optionCount == 2) {
                                    $('#removeOption').hide();
                                }
                            }
                            // Actualizar el valor de 'numOptions'
                            $('#numOptions').val(optionCount);
                        });
                    }
                }
            }
        });

        function addOption() {
            optionCount++;
            $('#optionInputs').append('<input placeholder="Opción '+optionCount+'" type="text" id="option'+optionCount+'" name="option'+optionCount+'" required>');
            $('#optionInputs').append('<input type="file" id="optionImage'+optionCount+'" name="optionImage'+optionCount+'">');
            if(optionCount > 2) {
                $('#removeOption').show();
            }
            // Actualizar el valor de 'numOptions'
            $('#numOptions').val(optionCount);
        }
    });
    
    </script>
</body>
</html>

