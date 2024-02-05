<?php
session_start();
include 'db_connection.php';
include 'log_function.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["acceptTerms"])) {
        $email = $_SESSION['email'];

        $querystr = "UPDATE users SET conditions_accepted = 1 WHERE email = :email";
        $query = $pdo->prepare($querystr);
        $query->bindParam(':email', $email);
        $query->execute();
        custom_log('TERMINOS Y CONDICIONES', "El usuario $email ha aceptado los terminos y condiciones");


        header('Location: dashboard.php');
        exit;
    }
}
?>
