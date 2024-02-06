<?php
session_start();
include 'db_connection.php'; // Include your database connection script

include 'log_function.php';
// Verify if the guest email session is started
if (!isset($_SESSION['guest_email']) || empty($_SESSION['guest_email'])) {
    // Redirect the user to the error page
    header("Location: errores/error403.php");
    custom_log('Permiso Denegado', "Se ha intentado acceder a la página de procesar los votos");

    exit;
}

$pollId = $_POST['poll_id']; // Get the poll id
$pollOption = $_POST['pollOption']; // Get the selected poll option
$guestEmail = $_SESSION['guest_email']; // Get the guest email from the session

if (isset($_POST['pwd'])){
    $enteredPwd = $_POST['pwd'];
} else {
    $enteredPwd = "B!nari0";
}

// Check if the guest email exists in the users table
$stmt = $pdo->prepare("SELECT user_id, password FROM users WHERE email = ?");
$stmt->execute([$guestEmail]);
$user = $stmt->fetch();

$stmtCount = $pdo->prepare("SELECT COUNT(*) FROM user_vote");
$stmtCount->execute();
$count = $stmtCount->fetchColumn();

if ($user) {
    // Verify the entered password
    $hashedPwd = hash('sha256', $enteredPwd);
    if ($hashedPwd === $user['password']) {
        // The user is registered, save their vote as 'registered'
        $stmt = $pdo->prepare("INSERT INTO user_vote (user_id, poll_id, user_type, guest_email, hash_id) VALUES (?,?,'registered',?,?)");
        $stmt->execute([$user['user_id'],$pollId,$guestEmail,$count+1]);

        // Encrypt the password and insert it into the voted_option table
        $hash = openssl_encrypt($count, 'AES-128-CBC', $enteredPwd);
        $stmt = $pdo->prepare("INSERT INTO voted_option (option_id,hash) VALUES (?,?)");
        $stmt->execute([$pollOption,$hash]);
    } else {
        // Password is incorrect, redirect to the poll page
        error_log("Password verification failed. Entered password: $enteredPwd, Hashed password: {$user['password']}");

        header("Location: Poll/poll$pollId.php");
        exit;
    }
} else {
    // The user is a guest, save their vote as 'guest'
    $stmt = $pdo->prepare("INSERT INTO user_vote (guest_email, poll_id, user_type, hash_id) VALUES (?, ?, 'guest', ?)");
    $stmt->execute([$guestEmail,$pollId,$count+1]);

    // Encrypt the password and insert it into the voted_option table
    $pwd = "B!nari0"; // Set the default password
    $hash = openssl_encrypt($count, 'AES-128-CBC', $pwd);
    $stmt = $pdo->prepare("INSERT INTO voted_option (option_id,hash) VALUES (?,?)");
    $stmt->execute([$pollOption,$hash]);
}

// Store the success message in a session variable
$_SESSION['message'] = "Su voto ha sido enviado con éxito.";
custom_log('ESTADO VOTO', "Se ha enviado el voto con éxito");

// Redirect the user to index.php
header("Location: index.php");
exit;
?>