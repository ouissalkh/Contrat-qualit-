<?php
header('Content-Type: application/json');

// Récupération des paramètres
$mois = $_GET['mois'] ?? null;
$annee = $_GET['annee'] ?? null;

if (!$mois || !$annee) {
    echo json_encode(["status" => "error", "message" => "Mois et année requis"]);
    exit;
}

// Connexion à la base de données
$conn = new mysqli("10.10.10.55", "cq_projet", "Z9#k*E)dl*o(0I", "indicateur");
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Erreur connexion"]);
    exit;
}

// Définition des zones et types
$zones = [
    ["zone" => "ZONE A", "type" => "PLP", "id" => "taux_zone_a_plp", "filtre_prise" => ["Prise existante"]],
    ["zone" => "ZONE B", "type" => "PLP", "id" => "taux_zone_b_plp", "filtre_prise" => ["Prise existante"]],
    ["zone" => "ZONE C", "type" => "PLP", "id" => "taux_zone_c_plp", "filtre_prise" => ["Prise existante"]],
    ["zone" => "ZONE A", "type" => "HOTLINE", "id" => "taux_zone_a_hotline", "filtre_prise" => ["HOTLINE"]],
    ["zone" => "ZONE B", "type" => "HOTLINE", "id" => "taux_zone_b_hotline", "filtre_prise" => ["HOTLINE"]],
    ["zone" => "ZONE C", "type" => "HOTLINE", "id" => "taux_zone_c_hotline", "filtre_prise" => ["HOTLINE"]],
    ["zone" => "ZONE A", "type" => "Construction", "id" => "taux_zone_a_construction", "filtre_prise" => ["Prise non-existante", "Prise non-existante (HOTLINE)"]],
    ["zone" => "ZONE B", "type" => "Construction", "id" => "taux_zone_b_construction", "filtre_prise" => ["Prise non-existante", "Prise non-existante (HOTLINE)"]],
    ["zone" => "ZONE C", "type" => "Construction", "id" => "taux_zone_c_construction", "filtre_prise" => ["Prise non-existante", "Prise non-existante (HOTLINE)"]]
];

$tauxResultats = [];

foreach ($zones as $z) {
    $where = "`Zone contrat 2023` = ? AND MONTH(`Date Rdv Racc`) = ? AND YEAR(`Date Rdv Racc`) = ? AND `RANG_RDV (copie)` = 1 AND `TYPE_PRESTATION` != 'PLP_BRASSAGE'";
    $params = [$z['zone'], $mois, $annee];
    $types = "sii";

    if (count($z['filtre_prise']) === 1) {
        $where .= " AND `Statut Prise Cmdacces` = ?";
        $params[] = $z['filtre_prise'][0];
        $types .= "s";
    } else {
        $placeholders = implode(",", array_fill(0, count($z['filtre_prise']), "?"));
        $where .= " AND `Statut Prise Cmdacces` IN ($placeholders)";
        $params = array_merge($params, $z['filtre_prise']);
        $types .= str_repeat("s", count($z['filtre_prise']));
    }

    // Total RDV
    $sql_total = "SELECT COUNT(*) as total FROM `racc - taux de cr ok - 1er rdv` WHERE $where AND `GRP_STATUT_CRINSTALL_MNT` IN ('CR_MNT_DELAI','CR_MNT_NOK','CR_MNT_NOK - MAUVAIS CODE','CR_MNT_OK')";
    $stmt_total = $conn->prepare($sql_total);
    $stmt_total->bind_param($types, ...$params);
    $stmt_total->execute();
    $res_total = $stmt_total->get_result()->fetch_assoc();
    $total = $res_total['total'] ?? 0;
    $stmt_total->close();

    // RDV OK
    $sql_ok = "SELECT COUNT(*) as ok FROM `racc - taux de cr ok - 1er rdv` WHERE $where AND `GRP_STATUT_CRINSTALL_MNT` = 'CR_MNT_OK'";
    $stmt_ok = $conn->prepare($sql_ok);
    $stmt_ok->bind_param($types, ...$params);
    $stmt_ok->execute();
    $res_ok = $stmt_ok->get_result()->fetch_assoc();
    $ok = $res_ok['ok'] ?? 0;
    $stmt_ok->close();

    $taux = ($total > 0) ? round(($ok / $total) * 100, 2) : 0;
    $tauxResultats[$z['id']] = $taux;
}

