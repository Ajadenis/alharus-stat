// ========================================
// SLIDESHOW dengan Navigasi Manual & Autoplay
// ========================================

let slideIndex = 1; // Mulai dari slide 1
let slideInterval;

// Jalankan slideshow saat halaman dimuat
document.addEventListener("DOMContentLoaded", function () {
    showSlides(slideIndex);
    startAutoSlide();

    // Pause autoplay saat hover
    const container = document.querySelector(".slideshow-container");
    if (container) {
        container.addEventListener("mouseenter", function () {
            clearInterval(slideInterval);
        });
        container.addEventListener("mouseleave", function () {
            startAutoSlide();
        });
    }
});

// Fungsi untuk menampilkan slide berdasarkan index
function showSlides(n) {
    let slides = document.getElementsByClassName("mySlides");
    let dots = document.getElementsByClassName("dot");

    // Jika tidak ada slide, berhenti
    if (slides.length === 0) return;

    // Reset jika index melebihi batas
    if (n > slides.length) {
        slideIndex = 1;
    }
    if (n < 1) {
        slideIndex = slides.length;
    }

    // Sembunyikan semua slide
    for (let i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
        slides[i].style.opacity = "0";
    }

    // Nonaktifkan semua dot
    for (let i = 0; i < dots.length; i++) {
        dots[i].className = dots[i].className.replace(" active", "");
    }

    // Tampilkan slide yang aktif dengan animasi fade
    slides[slideIndex - 1].style.display = "block";
    setTimeout(() => {
        slides[slideIndex - 1].style.opacity = "1";
    }, 50);

    // Aktifkan dot yang sesuai
    if (dots.length > 0 && dots[slideIndex - 1]) {
        dots[slideIndex - 1].className += " active";
    }
}

// Fungsi untuk navigasi Next/Previous
function plusSlides(n) {
    clearInterval(slideInterval); // Hentikan autoplay sementara
    showSlides((slideIndex += n));
    startAutoSlide(); // Mulai ulang autoplay
}

// Fungsi untuk pindah ke slide tertentu (via dot)
function currentSlide(n) {
    clearInterval(slideInterval);
    showSlides((slideIndex = n));
    startAutoSlide();
}

// Fungsi untuk memulai autoplay
function startAutoSlide() {
    clearInterval(slideInterval); // Hapus interval yang lama
    slideInterval = setInterval(function () {
        plusSlides(1);
    }, 4000); // Ganti setiap 4 detik
}

// ========================================
// VERSI SEDERHANA (Jika Anda lebih suka)
// ========================================

/*
// Versi sederhana tanpa fitur hover pause
let slideIndex = 0;

function showSlides() {
    let i;
    let slides = document.getElementsByClassName("mySlides");
    let dots = document.getElementsByClassName("dot");
    
    for (i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }
    
    slideIndex++;
    if (slideIndex > slides.length) {
        slideIndex = 1;
    }
    
    // Update dots
    for (i = 0; i < dots.length; i++) {
        dots[i].className = dots[i].className.replace(" active", "");
    }
    
    slides[slideIndex - 1].style.display = "block";
    if (dots.length > 0) {
        dots[slideIndex - 1].className += " active";
    }
    
    setTimeout(showSlides, 4000);
}

// Jalankan slideshow
document.addEventListener('DOMContentLoaded', showSlides);

// Fungsi untuk navigasi manual
function plusSlides(n) {
    slideIndex += n - 1;
    if (slideIndex < 0) slideIndex = 0;
    showSlides();
}

function currentSlide(n) {
    slideIndex = n - 1;
    showSlides();
}
*/
