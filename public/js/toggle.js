// Navbar: buka/tutup menu (layar kecil) dan submenu Profil
const menuBtn = document.querySelector("#menu");
const navbarNav = document.querySelector(".navbar-nav");

if (menuBtn && navbarNav) {
    // Tombol menu (hamburger)
    menuBtn.addEventListener("click", e => {
        e.preventDefault(); // href="#" tidak boleh melompat ke atas halaman
        navbarNav.classList.toggle("active");
    });

    // Submenu Profil: di layar kecil dibuka lewat ketukan.
    // Di desktop tetap dibuka lewat :hover di CSS.
    document.querySelectorAll(".navbar .dropdown > .dropbtn").forEach(btn => {
        btn.addEventListener("click", e => {
            e.preventDefault();
            btn.parentElement.classList.toggle("active");
        });
    });

    // Klik di luar menu: tutup menu dan submenu
    document.addEventListener("click", e => {
        if (!menuBtn.contains(e.target) && !navbarNav.contains(e.target)) {
            navbarNav.classList.remove("active");
            document
                .querySelectorAll(".navbar .dropdown.active")
                .forEach(d => d.classList.remove("active"));
        }
    });
}