// Hors rang
$zonesHorsRang = [
    ["zone" => "ZONE A", "id" => "taux_hors_rang_a"],
    ["zone" => "ZONE B", "id" => "taux_hors_rang_b"],
    ["zone" => "ZONE C", "id" => "taux_hors_rang_c"],
];

foreach ($zonesHorsRang as $z) {
    $where = "`Zone contrat 2023` = ? AND MONTH(`Date Rdv Racc`) = ? AND YEAR(`Date Rdv Racc`) = ? AND `RANG_RDV (copie)` >= 2";
    $params = [$z['zone'], $mois, $annee];
    $types = "sii";

    $sql_total = "SELECT COUNT(*) as total FROM `racc - taux de cr ok - 1er rdv` WHERE $where AND `GRP_STATUT_CRINSTALL_MNT` IN ('CR_MNT_DELAI','CR_MNT_NOK','CR_MNT_NOK - MAUVAIS CODE','CR_MNT_OK')";
    $stmt_total = $conn->prepare($sql_total);
    $stmt_total->bind_param($types, ...$params);
    $stmt_total->execute();
    $res_total = $stmt_total->get_result()->fetch_assoc();
    $total = $res_total['total'] ?? 0;
    $stmt_total->close();

    $sql_ok = "SELECT COUNT(*) as ok FROM `racc - taux de cr ok - 1er rdv` WHERE $where AND `GRP_STATUT_CRINSTALL_MNT` = 'CR_MNT_OK'";
    $stmt_ok = $conn->prepare($sql_ok);
    $stmt_ok->bind_param($types, ...$params);
    $stmt_ok->execute();
    $res_ok = $stmt_ok->get_result()->fetch_assoc();
    $ok = $res_ok['ok'] ?? 0;
    $stmt_ok->close();

    $taux = ($total > 0) ? round(($ok / $total) * 100, 2) : 0;
    $tauxResultats[$z['id']] = $taux;
}

// Global CR OK
$sql_cr_global_total = "SELECT COUNT(*) as total FROM `racc - taux de cr ok - 1er rdv`
WHERE MONTH(`Date Rdv Racc`) = ? 
AND YEAR(`Date Rdv Racc`) = ? 
AND `GRP_STATUT_CRINSTALL_MNT` IN ('CR_MNT_DELAI','CR_MNT_NOK','CR_MNT_NOK - MAUVAIS CODE','CR_MNT_OK')";

$stmt_cr_total = $conn->prepare($sql_cr_global_total);
$stmt_cr_total->bind_param("ii", $mois, $annee);
$stmt_cr_total->execute();
$res_cr_total = $stmt_cr_total->get_result()->fetch_assoc();
$stmt_cr_total->close();
$total_cr_global = $res_cr_total['total'] ?? 0;

$sql_cr_global_ok = "SELECT COUNT(*) as ok FROM `racc - taux de cr ok - 1er rdv`
WHERE MONTH(`Date Rdv Racc`) = ? 
AND YEAR(`Date Rdv Racc`) = ? 
AND `GRP_STATUT_CRINSTALL_MNT` = 'CR_MNT_OK'";

$stmt_cr_ok = $conn->prepare($sql_cr_global_ok);
$stmt_cr_ok->bind_param("ii", $mois, $annee);
$stmt_cr_ok->execute();
$res_cr_ok = $stmt_cr_ok->get_result()->fetch_assoc();
$stmt_cr_ok->close();
$ok_cr_global = $res_cr_ok['ok'] ?? 0;

$tauxResultats['taux_cr_ok_global'] = ($total_cr_global > 0) ? round(($ok_cr_global / $total_cr_global) * 100, 2) : 0;

// Délai 1er RDV
$sql_delai = "SELECT 
    COUNT(*) as total,
    SUM(`Delais 1er rdv inf 20j`) as dans_delai
FROM `racc - délais de prise du 1er rdv`
WHERE MONTH(`Date Ss`) = ? 
AND YEAR(`Date Ss`) = ? 
AND `Flag précommande` != 'PRECOMMANDE'";

