<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Kyntus Maroc</title>
  <link rel="stylesheet" href="libs/bootstrap.min.css" />
  <link rel="stylesheet" href="libs/fontawesome.min.css" />
  <link rel="stylesheet" href="style.css" />

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0"
  />
</head>
<body>
  <!-- Navbar -->
  <nav class="site-nav">
    <button class="sidebar-toggle" aria-label="Toggle sidebar">
      <span class="material-symbols-rounded">menu</span>
    </button>
  </nav>

  <!-- Conteneur principal -->
  <div class="container collapsed">
    <!-- Sidebar -->
    <aside class="sidebar collapsed" role="navigation" aria-label="Sidebar menu">
      <header class="sidebar-header">
        <img src="logo.png" alt="Logo" class="header-logo" />
        <button class="sidebar-toggle" aria-label="Toggle sidebar">
          <span class="material-symbols-rounded">chevron_left</span>
        </button>
      </header>

      <div class="sidebar-content">
        <form action="#" class="search-form" role="search">
          <span class="material-symbols-rounded">search</span>
          <input type="search" placeholder="Search" aria-label="Search" required />
        </form>

        <ul class="menu-list">
          <li class="menu-item dropdown">
            <a href="#" id="link-espace" class="menu-link active" aria-haspopup="true" aria-expanded="false">
              <span class="material-symbols-rounded">apps</span>
              <span class="menu-label">Contrat Qualité</span>
            </a>
            <div class="submenu" aria-label="Sous-menu Contrat Qualité">
              <a href="javascript:void(0)" class="submenu-link" data-page="SAV">SAV</a>
              <a href="javascript:void(0)" class="submenu-link" data-page="RACC">RACC</a>
            </div>
          </li>

          <li class="menu-item dropdown">
            <a href="javascript:void(0)" class="menu-link">
              <span class="material-symbols-rounded">analytics</span>
              <span class="menu-label">Analytics</span>
            </a>
            <div class="submenu" aria-label="Sous-menu Analytics">
              <a href="javascript:void(0)" class="submenu-link" data-page="Analytics/Analytics">SAV</a>
              <a href="javascript:void(0)" class="submenu-link" data-page="Analytics/Analytics-Racc">RACC</a>         
            </div>
          </li>


          <li class="menu-item">
            <a href="Login/home.php" class="menu-link">
              <span class="material-symbols-rounded">group</span>
              <span class="menu-label">Utilisateurs</span>
            </a>
          </li>
        </ul>
      </div>

      <div class="sidebar-footer">
        <button class="theme-toggle" aria-label="Toggle dark mode">
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

    <main class="main-content" id="contenu" tabindex="-1" role="main">
      <!-- Contenu dynamique chargé ici -->
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const sidebar = document.querySelector(".sidebar");
      const sidebarToggleBtn = document.querySelectorAll(".sidebar-toggle");
      const themeToggleBtn = document.querySelector(".theme-toggle");
      const themeIcon = themeToggleBtn.querySelector(".theme-icon");
      const searchForm = document.querySelector(".search-form");
      const contenu = document.getElementById("contenu");

      // Gestion thème
      function updateThemeIcon() {
        const isDark = document.body.classList.contains("dark-theme");
        themeIcon.textContent = sidebar.classList.contains("collapsed")
          ? (isDark ? "light_mode" : "dark_mode")
          : "dark_mode";
      }
      const savedTheme = localStorage.getItem("theme");
      const systemPrefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
      const shouldUseDark = savedTheme === "dark" || (!savedTheme && systemPrefersDark);
      document.body.classList.toggle("dark-theme", shouldUseDark);
      updateThemeIcon();

      // Toggle sidebar
      sidebarToggleBtn.forEach(btn => btn.addEventListener("click", () => {
        sidebar.classList.toggle("collapsed");
        updateThemeIcon();
      }));

      // Focus champ recherche si sidebar repliée
      searchForm.addEventListener("click", () => {
        if (sidebar.classList.contains("collapsed")) {
          sidebar.classList.remove("collapsed");
          searchForm.querySelector("input").focus();
        }
      });

      // Toggle thème dark/light
      themeToggleBtn.addEventListener("click", () => {
        const isDark = document.body.classList.toggle("dark-theme");
        localStorage.setItem("theme", isDark ? "dark" : "light");
        updateThemeIcon();
      });

      // Chargement dynamique d'une page
      // async function loadPage(page) {
      //   let htmlPath, cssPath, jsPath, scriptId;

      //   if (page.startsWith("Analytics/")) {
      //     htmlPath = `${page}.php`;
      //     cssPath = `${page}.css`;
      //     jsPath = `${page}.js`;
      //     scriptId = `script-${page.replace("/", "-")}`;
      //   } else {
      //     htmlPath = `${page}/${page}.php`;
      //     cssPath = `${page}/${page}.css`;
      //     jsPath = `${page}/${page}.js`;
      //     scriptId = `script-${page}`;
      //   }

      //   try {
      //     const response = await fetch(htmlPath);
      //     if (!response.ok) throw new Error(`Erreur HTTP ${response.status}`);
      //     const html = await response.text();

      //     contenu.innerHTML = html;
      //     contenu.scrollTop = 0;
      //     contenu.focus();

      //     // Charger CSS si absent
      //     if (!document.querySelector(`link[href="${cssPath}"]`)) {
      //       const link = document.createElement("link");
      //       link.rel = "stylesheet";
      //       link.href = cssPath;
      //       document.head.appendChild(link);
      //     }

      //     // Retirer ancien script
      //     const oldScript = document.getElementById(scriptId);
      //     if (oldScript) oldScript.remove();

      //     // Charger script JS
      //     const script = document.createElement("script");
      //     script.src = jsPath;
      //     script.id = scriptId;
      //     script.onload = async () => {
      //       if (page === "Analytics/Analytics-Racc") {
      //         const dataResponse = await fetch("Analytics/getAnalyticsDataRacc.php", {
      //           headers: { "X-Requested-With": "XMLHttpRequest" }
      //         });
      //         if (!dataResponse.ok) throw new Error(`Erreur chargement des données RACC (${dataResponse.status})`);
      //         const data = await dataResponse.json();

      //         if (typeof afficherIndicateurs === "function") {
      //           afficherIndicateurs(data);
      //         }
      //       }
      //     };
      //     document.body.appendChild(script);

      //   } catch (err) {
      //     contenu.innerHTML = `<p style="color:red;">Erreur lors du chargement : ${err.message}</p>`;
      //     console.error(err);
      //   }
      // }
      async function loadPage(page) {
        let htmlPath, cssPath, jsPath, scriptId;

        if (page.startsWith("Analytics/")) {
          htmlPath = `${page}.php`;
          cssPath = `${page}.css`;
          jsPath = `${page}.js`;
          scriptId = `script-${page.replace("/", "-")}`;
        } else {
          htmlPath = `${page}/${page}.php`;
          cssPath = `${page}/${page}.css`;
          jsPath = `${page}/${page}.js`;
          scriptId = `script-${page}`;
        }

        try {
          const response = await fetch(htmlPath);
          if (!response.ok) throw new Error(`Erreur HTTP ${response.status}`);
          const html = await response.text();

          contenu.innerHTML = html;
          contenu.scrollTop = 0;
          contenu.focus();

          // Charger CSS (en supprimant l'ancien)
          injectCSS(cssPath);

          // Retirer ancien script
          const oldScript = document.getElementById(scriptId);
          if (oldScript) oldScript.remove();

          // Charger script JS
          await injectScript(jsPath, scriptId);

          // Appeler fonction spécifique selon la page
          if (page === "Analytics/Analytics-Racc") {
            if (typeof initAnalytics === "function") {
              initAnalytics();
            }
            const dataResponse = await fetch("Analytics/getAnalyticsDataRacc.php", {
              headers: { "X-Requested-With": "XMLHttpRequest" }

            });
            if (!dataResponse.ok) throw new Error(`Erreur chargement des données RACC (${dataResponse.status})`);
            const data = await dataResponse.json();

            if (typeof afficherIndicateurs === "function") {
              afficherIndicateurs(data);
            }
          } else if (page === "Analytics/Analytics") {
            if (typeof initAnalytics === "function") {
              initAnalytics();
            }
          }

        } catch (err) {
          contenu.innerHTML = `<p style="color:red;">Erreur lors du chargement : ${err.message}</p>`;
          console.error(err);
        }
      }

      // async function loadPage(page) {
      //   const htmlPath = `${page}/${page}.php`;
      //   const cssPath = `${page}/${page}.css`;
      //   const jsPath = `${page}/${page}.js`;
      //   const scriptId = `script-${page}`;

      //   try {
      //     const response = await fetch(htmlPath);
      //     if (!response.ok) throw new Error(`Erreur HTTP ${response.status}`);
      //     const html = await response.text();

      //     contenu.innerHTML = html;
      //     contenu.scrollTop = 0;
      //     contenu.focus();

      //     // Charger CSS si absent
      //     if (!document.querySelector(`link[href="${cssPath}"]`)) {
      //       const link = document.createElement("link");
      //       link.rel = "stylesheet";
      //       link.href = cssPath;
      //       document.head.appendChild(link);
      //     }

      //     // Retirer ancien script
      //     const oldScript = document.getElementById(scriptId);
      //     if (oldScript) oldScript.remove();

      //     // Charger script JS
      //     const script = document.createElement("script");
      //     script.src = jsPath;
      //     script.id = scriptId;
      //     script.onload = () => {
      //       // Initialisation spécifique par page si besoin
      //       if (page === "Analytics" && typeof initAnalytics === "function") initAnalytics();
      //       if (page === "SAV" && typeof mettreAJourTotaux === "function") mettreAJourTotaux();
      //     };
      //     document.body.appendChild(script);

      //   } catch (err) {
      //     contenu.innerHTML = `<p style="color:red;">Erreur lors du chargement : ${err.message}</p>`;
      //     console.error(err);
      //   }
      // }

      // Gère activation menu
      function setActiveMenu(link) {
        document.querySelectorAll(".menu-link").forEach(l => l.classList.remove("active"));
        link.classList.add("active");
      }

      // Clic sur menu pages dynamiques
      document.querySelectorAll(".menu-link[data-page]").forEach(link => {
        link.addEventListener("click", async (e) => {
          e.preventDefault();
          setActiveMenu(link);
          await loadPage(link.dataset.page);
        });
      });

      // Clic sur sous-menus
      document.querySelectorAll(".submenu-link").forEach(link => {
        link.addEventListener("click", async (e) => {
          e.preventDefault();
          const page = link.dataset.page;
          if (page === "RACC") {
            try {
              const htmlResponse = await fetch("Racc/tableauracc.html");
              if (!htmlResponse.ok) throw new Error(`Erreur chargement tableauracc.html (${htmlResponse.status})`);
              contenu.innerHTML = await htmlResponse.text();

              const dataResponse = await fetch("Racc/taux.php", { headers: { "X-Requested-With": "XMLHttpRequest" } });
              if (!dataResponse.ok) throw new Error(`Erreur chargement taux.php (${dataResponse.status})`);
              const data = await dataResponse.json();

              const oldScript = document.getElementById("script-RACC");
              if (oldScript) oldScript.remove();

              await new Promise((resolve, reject) => {
                const script = document.createElement("script");
                script.src = "Racc/RACC.js";
                script.id = "script-RACC";
                script.onload = () => resolve();
                script.onerror = () => reject(new Error("Erreur chargement RACC.js"));
                document.body.appendChild(script);
              });

              // Initialisations RACC
              if (typeof initializeCache === "function") initializeCache();
              if (typeof initialiserCalculs === "function") initialiserCalculs();
              if (typeof attacherEventListeners === "function") attacherEventListeners();
              if (typeof initialiserToggleButtons === "function") initialiserToggleButtons();
              if (typeof chargerIndicateurs === "function") chargerIndicateurs(data);
              if (typeof attacherEventListenersFiltres === "function") attacherEventListenersFiltres();

            } catch (error) {
              contenu.innerHTML = `<p style="color:red;">Erreur : ${error.message}</p>`;
              console.error(error);
            }
          } else {
            await loadPage(page);
          }
        });
      });


      // Sidebar collapsed par défaut sur petit écran
      if (window.innerWidth <= 768) {
        sidebar.classList.add("collapsed");
      } else {
        sidebar.classList.remove("collapsed");
      }

      // Recharge police Material Symbols si absente
      if (!document.fonts.check("24px 'Material Symbols Rounded'")) {
        const link = document.createElement("link");
        link.rel = "stylesheet";
        link.href = "https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded";
        document.head.appendChild(link);
      }

      // Charge la page par défaut au chargement (exemple : SAV)
      loadPage("SAV");
    });


    // //sous menu de Analytics
    // document.querySelectorAll(".submenu-link").forEach(link => {
    //   link.addEventListener("click", async (e) => {
    //     e.preventDefault();

    //     const page = link.dataset.page;
    //     const contenu = document.getElementById("contenu");

    //     try {
    //       if (page === "RACC") {
    //         // Charger HTML RACC
    //         const htmlResponse = await fetch("Analytics/Analytics-Racc.php");
    //         if (!htmlResponse.ok) throw new Error(`Erreur chargement Analytics-Racc.php (${htmlResponse.status})`);
    //         contenu.innerHTML = await htmlResponse.text();

    //         // Injecter CSS RACC
    //         await injectCSS("Analytics/Analytics-Racc.css");

    //         // Charger données depuis getAnalyticsData.php spécifique à RACC
    //         const dataResponse = await fetch("Analytics/getAnalyticsData.php", {
    //           headers: { "X-Requested-With": "XMLHttpRequest" }
    //         });
    //         if (!dataResponse.ok) throw new Error(`Erreur chargement des données RACC (${dataResponse.status})`);
    //         const data = await dataResponse.json();

    //         // Charger script RACC
    //         await injectScript("Analytics/Analytics-Racc.js", "script-RACC");

    //         // Appeler une fonction d'affichage si elle existe
    //         if (typeof afficherIndicateurs === "function") {
    //           afficherIndicateurs(data);
    //         }

    //       } else if (page === "SAV") {
    //         // Charger HTML SAV
    //         const htmlResponse = await fetch("Analytics/Analytics.php");
    //         if (!htmlResponse.ok) throw new Error(`Erreur chargement Analytics.php (${htmlResponse.status})`);
    //         contenu.innerHTML = await htmlResponse.text();

    //         // Injecter CSS SAV
    //         await injectCSS("Analytics/Analytics.css");

    //         // Charger données depuis getAnalyticsData.php pour SAV
    //         const dataResponse = await fetch("Analytics/getAnalyticsData.php", {
    //           headers: { "X-Requested-With": "XMLHttpRequest" }
    //         });
    //         if (!dataResponse.ok) throw new Error(`Erreur chargement des données SAV (${dataResponse.status})`);
    //         const data = await dataResponse.json();

    //         // Charger script SAV
    //         await injectScript("Analytics/Analytics.js", "script-SAV");

    //         // Appeler une fonction d'affichage si elle existe
    //         if (typeof afficherIndicateurs === "function") {
    //           afficherIndicateurs(data);
    //         }
    //       }
    //     } catch (error) {
    //       contenu.innerHTML = `<p style="color:red;">${error.message}</p>`;
    //     }
    //   });
    // });

    // Fonction d'injection JS
    async function injectScript(src, id) {
      const oldScript = document.getElementById(id);
      if (oldScript) oldScript.remove();

      await new Promise((resolve, reject) => {
        const script = document.createElement("script");
        script.src = src;
        script.id = id;
        script.onload = resolve;
        script.onerror = () => reject(new Error(`Erreur chargement du script ${src}`));
        document.body.appendChild(script);
      });
    }

    // Fonction d'injection CSS
    function injectCSS(href) {
      const oldLink = document.getElementById("style-analytics");
      if (oldLink) oldLink.remove();

      const link = document.createElement("link");
      link.rel = "stylesheet";
      link.href = href;
      link.id = "style-analytics";
      document.head.appendChild(link);
    }


  </script>
</body>
</html>
