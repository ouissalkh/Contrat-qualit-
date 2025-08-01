// document.addEventListener("DOMContentLoaded", function () {
// // variable labels pour les etiquetes des graphes
// const labels = ['OK', 'NOK', 'OK', 'OK'];
// // les valeurs des etiquètes
// const dataValues = [75, 60, 80, 30];
// const colors = dataValues.map(val => {
//   if (val >= 85) return '#229351ff';
//   if (val >= 40) return '#229351ff';
//   return '#e74c3c';
// });

// // creation des graphes

// // Bar Chart
// // const barCtx = document.getElementById('barChart').getContext('2d');
// // new Chart(barCtx, {
// //   type: 'bar',
// //   data: {
// //     labels: labels,
// //     // les donnnes du tableau sont stocker dans datasets
// //     datasets: [{
// //       label: 'Valeur (%)', // le titre afficher dans la legende
// //       data: dataValues, //les valeurs a afficher 
// //       backgroundColor: colors // la couleur de chaque valeur
// //     }]
// //   },
// //   options: {
// //     // les plugens : title ,legend, tooltip
// //     plugins: {
// //       title: {
// //         display: true,
// //         text: 'Graphique en Barres'
// //       }
// //     }
// //   }
// // });

// const barCanvas = document.getElementById('barChart');
// if (barCanvas) {
//   const barCtx = barCanvas.getContext('2d');
//   new Chart(barCtx, {
//     type: 'bar',
//     data: {
//       labels: labels,
//       datasets: [{
//         label: 'Valeur (%)',
//         data: dataValues,
//         backgroundColor: colors
//       }]
//     },
//     options: {
//       plugins: {
//         title: {
//           display: true,
//           text: 'Graphique en Barres'
//         }
//       }
//     }
//   });
// } else {
//   console.warn("Canvas barChart introuvable dans le DOM.");
// }


// // Pie Chart
// const pieCtx = document.getElementById('pieChart').getContext('2d');
// new Chart(pieCtx, {
//   type: 'pie',
//   data: {
//     labels: labels,
//     datasets: [{
//       data: dataValues,
//       backgroundColor: colors
//     }]
//   },
//   options: {
//     plugins: {
//       title: { 
//         display: true, //le titre sera afficher
//         text: 'Graphique en Camembert'
//       }
//     }
//   }
// });

// // Recherche / filtre des cartes
// function filtrerCartes() {
//   const filtre = document.getElementById('searchInput').value.toLowerCase();
//   const cartes = document.querySelectorAll('.indicateur-cards .card');

//   cartes.forEach(carte => {
//     const titre = carte.querySelector('h3').textContent.toLowerCase();
//     carte.style.display = titre.includes(filtre) ? 'block' : 'none';
//   });
// }


// // // pour le boutton sav racc
// //   // Gestion du bouton pour afficher/masquer le menu déroulant
// //   const toggleBtn = document.getElementById('toggle-menu');
// //   const dropdown = document.getElementById('dropdown-menu');

// //   toggleBtn.addEventListener('click', (e) => {
// //     e.stopPropagation();
// //     dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
// //   });

// //   document.addEventListener('click', () => {
// //     dropdown.style.display = 'none';
// //   });

// //switch vers Analyticsracc
// document.querySelectorAll('.submenu-link').forEach(link => {
//     link.addEventListener('click', async function () {
//       const page = this.dataset.page;
//       await loadPage(page);
//     });
//   });

//   async function loadPage(page) {
//     const htmlPath = `${page}/${page}.html`;
//     const cssPath = `${page}/${page}.css`;
//     const jsPath = `${page}/${page}.js`;

//     // Charger le HTML dans un conteneur (ex: <main id="main-content">)
//     const response = await fetch(htmlPath);
//     if (response.ok) {
//       const html = await response.text();
//       document.getElementById('main-content').innerHTML = html;
//       document.getElementById('contenu').innerHTML = html;
//     } else {
//       console.error(`Erreur de chargement de ${htmlPath}`);
//     }
//     // Injecter le CSS dynamiquement
//     const existingCss = document.getElementById('dynamic-css');
//     if (existingCss) existingCss.remove();

//     const link = document.createElement('link');
//     link.rel = 'stylesheet';
//     link.href = cssPath;
//     link.id = 'dynamic-css';
//     document.head.appendChild(link);

//     // Appliquer le CSS dynamiquement
//     // Charger le JS dynamiquement
// const existingScript = document.getElementById('dynamic-js');
// if (existingScript) existingScript.remove();

// const script = document.createElement('script');
// script.src = jsPath;
// script.id = 'dynamic-js';
// script.onload = () => {
//   console.log(`${jsPath} chargé avec succès`);
// };
// document.body.appendChild(script);

//   }