$stmt_delai = $conn->prepare($sql_delai);
$stmt_delai->bind_param("ii", $mois, $annee);
$stmt_delai->execute();
$res_delai = $stmt_delai->get_result()->fetch_assoc();
$stmt_delai->close();

$tauxResultats['delai_prise_1er_rdv'] = ($res_delai['total'] > 0) ? round(($res_delai['dans_delai'] / $res_delai['total']) * 100, 2) : 0;

// Report
$sql_report = "SELECT 
    COUNT(*) as total_rdv,
    SUM(CASE WHEN `Taux de RDV planifiés N'AYANT PAS eu lieu à la date de planif` = 1 THEN 1 ELSE 0 END) as rdv_reports
FROM `racc - taux de reports de rdv`
WHERE MONTH(`Date Rdv`) = ? 
AND YEAR(`Date Rdv`) = ?";

$stmt_report = $conn->prepare($sql_report);
$stmt_report->bind_param("ii", $mois, $annee);
$stmt_report->execute();
$res_report = $stmt_report->get_result()->fetch_assoc();
$stmt_report->close();

$tauxResultats['taux_report'] = ($res_report['total_rdv'] > 0) ? round(($res_report['rdv_reports'] / $res_report['total_rdv']) * 100, 2) : 0;

// SATCLI
$sql_satcli_ok = "
SELECT COUNT(*) AS total_ok
FROM `SATCLI_SEM_RACC` s
JOIN `racc - taux de cr ok - 1er rdv` r ON s.`Idnt Ext Interv` = r.`Lib Ref Erdv`
WHERE r.`GRP_STATUT_CRINSTALL_MNT` = 'CR_MNT_OK'
  AND MONTH(s.`Date Inter`) = ?
  AND YEAR(s.`Date Inter`) = ?";
$stmt_satcli_ok = $conn->prepare($sql_satcli_ok);
$stmt_satcli_ok->bind_param("ii", $mois, $annee);
$stmt_satcli_ok->execute();
$res_satcli_ok = $stmt_satcli_ok->get_result()->fetch_assoc();
$stmt_satcli_ok->close();
$satcli_ok = $res_satcli_ok['total_ok'] ?? 0;

$sql_satcli_nok = "
SELECT COUNT(*) AS total_nok
FROM `SATCLI_SEM_RACC` s
JOIN `racc - taux de cr ok - 1er rdv` r ON s.`Idnt Ext Interv` = r.`Lib Ref Erdv`
WHERE r.`GRP_STATUT_CRINSTALL_MNT` != 'CR_MNT_OK'
  AND MONTH(s.`Date Inter`) = ?
  AND YEAR(s.`Date Inter`) = ?";
$stmt_satcli_nok = $conn->prepare($sql_satcli_nok);
$stmt_satcli_nok->bind_param("ii", $mois, $annee);
$stmt_satcli_nok->execute();
$res_satcli_nok = $stmt_satcli_nok->get_result()->fetch_assoc();
$stmt_satcli_nok->close();
$satcli_nok = $res_satcli_nok['total_nok'] ?? 0;

$sql_satcli_total = "
SELECT COUNT(*) AS total
FROM `SATCLI_SEM_RACC` s
JOIN `racc - taux de cr ok - 1er rdv` r ON s.`Idnt Ext Interv` = r.`Lib Ref Erdv`
WHERE MONTH(s.`Date Inter`) = ?
  AND YEAR(s.`Date Inter`) = ?";
$stmt_satcli_total = $conn->prepare($sql_satcli_total);
$stmt_satcli_total->bind_param("ii", $mois, $annee);
$stmt_satcli_total->execute();
$res_satcli_total = $stmt_satcli_total->get_result()->fetch_assoc();
$stmt_satcli_total->close();
$total_satcli = $res_satcli_total['total'] ?? 0;

$tauxResultats['satcli_rdv_ok'] = ($total_satcli > 0) ? round(($satcli_ok / $total_satcli) * 100, 2) : 0;
$tauxResultats['satcli_rdv_nok'] = ($total_satcli > 0) ? round(($satcli_nok / $total_satcli) * 100, 2) : 0;

$conn->close();

echo json_encode([
    "status" => "ok",
    "taux" => $tauxResultats
]);
