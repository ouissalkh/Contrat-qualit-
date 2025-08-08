// document.addEventListener("DOMContentLoaded", function () {
//   initAnalytics();
// });

// function fetchDataAndRenderCharts(filtre, mois) {
//   const url = `/projet-stage/Analytics/getAnalyticsDataRacc.php?mois=${mois}`;
//   fetch(url)
//     .then(response => response.json())
//     .then(data => {
//       console.log("Données reçues pour line chart :", data);
//       genererLineChart(data, filtre);
//       genererPieChartRacc(data);
//       afficherIndicateurs(data);
//     })
//     .catch(error => console.error("Erreur:", error));
// }
document.addEventListener("DOMContentLoaded", function () {
  // Chargement initial cartes + line chart (mois courant, sans paramètre)
  fetchLineChartAndIndicators();

  // Chargement initial pie chart (mois sélectionné par défaut)
  const selectMoisPie = document.getElementById("filtreMoisPie");
  fetchPieChartData(selectMoisPie.value);

  // Filtrer pie chart au clic du bouton
  document.getElementById("btnFiltrerPie").addEventListener("click", () => {
    fetchPieChartData(selectMoisPie.value);
  });
});
function fetchDataAndRenderCharts(filtre, mois) {
  const url = `/projet-stage/Analytics/getAnalyticsDataRacc.php?mois=${mois}`;
  fetch(url)
    .then(response => response.json())
    .then(data => {
      console.log("Données reçues pour line chart :", data);
      genererLineChart(data, filtre);
      genererPieChartRacc(data);
      afficherIndicateurs(data);
    })
    .catch(error => console.error("Erreur:", error));
}
function fetchLineChartAndIndicators() {
  fetch("/projet-stage/Analytics/getAnalyticsDataRacc.php") // pas de paramètre mois
    .then(res => res.json())
    .then(data => {
      genererLineChart(data, "tous");
      afficherIndicateurs(data);
    });
}



function fetchPieChartData(mois) {
  fetch(`/projet-stage/Analytics/getAnalyticsDataRacc.php?mois=${mois}`)
    .then(res => res.json())
    .then(data => {
      genererPieChartRacc(data);
    });
}

const selectFiltre = document.getElementById("filtreIndicateurLine");
selectFiltre.addEventListener("change", () => {
  const filtre = selectFiltre.value;
  if (window.lineChartData) {
    genererLineChart(window.lineChartData, filtre);
  }
});

function afficherIndicateurs(data) {
  const idMapping = {
    taux_cr_ok: "tauxCR_OK",
    delai_rdv_sav: "delaiPriseRDV",
    client_satisfaits: "clientsatisfait",
    clients_insatisfait: "clientsTresInsatisfaits"
  };

  for (const key in idMapping) {
    const element = document.getElementById(idMapping[key]);
    if (element && data[key] !== undefined) {
      let val = Array.isArray(data[key]) ? data[key][data[key].length - 1] : data[key];
      if (typeof val === "number") {
        const value = `${val.toFixed(2)}%`;
        element.textContent = value;
      } else {
        element.textContent = "N/A";
        console.warn(`Valeur pour ${key} non numérique`, val);
      }
    }
  }
}


function genererPieChartRacc(data) {
  const ctx = document.getElementById("pieChartRacc")?.getContext("2d");
  if (!ctx) return;

  if (window.pieChartRacc instanceof Chart) {
    window.pieChartRacc.destroy();
  }

  // On récupère la dernière valeur des tableaux
  const dernierTauxOk = Array.isArray(data.taux_cr_ok) ? data.taux_cr_ok[data.taux_cr_ok.length - 1] : data.taux_cr_ok;
  const dernierTauxNok = 100 - dernierTauxOk;

  window.pieChartRacc = new Chart(ctx, {
    type: "pie",
    data: {
      labels: ["CR_MNT_OK (%)", "CR_MNT_NOK (%)"],
      datasets: [{
        data: [dernierTauxOk, dernierTauxNok],
        backgroundColor: ["#007bffff", "#F44336"]
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'bottom' },
        title: {
          display: true,
          text: `Taux CR_MNT_OK / CR_MNT_NOK (${new Date().toLocaleString('fr-FR', { month: 'long', year: 'numeric' })})`
        }
      }
    }
  });
}




function genererLineChart(data, filtre) {
  const labels = data.labels;
  const datasets = [];

  if (filtre === "tous" || filtre === "taux_cr_ok") {
    datasets.push({
      label: "Taux CR OK (%)",
      data: data.taux_cr_ok,
      borderColor: "#3e95cd",
      fill: false,
      tension: 0.2
    });
  }

  if (filtre === "tous" || filtre === "delai_rdv_sav") {
    datasets.push({
      label: "Délai prise RDV SAV (%)",
      data: data.delai_rdv_sav,
      borderColor: "#8e5ea2",
      fill: false,
      tension: 0.2
    });
  }

  if (filtre === "tous" || filtre === "client_satisfaits") {
    datasets.push({
      label: "Satcli OK (%)",
      data: data.client_satisfaits,
      borderColor: "#3cba9f",
      fill: false,
      tension: 0.2
    });
  }

  if (filtre === "tous" || filtre === "clients_insatisfait") {
    datasets.push({
      label: "Satcli NOK (%)",
      data: data.clients_insatisfait,
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
      data: { labels: labels, datasets: datasets },
      options: {
        responsive: true,
        plugins: {
          title: {
            display: true,
            text: "Evolution des indicateurs (5 derniers mois)"
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

function initAnalytics() {
  const selectFiltre = document.getElementById("filtreIndicateurLine");
  const selectMoisPie = document.getElementById("filtreMoisPie");
  const btnFiltrerPie = document.getElementById("btnFiltrerPie");

  fetchDataAndRenderCharts("tous");

  selectFiltre?.addEventListener("change", function () {
    const filtre = this.value;
    fetchDataAndRenderCharts(filtre);
  });

  document.getElementById("btnFiltreIndicateurLine")?.addEventListener("click", function () {
    const filtre = selectFiltre.value;
    fetchDataAndRenderCharts(filtre);
  });

  // Chargement initial du pie chart avec mois sélectionné par défaut
  fetchPieChartData(selectMoisPie.value);

  // Au clic sur bouton filtrer pour le pie chart
  btnFiltrerPie?.addEventListener("click", () => {
    const moisChoisi = selectMoisPie.value;
    fetchPieChartData(moisChoisi);
  });
}


// fonction dédiée pour le pie chart avec un paramètre mois
// function fetchPieChartData(mois) {
//   // Appelle le PHP en envoyant le mois en GET
//   fetch(`/projet-stage/Analytics/getAnalyticsDataRacc.php?mois=${mois}`)
//     .then(response => response.json())
//     .then(data => {
//       console.log("Données reçues pour pie chart :", data);
//       genererPieChartRacc(data);
//     })
//     .catch(error => {
//       console.error("Erreur récupération données pie chart :", error);
//     });
// }
