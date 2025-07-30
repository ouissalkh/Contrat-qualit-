<?php
$servername = "10.10.10.55";
$dbname = "indicateur";
$db_username = "cq_projet";
$db_password = "Z9#k*E)dl*o(0I";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>