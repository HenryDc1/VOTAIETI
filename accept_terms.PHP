<?php
session_start();
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['acceptTerms'])) {
        $email = $_SESSION['email'];

        $sql = "UPDATE users SET conditions_accepted = TRUE WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);

        header("Location: login.php");
        exit;
    }
}
?>