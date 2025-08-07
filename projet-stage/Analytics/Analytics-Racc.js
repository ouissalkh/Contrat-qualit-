// document.addEventListener("DOMContentLoaded", function () {
//   // fetchDataAndRenderCharts("tous");
// });

function fetchDataAndRenderCharts(filtre) {
  fetch("/projet-stage/Analytics/getAnalyticsDataRacc.php")
    .then(response => response.text())
    .then(text => {
      console.log("Réponse brute reçue :", text);
      try {
        const data = JSON.parse(text);
        console.log("Données JSON parsées :", data);
        genererLineChart(data, filtre);
      } catch(e) {
        console.error("Erreur de parsing JSON :", e);
        // Affiche aussi le texte complet de la réponse pour debug
        console.log("Contenu complet reçu (debug):", text);
      }
    })
    .catch(error => {
      console.error("Erreur lors de la récupération des données RACC :", error);
    });

}


// function afficherIndicateurs(data) {
  
//   const idMapping = {
//     taux_cr_ok: "tauxCR_OK",
//     delai_rdv_sav: "delaiPriseRDV",
//     client_satisfaits: "clientsatisfait",
//     clients_insatisfait: "clientsTresInsatisfaits"
//   };


//   for (const key in idMapping) {
//     const element = document.getElementById(idMapping[key]);
//     if (element && data[key] !== undefined) {
//       const value = (key === "delai_rdv_sav") ? `${data[key].toFixed(2)} %` : `${data[key].toFixed(2)}%`;

//       element.textContent = value;
//     }
//   }
// }
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
      let val;
      if (Array.isArray(data[key])) {
        // Prendre la dernière valeur du tableau
        val = data[key][data[key].length - 1];
      } else {
        val = data[key];
      }

      if (typeof val === "number") {
        const value = (key === "delai_rdv_sav") ? `${val.toFixed(2)} %` : `${val.toFixed(2)}%`;
        element.textContent = value;
      } else {
        element.textContent = "N/A";
        console.warn(`Valeur pour ${key} non numérique`, val);
      }
    }
  }
}


function genererLineChart(data , filtre){
  const labels = data.labels;
  const datasets = [];

  if (filtre === "tous" || filtre === "taux_cr_ok"){
    datasets.push({
      label : "Taux CR OK (%)",
      data: data.taux_cr_ok,
      borderColor: "#3e95cd",
      fill: false,
      tension: 0.2
    });
  }

  if (filtre === "tous" || filtre === "delai_rdv_sav"){
    datasets.push({
      label: "Délai prise RDV SAV(%)",
      data: data.delai_rdv_sav,
      borderColor: "#8e5ea2",
      fill: false,
      tension: 0.2
    });
  }

  if (filtre === "tous" || filtre ==="client_satisfaits"){
    datasets.push({
      label: "Satcli OK (%)",
      data: data.client_satisfaits,
      borderColor: "#3cba9f",
      fill : false,
      tension: 0.2 
    });
  }


  if (filtre === "tous" || filtre === "clients_insatisfait"){
    datasets.push({
      label :"Satcli NOK(%)",
      data: data.clients_insatisfait ,
      borderColor: "#e8c3b9",
      fill: false,
      tension: 0.2
    });
  }

  const lineCtx = document.getElementById("lineChart")?.getContext("2d");
  if (lineCtx){
    if (window.lineChart instanceof Chart) window.lineChart.destroy();
    window.lineChart = new Chart(lineCtx , {
      type: "line",
      data: {
        labels: labels,
        datasets: datasets
      },
      options: {
        responsive: true, 
        plugins : {
          title : {
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

//Initialise les filtres
function initAnalytics(){
  const selectFiltre = document.getElementById("filtreIndicateurLine");

  fetchDataAndRenderCharts("tous");

  selectFiltre?.addEventListener("change" ,function(){
    const filtre= this.value;
    fetchDataAndRenderCharts(filtre);
  });
  const boutonFiltre = document.getElementById("btnFiltreIndicateurLine");
  boutonFiltre?.addEventListener("click",function (){
    const filtre =selectFiltre.value;
    fetchDataAndRenderCharts(filtre);
  });
  //pour charger l'histogramme
  chargerHistogrammeDepartements();
}


// barchart
function initBarChartFiltre() {
  console.log("iniBarChartFiltre appellée");

  const selectDepart = document.getElementById("filtreIndicateurPie");
  const btnFiltrer = document.getElementById("btnFiltrerIndicateurPie");


  if (!selectDepart) console.warn("Select département introuvable");
  if (!btnFiltrer) console.warn("Bouton filtrer département introuvable");

  const updateBarChart = () => {
    const departement = selectDepart?.value || "SAV";
    console.log("Fetch bar chart avec département:", departement);
    fetch("/projet-stage/Analytics/getAnalyticsDataRacc.php?departement=" + departement)
      .then(response => response.json())
      .then(data => {
        console.log("Data reçue par fetch bar chart (RAcc):", data);
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
          beginAtZero: true
        }
      }
    }
  });
}



//pour le style de menu
document.addEventListener("DOMContentLoaded", function () {
  const select = document.getElementById("filtreIndicateurLine");
  initAnalytics(); // ✅ Appelle l'initialisation
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
  fetch("/projet-stage/Analytics/getAnalyticsDataRacc.php?type=departements")
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

// function genererGraphiques(data) {
//   const labels = ["Taux CR OK", "Sécurisation RDV", "Délai Prise RDV", "Insatisfaction"];
//   const valeurs = [
//     data.tauxCR_OK,
//     data.securisationRDV,
//     data.delaiPriseRDV,
//     data.clientsTresInsatisfaits
//   ];

//   // Création du lineChart
//   const lineCtx = document.getElementById("lineChart")?.getContext("2d");
//   if (lineCtx) {
//     // (Ici gestion de destruction du chart si besoin)
//     new Chart(lineCtx, {
//       type: "line",
//       data: {
//         labels: labels,
//         datasets: [{
//           label: "Indicateurs du mois courant",
//           data: valeurs,
//           borderColor: "#3e95cd",
//           fill: false,
//           tension: 0.2
//         }]
//       },
//       options: {
//         responsive: true,
//         plugins: {
//           title: { display: true, text: 'Évolution des indicateurs - Mois courant' }
//         },
//         scales: { y: { beginAtZero: true } }
//       }
//     });
//   }

//   // Création du pieChart
//   const pieCtx = document.getElementById("pieChart")?.getContext("2d");
//   if (pieCtx) {
//     new Chart(pieCtx, {
//       type: "pie",
//       data: {
//         labels: labels,
//         datasets: [{
//           data: valeurs,
//           backgroundColor: ["#4CAF50", "#2196F3", "#FFC107", "#F44336"]
//         }]
//       },
//       options: {
//         responsive: true,
//         plugins: {
//           title: { display: true, text: 'Répartition des indicateurs' }
//         }
//       }
//     });
//   }
// }
// document.querySelectorAll('.submenu-link').forEach(link => {
//   link.addEventListener('click', e => {
//     const page = e.target.dataset.page;
//     let url = '';

//     if (page === 'Analytics-Racc') {
//       url = 'Analytics-Racc.php';
//     } else if (page === 'SAV') {
//       url = 'SAV.php';
//     }

//     if (url) {
//       fetch(url)
//         .then(res => res.text())
//         .then(html => {
//           const mainContent = document.getElementById('mainContent');
//           if (mainContent) {
//             mainContent.innerHTML = html;  // Remplace le contenu affiché
//           }
//         })
//         .catch(err => console.error('Erreur chargement page:', err));
//     }

//   });
// });
