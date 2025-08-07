<?php
require_once 'config.php';
require_once 'indicateurs.php';

// // Définir le mois et l'année courants (entiers)
// $mois = (int)date('m');
// $annee = (int)date('Y');

// // Appels sans filtre, les fonctions sont codées pour juillet
// $taux_cr_ok = tauxCR_OK($pdo, $mois, $annee);
// $delai_rdv_sav = DelaiPriseRDVSAV($pdo, $mois, $annee);
// $secu_rdv_sav = SecuRDVSAV($pdo, $mois, $annee);
// $clients_tres_insatisfait = clientsTresInsatisfaits($pdo,$mois, $annee);

// Calcul du mois précédent (avec gestion de l'année)
$mois_courant = (int)date('m');
$annee_courante = (int)date('Y');

if ($mois_courant === 1) {
    $mois_precedent = 12;
    $annee_precedente = $annee_courante - 1;
} else {
    $mois_precedent = $mois_courant - 1;
    $annee_precedente = $annee_courante;
}

// Appel des fonctions avec mois et année précédents
$taux_cr_ok = tauxCR_OK($pdo, $mois_precedent, $annee_precedente);
$delai_rdv_sav = DelaiPriseRDVSAV($pdo, $mois_precedent, $annee_precedente);
$secu_rdv_sav = SecuRDVSAV($pdo, $mois_precedent, $annee_precedente);
$clients_tres_insatisfait = clientsTresInsatisfaits($pdo, $mois_precedent, $annee_precedente);
?>



<!-- juste après la balise <body> -->
<?php echo "<!-- VERSION 2 -->"; ?>


<!-- En-tête Analytics -->
 <!--barre de navigation --> 
  <div class="header">
    <div class="header-left">
        <i class="fa fa-home"></i>
        <span class="breadcrumb">/ Contrat Qualité</span>
        <span class="page-title">SAV</span>

       
    </div>

  
</div>
<!-- fin de la nav de barre -->
<!-- Cartes d'indicateurs -->
<div class="indicateur-cards">
  <div class="card">
    <div class="card-icon">📈</div>
    <div class="card-content">
      <h3>Taux de CR OK</h3>
      <p id="tauxCR_OK"><?= number_format($taux_cr_ok, 2, ',', ' ') ?>%</p>
    </div>
  </div>
  <div class="card">
    <div class="card-icon">📅</div>
    <div class="card-content">
      <h3>Sécurisation RDV</h3>
      <p id="securisationRDV"><?= number_format($secu_rdv_sav, 2, ',', ' ') ?>%</p>
    </div>
  </div>
  <div class="card">
    <div class="card-icon">⏱️</div>
    <div class="card-content">
      <h3>Délai prise RDV</h3>
      <p id="delaiPriseRDV"><?= number_format($delai_rdv_sav, 2, ',', ' ') ?>%</p>
    </div>
  </div>
  <div class="card">
    <div class="card-icon">🙁</div>
    <div class="card-content">
      <h3>Clients insatisfaits</h3>
      <p id="clientsTresInsatisfaits"><?= number_format($clients_tres_insatisfait, 2, ',', ' ') ?>%</p>
    </div>
  </div>
</div>


<!-- Graphiques -->
<div class="charts">
  <!-- Line Chart Section -->
  <div class="linechart-container">
    <div class="chart-header">
      <label for="filtreIndicateurLine">Filtrer le graphe :</label>
      <select id="filtreIndicateurLine">
        <option value="tous">Tous</option>
        <option value="tauxCR_OK">Taux CR OK</option>
        <option value="securisationRDV">Sécurisation RDV</option>
        <option value="delaiPriseRDV">Délai Prise RDV</option>
        <option value="clientsTresInsatisfaits">Clients Insatisfaits</option>
      </select>
      <button id="btnFiltrerIndicateurLine">Filtrer</button>
    </div>
    <canvas id="lineChart" width="500" height="250"></canvas>
  </div>

  


  <canvas id="barChart" width="470" height="250"></canvas>
</div>
<div id="contenuRACC"></div>
