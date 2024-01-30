<?php
session_start();
include 'db_connection.php'; // Incluye el archivo de conexión a la base de datos
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow">
    <meta name="keywords" content="votaieti, votación en línea, votación, encuestas, elecciones, privacidad, seguridad">
    <meta name="description" content="Plataforma de votación en línea comprometida con la privacidad y seguridad de los usuarios. Regístrate ahora y participa en encuestas y elecciones de manera segura.">
    <meta property="og:title" content="Votaieti">
    <meta property="og:description" content="Plataforma de votación en línea comprometida con la privacidad y seguridad de los usuarios. Regístrate ahora y participa en encuestas y elecciones de manera segura.">
    <meta property="og:image" content="../imgs/votaietilogo.png">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="author" content="Arnau Mestre, Claudia Moyano i Henry Doudo">
    <title>Votaieti</title>
    <link rel="shortcut icon" href="../imgs/logosinfondo.png" />
    <link rel="stylesheet" href="styles.css">
    <script src="../styles + scripts/script.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
</head>

<body>
    <div class="contenedorHeader">
        <?php include 'header.php'; ?>
    </div>
    <div class="contenedorPreguntaChart">
    <?php

if (isset($_POST['poll_id'])) {
    $pollId = $_POST['poll_id'];

    try {
        $sqlQuestion = "SELECT question, start_date, end_date FROM poll WHERE poll_id = :poll_id";
        $stmtQuestion = $pdo->prepare($sqlQuestion);
        $stmtQuestion->bindParam(':poll_id', $pollId, PDO::PARAM_INT);
        $stmtQuestion->execute();

        $resultadosQuestion = $stmtQuestion->fetch(PDO::FETCH_ASSOC);

        if ($resultadosQuestion) {
            echo "<div class='pregunta'><h2>Pregunta: " . $resultadosQuestion['question'] . " - Fecha de inicio: " . $resultadosQuestion['start_date'] . " - Fecha de fin: " . $resultadosQuestion['end_date'] . "</h2></div>";

            $sqlOptions = "SELECT option_text, option_id FROM poll_options WHERE poll_id = :poll_id";
            $stmtOptions = $pdo->prepare($sqlOptions);
            $stmtOptions->bindParam(':poll_id', $pollId, PDO::PARAM_INT);
            $stmtOptions->execute();

            $options = $stmtOptions->fetchAll(PDO::FETCH_ASSOC);

            if ($options) {
                echo "<div class='opciones'><h3>Opciones:</h3><ul>";
                foreach ($options as $option) {
                    // Hacer la consulta directamente dentro del bucle
                    $sqlVoteCount = "SELECT COUNT(*) AS vote_count FROM user_vote WHERE poll_id = :poll_id AND option_id = :option_id";
                    $stmtVoteCount = $pdo->prepare($sqlVoteCount);
                    $stmtVoteCount->bindParam(':poll_id', $pollId, PDO::PARAM_INT);
                    $stmtVoteCount->bindParam(':option_id', $option['option_id'], PDO::PARAM_INT);
                    $stmtVoteCount->execute();
                    $voteCount = $stmtVoteCount->fetchColumn();

                    echo "<li>" . $option['option_text'] . " - Votos: " . $voteCount . "</li>";
                }
                echo "</ul></div>";

                // Ahora, construir el gráfico con los datos obtenidos
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
                echo "width: 800,";
                echo "legend: { position: 'none' },";
                echo "chart: { title: 'Poll Results', subtitle: 'Votes per option' },";
                echo "axes: { x: { 0: { side: 'top', label: 'Options' } } },";
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
