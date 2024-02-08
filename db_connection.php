<?php
$countries = [];

try {
    $hostname = "localhost";
    $dbname = "VOTE";
    $username = "aws21";
    $password = "";

    $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);

    // Consulta para obtener los países y sus prefijos
    $stmt = $pdo->prepare('SELECT paisnombre, paisprefijo FROM pais ORDER BY paisnombre ASC');
    $stmt->execute();

    $countries = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Failed to get DB handle: " . $e->getMessage() . "\n";
    exit;
}