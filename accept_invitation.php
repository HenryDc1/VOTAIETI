<?php
// Start the session
session_start();
include 'log_function.php';
include 'db_connection.php'; 

if(isset($_GET['token'])) {
    $token = $_GET['token'];

    // Search for the token in the invitation table
    $sql = "SELECT poll_id, guest_email, blocked FROM invitation WHERE token = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$token]);
    if ($stmt->rowCount() > 0) {
        $invitation = $stmt->fetch();

        // Check if the invitation is blocked
        if ($invitation['blocked']) {
            // The invitation is blocked, redirect to error page
            header("Location: errores/error403.php");
            custom_log('PERMISO DENEGADO', "Se ha intentado acceder a una encuesta bloqueada");
            exit;
        }

        // Save the guest_email in a variable
        $guest_email = $invitation['guest_email'];
        // Save the guest_email in a session variable
        $_SESSION['guest_email'] = $invitation['guest_email'];

        // Check if the user has already voted
        $sql = "SELECT * FROM user_vote WHERE poll_id = ? AND guest_email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$invitation['poll_id'], $guest_email]);
        if ($stmt->rowCount() > 0) {
            // The user has already voted, redirect to error page
            header("Location: errores/error403.php");
            custom_log('PERMISO DENEGADO', "Se ha intentado acceder de nuevo al enlace una vez votado");
            exit;
        }

        // Update the token_accepted field in the invitation table
        $sql = "UPDATE invitation SET token_accepted = 1 WHERE token = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$token]);
        custom_log('INVITACION ACEPTADA', "Se ha aceptado la invitación con éxito");

        // Redirect the user to the poll page in the Poll folder
        header("Location: Poll/poll" . $invitation['poll_id'] . ".php");
        exit;
    } else {
        echo "Invalid token.";
    }
} else {
    echo "Token not provided.";
}
?>