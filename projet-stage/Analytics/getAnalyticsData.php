<?php
require_once 'config.php';
require_once 'indicateurs.php';



// ✅ Récupération du département (optionnel)
$departement = $_GET['departement'] ?? null;

// // 🔹 Calcul des 5 derniers mois
$dates = [];
$current = new DateTime('first day of this month');
for ($i = 4; $i >= 0; $i--) {
    $dt = clone $current;
    $dt->modify("-$i months");
    $dates[] = ['mois' => (int)$dt->format('m'), 'annee' => (int)$dt->format('Y')];
}

// $mois_filtre = isset($_GET['mois']) ? (int)$_GET['mois'] : null;
// $annee_filtre = isset($_GET['annee']) ? (int)$_GET['annee'] : null;
// $dates = [];

// if ($mois_filtre && $annee_filtre) {
//     // ✅ Si mois/année donnés => 1 seul mois affiché
//     $dates[] = ['mois' => $mois_filtre, 'annee' => $annee_filtre];
// } else {
//     // ✅ Par défaut => 5 derniers mois
//     $current = new DateTime('first day of this month');
//     for ($i = 4; $i >= 0; $i--) {
//         $dt = clone $current;
//         $dt->modify("-$i months");
//         $dates[] = ['mois' => (int)$dt->format('m'), 'annee' => (int)$dt->format('Y')];
//     }
// }


// 🔹 Préparer les tableaux
$tauxCR_OK = [];
$secuRDV = [];
$delaiRDV = [];
$clientsInsatisfaits = [];
$sommeEPS = []; // ✅ uniquement si $departement est précisé

foreach ($dates as $d) {
    $mois = $d['mois'];
    $annee = $d['annee'];

    // ✅ Ces fonctions NE dépendent PAS du département
    $tauxCR_OK[] = tauxCR_OK($pdo, $mois, $annee);
    $secuRDV[] = SecuRDVSAV($pdo, $mois, $annee);
    $delaiRDV[] = DelaiPriseRDVSAV($pdo, $mois, $annee);
    $clientsInsatisfaits[] = clientsTresInsatisfaits($pdo, $mois, $annee);

    // ✅ sommeEPS dépend du département
    if ($departement) {
        $sommeEPS[] = sommeEPS($pdo, $mois, $annee, $departement);
    }
}

// 🔹 Générer les labels des mois
setlocale(LC_TIME, 'fr_FR.UTF-8');
$labels = [];
foreach ($dates as $d) {
    $dt = DateTime::createFromFormat('Y-m', sprintf('%04d-%02d', $d['annee'], $d['mois']));
    $labels[] = ucfirst(strftime('%b', $dt->getTimestamp()));
}

// 🔹 Réponse JSON
$response = [
    'labels' => $labels,
    'tauxCR_OK' => $tauxCR_OK,
    'securisationRDV' => $secuRDV,
    'delaiPriseRDV' => $delaiRDV,
    'clientsTresInsatisfaits' => $clientsInsatisfaits,
];

// ✅ Ajouter sommeEPS SEULEMENT si un département a été fourni
if ($departement) {
    $response['sommeEPS'] = $sommeEPS;
}

header('Content-Type: application/json');
if (isset($_GET['type']) && $_GET['type'] === 'departements') {
    $data = getTermineesParDepartement($pdo);
    
    $labels = array_column($data, 'Departement');
    $totaux = array_column($data, 'total');

    echo json_encode([
        'labels' => $labels,
        'data' => $totaux
    ]);
    exit;
}

echo json_encode($response);
exit;
