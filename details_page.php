<?php
session_start();
include 'db_connection.php'; // Incluye el archivo de conexión a la base de datos
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Otros metadatos aquí -->
    <title>Votaieti</title>
    <link rel="shortcut icon" href="../imgs/logosinfondo.png" />
    <link rel="stylesheet" href="styles.css">
    <script src="../styles + scripts/script.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
</head>

<body class="bodyIndex">
    <div class="contenedorHeader">
        <?php include 'header.php'; ?>
    </div>

    <div class="imagenCabecera">
        <h1>VOTAIETI</h1>
        <h2>Detalles</h2>
    </div>
    <div class="contenedorPreguntaChart">
        <?php

        if (isset($_POST['poll_id'])) {
            $pollId = $_POST['poll_id'];

            try {
                $sqlQuestion = "SELECT question, start_date, end_date, path_image FROM poll WHERE poll_id = :poll_id";
                $stmtQuestion = $pdo->prepare($sqlQuestion);
                $stmtQuestion->bindParam(':poll_id', $pollId, PDO::PARAM_INT);
                $stmtQuestion->execute();

                $resultadosQuestion = $stmtQuestion->fetch(PDO::FETCH_ASSOC);

                if ($resultadosQuestion) {
                    echo '<div style="text-align: center;">
                        <table class="pregunta" style="width: 900px; margin: auto;">
                            <tr>
                                <th style="border: none; min-width: 600px;display: flex;">Pregunta</th>
                                <th style="border: none;">Fecha de inicio</th>
                                <th style="border: none;">Fecha de fin</th>
                                <th style="border: none;">Foto</th>
                            </tr>
                            <tr>
                                <td style="border: none; max-width: 800px;">' . $resultadosQuestion['question'] . '</td>
                                <td style="border: none;">' . $resultadosQuestion['start_date'] . '</td>
                                <td style="border: none;">' . $resultadosQuestion['end_date'] . '</td>
                                <td style="border: none;">';

                    if (!empty($resultadosQuestion['path_image'])) {
                        echo '<a href="/' . $resultadosQuestion['path_image'] . '" target="_blank">Ver foto</a>';
                    } else {
                        echo '-';
                    }

                    echo '</td>
                            </tr>
                        </table>
                    </div>
                    <div id="divImgDetailsPage" style="text-align: center; margin-top: 20px;">';

                    echo '</div>';

                    $sqlOptions = "SELECT option_text, option_id FROM poll_options WHERE poll_id = :poll_id";
                    $stmtOptions = $pdo->prepare($sqlOptions);
                    $stmtOptions->bindParam(':poll_id', $pollId, PDO::PARAM_INT);
                    $stmtOptions->execute();

                    $options = $stmtOptions->fetchAll(PDO::FETCH_ASSOC);

                    if ($options) {
                        // Ahora, construir el gráfico con los datos obtenidos
                        echo "<div id='divH1' syle='width: 900px;'><h1 > Grafico de la encuesta</div></h1>";
                        echo "<div class='chart_div' id='chart_div'></div>";
                        echo "<script type='text/javascript'>";
                        echo "google.charts.load('current', {'packages':['bar']});";
                        echo "google.charts.setOnLoadCallback(drawStuff);";
                        echo "function drawStuff() {";
                        echo "var data = new google.visualization.arrayToDataTable([";
                        echo "['Option', 'Votes'],";
                        foreach ($options as $option) {
                            $sqlVoteCount = "SELECT COUNT(*) AS vote_count FROM user_vote WHERE poll_id = :poll_id AND option_id = :option_id";
                            $stmtVoteCount = $pdo->prepare($sqlVoteCount);
                            $stmtVoteCount->bindParam(':poll_id', $pollId, PDO::PARAM_INT);
                            $stmtVoteCount->bindParam(':option_id', $option['option_id'], PDO::PARAM_INT);
                            $stmtVoteCount->execute();
                            $voteCount = $stmtVoteCount->fetchColumn();

                            echo '["' . $option['option_text'] . '", ' . $voteCount . '],';
                        }
                        echo "]);";
                        echo "var options = {";
                        echo "width: 900,";
                        echo "legend: { position: 'none' },";
                        echo "chart: { title: 'Resultados encuesta', subtitle: 'Votos por opción' },";
                        echo "axes: { x: { 0: { side: 'top', label: 'Opciones' } } },";
                        echo "bar: { groupWidth: '90%' }";
                        echo "};";
                        echo "var chart = new google.charts.Bar(document.getElementById('chart_div'));";
                        echo "chart.draw(data, google.charts.Bar.convertOptions(options));";
                        echo "}";
                        echo "</script>";
                    } else {
                        echo "No se encontraron opciones para el poll_id proporcionado.";
                    }
                } else {
                    echo "No se encontraron resultados para el poll_id proporcionado.";
                }
            } catch (PDOException $e) {
                echo "Error en la conexión: " . $e->getMessage();
            }
        } else {
            echo "La variable de formulario 'poll_id' no está definida.";
        }
        ?>
    </div>
</body>
</html>
