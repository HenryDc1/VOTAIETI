<?php
function custom_log($event, $details) {
    $date = date('Y-m-d');
    $logFile = "../logs/log-$date.txt";
    $current_time = date('H:i:s');
    $formattedMessage = "[$date $current_time] $event: $details\n";
    error_log($formattedMessage, 3, $logFile);
}
?>
