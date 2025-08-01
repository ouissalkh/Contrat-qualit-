<?php
// --------------------- CALCUL TAUX DE CR OK GLOBAL ---------------------
function calculerTauxCROkGlobal(PDO $conn, int $mois, int $annee): float {
    $sql_cr_global_total = "SELECT COUNT(*) as total 
        FROM `racc - taux de cr ok - 1er rdv`
        WHERE MONTH(`Date Rdv Racc`) = :mois 
        AND YEAR(`Date Rdv Racc`) = :annee 
        AND `GRP_STATUT_CRINSTALL_MNT` IN ('CR_MNT_DELAI','CR_MNT_NOK','CR_MNT_NOK - MAUVAIS CODE','CR_MNT_OK')";

    $stmt_cr_total = $conn->prepare($sql_cr_global_total);
    $stmt_cr_total->execute([':mois' => $mois, ':annee' => $annee]);
    $res_cr_total = $stmt_cr_total->fetch(PDO::FETCH_ASSOC);
    $total_cr_global = $res_cr_total['total'] ?? 0;

    $sql_cr_global_ok = "SELECT COUNT(*) as ok 
        FROM `racc - taux de cr ok - 1er rdv`
        WHERE MONTH(`Date Rdv Racc`) = :mois 
        AND YEAR(`Date Rdv Racc`) = :annee 
        AND `GRP_STATUT_CRINSTALL_MNT` = 'CR_MNT_OK'";

    $stmt_cr_ok = $conn->prepare($sql_cr_global_ok);
    $stmt_cr_ok->execute([':mois' => $mois, ':annee' => $annee]);
    $res_cr_ok = $stmt_cr_ok->fetch(PDO::FETCH_ASSOC);
    $ok_cr_global = $res_cr_ok['ok'] ?? 0;

    return ($total_cr_global > 0) ? round(($ok_cr_global / $total_cr_global) * 100, 2) : 0;
}

// --------------------- DÉLAI PRISE RDV ---------------------
function calculerTauxDelaiPriseRdv(PDO $conn, int $mois, int $annee): float {
    $sql = "SELECT 
                COUNT(*) as total,
                SUM(`Delais 1er rdv inf 20j`) as dans_delai
            FROM `racc - délais de prise du 1er rdv`
            WHERE MONTH(`Date Ss`) = :mois 
              AND YEAR(`Date Ss`) = :annee 
              AND `Flag précommande` != 'PRECOMMANDE'";

    $stmt = $conn->prepare($sql);
    $stmt->execute([':mois' => $mois, ':annee' => $annee]);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);

    return ($res['total'] > 0) ? round(($res['dans_delai'] / $res['total']) * 100, 2) : 0;
}

// --------------------- SATCLI OK ---------------------
function calculerSATCLIRDV_OK(PDO $conn, int $mois, int $annee): int {
    $sql = "
        SELECT COUNT(*) AS total_ok
        FROM `SATCLI_SEM_RACC` s
        JOIN `racc - taux de cr ok - 1er rdv` r 
            ON s.`Idnt Ext Interv` = r.`Lib Ref Erdv`
        WHERE r.`GRP_STATUT_CRINSTALL_MNT` = 'CR_MNT_OK'
          AND MONTH(s.`Date Inter`) = :mois
          AND YEAR(s.`Date Inter`) = :annee
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([':mois' => $mois, ':annee' => $annee]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['total_ok'] ?? 0;
}

// --------------------- SATCLI NOK ---------------------
function calculerSATCLIRDV_NOK(PDO $conn, int $mois, int $annee): int {
    $sql = "
        SELECT COUNT(*) AS total_nok
        FROM `SATCLI_SEM_RACC` s
        JOIN `racc - taux de cr ok - 1er rdv` r 
            ON s.`Idnt Ext Interv` = r.`Lib Ref Erdv`
        WHERE r.`GRP_STATUT_CRINSTALL_MNT` != 'CR_MNT_OK'
          AND MONTH(s.`Date Inter`) = :mois
          AND YEAR(s.`Date Inter`) = :annee
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([':mois' => $mois, ':annee' => $annee]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['total_nok'] ?? 0;
}
