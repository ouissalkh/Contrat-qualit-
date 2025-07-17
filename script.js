const sidebar = document.querySelector(".sidebar")
const sidebarToggleBtn = document.querySelectorAll(".sidebar-toggle");
const themeToggleBtn = document.querySelector(".theme-toggle");
const themeIcon = themeToggleBtn.querySelector(".theme-icon");
const searchForm = document.querySelector(".search-form");

//updates the theme icon based on current theme and sidbar state
const updateThemeIcon = () => {
    const isDark = document.body.classList.contains("dark-theme");
    themeIcon.textContent = sidebar.classList.contains("collapsed") ?
    (isDark ? "light_mode" : "dark_mode") : "dark_mode";
}

//Apply dark theme if saved or system prefers
const savedTheme = localStorage.getItem("theme" );
const systemPrefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
const shouldUserDarkTheme = savedTheme === "dark" || (!savedTheme && systemPrefersDark);

document.body.classList.toggle("dark-theme" , shouldUserDarkTheme);
updateThemeIcon();

//Toggle sidebar collapsed state on buttons click
sidebarToggleBtn.forEach((btn) => {
  btn.addEventListener("click" , () => {
    sidebar.classList.toggle("collapsed");
    updateThemeIcon();
  })
});

//Expand the sidebar when the search form is clicked
searchForm.addEventListener("click" ,() =>{
  if(sidebar.classList.contains("collapsed")){
    sidebar.classList.remove("collapsed");
    searchForm.querySelector("input").focus();//Focus the input
  }
});

//Toggle between theme on theme button click
themeToggleBtn.addEventListener("click",() => {
    const isDark = document.body.classList.toggle("dark-theme");
    localStorage.setItem("theme" , isDark ? "dark" : "light");
    updateThemeIcon();
});
if (window.innerWidth > 768) sidebar.classList.add
("collapsed");