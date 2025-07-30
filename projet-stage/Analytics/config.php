<?php
// config.php

$host = "10.10.10.55";
$username = "cq_projet";
$password = "Z9#k*E)dl*o(0I";
$database = "indicateur";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
