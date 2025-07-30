<?php

/*$host = "10.10.10.55";
$dbname = "indicateur";
$username = "cq_projet";
$password = "Z9#k*E)dl*o(0I";

try {
    // Connexion à la base de données
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Requête SQL (table avec nom spécial, entourée de backticks)
    $sql = 'SELECT * FROM `sav - taux de cr ok - 1er rdv`';
    $stmt = $pdo->query($sql);

    // Affichage des résultats dans un tableau HTML
    echo "<table border='1'>";
    echo "<tr>";

    // En-têtes
    for ($i = 0; $i < $stmt->columnCount(); $i++) {
        $col = $stmt->getColumnMeta($i);
        echo "<th>" . htmlspecialchars($col['name']) . "</th>";
    }
    echo "</tr>";

    // Données
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }

    echo "</table>";
} catch (PDOException $e) {
    echo "Erreur de connexion ou d'exécution : " . $e->getMessage();
}*/


$host = "10.10.10.55";
$dbname = "indicateur";
$username = "cq_projet";
$password = "Z9#k*E)dl*o(0I";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Définir le mode d'erreur
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>