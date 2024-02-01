<?php
function custom_log($event, $details) {
    $date = date('Y-m-d');
    $logDir = "logs";
    $logFile = "$logDir/log-$date.txt";
    $current_time = date('H:i:s');
    $formattedMessage = "[$date $current_time] $event: $details\n";

    // Crear el directorio logs si no existe
    if (!file_exists($logDir)) {
        mkdir($logDir, 0777, true);
    }

    error_log($formattedMessage, 3, $logFile);
}
?>