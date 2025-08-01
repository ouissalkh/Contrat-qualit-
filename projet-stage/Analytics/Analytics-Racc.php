<?php
require_once 'config.php';
require_once 'indicateurRACC.php';

// Récupérer les valeurs du filtre (mois et année)
$mois = isset($_GET['mois']) ? $_GET['mois'] : date('m');
$annee = isset($_GET['annee']) ? $_GET['annee'] : date('Y');
$semaine = isset($_GET['semaine']) ? $_GET['semaine'] : 'toutes';

// Appel dynamique de la fonction avec filtre
$taux_cr_ok = calculerTauxCROkGlobal($pdo, $mois, $annee);
$delai_rdv_sav = calculerTauxDelaiPriseRdv($pdo, $mois, $annee);
$client_satisfaits = calculerSATCLIRDV_OK($pdo, $mois, $annee);
$clients_insatisfait = calculerSATCLIRDV_NOK($pdo, $mois, $annee);
?>


<!-- En-tête Analytics -->
<div class="header">
  <div class="header-left">
    <i class="fa fa-home"></i>
    <span class="breadcrumb">/ Analytics</span>
    <span class="page-title">RACC</span>

    <div class="menu-dropdown">
      <div class="menu-button">
        <span class="material-symbols-rounded">expand_more</span>
      </div>
      <div class="dropdown-menu">
        <a href="javascript:void(0)" class="submenu-link" data-page="SAV">SAV</a>
        <a href="javascript:void(0)" class="submenu-link" data-page="Analytics-Racc">RAC</a>
        
      </div>
    </div>
  </div>

  <div class="header-right">
    <div class="search-box">
      <i class="fa fa-search"></i>
      <input type="text" id="searchInput" placeholder="Type here..." oninput="filtrerCartes()" />
    </div>
    <div class="signin">
      <i class="fa fa-user"></i>
      <span>Sign in</span>
      <i class="fa fa-cog"></i>
    </div>
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
      <p id="delaiPriseRDV"><?= number_format($delai_rdv_sav, 2, ',', ' ') ?> jours</p>
    </div>
  </div>
  <div class="card">
    <div class="card-icon"> 😊</div>
    <div class="card-content">
      <h3>Client Satisfait</h3>
      <p id="clientsatisfait"><?= $client_satisfaits ?>%</p>
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
  <!-- <canvas id="barChart"></canvas> -->
  <canvas id="lineChart"></canvas>
  <canvas id="pieChart"></canvas>
</div>
<div id="mainContent"></div>

