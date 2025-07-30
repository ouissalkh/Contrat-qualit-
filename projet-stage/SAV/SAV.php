<?php
require_once 'configSAV.php';
require_once 'indicateursSAV.php';

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

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Contrat Qualité</title>
    <link rel="stylesheet" href="SAV/SAV.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Google Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" rel="stylesheet">

    <style>

        table {
            border-collapse: collapse;
            width: 100%;
            border: none; /* Supprime la bordure du tableau */
        }

        th, td {
            border: none; /* Supprime les bordures des cellules */
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f0f0f0;
        }

        .indicateur {
            text-align: left;
        }

        td:nth-child(2),
        th:nth-child(2) {
            width: 120px; /* Largeur personnalisée de la 2e colonne */
        }

        input.kpi-min,
v       input.kpi-max {
            width: 60px;
            box-sizing: border-box;
            text-align: right;
            border: none;       
            background: none;   
            outline: none;     
        }

        #fenetreVF {
            max-width: 90%;
            max-height: 90vh;
            overflow: auto;
            background-color: white;
            padding: 20px;
            border: 2px solid #333;
            border-radius: 10px;
        }

        #contenuVF table {
            min-width: 800px; /* force une largeur min. pour pouvoir scroller */
        }
        /**/ 
        table {
            width: 100%;
            table-layout: auto !important;
            border-collapse: collapse;
        }

        th, td {
            white-space: nowrap;
            overflow: visible;
            text-overflow: unset;
            padding: 10px;
            border: 1px solid #ccc;
        }

        #contrat {
            max-width: 100%;
            overflow-x: visible !important;
        }

        .titre-contrat {
            text-align: center;
            font-weight: 700;
            font-size: 3rem;
            margin-top: 20px;
            margin-bottom: 12px;
            font-family: 'Poppins', sans-serif;
            color: #293f54ff;
        }
        /* */
        .filtre-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 4px;
        }

        .filtre-container form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            justify-content: center;
        }

        .filtre-container label,
        .filtre-container select,
        .filtre-container button {
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
        }

        /* */
        .actions-bas {
            display: flex;
            justify-content: center; /* centre horizontalement */
            gap: 15px;               /* espace entre boutons */
            margin-top: 15px;        /* espace au-dessus */
            padding-bottom: 20px;    /* espace en bas si besoin */
        }

        .btn-filtrer {
            display: inline-block;
            margin: 0;
            padding: 10px 24px;
            font-weight: 600;
            font-size: 1rem;
            border-radius: 30px;
            cursor: pointer;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            box-shadow: 0 6px 20px rgba(102,126,234,0.5);
            transition: background 0.3s ease;
            user-select: none;
            white-space: nowrap;
        }

        .btn-filtrer:hover {
            background: linear-gradient(135deg, #764ba2, #667eea);
        }
    </style>

</head>
<body>
  <div class="header">
    <div class="header-left">
        <i class="fa fa-home"></i>
        <span class="breadcrumb">/ Contrat Qualité</span>
        <span class="page-title">SAV</span>

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
    <!-- <button class="settings-btn" title="Paramètres">
      <span class="material-symbols-rounded">settings</span>
    </button>
  </div>
</div>  -->


    <div id="contrat">
        
        <h2 class="titre-contrat">Contrat SAV</h2>
        <div class="filtre-container">
        <form method="GET" style="margin-bottom: 20px;" id="formFiltrer">
            <label for="annee">Année :</label>
            <select name="annee" id="annee">
                <?php
                foreach (['2023', '2024', '2025'] as $a) {
                    $selected = ($annee == $a) ? 'selected' : '';
                    echo "<option value=\"$a\" $selected>$a</option>";
                }
                ?>
            </select>

            <label for="mois">Mois :</label>
            <select name="mois" id="mois">
                <option value="">-- Tous --</option>
                <?php
                $moisLabels = [
                    '01' => 'Janvier', '02' => 'Février', '03' => 'Mars',
                    '04' => 'Avril',   '05' => 'Mai',     '06' => 'Juin',
                    '07' => 'Juillet', '08' => 'Août',    '09' => 'Septembre',
                    '10' => 'Octobre', '11' => 'Novembre','12' => 'Décembre'
                ];
                foreach ($moisLabels as $val => $label) {
                    $selected = ($mois == $val) ? 'selected' : '';
                    echo "<option value=\"$val\" $selected>$label</option>";
                }
                ?>
            </select>
            <label for="semaine">Semaine :</label>
            <select name="semaine" id="semaine">
                <option value="toutes">-- Toutes --</option>
                <?php
                for ($i = 1; $i <= 52; $i++) {
                    $selected = (isset($_GET['semaine']) && $_GET['semaine'] == $i) ? 'selected' : '';
                    echo "<option value=\"$i\" $selected>Semaine $i</option>";
                }
                ?>
            </select>
            <button type="submit">Filtrer</button>
        </form>
        </div>
        <!--<p>Taux de CR OK pour <?= $mois ?>/<?= $annee ?> : <strong><?= $taux_cr_ok ?>%</strong></p>-->
        <table>
        <thead class="en-tete-tableau">
            <tr >
                <th>Indicateur</th>
                <th>Résultat</th>
                <th>Répartition des RDV</th>
                <th>KPI min</th>
                <th>KPI max</th>
                <th>Points MAX</th>
                <th>Bonus</th>
            </tr>
        </thead>
        <tbody>
            <tr data-annee="<?= $annee ?>" data-mois="<?= $mois ?>" data-semaine="<?= $semaine ?>">
                <td class="indicateur">Taux de CR OK</td>
                <td><?= number_format($taux_cr_ok, 2, ',', ' ') ?>%</td>
                <td>-</td>
                <td><span class="kpi-min">78</span>%</td>
                <td><span class="kpi-max">86</span>%</td>
                <td><span class="points-max">3</span></td>
                <td class="bonus"></td>
            </tr>

            <tr data-annee="<?= $annee ?>" data-mois="<?= $mois ?>" data-semaine="<?= $semaine ?>">
                <td class="indicateur">Délai de prise RDV SAV</td>
                <td><?= $delai_rdv_sav ?></td>
                <td>-</td>
                <td><span class="kpi-min">91</span>%</td>
                <td><span class="kpi-max">96</span>%</td>
                <td><span class="points-max">4</span></td>
                <td class="bonus"></td>
            </tr>

            <tr data-annee="<?= $annee ?>" data-mois="<?= $mois ?>" data-semaine="<?= $semaine ?>">
                <td class="indicateur">Sécurisation de RDV</td>
                <td><?= $secu_rdv_sav ?></td>
                <td>-</td>
                <td><span class="kpi-min">4</span>%</td>
                <td><span class="kpi-max">0</span>%</td>
                <td><span class="points-max">3</span></td>
                <td class="bonus"></td>
            </tr>

            <tr data-annee="<?= $annee ?>" data-mois="<?= $mois ?>" data-semaine="<?= $semaine ?>">
                <td class="indicateur">Délai de traitement Audit PM</td>
                <td>1</td>
                <td>-</td>
                <td><span class="kpi-min">3</span></td>
                <td><span class="kpi-max">1</span></td>
                <td><span class="points-max">2</span></td>
                <td class="bonus"></td>
            </tr>

            <tr data-annee="<?= $annee ?>" data-mois="<?= $mois ?>" data-semaine="<?= $semaine ?>">
                <td class="indicateur">CLIENTS TRES INSATISFAIT</td>
                <td>10,27</td>
                <td>-</td>
                <td><span class="kpi-min">15</span>%</td>
                <td><span class="kpi-max">5</span>%</td>
                <td><span class="points-max">3</span></td>
                <td class="bonus"></td>
            </tr>

            <tr data-annee="<?= $annee ?>" data-mois="<?= $mois ?>" data-semaine="<?= $semaine ?>">
                <td class="indicateur">CONFIRMITE DE CR</td>
                <td><?= $conformite_cr_sav ?></td>
                <td>-</td>
                <td><span class="kpi-min">5</span>%</td>
                <td><span class="kpi-max">1</span>%</td>
                <td><span class="points-max">3</span></td>
                <td class="bonus"></td>
            </tr>

            <tr data-annee="<?= $annee ?>" data-mois="<?= $mois ?>" data-semaine="<?= $semaine ?>">
                <td class="indicateur">DELAI TRAITEMENT REMISE EN ETAT</td>
                <td>29,28</td>
                <td>-</td>
                <td><span class="kpi-min">9</span></td>
                <td><span class="kpi-max">3</span></td>
                <td><span class="points-max">2</span></td>
                <td class="bonus"></td>
            </tr>

            <!-- Ligne Points MAX et Bonus vide -->
            <tr data-annee="<?= $annee ?>" data-mois="<?= $mois ?>" data-semaine="<?= $semaine ?>">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td id="total-points-max"></td>
                <td id="total-bonus"></td>
            </tr>

            <!-- Résultat contrat qualité -->
            <tr data-annee="<?= $annee ?>" data-mois="<?= $mois ?>" data-semaine="<?= $semaine ?>">
                <td><strong>Résultat contrat qualité</strong></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td id="resultat-contrat">?</td>
            </tr>
        </tbody>
    </table>
    </div>
    
    <div class="actions-bas">
        <!--bouton exporter PDF et modifier vals-->
        <button class="btn-filtrer" onclick="exporterPDF()">📤Exporter le contrat en PDF</button>
        <button class="btn-filtrer" onclick="exporterExcel()">📤 Exporter en Excel</button>
        <button class="btn-filtrer" onclick="ouvrirFenetreVF()">✏️ Modifier valeurs fixes</button>
    </div>
    <!--calcul automatique de Points MAX-->
    <script>
    function calculerTotalPointsMax() {
        let total = 0;
        document.querySelectorAll('.points-max').forEach(cell => {
            let val = parseFloat(cell.textContent.replace(',', '.'));
            if (!isNaN(val)) {
            total += val;
            }
        });
        document.getElementById('total-points-max').textContent = total.toFixed(2);
    }

    function calculerBonus() {
        const rows = document.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const resultatCell = row.cells[1];
            const kpiMinSpan = row.querySelector('.kpi-min');
            const kpiMaxSpan = row.querySelector('.kpi-max');
            const pointsMaxSpan = row.querySelector('.points-max');
            const bonusCell = row.cells[6];

            if (!kpiMinSpan || !kpiMaxSpan || !pointsMaxSpan || !bonusCell) return;

            let resultat = parseFloat(resultatCell.textContent.replace(',', '.')) || 0;
            let kpiMin = parseFloat(kpiMinSpan.textContent.replace(',', '.'));
            let kpiMax = parseFloat(kpiMaxSpan.textContent.replace(',', '.'));
            let pointsMax = parseFloat(pointsMaxSpan.textContent.replace(',', '.'));

            if (isNaN(resultat) || isNaN(kpiMin) || isNaN(kpiMax) || isNaN(pointsMax) || (kpiMax - kpiMin) === 0) {
                bonusCell.textContent = '';
                return;
            }
            
            let bonus = ((resultat - kpiMin) / (kpiMax - kpiMin)) * pointsMax;
            if (bonus < 0) bonus = 0;
            if (bonus > pointsMax) bonus = pointsMax;

            bonusCell.textContent = bonus.toFixed(2);
        });
    /*    //appel automatique après calcul
        enregistrerDansBase();*/
    }
    
    //la somme des bonus
    function calculerTotalBonus() {
        let totalBonus = 0;

        document.querySelectorAll('td.bonus').forEach(function (cell) {
            const valeur = parseFloat(cell.textContent.replace(',', '.'));
            if (!isNaN(valeur)) {
                totalBonus += valeur;
            }
        });
   
    document.getElementById('total-bonus').textContent = totalBonus.toFixed(2);
    }

    //calcul resultat contrat qualité
    function calculerResultatContratQualite() {
        const totalBonus = parseFloat(document.getElementById("total-bonus").textContent) || 0;
        const resultatContrat = 90 + totalBonus;
        document.getElementById("resultat-contrat").textContent = resultatContrat.toFixed(2);
    }


    // Calcul initial
    calculerBonus();
    calculerTotalPointsMax();
    calculerTotalBonus();
    calculerResultatContratQualite();

    //exporter contrat sous format pdf
    function exporterPDF() {
        const element = document.getElementById("contrat");

        const options = {
            margin:       0.5,
            filename:     'contrat_qualite.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2 },
            jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
        };

        html2pdf().set(options).from(element).save();
    }

    //exporter contrat sous format excel
    function exporterExcel() {
        const table = document.getElementById("contrat"); // mets l'ID de ton tableau ici
        if (!table) {
            alert("Tableau introuvable !");
            return;
        }

        // Convertir le tableau HTML en worksheet
        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.table_to_sheet(table);
        XLSX.utils.book_append_sheet(wb, ws, "Contrat Qualité");

        // Exporter
        XLSX.writeFile(wb, "contrat_qualite.xlsx");
    }


    //fenêtre modifier
    /*function ouvrirFenetreVF() {
        document.getElementById("modalVF").style.display = "block";
        afficherTableauModification(); // rempli la table avec les données actuelles
    }*/

    
    function ouvrirFenetreVF() {
        document.getElementById("modalVF").style.display = "block";

        // Insérer un tableau vide si ce n'est pas déjà fait
        const contenuVF = document.getElementById("contenuVF");
        if (!contenuVF.querySelector("table")) {
            contenuVF.innerHTML = `
                <table border="1" style="width:100%; text-align:center;">
                    <thead>
                        <tr>
                            <th>Indicateur</th><th>Résultat</th><th>Répartition RDV</th>
                            <th>KPI min</th><th>KPI max</th><th>Points MAX</th><th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            `;
        }

        afficherTableauModification(); // remplir ce tableau
    }




    function fermerFenetreVF() {
        document.getElementById("modalVF").style.display = "none";
        document.getElementById("contenuVF").innerHTML = ""; // reset contenu
    }

    // === AJOUT D'UN INDICATEUR ===
    function afficherFormulaireAjout() {
        const form = `
            <table border="1" style="width:100%; text-align:center;">
                <thead>
                    <tr>
                        <th>Indicateur</th><th>Résultat</th><th>Répartition RDV</th>
                        <th>KPI min</th><th>KPI max</th><th>Points MAX</th><th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" id="newIndicateur"></td>
                        <td><input type="text" id="newResultat"></td>
                        <td><input type="text" id="newRepartition"></td>
                        <td><input type="number" id="newKpiMin"></td>
                        <td><input type="number" id="newKpiMax"></td>
                        <td><input type="number" id="newPointsMax"></td>
                        <td><button onclick="ajouterIndicateur()">Ajouter</button></td>
                    </tr>
                </tbody>
            </table>
        `;
        document.getElementById("contenuVF").innerHTML = form;
    }
    function ajouterIndicateur() {
        const indicateur = document.getElementById("newIndicateur").value.trim();
        const resultat = document.getElementById("newResultat").value.trim();
        const repartition = document.getElementById("newRepartition").value.trim();
        const kpiMin = document.getElementById("newKpiMin").value.trim();
        const kpiMax = document.getElementById("newKpiMax").value.trim();
        const pointsMax = document.getElementById("newPointsMax").value.trim();

        if (!indicateur || !resultat || !repartition || !kpiMin || !kpiMax || !pointsMax) {
            alert("Tous les champs doivent être remplis.");
            return;
        }

        const tbody = document.querySelector("tbody");
        const row = document.createElement("tr");
        row.innerHTML = `
            <td class="indicateur">${indicateur}</td>
            <td>${resultat}</td>
            <td>${repartition}</td>
            <td><span class="kpi-min">${kpiMin}</span>%</td>
            <td><span class="kpi-max">${kpiMax}</span>%</td>
            <td><span class="points-max">${pointsMax}</span></td>
            <td></td>
        `;
        tbody.insertBefore(row, tbody.lastElementChild.previousElementSibling);

        alert("Indicateur ajouté avec succès.");
        fermerFenetreVF();
        mettreAJourCalculs(); // si tu as une fonction qui met à jour bonus, total, etc.
    }

    //pour bouton modifier
    function sauvegarderLigne(btn) {
        const row = btn.closest("tr");
        const tbody = row.parentElement;
        const index = Array.from(tbody.children).indexOf(row);

        const inputs = row.querySelectorAll("input");
        const values = Array.from(inputs).map(inp => inp.value.trim());

        if (values.some(v => v === "")) {
            alert("Veuillez remplir tous les champs !");
            return;
        }

        const resultat = parseFloat(values[1].replace(",", "."));
        const kpiMin = parseFloat(values[3]);
        const kpiMax = parseFloat(values[4]);
        const pointsMax = parseFloat(values[5]);

        let bonus = ((resultat - kpiMin) / (kpiMax - kpiMin)) * pointsMax;
        if (bonus < 0) bonus = 0;
        if (bonus > pointsMax) bonus = pointsMax;

        bonus = bonus.toFixed(2); // garder deux chiffres après la virgule

        // On récupère le tableau principal (celui hors modale)
        const mainRows = document.querySelectorAll("table:not(#contenuVF table) tbody tr");

        if(index < mainRows.length) {
            mainRows[index].innerHTML = `
                <td class="indicateur">${values[0]}</td>
                <td>${parseFloat(values[1]).toFixed(2)}%</td>
                <td>${values[2]}</td>
                <td><span class="kpi-min">${values[3]}</span>%</td>
                <td><span class="kpi-max">${values[4]}</span>%</td>
                <td><span class="points-max">${values[5]}</span></td>
                <td class="cell-bonus">${bonus}</td>
            `;
        } else {
            alert("Erreur : index hors limites !");
        }

        alert("Ligne modifiée !");
        mettreAJourCalculs();
    }


    //pour bouton supprimer
    function supprimerLigne(btn) {
        const row = btn.closest("tr");
        const tbodyModal = row.parentElement;
        const index = Array.from(tbodyModal.children).indexOf(row);

        // Supprimer la ligne dans la modale
        row.remove();

        // Supprimer la ligne correspondante dans le tableau principal
        const mainRows = document.querySelectorAll("table:not(#contenuVF table) tbody tr");

        if (index < mainRows.length) {
            mainRows[index].remove();
        } else {
            alert("Erreur : index hors limites !");
        }

        // Si tu as besoin de recalculer après suppression
        mettreAJourCalculs();
        alert("Ligne supprimée");
}



    function afficherTableauModification() {
        const tbody = document.querySelector("#contenuVF tbody");
        tbody.innerHTML = "";
        

        const lignes = document.querySelectorAll("table tbody tr");

        lignes.forEach((ligne, index) => {
            // Ignorer les deux dernières lignes (somme et totaux)
            if (index >= lignes.length - 2) return;

            const cellules = ligne.querySelectorAll("td");
            if (cellules.length < 6) return;

            const indicateur = cellules[0].textContent.trim();
            const resultat = cellules[1].textContent.trim().replace('%', '').replace(',', '.');
            const repartition = cellules[2].textContent.trim();
            const kpiMin = cellules[3].textContent.trim().replace('%', '').replace(',', '.');
            const kpiMax = cellules[4].textContent.trim().replace('%', '').replace(',', '.');
            const pointsMax = cellules[5].textContent.trim().replace(',', '.');

            const nouvelleLigne = document.createElement("tr");
            nouvelleLigne.setAttribute("data-index", index);  // 🔥 associe l'index de la vraie ligne
            nouvelleLigne.setAttribute("data-indicateur", indicateur);


            nouvelleLigne.innerHTML = `
                <td><input type="text" value="${indicateur}"></td>
                <td><input type="number" step="0.01" value="${resultat}" oninput="calculerBonusLigneModale(this)"></td>
                <td><input type="text" value="${repartition}"></td>
                <td><input type="number" step="0.01" value="${kpiMin}" oninput="calculerBonusLigneModale(this)"></td>
                <td><input type="number" step="0.01" value="${kpiMax}" oninput="calculerBonusLigneModale(this)"></td>
                <td><input type="number" step="0.01" value="${pointsMax}" oninput="calculerBonusLigneModale(this)"></td>
                <td>
                    <button onclick="sauvegarderLigne(this)"  title="Modifier">
                        <i class="fas fa-edit" style="pointer-events: none;"></i>
                    </button>
                    <button onclick="supprimerLigne(this)" title="Supprimer" style="background:none; border:none; color:#e74c3c; cursor:pointer; margin-left:5px;">
                        <i class="fas fa-trash-alt" style="pointer-events:none;"></i>
                    </button>

                </td>

                    
            `;
            tbody.appendChild(nouvelleLigne);
        });
    }


    function validerModificationsVF() {
        const modalRows = document.querySelectorAll("#contenuVF tbody tr");
        const mainRows = document.querySelectorAll("table tbody tr");

        modalRows.forEach((modalRow, i) => {
            const inputs = modalRow.querySelectorAll("input");
            if (inputs.length < 6) return;

            const indicateur = inputs[0].value;
            const resultat = inputs[1].value;
            const repartition = inputs[2].value;
            const kpiMin = inputs[3].value;
            const kpiMax = inputs[4].value;
            const pointsMax = inputs[5].value;

            // Remplace dans le tableau principal (sauf les lignes finales)
            if (i < mainRows.length - 2) {
                const cells = mainRows[i].querySelectorAll("td");
                cells[0].textContent = indicateur;
                cells[1].textContent = resultat;
                cells[2].textContent = repartition;
                cells[3].textContent = kpiMin + "%";
                cells[4].textContent = kpiMax + "%";
                cells[5].textContent = pointsMax;

                // Forcer le recalcul du bonus après modif
                recalculerBonusLigne(mainRows[i]);
            }
            calculerBonus();
            calculerTotalPointsMax();
            calculerTotalBonus();
            calculerResultatContratQualite();
        });

        // Recalcul des totaux
        calculerTotalPointsMax();
        calculerTotalBonus();
        calculerResultatContrat();

        // Fermer la fenêtre modale
        fermerFenetreVF();
    }

    //recalculer bonus de chaque ligne
    function calculerBonusLigneModale(input) {
        const ligne = input.closest("tr");
        const inputs = ligne.querySelectorAll("input");

        const resultat = parseFloat(inputs[1].value.replace(',', '.'));
        const kpiMin = parseFloat(inputs[3].value.replace(',', '.'));
        const kpiMax = parseFloat(inputs[4].value.replace(',', '.'));
        const pointsMax = parseFloat(inputs[5].value.replace(',', '.'));

        // Vérification : ne calcule que si toutes les valeurs sont valides
        if (isNaN(resultat) || isNaN(kpiMin) || isNaN(kpiMax) || isNaN(pointsMax)) return;

        const min = Math.min(kpiMin, kpiMax);
        const max = Math.max(kpiMin, kpiMax);
        let bonus = 0;
 
        if (resultat > max) {
            bonus = pointsMax;
        } else if (resultat >= min && resultat <= max) {
            bonus = ((resultat - min) / (max - min)) * pointsMax;
        } else {
            bonus = 0;
        }


        bonus = bonus.toFixed(2); // Arrondi à deux décimales

      
    }



    function mettreAJourCalculs() {
        calculerBonusParLigne(); // ta fonction existante
        calculerTotalPointsMax(); // ta fonction existante
        calculerTotalBonus(); // ta fonction existante
        calculerResultatContratQualite(); // ta fonction existante
    }


    </script>
    <!--pour pdf-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <!--pour excel-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>


    <div id="modalVF" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999;">
        <div style="background:#fff; padding:20px; margin:50px auto; width:90%; max-width:1000px; height:90%; display:flex; flex-direction:column; position:relative; border-radius:10px;">
        
            <!-- Titre + bouton fermer -->
            <div style="flex-shrink:0;">
                <h3 style="margin:0;">Modifier les valeurs fixes</h3>
                <button onclick="fermerFenetreVF()" style="position:absolute; top:10px; right:10px;">X</button>
            </div>

            <!-- Boutons Ajouter / Modifier -->
            <div style="flex-shrink:0; margin-bottom:10px;">
                <button onclick="afficherFormulaireAjout()">➕ Ajouter</button>
                <!--<button onclick="afficherTableauModification()">✏️ Modifier</button>-->
            </div>

            <!-- Contenu scrollable -->
            <div id="contenuVF" style="flex:1; overflow:auto; max-height:100%; padding-right:10px;"></div>

            <!-- ✅ Bouton Valider toujours visible 
            <div style="flex-shrink:0; margin-top:10px; text-align:center;">
                <button onclick="validerModificationsVF()" style="padding:8px 16px; font-weight:bold;">✅ Valider les modifications</button>
            </div>-->

        </div>
    </div>



</body>
</html>