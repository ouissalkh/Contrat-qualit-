let elementsCache = {};
let calculTimeout = null;

// Initialiser le cache des éléments
function initializeCache() {
    calculerPointsMaxDynamique();
    elementsCache = {
        pointsMax: document.querySelectorAll(".pointsMax"),
        repartition: document.querySelectorAll(".repartition"),
        bonus: document.querySelectorAll(".Bonus"),
        reparHors: document.querySelectorAll(".repar_hors"),
        maxOKHORSRANG: document.querySelectorAll(".MaxOKHORSRANG"),
        resultatMax: document.querySelectorAll(".ResulatMax"),
        resultatBonus: document.querySelectorAll(".ResultatBonus"),
        performance: document.querySelectorAll(".performance"),
        
        // Éléments de total
        totalPointsMax: document.getElementById("totalPointsMax"),
        totalRepartition: document.getElementById("totalRepartition"),
        totalBonus: document.getElementById("TotalBonus"),
        totalrepHors: document.getElementById("totalrep_hors"),
        totalMaxHORSRANG: document.getElementById("totalMaxHORSRANG"),
        totalResulatMAX: document.getElementById("TotalResulatMAX"),
        totalResultatBonus: document.getElementById("TotalResultatBonus"),
        totalPerformance: document.getElementById("totalPerformance"),
        resultatContratQualite: document.getElementById("resultat_contrat_qualite")
    };
        
}
function calculerPointsMaxDynamique() {
    // Totaux fixes par catégorie
    const TOTAUX_FIXES = {
        "tauxCR": 4,        // 1er RDV
        "tauxCROKHORS": 3   // Hors rang
    };

    // Mise à jour des totaux fixes affichés
    const totalPointsMaxElem = document.getElementById("totalPointsMax");
    const totalMaxHORSRANGElem = document.getElementById("totalMaxHORSRANG");
    if(totalPointsMaxElem) totalPointsMaxElem.textContent = TOTAUX_FIXES["tauxCR"].toFixed(2);
    if(totalMaxHORSRANGElem) totalMaxHORSRANGElem.textContent = TOTAUX_FIXES["tauxCROKHORS"].toFixed(2);

    // Pour chaque ligne enfant
    document.querySelectorAll("#monTableau tr.child").forEach(row => {
        const repartitionCell = row.querySelector(".repartition, .repar_hors"); // prise en compte repartition ou repar_hors
        // Trouver parent (avec data-id)
        let parentRow = row.previousElementSibling;
        while (parentRow && !parentRow.classList.contains("parent")) {
            parentRow = parentRow.previousElementSibling;
        }
        if (!parentRow) return;

        const parentId = parentRow.dataset.id;

        if (parentId === "tauxCR") {
            const pointsMaxCell = row.querySelector(".pointsMax");
            if (repartitionCell && pointsMaxCell) {
                const repartition = parsePercent(repartitionCell.textContent);
                if (!isNaN(repartition)) {
                    const points = (repartition * TOTAUX_FIXES[parentId]) / 100;
                    pointsMaxCell.textContent = points.toFixed(2);
                }
            }
        } else if (parentId === "tauxCROKHORS") {
            const maxOKCell = row.querySelector(".MaxOKHORSRANG");
            if (repartitionCell && maxOKCell) {
                const repartition = parsePercent(repartitionCell.textContent);
                if (!isNaN(repartition)) {
                    const points = (repartition * TOTAUX_FIXES[parentId]) / 100;
                    maxOKCell.textContent = points.toFixed(2);
                }
            }
        }
    });
}