// document.addEventListener("DOMContentLoaded", () => {
//   fetch("Analytics/indicateurs.php")
//     .then(res => res.json())
//     .then(data => {
//       document.getElementById("taux-cr-ok").textContent = data.taux_cr_ok ? data.taux_cr_ok + "%" : "N/A";
//       document.getElementById("securisation-rdv").textContent = data.securisation_rdv ? data.securisation_rdv + "%" : "N/A";
//       document.getElementById("delai-rdv-sav").textContent = data.delai_rdv_sav ? data.delai_rdv_sav + "j" : "N/A";
//       document.getElementById("clients-insatisfaits").textContent = data.clients_insatisfaits ? data.clients_insatisfaits + "%" : "N/A";
//     })
//     .catch(err => {
//       console.error("Erreur lors de la récupération :", err);
//     });
// });





// // Fonction de filtrage des cartes d'indicateurs
// function filtrerCartes() {
//   const filtre = document.getElementById('searchInput').value.toLowerCase();
//   const cartes = document.querySelectorAll('.indicateur-cards .card');

//   cartes.forEach(carte => {
//     const titre = carte.querySelector('h3').textContent.toLowerCase();
//     carte.style.display = titre.includes(filtre) ? 'block' : 'none';
//   });
// }

// // Fonction pour charger dynamiquement les pages via menu
// async function loadPage(page) {
//   const htmlPath = `${page}/${page}.html`;
//   const cssPath = `${page}/${page}.css`;
//   const jsPath = `${page}/${page}.js`;

//   try {
//     const response = await fetch(htmlPath);
//     if (!response.ok) throw new Error(`Erreur chargement ${htmlPath}`);
//     const html = await response.text();
//     document.getElementById('main-content').innerHTML = html;
//     document.getElementById('contenu').innerHTML = html;

//     // Injecter CSS
//     const existingCss = document.getElementById('dynamic-css');
//     if (existingCss) existingCss.remove();
//     const link = document.createElement('link');
//     link.rel = 'stylesheet';
//     link.href = cssPath;
//     link.id = 'dynamic-css';
//     document.head.appendChild(link);

//     // Injecter JS
//     const existingScript = document.getElementById('dynamic-js');
//     if (existingScript) existingScript.remove();
//     const script = document.createElement('script');
//     script.src = jsPath;
//     script.id = 'dynamic-js';
//     script.onload = () => console.log(`${jsPath} chargé avec succès`);
//     document.body.appendChild(script);
//   } catch (error) {
//     console.error(error);
//   }
// }

// // Gestion des clics sur les liens du sous-menu
// document.querySelectorAll('.submenu-link').forEach(link => {
//   link.addEventListener('click', async function () {
//     const page = this.dataset.page;
//     await loadPage(page);
//   });
// });

// // Chargement des indicateurs au chargement de la page
// document.addEventListener("DOMContentLoaded", () => {
//   chargerIndicateurs();
// });

// // Fonction principale pour charger et afficher les indicateurs + graphique
// function chargerIndicateurs() {
//   const mois = document.getElementById("mois")?.value || "all";
//   const annee = document.getElementById("annee")?.value || "all";

//   const url = `getAnalyticsData.php?mois=${mois}&annee=${annee}`;

//   fetch(url)
//     .then(response => {
//       if (!response.ok) throw new Error(`Erreur HTTP: ${response.status}`);
//       return response.json();
//     })
//     .then(data => {
//       afficherIndicateurs(data);
//       afficherGraphique(data);
//     })
//     .catch(error => {
//       console.error("Erreur lors du chargement :", error);
//     });
// }

// // Affiche les indicateurs dans les cartes
// function afficherIndicateurs(data) {
//   document.getElementById("tauxCR_OK")?.textContent = `${data.tauxCR_OK ?? 0}%`;
//   document.getElementById("delaiPriseRDV")?.textContent = `${data.delaiPriseRDV ?? 0} j`;
//   document.getElementById("tauxReports")?.textContent = `${data.tauxReports ?? 0}%`;
//   document.getElementById("rating")?.textContent = `${data.rating ?? 0}`;
//   document.getElementById("satisfaction")?.textContent = `${data.satisfaction ?? 0}%`;
//   document.getElementById("securisationRDV")?.textContent = `${data.securisationRDV ?? 0}%`;
// }

// // Affiche un graphique combiné bar + line avec Chart.js
// function afficherGraphique(data) {
//   const canvas = document.getElementById("barChart");
//   if (!canvas) {
//     console.error("Canvas barChart introuvable dans le DOM.");
//     return;
//   }
//   const ctx = canvas.getContext("2d");

//   const labels = ["CR OK", "Délai RDV",  "Satisfaction", "Sécurisation"];
//   const values = [
//     data.tauxCR_OK ?? 0,
//     data.delaiPriseRDV ?? 0,
//     // data.tauxReports ?? 0,
//     // data.rating ?? 0,
//     data.satisfaction ?? 0,
//     data.securisationRDV ?? 0
//   ];

