<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=ç, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Pagina de detalles </h1>
    <?php
try {
    // Configura la conexión a la base de datos
    include 'db_connection.php';

    // Prepara la consulta SQL
    $consulta = "SELECT poll_id, question, start_date, end_date, poll_state FROM poll where ";

    // Ejecuta la consulta
    $resultado = $pdo->query($consulta);

    // Recorre los resultados
    while ($fila = $resultado->fetch(PDO::FETCH_ASSOC)) {
        // Imprime los campos que solicitaste
        echo "poll_id: " . $fila['poll_id'] . "<br>";
        echo "question: " . $fila['question'] . "<br>";
        echo "start_date: " . $fila['start_date'] . "<br>";
        echo "end_date: " . $fila['end_date'] . "<br>";
        echo "poll_state: " . $fila['poll_state'] . "<br>";
        echo "------------------------<br>";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Cierra la conexión
$pdo = null;
?>
</body>
</html>