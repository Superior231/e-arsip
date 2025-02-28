document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.querySelector(".sidebar");
    const toggle = document.getElementById("sidebarToggle");
    const navNameBrand = document.getElementById("navNameBrand");
    const navTexts = document.querySelectorAll(".nav-text");

    const archiveMenu = document.querySelector('.archive-menu');
    const archiveMenuMobile = document.querySelector('.archive-menu-mobile');

    let tooltipList = [];

    function tooltips() {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        return [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    }

    function removeTooltips() {
        tooltipList.forEach(tooltip => tooltip.dispose());
        tooltipList = [];
    }

    function closeSidebar() {
        sidebar.classList.add("closed");
        sidebar.style.width = "80px";
        navNameBrand.style.display = "none";
        navTexts.forEach(navText => navText.style.display = "none");
        toggle.classList.remove("bx-chevron-left");
        toggle.classList.add("bx-chevron-right");
        archiveMenu.classList.add('d-none');
        localStorage.setItem("sidebar", "closed");
        tooltipList = tooltips();
    }

    function openSidebar() {
        sidebar.classList.remove("closed");
        sidebar.style.width = "250px";
        navNameBrand.style.display = "flex";
        navTexts.forEach(navText => navText.style.display = "flex");
        toggle.classList.remove("bx-chevron-right");
        toggle.classList.add("bx-chevron-left");
        localStorage.setItem("sidebar", "open");
        removeTooltips();
    }

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



const toggleArchive = () => {
    const archiveMenu = document.querySelector('.archive-menu');
    const archiveMenuMobile = document.querySelector('.archive-menu-mobile');

    archiveMenuMobile.classList.toggle('d-none');
    archiveMenu.classList.toggle('d-none');
}