//   // Détruire le graphique existant si nécessaire (évite empilement)
//   if (window.myChart instanceof Chart) {
//     window.myChart.destroy();
//   }

//   window.myChart = new Chart(ctx, {
//     data: {
//       labels: labels,
//       datasets: [
//         {
//           type: 'bar',
//           label: "Indicateurs (Barres)",
//           data: values,
//           backgroundColor: [
//             "#4CAF50", "#2196F3", "#FFC107", "#FF5722", "#9C27B0", "#3F51B5"
//           ]
//         },
//         {
//           type: 'line',
//           label: "Tendance (Courbe)",
//           data: values,
//           borderColor: "#000",
//           borderWidth: 2,
//           fill: false,
//           tension: 0.3
//         }
//       ]
//     },
//     options: {
//       responsive: true,
//       scales: {
//         y: {
//           beginAtZero: true,
//           max: 100
//         }
//       },
//       plugins: {
//         title: {
//           display: true,
//           text: "Dashboard Indicateurs (Barres + Courbe)"
//         }
//       }
//     }
//   });
// }








document.addEventListener("DOMContentLoaded", function () {
  fetchDataAndRenderCharts();
});

function fetchDataAndRenderCharts() {
  fetch("/projet-stage/Analytics/getAnalyticsData.php")

    .then(response => response.json())
    .then(data => {
      console.log("Données reçues :", data);
      afficherIndicateurs(data);
      genererGraphiques(data);
    })
    .catch(error => {
      console.error("Erreur lors de la récupération des données :", error);
    });
}

function afficherIndicateurs(data) {
  const idMapping = {
    tauxCR_OK: "tauxCR_OK",
    securisationRDV: "securisationRDV",
    delaiPriseRDV: "delaiPriseRDV",
    clientsTresInsatisfaits: "clientsTresInsatisfaits"
  };

  for (const key in idMapping) {
    const element = document.getElementById(idMapping[key]);
    if (element) {
      if (data[key] === null || data[key] === undefined) {
        element.textContent = "N/A";
      } else {
        const value = key === "delaiPriseRDV" ? `${data[key].toFixed(2)} jours` : `${data[key].toFixed(2)}%`;
        element.textContent = value;
      }
    }
  }
}

function genererGraphiques(data) {
  const labels = ["Taux CR OK", "Sécurisation RDV", "Délai Prise RDV", "Insatisfaction"];
  const valeurs = [
    data.tauxCR_OK,
    data.securisationRDV,
    data.delaiPriseRDV,
    data.clientsTresInsatisfaits
  ];

  // Création du lineChart
  const lineCtx = document.getElementById("lineChart")?.getContext("2d");
  if (lineCtx) {
    // (Ici gestion de destruction du chart si besoin)
    new Chart(lineCtx, {
      type: "line",
      data: {
        labels: labels,
        datasets: [{
          label: "Indicateurs du mois courant",
          data: valeurs,
          borderColor: "#3e95cd",
          fill: false,
          tension: 0.2
        }]
      },
      options: {
        responsive: true,
        plugins: {
          title: { display: true, text: 'Évolution des indicateurs - Mois courant' }
        },
        scales: { y: { beginAtZero: true } }
      }
    });
  }

  // Création du pieChart
  const pieCtx = document.getElementById("pieChart")?.getContext("2d");
  if (pieCtx) {
    new Chart(pieCtx, {
      type: "pie",
      data: {
        labels: labels,
        datasets: [{
          data: valeurs,
          backgroundColor: ["#4CAF50", "#2196F3", "#FFC107", "#F44336"]
        }]
      },
      options: {
        responsive: true,
        plugins: {
          title: { display: true, text: 'Répartition des indicateurs' }
        }
      }
    });
  }
}
// filtrage
document.getElementById("filterForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const annee = document.getElementById("annee").value;
  const mois = document.getElementById("mois").value;
  const semaine = document.getElementById("semaine").value;

  fetchDataAndRenderCharts(annee, mois, semaine);
});

function fetchDataAndRenderCharts(annee = null, mois = null, semaine = null) {
  let url = "/projet-stage/Analytics/getAnalyticsData.php";
  const params = new URLSearchParams();

  if (annee) params.append("annee", annee);
  if (mois) params.append("mois", mois);
  if (semaine && semaine !== "toutes") params.append("semaine", semaine);

  if (params.toString()) {
    url += "?" + params.toString();
  }

  fetch(url)
    .then(response => response.json())
    .then(data => {
      afficherIndicateurs(data);
      genererGraphiques(data);
    })
    .catch(error => {
      console.error("Erreur lors de la récupération des données :", error);
    });
}
