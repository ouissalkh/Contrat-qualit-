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

// --------------------- CALCUL RÉPARTITION DES RDV 1ER RDV ---------------------

// 1. Calcul du total des 1er RDV
$sql_total_1er_rdv = "SELECT COUNT(*) as total FROM `racc - taux de cr ok - 1er rdv` 
WHERE MONTH(`Date Rdv Racc`) = ? 
AND YEAR(`Date Rdv Racc`) = ? 
AND `RANG_RDV (copie)` = 1 
AND `TYPE_PRESTATION` != 'PLP_BRASSAGE'
AND `GRP_STATUT_CRINSTALL_MNT` IN ('CR_MNT_DELAI','CR_MNT_NOK','CR_MNT_NOK - MAUVAIS CODE','CR_MNT_OK')";

$stmt_total = $conn->prepare($sql_total_1er_rdv);
$stmt_total->bind_param("ii", $mois, $annee);
$stmt_total->execute();
$res_total = $stmt_total->get_result()->fetch_assoc();
$total_1er_rdv = $res_total['total'] ?? 0;
$stmt_total->close();

// 2. Définition des zones et types pour la répartition
$zones_repartition = [
    ["zone" => "ZONE A", "type" => "PLP", "filtre_prise" => ["Prise existante"]],
    ["zone" => "ZONE B", "type" => "PLP", "filtre_prise" => ["Prise existante"]],
    ["zone" => "ZONE C", "type" => "PLP", "filtre_prise" => ["Prise existante"]],
    ["zone" => "ZONE A", "type" => "HOTLINE", "filtre_prise" => ["HOTLINE"]],
    ["zone" => "ZONE B", "type" => "HOTLINE", "filtre_prise" => ["HOTLINE"]],
    ["zone" => "ZONE C", "type" => "HOTLINE", "filtre_prise" => ["HOTLINE"]],
    ["zone" => "ZONE A", "type" => "Construction", "filtre_prise" => ["Prise non-existante", "Prise non-existante (HOTLINE)"]],
    ["zone" => "ZONE B", "type" => "Construction", "filtre_prise" => ["Prise non-existante", "Prise non-existante (HOTLINE)"]],
    ["zone" => "ZONE C", "type" => "Construction", "filtre_prise" => ["Prise non-existante", "Prise non-existante (HOTLINE)"]]
];

$repartitionResultats = [];

// 3. Calcul de la répartition pour chaque zone/type
foreach ($zones_repartition as $z) {
    $where = " `Zone contrat 2023` = ? AND MONTH(`Date Rdv Racc`) = ? AND YEAR(`Date Rdv Racc`) = ? AND `RANG_RDV (copie)` = 1 AND `TYPE_PRESTATION` != 'PLP_BRASSAGE'";
    $params = [$z['zone'], $mois, $annee];
    $types = "sii";

    // Gestion des filtres de prise
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

    // Compter les RDV pour cette zone/type
    $sql_count = "SELECT COUNT(*) as count FROM `racc - taux de cr ok - 1er rdv` 
    WHERE $where 
    AND `GRP_STATUT_CRINSTALL_MNT` IN ('CR_MNT_DELAI','CR_MNT_NOK','CR_MNT_NOK - MAUVAIS CODE','CR_MNT_OK')";
    
    $stmt_count = $conn->prepare($sql_count);
    $stmt_count->bind_param($types, ...$params);
    $stmt_count->execute();
    $res_count = $stmt_count->get_result()->fetch_assoc();
    $count = $res_count['count'] ?? 0;
    $stmt_count->close();

    // Calcul du pourcentage de répartition
    $pourcentage = ($total_1er_rdv > 0) ? round(($count / $total_1er_rdv) * 100, 2) : 0;

    $key = strtolower(str_replace(' ', '_', $z['zone']) . '_' . strtolower($z['type'])); // "zone_a_
    $repartitionResultats[$key] = [
        'zone' => $z['zone'],
        'type' => $z['type'],
        'count' => $count,
        'pourcentage' => $pourcentage
    ];
}

// --------------------- CALCUL RÉPARTITION HORS RANG 1 ---------------------

// 1. Calcul du total des RDV HORS RANG 1
$sql_total_hors_rang = "SELECT COUNT(*) as total FROM `racc - taux de cr ok - 1er rdv` 
WHERE MONTH(`Date Rdv Racc`) = ? 
AND YEAR(`Date Rdv Racc`) = ? 
AND `RANG_RDV (copie)` >= 2 
AND `GRP_STATUT_CRINSTALL_MNT` IN ('CR_MNT_DELAI','CR_MNT_NOK','CR_MNT_NOK - MAUVAIS CODE','CR_MNT_OK')";

$stmt_total_hr = $conn->prepare($sql_total_hors_rang);
$stmt_total_hr->bind_param("ii", $mois, $annee);
$stmt_total_hr->execute();
$res_total_hr = $stmt_total_hr->get_result()->fetch_assoc();
$total_hors_rang = $res_total_hr['total'] ?? 0;
$stmt_total_hr->close();

// 2. Calcul par zone pour HORS RANG 1
$zones_hors_rang = ["ZONE A", "ZONE B", "ZONE C"];
$repartitionHorsRang = [];

foreach ($zones_hors_rang as $zone) {
    $sql_count_hr = "SELECT COUNT(*) as count FROM `racc - taux de cr ok - 1er rdv` 
    WHERE `Zone contrat 2023` = ? 
    AND MONTH(`Date Rdv Racc`) = ? 
    AND YEAR(`Date Rdv Racc`) = ? 
    AND `RANG_RDV (copie)` >= 2 
    AND `GRP_STATUT_CRINSTALL_MNT` IN ('CR_MNT_DELAI','CR_MNT_NOK','CR_MNT_NOK - MAUVAIS CODE','CR_MNT_OK')";
    
    $stmt_count_hr = $conn->prepare($sql_count_hr);
    $stmt_count_hr->bind_param("sii", $zone, $mois, $annee);
    $stmt_count_hr->execute();
    $res_count_hr = $stmt_count_hr->get_result()->fetch_assoc();
    $count_hr = $res_count_hr['count'] ?? 0;
    $stmt_count_hr->close();

    $pourcentage_hr = ($total_hors_rang > 0) ? round(($count_hr / $total_hors_rang) * 100, 2) : 0;

    $key = strtolower(str_replace(' ', '_', $zone)) . '_hors_rang';
    $repartitionHorsRang[$key] = [
        'zone' => $zone,
        'count' => $count_hr,
        'pourcentage' => $pourcentage_hr
    ];
}

$conn->close();

// Retour des résultats
echo json_encode([
    "status" => "ok",
    "periode" => ["mois" => $mois, "annee" => $annee],
    "totaux" => [
        "total_1er_rdv" => $total_1er_rdv,
        "total_hors_rang" => $total_hors_rang
    ],
    "repartition_1er_rdv" => $repartitionResultats,
    "repartition_hors_rang" => $repartitionHorsRang
]);
?>