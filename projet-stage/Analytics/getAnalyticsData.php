<?php
require_once 'config.php';
require_once 'indicateurs.php';

// Lecture des filtres GET
$mois = $_GET['mois'] ?? 'all';
$annee = $_GET['annee'] ?? 'all';
$semaine = $_GET['semaine'] ?? 'toutes';

// Nettoyage / transformation des filtres
if ($mois === 'all' || $mois === 'toutes' || empty($mois)) {
    $mois = null;
} else {
    $mois = (int)$mois;
}

if ($annee === 'all' || $annee === 'toutes' || empty($annee)) {
    $annee = null;
} else {
    $annee = (int)$annee;
}

if ($semaine === 'toutes' || empty($semaine)) {
    $semaine = null;
} else {
    $semaine = (int)$semaine;
}

error_log("Filtres reçus : mois=$mois, annee=$annee, semaine=$semaine");

// Calcul des indicateurs avec filtres possibles null (pas de filtre)
$taux_cr_ok = tauxCR_OK($pdo, $mois, $annee, $semaine);
$delai_rdv_sav = DelaiPriseRDVSAV($pdo, $mois, $annee, $semaine);
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
