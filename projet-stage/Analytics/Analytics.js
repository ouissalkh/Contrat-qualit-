// variable labels pour les etiquetes des graphes
const labels = ['OK', 'NOK', 'OK', 'OK'];
// les valeurs des etiquètes
const dataValues = [75, 60, 80, 30];
const colors = dataValues.map(val => {
  if (val >= 85) return '#229351ff';
  if (val >= 40) return '#229351ff';
  return '#e74c3c';
});

// creation des graphes

// Bar Chart
// const barCtx = document.getElementById('barChart').getContext('2d');
// new Chart(barCtx, {
//   type: 'bar',
//   data: {
//     labels: labels,
//     // les donnnes du tableau sont stocker dans datasets
//     datasets: [{
//       label: 'Valeur (%)', // le titre afficher dans la legende
//       data: dataValues, //les valeurs a afficher 
//       backgroundColor: colors // la couleur de chaque valeur
//     }]
//   },
//   options: {
//     // les plugens : title ,legend, tooltip
//     plugins: {
//       title: {
//         display: true,
//         text: 'Graphique en Barres'
//       }
//     }
//   }
// });

const barCanvas = document.getElementById('barChart');
if (barCanvas) {
  const barCtx = barCanvas.getContext('2d');
  new Chart(barCtx, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [{
        label: 'Valeur (%)',
        data: dataValues,
        backgroundColor: colors
      }]
    },
    options: {
      plugins: {
        title: {
          display: true,
          text: 'Graphique en Barres'
        }
      }
    }
  });
} else {
  console.warn("Canvas barChart introuvable dans le DOM.");
}


// Pie Chart
const pieCtx = document.getElementById('pieChart').getContext('2d');
new Chart(pieCtx, {
  type: 'pie',
  data: {
    labels: labels,
    datasets: [{
      data: dataValues,
      backgroundColor: colors
    }]
  },
  options: {
    plugins: {
      title: { 
        display: true, //le titre sera afficher
        text: 'Graphique en Camembert'
      }
    }
  }
});

// Recherche / filtre des cartes
function filtrerCartes() {
  const filtre = document.getElementById('searchInput').value.toLowerCase();
  const cartes = document.querySelectorAll('.indicateur-cards .card');

  cartes.forEach(carte => {
    const titre = carte.querySelector('h3').textContent.toLowerCase();
    carte.style.display = titre.includes(filtre) ? 'block' : 'none';
  });
}


// // pour le boutton sav racc
//   // Gestion du bouton pour afficher/masquer le menu déroulant
//   const toggleBtn = document.getElementById('toggle-menu');
//   const dropdown = document.getElementById('dropdown-menu');

//   toggleBtn.addEventListener('click', (e) => {
//     e.stopPropagation();
//     dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
//   });

//   document.addEventListener('click', () => {
//     dropdown.style.display = 'none';
//   });

//switch vers Analyticsracc
document.querySelectorAll('.submenu-link').forEach(link => {
    link.addEventListener('click', async function () {
      const page = this.dataset.page;
      await loadPage(page);
    });
  });

  async function loadPage(page) {
    const htmlPath = `${page}/${page}.html`;
    const cssPath = `${page}/${page}.css`;
    const jsPath = `${page}/${page}.js`;

    // Charger le HTML dans un conteneur (ex: <main id="main-content">)
    const response = await fetch(htmlPath);
    if (response.ok) {
      const html = await response.text();
      document.getElementById('main-content').innerHTML = html;
    } else {
      console.error(`Erreur de chargement de ${htmlPath}`);
    }
    // Injecter le CSS dynamiquement
    const existingCss = document.getElementById('dynamic-css');
    if (existingCss) existingCss.remove();

    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = cssPath;
    link.id = 'dynamic-css';
    document.head.appendChild(link);

    // Appliquer le CSS dynamiquement
    // Charger le JS dynamiquement
const existingScript = document.getElementById('dynamic-js');
if (existingScript) existingScript.remove();

const script = document.createElement('script');
script.src = jsPath;
script.id = 'dynamic-js';
script.onload = () => {
  console.log(`${jsPath} chargé avec succès`);
};
document.body.appendChild(script);

  }

document.addEventListener("DOMContentLoaded", () => {
  fetch("Analytics/indicateurs.php")
    .then(res => res.json())
    .then(data => {
      document.getElementById("taux-cr-ok").textContent = data.taux_cr_ok ? data.taux_cr_ok + "%" : "N/A";
      document.getElementById("securisation-rdv").textContent = data.securisation_rdv ? data.securisation_rdv + "%" : "N/A";
      document.getElementById("delai-rdv-sav").textContent = data.delai_rdv_sav ? data.delai_rdv_sav + "j" : "N/A";
      document.getElementById("clients-insatisfaits").textContent = data.clients_insatisfaits ? data.clients_insatisfaits + "%" : "N/A";
    })
    .catch(err => {
      console.error("Erreur lors de la récupération :", err);
    });
});