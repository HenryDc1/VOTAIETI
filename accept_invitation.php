<?php
// Start the session
session_start();


include 'db_connection.php'; 

if(isset($_GET['token'])) {
    $token = $_GET['token'];

    // Search for the token in the invitation table
    $sql = "SELECT poll_id, guest_email FROM invitation WHERE token = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$token]);
    if ($stmt->rowCount() > 0) {
        $invitation = $stmt->fetch();

        // Save the guest_email in a variable
        $guest_email = $invitation['guest_email'];
        // Save the guest_email in a session variable
        $_SESSION['guest_email'] = $invitation['guest_email'];

        // Update the token_accepted field in the invitation table
        $sql = "UPDATE invitation SET token_accepted = 1 WHERE token = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$token]);

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