<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Kyntus Maroc</title>
  <link rel="stylesheet" href="libs/bootstrap.min.css">
  <link rel="stylesheet" href="libs/fontawesome.min.css">
  <link rel="stylesheet" href="style.css">
  <!-- Ajoute ceci au tout début du <head> -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
  <!-- Analytics.html -->
 
  <!-- <script src="SAV/SAV.js"></script> -->
  <!-- <script src="Racc/RACC.js"></script> -->
   


  
  <!-- <link rel="stylesheet" href="Users/technicien.css"> -->

  
</head>
<body>
  <!-- Navbar -->
  <nav class="site-nav">
    <button class="sidebar-toggle">
      <span class="material-symbols-rounded">menu</span>
    </button>
  </nav>

  <!-- Conteneur principal -->
  <div class="container collapsed">
    <!-- Sidebar -->
    <aside class="sidebar collapsed">
      <!-- Header -->
      <header class="sidebar-header">
        <img src="log.png" alt="Logo" class="header-logo">
        <button class="sidebar-toggle">
          <span class="material-symbols-rounded">chevron_left</span>
        </button>
      </header>

      <!-- Contenu Sidebar -->
      <div class="sidebar-content">
        <!-- Formulaire de recherche -->
        <form action="#" class="search-form">
          <span class="material-symbols-rounded">search</span>
          <input type="search" placeholder="Search" required>
        </form>

        <!-- Menu -->
        <ul class="menu-list">
          <!-- Contrat Qualité -->
          <li class="menu-item dropdown">
            <a href="#" id="link-espace" class="menu-link active">
              <span class="material-symbols-rounded">apps</span>
              <span class="menu-label">Contrat Qualité</span>
            </a>
            <div class="submenu">
              <a href="javascript:void(0)" class="submenu-link" data-page="SAV">SAV</a>
              <a href="javascript:void(0)" class="submenu-link" data-page="RAC">RAC</a>
              <a href="javascript:void(0)" class="submenu-link" data-page="TOUS">TOUS</a>
            </div>
          </li>

          <!-- Analytics -->
          <li class="menu-item">
            <a href="javascript:void(0)" class="menu-link" data-page="Analytics">
              <span class="material-symbols-rounded">analytics</span>
              <span class="menu-label">Analytics</span>
            </a>
          </li>

          <!-- Dashboard
          <li class="menu-item">
            <a href="javascript:void(0)" class="menu-link" data-page="dash">
              <span class="material-symbols-rounded">space_dashboard</span>
              <span class="menu-label">Dashboard</span>
            </a>
          </li> -->

          <!-- Saisie Mesure
          <li class="menu-item">
            <a href="javascript:void(0)" class="menu-link">
              <span class="material-symbols-rounded">edit_square</span>
              <span class="menu-label">Saisie Mesure</span>
            </a>
          </li> -->

          <!-- Utilisateurs -->
          <li class="menu-item">
            <a href="Login/home.php" class="menu-link" >
              <span class="material-symbols-rounded">group</span>
              <span class="menu-label">Utilisateurs</span>
            </a>
          </li>
        </ul>
      </div>

      <!-- Footer -->
      <div class="sidebar-footer">
        <button class="theme-toggle">
          <div class="theme-label">
            <span class="theme-icon material-symbols-rounded">dark_mode</span>
            <span class="theme-text">Dark Mode</span>
          </div>
          <div class="theme-toggle-track">
            <div class="theme-toggle-indicator"></div>
          </div>
        </button>
      </div>
    </aside>

    <!-- Contenu principal -->
    <div class="main-content" id="contenu">
      <!-- Le contenu dynamique sera injecté ici -->
    </div>
  </div>

  <!-- Script principal -->
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      console.log("DOM chargé");

      const sidebar = document.querySelector(".sidebar");
      const sidebarToggleBtn = document.querySelectorAll(".sidebar-toggle");
      const themeToggleBtn = document.querySelector(".theme-toggle");
      const themeIcon = themeToggleBtn.querySelector(".theme-icon");
      const searchForm = document.querySelector(".search-form");
      const contenu = document.getElementById("contenu");
      const utilisateursLink = document.getElementById("load-utilisateurs");

      if (utilisateursLink) {
        utilisateursLink.addEventListener("click", (e) => {
          e.preventDefault();
          console.log("Click sur Utilisateurs détecté");
          setActiveMenu(utilisateursLink);

          fetch("Login/home.php", {
            headers: {
              "X-Requested-With": "XMLHttpRequest"
            }
          })
            .then(response => {
              console.log("Réponse HTTP :", response.status);
              if (!response.ok) throw new Error("Erreur HTTP " + response.status);
              return response.text();
            })
            .then(data => {
              console.log("Contenu reçu :", data);
              contenu.innerHTML = data;
            })
            .catch(error => {
              console.error("Erreur lors du fetch:", error);
              contenu.innerHTML = `<p style="color:red;">Erreur de chargement : ${error.message}</p>`;
            });
        });
      } else {
        console.warn("Élément #load-utilisateurs introuvable");
      }

      // const utilisateursLink = document.getElementById("load-utilisateurs");

      // === Gestion du thème ===
      const updateThemeIcon = () => {
        const isDark = document.body.classList.contains("dark-theme");
        themeIcon.textContent = sidebar.classList.contains("collapsed")
          ? (isDark ? "light_mode" : "dark_mode")
          : "dark_mode";
      };

      const savedTheme = localStorage.getItem("theme");
      const systemPrefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
      const shouldUseDark = savedTheme === "dark" || (!savedTheme && systemPrefersDark);
      document.body.classList.toggle("dark-theme", shouldUseDark);
      updateThemeIcon();

      sidebarToggleBtn.forEach((btn) => {
        btn.addEventListener("click", () => {
          sidebar.classList.toggle("collapsed");
          updateThemeIcon();
        });
      });

      searchForm.addEventListener("click", () => {
        if (sidebar.classList.contains("collapsed")) {
          sidebar.classList.remove("collapsed");
          searchForm.querySelector("input").focus();
        }
      });

      themeToggleBtn.addEventListener("click", () => {
        const isDark = document.body.classList.toggle("dark-theme");
        localStorage.setItem("theme", isDark ? "dark" : "light");
        updateThemeIcon();
      });

      // === Charger page dynamiquement ===
      async function loadPage(page) {
        const folder = page;
        const htmlPath = `${folder}/${folder}.php`;
        // const htmlPath = folder === 'Analytics' ? `${folder}/${folder}.php` : `${folder}/${folder}.html`;

        const cssPath = `${folder}/${folder}.css`;
        const jsPath = `${folder}/${folder}.js`;
        // const phpPath = `${folder}/${folder}.php`;
        const scriptId = `script-${folder}`;

        try {
          const response = await fetch(htmlPath);
          if (!response.ok) throw new Error(`Erreur HTTP ${response.status}`);
          const html = await response.text();

          if (!contenu) throw new Error("Élément #contenu introuvable dans le DOM");
          contenu.innerHTML = html;
          contenu.scrollTop = 0;

          if (!document.querySelector(`link[href="${cssPath}"]`)) {
            const link = document.createElement("link");
            link.rel = "stylesheet";
            link.href = cssPath;
            document.head.appendChild(link);
          }

          // Supprimer ancien script si existant
          const oldScript = document.getElementById(scriptId);
          if (oldScript) oldScript.remove();

          // **Déclaration avant utilisation**
          const script = document.createElement("script");
          script.src = jsPath;
          script.id = scriptId;

          // Mettre à jour les bonus APRES chargement du script
          script.onload = () => {
            if (page === "SAV" && typeof mettreAJourTotaux === "function") {
              mettreAJourTotaux();
            }
            //+++
            if (page === "Analytics") {
              fetchDataAndRenderCharts();

              // Attacher listener sur btnRacc maintenant que le contenu est injecté
              const raccBtn = document.getElementById("btnRacc");
              if (raccBtn) {
                raccBtn.addEventListener("click", () => {
                  fetch("Analytics/Analytics-Racc.php")
                    .then(res => {
                      if (!res.ok) throw new Error("Erreur chargement Analytics-Racc.php");
                      return res.text();
                    })
                    .then(html => {
                      document.getElementById("contenuRACC").innerHTML = html;

                      if (!document.querySelector('link[href="Analytics/Analytics-Racc.css"]')) {
                        const link = document.createElement("link");
                        link.rel = "stylesheet";
                        link.href = "Analytics/Analytics-Racc.css";
                        document.head.appendChild(link);
                      }

                      // Charger Analytics-Racc.js (supprimer l'ancien si besoin)
                      const oldScript = document.getElementById("script-Analytics-Racc");
                      if (oldScript) oldScript.remove();

                      const script = document.createElement("script");
                      script.id = "script-Analytics-Racc";
                      script.src = "Analytics/Analytics-Racc.js";
                      document.body.appendChild(script);
                    })
                    .catch(console.error);
                });
              }
            }

            // +++++//
            if (page === "Analytics" && typeof fetchDataAndRenderCharts === "function") {
              fetchDataAndRenderCharts();
            }
            if (typeof createCharts === "function") {
              createCharts();
            }
          };

          document.body.appendChild(script);

        } catch (err) {
          if (contenu) {
            contenu.innerHTML = `<p style="color:red;">Erreur lors du chargement de la page : ${err.message}</p>`;
          }
          console.error(err);
        }
      }


      // === Active menu ===
      function setActiveMenu(link) {
        document.querySelectorAll(".menu-link").forEach((l) => l.classList.remove("active"));
        link.classList.add("active");
      }

      // === Gestion des clics menu pages statiques ===
      document.querySelectorAll(".menu-link[data-page]").forEach((link) => {
        link.addEventListener("click", async (e) => {
          e.preventDefault();
          const page = link.dataset.page;
          setActiveMenu(link);
          await loadPage(page);
        });
      });
    //charger Racc
      document.querySelectorAll(".submenu-link").forEach((link) => {
        link.addEventListener("click", async (e) => {
          e.preventDefault();
          const page = link.dataset.page;

          if (page === "RAC") {
            try {
              // 1. Charger le HTML
              const responseHTML = await fetch("Racc/tableauracc.html");
              if (!responseHTML.ok) throw new Error(`Erreur chargement tableauracc.html (${responseHTML.status})`);
              const htmlContent = await responseHTML.text();
              contenu.innerHTML = htmlContent;

              // 2. Charger les données dynamiques initiales
              const responseData = await fetch("Racc/taux.php", {
                headers: { "X-Requested-With": "XMLHttpRequest" }
              });
              if (!responseData.ok) throw new Error(`Erreur chargement taux.php (${responseData.status})`);
              const data = await responseData.json();

              // 3. Charger dynamiquement le script RACC.js (suppression script précédent)
              await new Promise((resolve, reject) => {
                const ancienScript = document.getElementById("script-RACC");
                if (ancienScript) ancienScript.remove();

                const script = document.createElement("script");
                script.src = "Racc/RACC.js";
                script.id = "script-RACC";
                script.onload = () => {
                  console.log("RACC.js chargé avec succès");
                  resolve();
                };
                script.onerror = () => {
                  console.error("Erreur chargement RACC.js");
                  reject(new Error("Erreur chargement RACC.js"));
                };
                document.body.appendChild(script);
              });

              // 4. Appeler les fonctions d'initialisation après chargement script + HTML
              if (typeof initializeCache === "function") initializeCache();
              if (typeof initialiserCalculs === "function") initialiserCalculs();
              if (typeof attacherEventListeners === "function") attacherEventListeners();
              if (typeof initialiserToggleButtons === "function") initialiserToggleButtons();

              // 5. Mettre à jour la page avec les données initiales reçues
              if (typeof chargerIndicateurs === "function") {
                chargerIndicateurs(data);
              } else {
                console.error("La fonction chargerIndicateurs n'existe pas");
              }

              // 6. Attacher les event listeners sur les filtres (mois/année)
              if (typeof attacherEventListenersFiltres === "function") {
                attacherEventListenersFiltres();
              } else {
                console.warn("La fonction attacherEventListenersFiltres n'est pas définie");
              }

            } catch (error) {
              contenu.innerHTML = `<p style="color:red;">Erreur : ${error.message}</p>`;
              console.error(error);
            }
          }

          
          else {
            // Pour les autres pages, utiliser la fonction loadPage habituelle
            await loadPage(page);
          }
        });
      });

      // Sidebar par défaut repliée sur écran large
      if (window.innerWidth <= 768) {
        sidebar.classList.add("collapsed");
      } else {
        sidebar.classList.remove("collapsed");
      }

      // === Gestion clic "Utilisateurs" ===
      // utilisateursLink.addEventListener("click", (e) => {
      //   e.preventDefault();
      //   console.log("Click sur Utilisateurs détecté");
      //   setActiveMenu(utilisateursLink);

      //   fetch("Users/technicien.php", {
      //     headers: {
      //       "X-Requested-With": "XMLHttpRequest"
      //     }
      //   })
      //   .then(response => {
      //     console.log("Réponse HTTP :", response.status);
      //     if (!response.ok) throw new Error("Erreur HTTP " + response.status);
      //     return response.text();
      //   })
      //   .then(data => {
      //     console.log("Contenu reçu :", data);
      //     contenu.innerHTML = data;
      //   })
      //   .catch(error => {
      //     console.error("Erreur lors du fetch:", error);
      //     contenu.innerHTML = `<p style="color:red;">Erreur de chargement : ${error.message}</p>`;
      //   });
      // });

    });

    function attacherEventListenersFiltres() {
        const moisElem = document.getElementById('filterMonth');
        const anneeElem = document.getElementById('filterYear');

        if (moisElem && anneeElem) {
            moisElem.addEventListener('change', chargerIndicateurs);
            anneeElem.addEventListener('change', chargerIndicateurs);
        }
    }

    // window.addEventListener('load', () => {
    //     initializeCache(); // Initialiser le cache en premier
    //     initialiserCalculs();
    //     attacherEventListeners();
        
    //     initialiserToggleButtons();

    //     // Eventuellement, charger les données initiales au chargement de la page
    //     // chargerTousLesTaux();
    // });
    document.addEventListener("DOMContentLoaded", function () {
      if (!document.fonts.check("24px 'Material Symbols Rounded'")) {
        const link = document.createElement("link");
        link.rel = "stylesheet";
        link.href = "https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded";
        document.head.appendChild(link);
        console.log("Police Material Symbols rechargée dynamiquement.");
      }
    });




    


  </script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="Analytics/Analytics.js"></script>
</body>
</html>