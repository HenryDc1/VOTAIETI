<?php
session_start();
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["acceptTerms"])) {
        $email = $_SESSION['email'];

        $querystr = "UPDATE users SET conditions_accepted = 1 WHERE email = :email";
        $query = $pdo->prepare($querystr);
        $query->bindParam(':email', $email);
        $query->execute();
        custom_log('Treminos y Condiciones aceptadas', "El usuario $email ha aceptado los terminos y condiciones");


        header('Location: dashboard.php');
        exit;
    }
}
?>
