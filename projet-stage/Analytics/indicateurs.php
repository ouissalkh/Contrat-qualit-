<?php
// indicateurs.php

function tauxCR_OK(PDO $pdo, int $mois, int $annee): float {
    $sql = "
        SELECT  
            SUM(CASE WHEN `Statut Intervention` = 'TERMINEE_OK' THEN 1 ELSE 0 END) AS total_OK,
            SUM(CASE WHEN `Statut Intervention` IN ('TERMINEE_OK', 'TERMINEE_KO') THEN 1 ELSE 0 END) AS total_all
        FROM `sav - taux de cr ok - 1er rdv`
        WHERE MONTH(`Date Debut Rdv Client`) = :mois AND YEAR(`Date Debut Rdv Client`) = :annee
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':mois' => $mois, ':annee' => $annee]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $total_OK = $row['total_OK'] ?? 0;
    $total_all = $row['total_all'] ?? 0;

    return ($total_all > 0) ? round(($total_OK / $total_all) * 100, 2) : 0;
}

function SecuRDVSAV(PDO $pdo, int $mois, int $annee): ?float {
    $sql = "
        SELECT 
            SUM(CASE WHEN `flag_secu_interv_cq2024` = '1' THEN 1 ELSE 0 END) AS nb_1,
            SUM(CASE WHEN `flag_secu_interv_cq2024` = '0' THEN 1 ELSE 0 END) AS nb_0
        FROM `sav - taux de cr ok - 1er rdv`
        WHERE MONTH(`Date Debut Rdv Client`) = :mois AND YEAR(`Date Debut Rdv Client`) = :annee
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':mois' => $mois, ':annee' => $annee]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $nb_1 = $result['nb_1'] ?? 0;
    $nb_0 = $result['nb_0'] ?? 0;
    $total = $nb_1 + $nb_0;

    return ($total > 0) ? round(($nb_1 / $total) * 100, 2) : null;
}

function DelaiPriseRDVSAV(PDO $pdo, int $mois, int $annee): ?float {
    $sql = "
        SELECT 
            SUM(CASE WHEN `[CONTRAT_QUALITE_2024]Taux_ds_délais` = '1' THEN 1 ELSE 0 END) AS nb_1,
            SUM(CASE WHEN `[CONTRAT_QUALITE_2024]Taux_ds_délais` = '0' THEN 1 ELSE 0 END) AS nb_0
        FROM `sav - taux de cr ok - 1er rdv`
        WHERE MONTH(`Date Debut Rdv Client`) = :mois AND YEAR(`Date Debut Rdv Client`) = :annee
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':mois' => $mois, ':annee' => $annee]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $nb_1 = $result['nb_1'] ?? 0;
    $nb_0 = $result['nb_0'] ?? 0;
    $total = $nb_1 + $nb_0;

    return ($total > 0) ? round(($nb_1 / $total) * 100, 2) : null;
}

function clientsTresInsatisfaits(PDO $pdo, int $mois, int $annee): ?float {
    $sql = "
        SELECT 
            SUM(CASE WHEN `Note Satcli` IN (1, 2) THEN 1 ELSE 0 END) AS total_insatisfaits,
            SUM(CASE WHEN `Note Satcli` IS NOT NULL AND `Note Satcli` <> '' THEN 1 ELSE 0 END) AS total_non_vides
        FROM `sav - taux de cr ok - 1er rdv`
        WHERE MONTH(`Date Debut Rdv Client`) = :mois AND YEAR(`Date Debut Rdv Client`) = :annee
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':mois' => $mois, ':annee' => $annee]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $insatisfaits = $result['total_insatisfaits'] ?? 0;
    $non_vides = $result['total_non_vides'] ?? 0;

    return ($non_vides > 0) ? round(($insatisfaits / $non_vides) * 100, 2) : 0.0;
}
// --------------------- somme des EPS ---------------------

function sommeEPS(PDO $pdo, int $mois, int $annee, string $departement): int {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM interventions
        WHERE 
            `Statut Intervention` IN ('TERMINEE_OK', 'TERMINEE_KO')
            AND MONTH(`Date Intervention`) = :mois
            AND YEAR(`Date Intervention`) = :annee
            AND `Departement` = :departement
    ");
    $stmt->execute([
        'mois' => $mois,
        'annee' => $annee,
        'departement' => $departement
    ]);
    return (int)$stmt->fetchColumn();
}
// bar des histogramme
function getTermineesParDepartement(PDO $pdo): array {
    $sql = "
        SELECT `Departement`, 
               SUM(CASE WHEN `Statut Intervention` IN ('TERMINEE_OK', 'TERMINEE_KO') THEN 1 ELSE 0 END) AS total
        FROM `sav - taux de cr ok - 1er rdv`
        GROUP BY `Departement`
        ORDER BY `Departement`
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
