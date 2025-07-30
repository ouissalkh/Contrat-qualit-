
<?php
$servername = "10.10.10.55";
$dbname = "indicateur";
$db_username = "cq_projet";
$db_password = "Z9#k*E)dl*o(0I";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Lister les tables
$sql = "SHOW TABLES";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h2>Contenu de la base de données <em>$dbname</em> :</h2>";

    while($row = $result->fetch_array()) {
        $table = $row[0];
        echo "<h3>Table : <strong>$table</strong></h3>";

        // Récupérer toutes les données de la table
        $table_sql = "SELECT * FROM `$table`";
        $table_result = $conn->query($table_sql);

        if ($table_result->num_rows > 0) {
            echo "<table border='1' cellpadding='5' cellspacing='0'>";
            
            // En-tête (colonnes)
            $fields = $table_result->fetch_fields();
            echo "<tr>";
            foreach ($fields as $field) {
                echo "<th>" . htmlspecialchars($field->name) . "</th>";
            }
            echo "</tr>";

            // Données
            while ($row_data = $table_result->fetch_assoc()) {
                echo "<tr>";
                foreach ($row_data as $value) {
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                }
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "<p>Aucune donnée dans la table.</p>";
        }
    }
} else {
    echo "<p>Aucune table trouvée dans la base de données.</p>";
}

$conn->close();
?>
