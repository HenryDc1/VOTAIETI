<?php
function custom_log($event, $details) {
    // Crear el nombre del archivo con el formato "log_aaaa_mm_dd.txt"
    $logFile = "logs/log_" . date("Y-m-d") . ".txt";

    // Asegurarse de que la carpeta "Logs" exista, si no, crearla
    if (!is_dir("logs")) {
        mkdir("logs");
    }

    // Escribir en el archivo de registro
    $current_time = date("Y-m-d H:i:s");
    $formattedMessage = "$current_time - $event: $details\n";
    file_put_contents($logFile, $formattedMessage, FILE_APPEND);
}
?>
