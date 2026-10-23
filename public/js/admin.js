document.addEventListener("DOMContentLoaded", function () {
    // ========================================
    // TOGGLE SIDEBAR
    // ========================================
    const toggleBtn = document.getElementById("toggleSidebar");
    const sidebar = document.getElementById("adminSidebar");

    // 1. BUAT OVERLAY SECARA OTOMATIS (BARU!)
    let overlay = document.querySelector(".sidebar-overlay");
    if (!overlay) {
        overlay = document.createElement("div");
        overlay.className = "sidebar-overlay";
        document.body.appendChild(overlay);
    }

    // 2. FUNGSI TOGGLE DENGAN OVERLAY (BARU!)
    function toggleSidebar() {
        sidebar.classList.toggle("open");
        overlay.classList.toggle("active");

        // Cegah scroll body saat sidebar terbuka (BARU!)
        if (sidebar.classList.contains("open")) {
            document.body.style.overflow = "hidden";
        } else {
            document.body.style.overflow = "";
        }
    }

    // 3. EVENT TOGGLE BUTTON (SAMA)
    if (toggleBtn) {
        toggleBtn.addEventListener("click", function (e) {
            e.preventDefault();
            toggleSidebar();
        });
    }

    // 4. EVENT OVERLAY (BARU!)
    overlay.addEventListener("click", function () {
        if (sidebar.classList.contains("open")) {
            toggleSidebar();
        }
    });

    // 5. AUTO CLOSE SAAT RESIZE KE DESKTOP (BARU!)
    window.addEventListener("resize", function () {
        if (window.innerWidth > 991) {
            sidebar.classList.remove("open");
            overlay.classList.remove("active");
            document.body.style.overflow = "";
        }
    });

    // ========================================
    // KONFIRMASI HAPUS (TETAP SAMA)
    // ========================================
    const deleteForms = document.querySelectorAll(".delete-form");
    deleteForms.forEach(form => {
        form.addEventListener("submit", function (e) {
            if (!confirm("Yakin ingin menghapus data ini?")) {
                e.preventDefault();
            }
        });
    });

    // ========================================
    // AUTO HIDE ALERT (TETAP SAMA)
    // ========================================
    const alerts = document.querySelectorAll(".alert");
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = "0";
            alert.style.transition = "opacity 0.5s ease";
            setTimeout(() => {
                alert.style.display = "none";
            }, 500);
        }, 5000);
    });
});
