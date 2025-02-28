document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.querySelector(".sidebar");
    const toggle = document.getElementById("sidebarToggle");
    const navNameBrand = document.getElementById("navNameBrand");
    const navTexts = document.querySelectorAll(".nav-text");

    const archiveMenu = document.querySelector('.archive-menu');
    const archiveIcon = document.getElementById('archiveIcon');

    let tooltipList = [];

    function tooltips() {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        return [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    }

    function removeTooltips() {
        tooltipList.forEach(tooltip => tooltip.dispose());
        tooltipList = [];
    }

    window.closeSidebar = function () {
        sidebar.classList.add("closed");
        sidebar.style.width = "80px";
        navNameBrand.style.display = "none";
        navTexts.forEach(navText => navText.style.display = "none");
        toggle.classList.replace("bx-chevron-left", "bx-chevron-right");

        archiveMenu.classList.add('d-none');
        archiveIcon.classList.replace("bx-chevron-down", "bx-chevron-right");

        localStorage.setItem("sidebar", "closed");
        tooltipList = tooltips();
    };

    window.openSidebar = function () {
        sidebar.classList.remove("closed");
        sidebar.style.width = "250px";
        navNameBrand.style.display = "flex";
        navTexts.forEach(navText => navText.style.display = "flex");
        toggle.classList.replace("bx-chevron-right", "bx-chevron-left");

        localStorage.setItem("sidebar", "open");
        removeTooltips();
    };

    // Cek apakah sidebar sebelumnya ditutup
    if (localStorage.getItem("sidebar") === "closed") {
        closeSidebar();
    }

    toggle.addEventListener("click", function () {
        if (sidebar.classList.contains("closed")) {
            openSidebar();
        } else {
            closeSidebar();
        }
    });
});


window.toggleArchive = function () {
    const sidebar = document.querySelector(".sidebar");
    const archiveMenu = document.querySelector('.archive-menu');
    const archiveMenuMobile = document.querySelector('.archive-menu-mobile');
    const archiveIcon = document.getElementById('archiveIcon');
    const archiveIconMobile = document.getElementById('archiveIconMobile');

    if (sidebar.classList.contains("closed")) {
        openSidebar();
    }

    archiveMenu.classList.toggle('d-none');
    if (archiveMenuMobile) {
        archiveMenuMobile.classList.toggle('d-none');
    }

    archiveIcon.classList.toggle("bx-chevron-down");
    archiveIcon.classList.toggle("bx-chevron-right");

    if (archiveIconMobile) {
        archiveIconMobile.classList.toggle("bx-chevron-down");
        archiveIconMobile.classList.toggle("bx-chevron-right");
    }
};



// Image Preview
const previewImage = document.getElementById('img');
const inputImage = document.getElementById('input-img');

try {
    inputImage.onchange = (e) => {
        if (inputImage.files && inputImage.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                previewImage.src = e.target.result;
            };
            reader.readAsDataURL(inputImage.files[0]);
        }
    };
} catch (error) {
    console.log('Image preview not found!');
}
// Image Preview End
