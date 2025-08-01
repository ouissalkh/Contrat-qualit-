<?php
require_once 'config.php';
require_once 'indicateursRACC.php';

// Filtrage dynamique possible via $_GET (optionnel)
$mois = isset($_GET['mois']) ? $_GET['mois'] : date('m');
$annee = isset($_GET['annee']) ? $_GET['annee'] : date('Y');
$semaine = isset($_GET['semaine']) ? $_GET['semaine'] : 'toutes';

// Calcul des indicateurs
$taux_cr_ok = calculerTauxCROkGlobal($pdo, $mois, $annee, $semaine);
$delai_rdv_sav = calculerTauxDelaiPriseRdv($pdo, $mois, $annee, $semaine);
$secu_rdv_sav = SecuRDVSAV($pdo, $mois, $annee, $semaine);
$clients_tres_insatisfait = clientsTresInsatisfaits($pdo, $mois, $annee, $semaine);

// Retour JSON
header('Content-Type: application/json');
echo json_encode([
    'tauxCR_OK' => round(floatval($taux_cr_ok), 2),
    'delaiPriseRDV' => round(floatval($delai_rdv_sav), 2),
    'securisationRDV' => round(floatval($secu_rdv_sav), 2),
    'clientsTresInsatisfaits' => round(floatval($clients_tres_insatisfait), 2),
]);
exit;
?>
