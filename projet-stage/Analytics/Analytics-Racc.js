







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
    if (element && data[key] !== undefined) {
      const value = key === "delaiPriseRDV" ? `${data[key].toFixed(2)} jours` : `${data[key].toFixed(2)}%`;
      element.textContent = value;
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
document.querySelectorAll('.submenu-link').forEach(link => {
  link.addEventListener('click', e => {
    const page = e.target.dataset.page;
    let url = '';

    if (page === 'Analytics-Racc') {
      url = 'Analytics-Racc.php';
    } else if (page === 'SAV') {
      url = 'SAV.php';
    }

    if (url) {
      fetch(url)
        .then(res => res.text())
        .then(html => {
          const mainContent = document.getElementById('mainContent');
          if (mainContent) {
            mainContent.innerHTML = html;  // Remplace le contenu affiché
          }
        })
        .catch(err => console.error('Erreur chargement page:', err));
    }
  });
});
