<?php
$host = "localhost";
$dbname = "u774715873_sohail";
$username = "u774715873_sohail";
$password = "Sohail123tygh#";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}
?> 