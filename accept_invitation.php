<?php
include 'db_connection.php'; 

if(isset($_GET['poll_token'])) {
    $pollToken = $_GET['poll_token'];

    // Search for the token in the poll table
    $sql = "SELECT poll_id FROM poll WHERE poll_token = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$pollToken]);
    if ($stmt->rowCount() > 0) {
        $poll = $stmt->fetch();

        // Redirect the user to the poll page
        header("Location: poll" . $poll['poll_id'] . ".php");
        exit;
    } else {
        echo "Invalid token.";
    }
} else {
    echo "Token not provided.";
}
?>