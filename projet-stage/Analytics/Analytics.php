<?php
require_once 'config.php';
require_once 'indicateurs.php';

// Récupérer les valeurs du filtre (mois et année)
$mois = isset($_GET['mois']) ? $_GET['mois'] : date('m');
$annee = isset($_GET['annee']) ? $_GET['annee'] : date('Y');
$semaine = isset($_GET['semaine']) ? $_GET['semaine'] : 'toutes';

// Appel dynamique de la fonction avec filtre
$taux_cr_ok = tauxCR_OK($pdo, $mois, $annee, $semaine);
$delai_rdv_sav = DelaiPriseRDVSAV($pdo, $mois, $annee, $semaine);
$secu_rdv_sav = SecuRDVSAV($pdo, $mois, $annee, $semaine);
$conformite_cr_sav = conformiteCRSAV($pdo, $mois, $annee, $semaine);

?>


<!-- En-tête Analytics -->
<div class="header">
  <div class="header-left">
    <i class="fa fa-home"></i>
    <span class="breadcrumb">/ Analytics</span>
    <span class="page-title">SAV</span>

    <div class="menu-dropdown">
      <div class="menu-button">
        <span class="material-symbols-rounded">expand_more</span>
      </div>
      <div class="dropdown-menu">
        <a href="javascript:void(0)" class="submenu-link" data-page="SAV">SAV</a>
        <a href="javascript:void(0)" class="submenu-link" data-page="Analytics-Racc">RAC</a>
        <a href="javascript:void(0)" class="submenu-link" data-page="TOUS">TOUS</a>
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
      <p id="taux-cr-ok"><?= number_format($taux_cr_ok, 2, ',', ' ') ?>?</p>
    </div>
  </div>
  <div class="card">
    <div class="card-icon">📅</div>
    <div class="card-content">
      <h3>Sécurisation RDV</h3>
      <p id="securisation-rdv"><?= number_format($securisation_rdv, 2, ',', ' ') ?>%</p>
    </div>
  </div>
  <div class="card">
    <div class="card-icon">⏱️</div>
    <div class="card-content">
      <h3>Délai prise RDV SAV</h3>
      <p id="delai-rdv-sav"><?= number_format($delai_rdv_sav, 2, ',', ' ') ?> jours</p>
    </div>
  </div>
  <div class="card">
    <div class="card-icon">🙁</div>
    <div class="card-content">
      <h3>Clients très insatisfaits</h3>
      <p id="clients-insatisfaits"><?= number_format($clients_insatisfaits, 2, ',', ' ') ?>%</p>
    </div>
  </div>
</div>


<!-- Graphiques -->
<div class="charts">
  <canvas id="barChart"></canvas>
  <canvas id="pieChart"></canvas>
</div>
