<?php
// indicateurs.php

function tauxCR_OK(PDO $pdo, $mois = null, $annee = null, $semaine = null): float {
    $conditions = [];
    $params = [];

    if (!empty($mois)) {
        $conditions[] = "MONTH(`Date Debut Rdv Client`) = :mois";
        $params[':mois'] = $mois;
    }
    if (!empty($annee)) {
        $conditions[] = "YEAR(`Date Debut Rdv Client`) = :annee";
        $params[':annee'] = $annee;
    }
    if (!empty($semaine) && $semaine !== "toutes") {
        $conditions[] = "WEEK(`Date Debut Rdv Client`, 1) = :semaine";
        $params[':semaine'] = $semaine;
    }

    $sql = "
        SELECT  
            SUM(CASE WHEN `Statut Intervention` = 'TERMINEE_OK' THEN 1 ELSE 0 END) AS total_OK,
            SUM(CASE WHEN `Statut Intervention` IN ('TERMINEE_OK', 'TERMINEE_KO') THEN 1 ELSE 0 END) AS total_all
        FROM `sav - taux de cr ok - 1er rdv`
    ";

    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(' AND ', $conditions);
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_OK = $row['total_OK'] ?? 0;
    $total_all = $row['total_all'] ?? 0;

    return ($total_all > 0) ? round(($total_OK / $total_all) * 100, 2) : 0;
}

function DelaiPriseRDVSAV($pdo, $mois = null, $annee = null, $semaine = null): ?float {
    $conditions = [];
    $params = [];

    if (!empty($mois)) {
        $conditions[] = "MONTH(`Date Debut Rdv Client`) = :mois";
        $params[':mois'] = $mois;
    }
    if (!empty($annee)) {
        $conditions[] = "YEAR(`Date Debut Rdv Client`) = :annee";
        $params[':annee'] = $annee;
    }
    if (!empty($semaine) && $semaine !== "toutes") {
        $conditions[] = "WEEK(`Date Debut Rdv Client`, 1) = :semaine";
        $params[':semaine'] = $semaine;
    }

    $sql = "SELECT 
                SUM(CASE WHEN `[CONTRAT_QUALITE_2024]Taux_ds_délais` = '1' THEN 1 ELSE 0 END) AS nb_1,
                SUM(CASE WHEN `[CONTRAT_QUALITE_2024]Taux_ds_délais` = '0' THEN 1 ELSE 0 END) AS nb_0
            FROM `sav - taux de cr ok - 1er rdv`";

    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $nb_1 = $result['nb_1'] ?? 0;
    $nb_0 = $result['nb_0'] ?? 0;
    $total = $nb_1 + $nb_0;

    if ($total > 0) {
        return round(($nb_1 / $total) * 100, 2);
    }
    return null;
}

function SecuRDVSAV($pdo, $mois = null, $annee = null, $semaine = null): ?float {
    $conditions = [];
    $params = [];

    if (!empty($mois)) {
        $conditions[] = "MONTH(`Date Debut Rdv Client`) = :mois";
        $params[':mois'] = $mois;
    }
    if (!empty($annee)) {
        $conditions[] = "YEAR(`Date Debut Rdv Client`) = :annee";
        $params[':annee'] = $annee;
    }
    if (!empty($semaine) && $semaine !== "toutes") {
        $conditions[] = "WEEK(`Date Debut Rdv Client`, 1) = :semaine";
        $params[':semaine'] = $semaine;
    }

    $sql = "SELECT 
                SUM(CASE WHEN `flag_secu_interv_cq2024` = '1' THEN 1 ELSE 0 END) AS nb_1,
                SUM(CASE WHEN `flag_secu_interv_cq2024` = '0' THEN 1 ELSE 0 END) AS nb_0
            FROM `sav - taux de cr ok - 1er rdv`";

    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $nb_1 = $result['nb_1'] ?? 0;
    $nb_0 = $result['nb_0'] ?? 0;
    $total = $nb_1 + $nb_0;

    if ($total > 0) {
        return round(($nb_1 / $total) * 100, 2);
    }
    return null;
}

// function conformiteCRSAV($pdo, $mois = null, $annee = null, $semaine = null): ?float {
//     $conditions = [];
//     $params = [];

//     if (!empty($mois)) {
//         $conditions[] = "MONTH(`Date Debut Rdv Client`) = :mois";
//         $params[':mois'] = $mois;
//     }
//     if (!empty($annee)) {
//         $conditions[] = "YEAR(`Date Debut Rdv Client`) = :annee";
//         $params[':annee'] = $annee;
//     }
//     if (!empty($semaine) && $semaine !== "toutes") {
//         $conditions[] = "WEEK(`Date Debut Rdv Client`, 1) = :semaine";
//         $params[':semaine'] = $semaine;
//     }

//     $sql = "SELECT 
//                 SUM(CASE WHEN `volume Cr non exploitable` = '1' THEN 1 ELSE 0 END) AS nb_1,
//                 SUM(CASE WHEN `volume Cr non exploitable` = '0' THEN 1 ELSE 0 END) AS nb_0
//             FROM `sav - taux de cr ok - 1er rdv`";

//     if (!empty($conditions)) {
//         $sql .= " WHERE " . implode(" AND ", $conditions);
//     }

//     $stmt = $pdo->prepare($sql);
//     $stmt->execute($params);
//     $result = $stmt->fetch(PDO::FETCH_ASSOC);

//     $nb_1 = $result['nb_1'] ?? 0;
//     $nb_0 = $result['nb_0'] ?? 0;
//     $total = $nb_1 + $nb_0;

//     if ($total > 0) {
//         return round(($nb_1 / $total) * 100, 2);
//     }
//     return null;
// }
function clientsTresInsatisfaits($pdo, $mois = null, $annee = null, $semaine = null): ?float {
    $conditions = [];
    $params = [];

    if (!empty($mois)) {
        $conditions[] = "MONTH(`Date Debut Rdv Client`) = :mois";
        $params[':mois'] = $mois;
    }
    if (!empty($annee)) {
        $conditions[] = "YEAR(`Date Debut Rdv Client`) = :annee";
        $params[':annee'] = $annee;
    }
    if (!empty($semaine) && $semaine !== "toutes") {
        $conditions[] = "WEEK(`Date Debut Rdv Client`, 1) = :semaine";
        $params[':semaine'] = $semaine;
    }

    $sql = "SELECT 
                SUM(CASE WHEN `Note Satcli` IN (1, 2) THEN 1 ELSE 0 END) AS total_insatisfaits,
                SUM(CASE WHEN `Note Satcli` IS NOT NULL AND `Note Satcli` <> '' THEN 1 ELSE 0 END) AS total_non_vides
            FROM `sav - taux de cr ok - 1er rdv`";

    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $insatisfaits = $result['total_insatisfaits'] ?? 0;
    $non_vides = $result['total_non_vides'] ?? 0;

    if ($non_vides > 0) {
        return round(($insatisfaits / $non_vides) * 100, 2);
    }
    return 0.0;  // Retourne 0 si pas de données, ou null selon ton besoin
}
