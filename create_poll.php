<?php
session_start(); // Iniciar la sesión
// Verificar si la sesión 'email' está establecida
if (!isset($_SESSION['email'])) {
    // Redirigir al usuario a la página de inicio de sesión
    header('Location: errores/error403.php');
    exit();
}

include 'db_connection.php'; 

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
            $_SESSION['error'] = "Has subido un archivo no valido. Solo se permiten archivos JPG, JPEG, PNG y GIF..";
            header('Location: create_poll.php');
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
    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $userId = $stmt->fetchColumn();

    // Determinar el estado de la encuesta
    $currentDate = date("Y-m-d");
    $pollState = "";
        
        // Validar que la fecha de inicio no sea anterior a la fecha actual


    if ($currentDate < $startDate) {
        $pollState = "not_started";
    } elseif ($startDate <= $currentDate && $currentDate <= $endDate) {
        $pollState = "active";
    } else {
        $pollState = "finished";
    }

    // Generar un token único
    $token = bin2hex(random_bytes(16));

    // Insertar la pregunta en la tabla de encuestas con el user_id
    $stmt = $pdo->prepare("INSERT INTO poll (question, user_id, start_date, end_date, poll_state, question_visibility, results_visibility, path_image, poll_token) 
    VALUES (?, ?, ?, ?, ?, NULL, NULL, ?, ?)");
    $stmt->execute([$question, $userId, $startDate, $endDate, $pollState, $imagePath, $token]);

    $pollId = $pdo->lastInsertId();
        
    
    

    // Preparar la consulta para insertar las opciones en la tabla poll_options
    $stmt = $pdo->prepare("INSERT INTO poll_options (poll_id, option_text, start_date, end_date, path_image) VALUES (?, ?, ?, ?, ?)");

        for ($i = 1; $i <= $numOptions; $i++) {
            $option = $_POST["option$i"];
            if (!empty($option)) {
                $target_file = NULL;
                // Manejar la carga de la imagen
                if(isset($_FILES["optionImage$i"]) && $_FILES["optionImage$i"]["error"] == 0){
                    $target_dir = "uploads/";
                    $target_file = $target_dir . time() . "_" . basename($_FILES["optionImage$i"]["name"]);

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
                                $_SESSION['error'] = "Solo se permiten archivos JPG, JPEG, PNG y GIF.";
                                header('Location: create_poll.php');
                                $target_file = NULL;
                                exit();
                                
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

                $stmt->execute([$pollId, $option, $startDate, $endDate, $target_file]);
            }
        }


        // GENERAR EL ARCHIVO PHP DE LA ENCUESTA //

        $pollData = $_POST;

        // Obtener la ruta de la imagen de la base de datos
        $stmt = $pdo->prepare("SELECT path_image FROM poll WHERE poll_id = ?");
        $stmt->execute([$pollId]);
        $imagePath = $stmt->fetchColumn();
        

        
        $phpContent = '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <link rel="stylesheet" href="../styles.css">
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Encuesta ' . $pollId . '</title>
            <style>
            .bodyVota {
                margin: 0;
                padding: 0;
            }

            .bodyVota .imagenCabecera {
                padding: 200px;
                background-image: url("../imgs/votacion.jpg");
            }

            .imagenCabecera h1 {
                margin-bottom: -30px;
                font-family: "Playfair Display", serif;
                font-size: 100px;
                color: #EDF2F4;
                text-align: center;
            }
            .vota {
                text-align: center;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                padding-top: 100px;
                padding-bottom: 100px;
                margin: 0;
               
               
            }

           .vota .options {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 40px;
                justify-items: center;
            }

            
            .options label {
                font-size: 20px !important; /* Aumenta el tamaño del texto de las opciones */
            }

            
            img {
                width: 200px;
                height: 200px;
                padding-top: 10px;
            }
            .imgHeader {
                width: 80px;
                margin: 10px;
                height: 75px;
                transition-duration: 3s;
            }

            .logoimgFooter {
                width: 60px;
                height: 50px;
            }
            h1 {
                margin-bottom: 50px; /* Añade espacio debajo de la pregunta */
                font-family: "Playfair Display", serif; /* Añade el tipo de letra */
            }
            button {
                margin-top: 50px; /* Añade espacio encima del botón */
            }
            #botonEnviar {
                padding: 10px 20px;
                border-radius: 5px;
                background: linear-gradient(45deg, #EF233C 50%, #D80032 50%);
                background-size: 200% 200%;
                background-position: 100%;
                border: none;
                border-radius: 10px;
                font-family: "Lato", sans-serif;
                font-size: 15px;
                color: #EDF2F4;
                cursor: pointer;
                transition: background-position 1s, color 1s;
            }
            </style>
            </head>
       
             <body class="bodyVota">
            <div class="contenedorHeader">
                <?php include "../header.php"; ?>
            </div>

            <div class="contenedor">
            <div class="imagenCabecera">
                <h1>VOTAIETI</h1>
                <h2>Tu elección, nuestro compromiso global</h2>
            </div>

            <div class="vota">
            


            
            <h1 >' . htmlspecialchars($pollData['question']) . '</h1>';

            // Si la encuesta tiene una imagen, añádela
            if ($imagePath) {
                $phpContent .= '<img src="/'. $imagePath.'" alt="Imagen de la pregunta">';
            }
            $phpContent .= '<div class="vota">';
            $phpContent .= '<div class="options">';
            // Añadir las opciones a la encuesta
            for ($i = 1; $i <= $pollData['numOptions']; $i++) {
                $phpContent .= '<div><input type="checkbox" id="option' . $i . '" name="option' . $i . '"><label for="option' . $i . '">' . htmlspecialchars($pollData['option' . $i]) . '</label>';
            
                // Obtener la ruta de la imagen de la opción de la base de datos
                $stmt = $pdo->prepare("SELECT path_image FROM poll_options WHERE poll_id = ? AND option_text = ?");
                $stmt->execute([$pollId, $pollData['option' . $i]]);
                $optionImagePath = $stmt->fetchColumn();
            
                // Si la opción tiene una imagen, añádela
                if ($optionImagePath) {
                    $phpContent .= '<br><img src="/' . $optionImagePath . '" alt="Imagen de la opción ' . $i . '">';
                }
                $phpContent .= '</div>';
            }
            $phpContent .= '</div>'; // Cierre del div de las opciones
            $phpContent .= '<button type="submit" id="botonEnviar">Enviar</button></div>'; // Cierre del div de vota
            $phpContent .= '</div>';
            $phpContent .= '<div class="contenedorFooter">';
            $phpContent .= '<?php include "../footer.php"; ?>';
            $phpContent .= '</div>';
            // Ahora puedes generar el archivo PHP
            file_put_contents('Poll/poll' . $pollId . '.php', $phpContent);
                
        

            //////////////////////////////////////////////////////////////////


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
    

    // Cerrar la conexión
    //$conn->close();
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
    // Restablecer las banderas al inicio
    var isNewOptionSetCreated = false;
    var isNewDateSetCreated = false;
    var isNewButtonCreated = false;

    setupOptionEvents(); // Configurar eventos para el conjunto inicial de campos de opciones

    $('#question').on('input', function() {
        // Restablecer todas las banderas y eliminar elementos si la pregunta está vacía
        if ($('#question').val().trim() === '') {
            $('#optionSet').remove();
            $('#dateStartSet').remove();
            $('#dateEndSet').remove();
            $('#btnCreatePoll').remove();
            isNewOptionSetCreated = false;
            isNewDateSetCreated = false;
            isNewButtonCreated = false;
        }
    });

    $('#question').on('keydown', function(e) {
        if ((e.which === 9 || e.which === 13) && !isNewOptionSetCreated) {
            e.preventDefault();
            var questionValue = $('#question').val();
            if (questionValue.trim() === '') {
                return;
            }
            const newOptionSet = $('<div class="datosCreatePoll" id="optionSet">' +
                '<label id="numeroOpciones">Número de opciones:</label>' +
                '<button type="button" id="addOption">+</button>' +
                '<button type="button" id="removeOption" style="display: none;">-</button>' +
                '<div id="optionInputs"></div>' +
                '</div>');
            $('#pollForm').append(newOptionSet);
            setupOptionEvents();
            isNewOptionSetCreated = true;
            
            // Restablecer la bandera isNewDateSetCreated
            isNewDateSetCreated = false;
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
        if ((e.which === 9 || e.which === 13) && !isNewDateSetCreated) {
            e.preventDefault();
            var option1Value = $('#option1').val();
            var option2Value = $('#option2').val();
            if (option1Value.trim() !== '' && option2Value.trim() !== '') {
                const newDateSet = $('<div class="datosCreatePoll" id="dateStartSet">' +
                    '<input type="date" id="startDate" name="startDate" required>' +
                    '<label for="startDate">Fecha de Inicio:</label>' +
                    '</div>' +
                    '<div class="datosCreatePoll" id="dateEndSet">' +
                    '<input type="date" id="endDate" name="endDate" required>' +
                    '<label for="endDate">Fecha de Finalización:</label>' +
                    '</div>');
                $('#pollForm').append(newDateSet);
                isNewDateSetCreated = true;
                
                // Restablecer la bandera isNewButtonCreated
                isNewButtonCreated = false;
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