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
document.getElementById('formFiltrer').addEventListener('submit', function(event) {
    event.preventDefault();  // bloque le rechargement

    // Récupérer les valeurs des filtres
    const annee = document.getElementById('annee').value;
    const mois = document.getElementById('mois').value;
    const semaine = document.getElementById('semaine').value;

    // Exemple : filtrer les lignes du tableau selon ces valeurs
    const rows = document.querySelectorAll('table tbody tr');

    rows.forEach(row => {
        // Assure-toi que chaque <tr> a des attributs data-annee, data-mois, data-semaine
        const anneeRow = row.getAttribute('data-annee') || '';
        const moisRow = row.getAttribute('data-mois') || '';
        const semaineRow = row.getAttribute('data-semaine') || '';

        let visible = true;

        if (annee && annee !== '' && anneeRow !== annee) visible = false;
        if (mois && mois !== '' && moisRow !== mois) visible = false;
        if (semaine && semaine !== '' && semaine !== 'toutes' && semaineRow !== semaine) visible = false;

        row.style.display = visible ? '' : 'none';
    });

    // Après filtrage, recalculer les valeurs affichées
    calculerBonus();
    calculerTotalPointsMax();
    calculerTotalBonus();
    calculerResultatContratQualite();
});
