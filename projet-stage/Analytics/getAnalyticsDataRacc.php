<?php
require_once 'config.php';
require_once 'indicateurRACC.php';

error_reporting(E_ALL);
ini_set('display_errors', 0);  // cacher les erreurs pour ne pas casser le JSON

// ✅ Récupération du département (optionnel)
$departement = $_GET['departement'] ?? null;

// $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// // Récupérer éventuellement un département
// $departement = $_GET['departement'] ?? null;

$dates = [];
$current = new DateTime('first day of this month');
for ($i = 4; $i >= 0; $i--) {
    $dt = clone $current;
    $dt->modify("-$i months");
    $dates[] = ['mois' => (int)$dt->format('m'), 'annee' => (int)$dt->format('Y')];
}

// Résultat à envoyer au JS
// $response = [
//     'labels' => [],
//     'tauxCR_OK' => [],
//     'delaiPriseRDV' => [],
//     'clientsTresInsatisfaits' => []
// ];

// 🔹 Préparer les tableaux
$taux_cr_ok = [];
$delai_rdv_sav = [];
$client_satisfaits = [];
$clients_insatisfait  = [];
$sommeEPS = []; // ✅ uniquement si $departement est précisé

foreach ($dates as $d) {
    $mois = $d['mois'];
    $annee = $d['annee'];

    // ✅ Ces fonctions NE dépendent PAS du département
    $taux_cr_ok[] = calculerTauxCROkGlobal($pdo, $mois, $annee); // anciennement $tauxCR_OK[]
    $delai_rdv_sav[] = calculerTauxDelaiPriseRdv($pdo, $mois, $annee); // anciennement $secuRDV[]
    $client_satisfaits[] = calculerSATCLIRDV_OK($pdo, $mois, $annee); // anciennement $delaiRDV[]
    $clients_insatisfait[] = calculerSATCLIRDV_NOK($pdo, $mois, $annee); // anciennement $clientsInsatisfaits[]

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
    'taux_cr_ok' => $taux_cr_ok,
    'delai_rdv_sav' => $delai_rdv_sav,
    'client_satisfaits' => $client_satisfaits,
    'clients_insatisfait' => $clients_insatisfait,
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