// Chargement des données optimisé
async function chargerIndicateurs() {
    const mois = document.getElementById("filterMonth").value;
    const annee = document.getElementById("filterYear").value;
    
    if (!mois || !annee) {
        alert("Merci de choisir un mois ET une année !");
        return;
    }
    
    // Afficher l'overlay de chargement
    const loadingHTML = `
        <div class="loading-overlay">
            <div class="loading-spinner"></div>
            <div class="loading-text">Chargement des données...</div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', loadingHTML);
    
    try {
        // Exécuter les requêtes en parallèle
        const [tauxResponse, repartitionResponse] = await Promise.all([
            fetch(`taux.php?mois=${mois}&annee=${annee}`),
            fetch(`repartition.php?mois=${mois}&annee=${annee}`)
        ]);
        
        const [tauxData, repartitionData] = await Promise.all([
            tauxResponse.json(),
            repartitionResponse.json()
        ]);
        
        // Traitement des données
        if (tauxData.status === "ok") {
            mettreAJourTaux(tauxData.taux);
        }
        
        if (repartitionData.status === "ok") {
            mettreAJourRepartition(repartitionData);
            calculerPointsMaxDynamique();
        }
        
        // Calculs optimisés
        initialiserCalculsOptimise();
        
    } catch (error) {
        console.error("Erreur lors du chargement:", error);
        afficherErreur();
    } finally {
        // Cacher l'overlay de chargement
        const overlay = document.querySelector('.loading-overlay');
        if (overlay) {
            overlay.style.opacity = '0';
            setTimeout(() => overlay.remove(), 30);
        }
        
      
        document.body.insertAdjacentHTML('beforeend', confirmationHTML);
        
        // Disparaître après 2 secondes
        setTimeout(() => {
            const confirmation = document.querySelector('.loading-overlay');
            if (confirmation) {
                confirmation.style.opacity = '0';
                setTimeout(() => confirmation.remove(), 300);
            }
        }, 2000);
    }
}

function mettreAJourTaux(taux) {
    Object.entries(taux).forEach(([id, valeur]) => {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = typeof valeur === 'number' ? valeur.toFixed(2) + '%' : valeur;
        }
    });
}

function mettreAJourRepartition(data) {
    const mapping = {
        // 1er RDV
        'zone_a_plp': 'zone_a_plp',
        'zone_b_plp': 'zone_b_plp',
        'zone_c_plp': 'zone_c_plp',
        'zone_a_hotline': 'zone_a_hotline',
        'zone_b_hotline': 'zone_b_hotline',
        'zone_c_hotline': 'zone_c_hotline',
        'zone_a_construction': 'zone_a_construction',
        'zone_b_construction': 'zone_b_construction',
        'zone_c_construction': 'zone_c_construction',
        
        // Hors rang
        'zone_a_hors_rang': 'zone_a_hors_rang',
        'zone_b_hors_rang': 'zone_b_hors_rang',
        'zone_c_hors_rang': 'zone_c_hors_rang'
    };

    // Mise à jour des répartitions
    if (data.repartition_1er_rdv) {
        Object.entries(data.repartition_1er_rdv).forEach(([key, val]) => {
            const htmlId = mapping[key];
            const el = document.getElementById(htmlId);
            if (el && val.pourcentage !== undefined) {
                el.textContent = val.pourcentage.toFixed(2) + '%';
            }
        });
    }
    
    if (data.repartition_hors_rang) {
        Object.entries(data.repartition_hors_rang).forEach(([key, val]) => {
            const htmlId = mapping[key];
            const el = document.getElementById(htmlId);
            if (el && val.pourcentage !== undefined) {
                el.textContent = val.pourcentage.toFixed(2) + '%';
            }
        });
    }
}

function afficherErreur() {
    const ids = [
        "taux_zone_a_plp", "taux_zone_b_plp", "taux_zone_c_plp",
        "taux_zone_a_hotline", "taux_zone_b_hotline", "taux_zone_c_hotline",
        "taux_zone_a_construction", "taux_zone_b_construction", "taux_zone_c_construction",
        "taux_hors_rang_a", "taux_hors_rang_b", "taux_hors_rang_c", "taux_cr_ok_global",
        "delai_prise_1er_rdv", "taux_report", "satcli_rdv_ok", "satcli_rdv_nok"
    ];
    
    ids.forEach(id => {
        const element = document.getElementById(id);
        if (element) element.textContent = "Erreur";
    });
}

// ===== FONCTIONS DE CALCUL OPTIMISÉES =====
function initialiserCalculsOptimise() {
    const totals = {
        pointsMax: 0,
        repartition: 0,
        bonus: 0,
        reparHors: 0,
        maxOKHORSRANG: 0,
        resultatMax: 0,
        resultatBonus: 0,
        performance: 0,
        contratQualite: 90 // Valeur de base
    };

    // Récupérer les totaux déjà calculés par calculerPointsMaxDynamique()
    totals.pointsMax = parseFloat(document.getElementById("totalPointsMax").textContent) || 0;
    totals.maxOKHORSRANG = parseFloat(document.getElementById("totalMaxHORSRANG").textContent) || 0;

    document.querySelectorAll("#monTableau tr").forEach(row => {
        // Calcul des bonus pour chaque ligne
        if (row.querySelector('.Bonus') || row.querySelector('.performance') || row.querySelector('.ResultatBonus')) {
            calculerBonusLigne(row);
        }

        // Mise à jour des autres totaux (sauf pointsMax et maxOKHORSRANG déjà calculés)
        if (row.querySelector('.repartition')) {
            totals.repartition += parsePercent(row.querySelector('.repartition').textContent) || 0;
        }
        if (row.querySelector('.Bonus')) {
            totals.bonus += parseFloat(row.querySelector('.Bonus').textContent) || 0;
        }
        if (row.querySelector('.repar_hors')) {
            totals.reparHors += parsePercent(row.querySelector('.repar_hors').textContent) || 0;
        }
        if (row.querySelector('.ResulatMax')) {
            totals.resultatMax += parseFloat(row.querySelector('.ResulatMax').textContent) || 0;
        }
        if (row.querySelector('.ResultatBonus')) {
            totals.resultatBonus += parseFloat(row.querySelector('.ResultatBonus').textContent) || 0;
        }
        if (row.querySelector('.performance')) {
            totals.performance += parseFloat(row.querySelector('.performance').textContent) || 0;
        }
    });

    // Mise à jour des totaux (sauf pointsMax et maxOKHORSRANG déjà à jour)
    if (elementsCache.totalRepartition) elementsCache.totalRepartition.textContent = totals.repartition.toFixed(2)+'%';
    if (elementsCache.totalBonus) elementsCache.totalBonus.textContent = totals.bonus.toFixed(2);
    if (elementsCache.totalrepHors) elementsCache.totalrepHors.textContent = totals.reparHors.toFixed(2)+"%";
    if (elementsCache.totalResulatMAX) elementsCache.totalResulatMAX.textContent = totals.resultatMax.toFixed(2);
    if (elementsCache.totalResultatBonus) elementsCache.totalResultatBonus.textContent = totals.resultatBonus.toFixed(2);
    if (elementsCache.totalPerformance) elementsCache.totalPerformance.textContent = totals.performance.toFixed(2);
    
    // Calcul final du résultat contrat qualité
    totals.contratQualite += totals.bonus + totals.performance + totals.resultatBonus;
    if (elementsCache.resultatContratQualite) {
        elementsCache.resultatContratQualite.textContent = totals.contratQualite.toFixed(2);
    }
}
function calculerBonusLigne(row) {
    const cells = row.cells;
    if (cells.length < 8) return;

    // Récupération des valeurs des cellules
    const resultat = parsePercent(cells[2].textContent);  // Résultat (colonne 3)
    const pointMin = parsePercent(cells[4].textContent);  // KPI Point min (colonne 5)
    const pointMax = parsePercent(cells[5].textContent);  // KPI Point MAX (colonne 6)
    const pointsMax = parseFloat(cells[6].textContent);   // Points MAX (colonne 7 - éditable)

    // Vérification que toutes les valeurs sont valides
    if ([resultat, pointMin, pointMax, pointsMax].some(isNaN)) {
        cells[7].textContent = '';
        return;
    }

    let bonus = 0;
    // Calcul du bonus selon 3 cas :
    if (resultat <= pointMin) {
        bonus = 0; // Si le résultat est inférieur ou égal au minimum, bonus = 0
    } else if (resultat >= pointMax) {
        bonus = pointsMax; // Si le résultat est supérieur ou égal au maximum, bonus = pointsMax actuel
    } else {
        // Calcul proportionnel entre le min et le max
        bonus = ((resultat - pointMin) / (pointMax - pointMin)) * pointsMax;
    }

    // Mise à jour de la cellule Bonus
    cells[7].textContent = bonus.toFixed(2);
    return bonus;
}

function parsePercent(text) {
    if (!text) return NaN;
    return parseFloat(text.replace('%', '').trim());
}

// ===== FONCTIONS EXPORT =====
function exportExcel() {
    var wb = XLSX.utils.table_to_book(document.getElementById('monTableau'), {sheet:"Feuille1"});
    XLSX.writeFile(wb, 'tableau.xlsx');
}

function exportPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    doc.autoTable({ html: '#monTableau' });
    doc.save('tableau.pdf');
}

// ===== TOGGLE BUTTONS =====
function initialiserToggleButtons() {
    document.querySelectorAll('.toggle-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const parentRow = btn.closest('tr');
            const parentId = parentRow.dataset.id;
            const children = document.querySelectorAll(`.child-of-${parentId}`);
            const isHidden = children[0].classList.contains('hidden');
            
            children.forEach(row => {
                if (isHidden) {
                    row.classList.remove('hidden');
                } else {
                    row.classList.add('hidden');
                }
            });
            
            btn.textContent = isHidden ? '▼' : '►';
        });
    });
}

// ===== DROPDOWN EXPORT =====
document.getElementById('exportBtn').addEventListener('click', function() {
    document.getElementById('exportMenu').classList.toggle('show');
});
window.addEventListener('load', () => {
    calculerPointsMaxDynamique();
});
// Fermer le dropdown si on clique ailleurs
window.addEventListener('click', function(e) {
    if (!e.target.matches('.export-btn')) {
        const dropdown = document.getElementById('exportMenu');
        if (dropdown.classList.contains('show')) {
            dropdown.classList.remove('show');
        }
    }
});

// ===== INITIALISATION GLOBALE =====
window.addEventListener('load', () => {
    initializeCache();
    initialiserToggleButtons();
    
    // Gestion du filtre
    document.querySelector('.filter-container button').addEventListener('click', chargerIndicateurs);
});
// Ajoutez cette fonction d'initialisation
function initialiserEcouteursModification() {
    // On écoute les colonnes 3, 5, 6, 7 dans chaque ligne
    document.querySelectorAll("#monTableau tr").forEach(row => {
        // On prend les cellules de la ligne
        const cells = row.cells;
        if (!cells) return;

        // Colonnes indices 2 (résultat), 4 (pointMin), 5 (pointMax), 6 (pointsMax)
        [2, 4, 5, 6].forEach(idx => {
            if (cells[idx]) {
                cells[idx].setAttribute('contenteditable', 'true'); // Rendre éditable si ce n'est pas déjà fait
                cells[idx].addEventListener('input', () => {
                    calculerBonusLigne(row);
                    initialiserCalculsOptimise();
                });
            }
        });
    });
}



// Appelez cette fonction dans votre initialisation
window.addEventListener('load', () => {
    initializeCache();
    initialiserToggleButtons();
    initialiserEcouteursModification(); // <-- Ajoutez cette ligne
    document.querySelector('.filter-container button').addEventListener('click', chargerIndicateurs);
});
   