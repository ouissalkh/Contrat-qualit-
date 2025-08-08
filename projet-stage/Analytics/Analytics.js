// Appelle PHP pour récupérer les données et générer le line chart (historique)
// function fetchDataAndRenderCharts(filtre) {
//   fetch("/projet-stage/Analytics/getAnalyticsData.php")
//     .then(response => response.json())
//     .then(data => {
//       console.log("Données reçues pour line chart :", data);
//       // afficherIndicateurs(data); // désactivé pour ne pas toucher aux cartes
//       genererLineChart(data, filtre);
//     })
//     .catch(error => {
//       console.error("Erreur lors de la récupération des données line chart :", error);
//     });
// }

function fetchDataAndRenderCharts(filtre, mois) {
  // Construire l'URL avec paramètre mois s'il est défini
  let url = "/projet-stage/Analytics/getAnalyticsData.php";
  if (mois) {
    url += "?mois=" + encodeURIComponent(mois);
  }
  
  fetch(url)
    .then(response => response.json())
    .then(data => {
      console.log("Données reçues pour line chart :", data);
      genererLineChart(data, filtre);
    })
    .catch(error => {
      console.error("Erreur lors de la récupération des données line chart :", error);
    });
}


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
      data: { labels, datasets },
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
              autoSkip: false,
              callback: value => value + "%"
            }
          }
        }
      }
    });
  }
}

// Récupère les données pour le pie chart d’un mois donné et génère le graphique
function fetchPieChartData(mois) {
  fetch(`/projet-stage/Analytics/getAnalyticsDataRacc.php?mois=${mois}`)
    .then(response => response.json())
    .then(data => {
      console.log("Données reçues pour pie chart :", data);
      genererPieChartRacc(data);
      afficherIndicateurs(data); // mise à jour des cartes si besoin
    })
    .catch(error => {
      console.error("Erreur récupération données pie chart :", error);
    });
}

// Initialise l’interface, gestion des filtres et boutons
// function initAnalytics() {
//   const selectFiltre = document.getElementById("filtreIndicateurLine");
//   const selectMoisPie = document.getElementById("filtreMoisPie");
//   const btnFiltrerPie = document.getElementById("btnFiltrerPie");
//   const btnFiltrerLine = document.getElementById("btnFiltrerIndicateurLine");

//   // Chargement initial line chart (historique)
//   fetchDataAndRenderCharts("tous");

//   // Chargement initial pie chart avec le mois sélectionné
//   fetchPieChartData(selectMoisPie.value);

//   // Changement filtre line chart
//   selectFiltre?.addEventListener("change", () => {
//     fetchDataAndRenderCharts(selectFiltre.value);
//   });

//   // Bouton filtrer line chart
//   btnFiltrerLine?.addEventListener("click", () => {
//     fetchDataAndRenderCharts(selectFiltre.value);
//   });

//   // Bouton filtrer pie chart
//   btnFiltrerPie?.addEventListener("click", () => {
//     fetchPieChartData(selectMoisPie.value);
//   });

//   // Charger l’histogramme des départements (bar chart)
//   chargerHistogrammeDepartements();
// }

function initAnalytics() {
  const selectFiltre = document.getElementById("filtreIndicateurLine");
  const selectMoisPie = document.getElementById("filtreMoisPie");
  const btnFiltrerPie = document.getElementById("btnFiltrerPie");
  const btnFiltrerLine = document.getElementById("btnFiltrerIndicateurLine");

  // Chargement initial line chart avec filtre "tous" et mois sélectionné
  fetchDataAndRenderCharts("tous", selectMoisPie.value);

  // Chargement initial pie chart avec le mois sélectionné
  fetchPieChartData(selectMoisPie.value);

  // Changement filtre line chart (passe aussi le mois sélectionné)
  selectFiltre?.addEventListener("change", () => {
    fetchDataAndRenderCharts(selectFiltre.value, selectMoisPie.value);
  });

  // Bouton filtrer line chart
  btnFiltrerLine?.addEventListener("click", () => {
    fetchDataAndRenderCharts(selectFiltre.value, selectMoisPie.value);
  });

  // Bouton filtrer pie chart
  btnFiltrerPie?.addEventListener("click", () => {
    const mois = selectMoisPie.value;
    fetchPieChartData(mois);
    // Si tu veux aussi recharger le line chart avec ce mois:
    fetchDataAndRenderCharts(selectFiltre.value, mois);
  });

  // Charger l’histogramme des départements (bar chart)
  chargerHistogrammeDepartements();
}


// Initialisation et filtrage du bar chart par département
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

// Exemple de fonction pour générer un bar chart EPS (à adapter selon tes données)
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
          min: 0,
          max: 100,
          ticks: {
            stepSize: 10
          }
        }
      }
    }
  });
}

// Charger histogramme départements
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

// Optionnel : fonction pour afficher/mettre à jour les indicateurs (cartes, etc.)
function afficherIndicateurs(data) {
  // Par exemple :
  // document.getElementById("indicateurCRok").textContent = data.taux_cr_ok + "%";
  // document.getElementById("indicateurDelaiRDV").textContent = data.delai_rdv_sav + "%";
  // etc.
  // Adapter en fonction de ta structure HTML
}
