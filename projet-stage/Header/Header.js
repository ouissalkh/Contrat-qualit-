document.addEventListener("DOMContentLoaded", () => {
  const menuButton = document.querySelector('.menu-button');
  const dropdownMenu = document.querySelector('.dropdown-menu');

  if (menuButton && dropdownMenu) {
    menuButton.addEventListener('click', (e) => {
      e.stopPropagation();
      dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
    });

    document.addEventListener('click', () => {
      dropdownMenu.style.display = 'none';
    });
  }
});
