<?php
// indicateurs.php

function tauxCR_OK(PDO $pdo, string $whereClause): float {
    $sql = "
        SELECT  
            SUM(CASE WHEN `Statut Intervention` = 'TERMINEE_OK' THEN 1 ELSE 0 END) AS total_OK,
            SUM(CASE WHEN `Statut Intervention` IN ('TERMINEE_OK', 'TERMINEE_KO') THEN 1 ELSE 0 END) AS total_all
        FROM `sav - taux de cr ok - 1er rdv`
        WHERE $whereClause
    ";

    $stmt = $pdo->query($sql);

    if ($stmt) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $total_OK = $row['total_OK'] ?? 0;
        $total_all = $row['total_all'] ?? 0;

        return ($total_all > 0) ? round(($total_OK / $total_all) * 100, 2) : 0;
    } 

    return 0;
}
