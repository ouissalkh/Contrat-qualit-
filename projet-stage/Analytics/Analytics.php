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
// $conformite_cr_sav = conformiteCRSAV($pdo, $mois, $annee, $semaine);
$clients_tres_insatisfait = clientsTresInsatisfaits($pdo, $mois, $annee, $semaine);
?>

<form id="filterForm">
  <label for="annee">Année :</label>
  <select id="annee" name="annee">
    <option value="2025">2025</option>
    <option value="2024">2024</option>
    <option value="2023">2023</option>
  </select>

  <label for="mois">Mois :</label>
  <select id="mois" name="mois">
    <option value="01">Janvier</option>
    <option value="02">Février</option>
    <option value="02">Mars</option>
    <option value="02">Avril</option>
    <option value="02">Mai</option>
    <option value="02">Juin</option>
    <option value="02">Juillet</option>
    <option value="02">Aout</option>
    <option value="02">Septembre</option>
    <option value="02">Octobre</option>
    <option value="02">Novembre</option>
    <option value="12">Décembre</option>
  </select>

  <label for="semaine">Semaine :</label>
  <select id="semaine" name="semaine">
    <option value="toutes">Toutes</option>
    <?php for ($i = 1; $i <= 52; $i++): ?>
      <option value="<?= $i ?>"><?= $i ?></option>
    <?php endfor; ?>
  </select>

  <button type="submit">Filtrer</button>
</form>

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
        <a href="javascript:void(0)" class="submenu-link" data-page="Analytics-Racc" id="btnRacc">RAC</a>
        
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
    <div class="card-icon">📅</div>
    <div class="card-content">
      <h3>Sécurisation RDV</h3>
      <p id="securisationRDV"><?= $secu_rdv_sav ?>%</p>
    </div>
  </div>
  <div class="card">
    <div class="card-icon">⏱️</div>
    <div class="card-content">
      <h3>Délai prise RDV</h3>
      <p id="delaiPriseRDV"><?= number_format($delai_rdv_sav, 2, ',', ' ') ?> jours</p>
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
  <!-- <canvas id="barChart"></canvas> -->
  <canvas id="lineChart"></canvas>
  <canvas id="pieChart"></canvas>
</div>

<div id="contenuRACC"></div>

