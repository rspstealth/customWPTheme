document.addEventListener("DOMContentLoaded", function () {
    const menuToggle = document.querySelector(".menu-toggle");
    const menu = document.querySelector(".menu");

    // toggle display of main menu on small screens
    menuToggle.addEventListener("click", function () {
        menu.classList.toggle("active");

        // Force sub-menus to be always open when menu is toggled
        if (menu.classList.contains("active")) {
            document.querySelectorAll(".menu .sub-menu").forEach(subMenu => {
                subMenu.style.display = "block";
            });
        }
    });
});