
// Appelle PHP pour récupérer les données et générer les graphiques
function fetchDataAndRenderCharts(filtre) {
  fetch("/projet-stage/Analytics/getAnalyticsData.php")
    .then(response => response.json())
    .then(data => {
      console.log("Données reçues :", data);
      // afficherIndicateurs(data); // ⛔ Supprimé pour ne pas toucher aux cartes
      genererLineChart(data, filtre);  // ✅ Graphe uniquement
    })
    .catch(error => {
      console.error("Erreur lors de la récupération des données :", error);
    });
}



// ❌ afficherIndicateurs n’est plus utilisé, mais tu peux le garder si besoin ailleurs

// Génère le graphique en ligne (LineChart)
function genererLineChart(data, filtre) {
  const labels = data.labels;
  const datasets = [];

  if (filtre === "tous" || filtre === "tauxCR_OK") {
    datasets.push({
      label: "Taux CR OK (%)",
      data: data.tauxCR_OK,
      borderColor: "#3e95cd",
      fill: false,
      tension: 0.2
    });
  }

  if (filtre === "tous" || filtre === "securisationRDV") {
    datasets.push({
      label: "Sécurisation RDV (%)",
      data: data.securisationRDV,
      borderColor: "#8e5ea2",
      fill: false,
      tension: 0.2
    });
  }

  if (filtre === "tous" || filtre === "delaiPriseRDV") {
    datasets.push({
      label: "Délai Prise RDV (%)",
      data: data.delaiPriseRDV,
      borderColor: "#3cba9f",
      fill: false,
      tension: 0.2
    });
  }

  if (filtre === "tous" || filtre === "clientsTresInsatisfaits") {
    datasets.push({
      label: "Clients Insatisfaits (%)",
      data: data.clientsTresInsatisfaits,
      borderColor: "#e8c3b9",
      fill: false,
      tension: 0.2
    });
  }

  const lineCtx = document.getElementById("lineChart")?.getContext("2d");
  if (lineCtx) {
    if (window.lineChart instanceof Chart) window.lineChart.destroy();
    window.lineChart = new Chart(lineCtx, {
      type: "line",
      data: {
        labels: labels,
        datasets: datasets
      },
      options: {
        responsive: true,
        plugins: {
          title: {
            display: true,
            text: "Évolution des indicateurs (5 derniers mois)"
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            min: 0,
            max: 100,
            ticks: {
              stepSize: 10,
              autoSkip: false,      // 🚫 Empêche Chart.js d’ignorer les valeurs intermédiaires
              callback: function(value) {
                return value + "%"; // ✅ Affiche 0%, 10%, ... 100% si tu veux, ou juste `return value;`
              }
            }
          }
        }



      }
    });
  }
}

// Initialise les filtres
function initAnalytics() {
  const selectFiltre = document.getElementById("filtreIndicateurLine");

  fetchDataAndRenderCharts("tous"); // Chargement initial du graphe
   

  selectFiltre?.addEventListener("change", function () {
    const filtre = this.value;
    fetchDataAndRenderCharts(filtre);
  });

  const boutonFiltre = document.getElementById("btnFiltrerIndicateurLine");
  boutonFiltre?.addEventListener("click", function () {
    const filtre = selectFiltre.value;
    fetchDataAndRenderCharts(filtre);
  });
  // **AJOUTER CETTE LIGNE POUR CHARGER L’HISTOGRAMME**
  chargerHistogrammeDepartements();
}

// function initAnalytics() {
//   const selectFiltre = document.getElementById("filtreIndicateurLine");
//   const boutonFiltre = document.getElementById("btnFiltrerIndicateurLine");

//   // Initialisation
//   fetchDataAndRenderCharts("tous");

//   // Sur clic du bouton
//   boutonFiltre?.addEventListener("click", function () {
//     const filtre = selectFiltre.value;
//     fetchDataAndRenderCharts(filtre);
//   });
// }

// barchart
function initBarChartFiltre() {
  console.log("initBarChartFiltre appelée");

  const selectDepart = document.getElementById("filtreIndicateurPie");
  const btnFiltrer = document.getElementById("btnFiltrerIndicateurPie");

  if (!selectDepart) console.warn("Select département introuvable");
  if (!btnFiltrer) console.warn("Bouton filtrer département introuvable");

  const updateBarChart = () => {
    const departement = selectDepart?.value || "SAV";
    console.log("Fetch bar chart avec département:", departement);
    fetch("/projet-stage/Analytics/getAnalyticsData.php?departement=" + departement)
      .then(response => response.json())
      .then(data => {
        console.log("Data reçue par fetch bar chart :", data);
        genererBarChartEPS(data);
      })
      .catch(error => {
        console.error("Erreur lors du chargement du bar chart :", error);
      });
  };

  btnFiltrer?.addEventListener("click", () => {
    console.log("Bouton filtrer cliqué");
    updateBarChart();
  });
  selectDepart?.addEventListener("change", () => {
    console.log("Select département changé");
    updateBarChart();
  });

  // Chargement initial
  updateBarChart();
}

function genererBarChartEPS(data) {
  console.log("Data pour Bar Chart :", data);

  const ctx = document.getElementById("barChart")?.getContext("2d");
  if (!ctx) {
    console.error("Canvas barChart introuvable");
    return;
  }

  if (window.barChart instanceof Chart) window.barChart.destroy();

  window.barChart = new Chart(ctx, {
    type: "bar",
    data: {
      labels: data.labels,
      datasets: [{
        label: "Nombre EPS",
        data: data.sommeEPS || [],
        backgroundColor: "#4e73df"
      }]
    },
    options: {
      responsive: true,
      plugins: {
        title: {
          display: true,
          text: "EPS par mois et par département"
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          min:0,
          max:100,
          ticks:{
            stepSize: 10
          }
        }
      }
    }
  });
}



//pour le style de menu
document.addEventListener("DOMContentLoaded", function () {
  const select = document.getElementById("filtreIndicateurLine");

  function updateSelectStyle() {
    if (select.value === "tous") {
      select.classList.add("tous-selected");
    } else {
      select.classList.remove("tous-selected");
    }
  }

  // Exécuter une fois au chargement
  updateSelectStyle();

  // Mettre à jour quand l'utilisateur change la sélection
  select.addEventListener("change", updateSelectStyle);
});



// histogramme
function chargerHistogrammeDepartements() {
  fetch("/projet-stage/Analytics/getAnalyticsData.php?type=departements")
    .then(response => response.json())
    .then(data => {
      const ctx = document.getElementById("barChart")?.getContext("2d");
      if (!ctx) return;

      if (window.departementChart instanceof Chart) {
        window.departementChart.destroy();
      }

      window.departementChart = new Chart(ctx, {
        type: "bar",
        data: {
          labels: data.labels,
          datasets: [{
            label: "CR",
            data: data.data,
            backgroundColor: "#36a2eb"
          }]
        },
        options: {
          responsive: true,
          plugins: {
            title: {
              display: true,
              text: "Nombre d'interventions par département"
            }
          },
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });
    })
    .catch(error => {
      console.error("Erreur lors du chargement de l'histogramme :", error);
    });
}
