
<?php
$servername = "10.10.10.55";
$dbname = "indicateur";
$db_username = "cq_projet";
$db_password = "Z9#k*E)dl*o(0I";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SHOW TABLES";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h3>Tables dans la base <code>$dbname</code> :</h3><ul>";
    while($row = $result->fetch_row()) {
        echo "<li>" . htmlspecialchars($row[0]) . "</li>";
    }
    echo "</ul>";
} else {
    echo "Aucune table trouvée.";
}

$conn->close();
?>
