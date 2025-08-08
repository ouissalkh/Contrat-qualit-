<?php
require_once 'config.php';
require_once 'indicateurRACC.php';

// // Récupérer les valeurs du filtre (mois et année)
// $mois = isset($_GET['mois']) ? $_GET['mois'] : date('m');
// $annee = isset($_GET['annee']) ? $_GET['annee'] : date('Y');
// $semaine = isset($_GET['semaine']) ? $_GET['semaine'] : 'toutes';

$mois_courant = (int)date('m');
$annee_courante = (int)date('Y');

// ✅ Appels avec le mois courant
$taux_cr_ok = calculerTauxCROkGlobal($pdo, $mois_courant, $annee_courante);
$delai_rdv_sav = calculerTauxDelaiPriseRdv($pdo, $mois_courant, $annee_courante);
$client_satisfaits = calculerSATCLIRDV_OK($pdo, $mois_courant, $annee_courante);
$clients_insatisfait = calculerSATCLIRDV_NOK($pdo, $mois_courant, $annee_courante);

?>


<!-- En-tête Analytics -->
<div class="header">
  <div class="header-left">
    <i class="fa fa-home"></i>
    <span class="breadcrumb">/ Analytics</span>
    <span class="page-title">RACC</span>

  </div>

 
</div>

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
    <div class="card-icon">⏱️</div>
    <div class="card-content">
      <h3>Délai prise RDV SAV</h3>
      <p id="delaiPriseRDV"><?= number_format($delai_rdv_sav, 2, ',', ' ') ?> %</p>
    </div>
  </div>
  <div class="card">
    <div class="card-icon"> 😊</div>
    <div class="card-content">
      <h3>Client Satisfait</h3>
      <p id="clientsatisfait"><?= number_format($client_satisfaits , 2, ',', ' ') ?> %</p>
    </div>
  </div>

  <div class="card">
    <div class="card-icon">🙁</div>
    <div class="card-content">
      <h3>Clients insatisfaits</h3>
      <p id="clientsTresInsatisfaits"><?= number_format($clients_insatisfait, 2, ',', ' ') ?>%</p>
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
        <option value="taux_cr_ok">Taux CR OK</option>
        <option value="delai_rdv_sav">Délai prise RDV SAV</option>
        <option value="client_satisfaits">Client Satisfait</option>
        <option value="clients_insatisfait">Clients insatisfaits</option>

      </select>
      <button id="btnFiltrerIndicateurLine">Filtrer</button>
    </div>
    <canvas id="lineChart" width="500" height="250"></canvas>
  </div>

 
<!-- <div class="chart-container" style="width: 60%; margin: auto;">
  <canvas id="pieChartRacc"></canvas>
</div> -->

<div class="piechart-container" width="450" height="250">
  <div class="chart-header">
    <label for="filtreMoisPie">Choisir le mois :</label>
    <select id="filtreMoisPie">
      <option value="1">Janvier</option>
      <option value="2">Février</option>
      <option value="3">Mars</option>
      <option value="4">Avril</option>
      <option value="5">Mai</option>
      <option value="6">Juin</option>
      <option value="7">Juillet</option>
      <option value="8" selected>Août</option> <!-- par exemple le mois courant sélectionné par défaut -->
      <option value="9">Septembre</option>
      <option value="10">Octobre</option>
      <option value="11">Novembre</option>
      <option value="12">Décembre</option>
    </select>
    <button id="btnFiltrerPie">Filtrer</button>
  </div>
  <canvas id="pieChartRacc"></canvas>
</div>

<div id="mainContent"></div>

