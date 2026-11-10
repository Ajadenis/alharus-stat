// Slideshow beranda: navigasi manual (panah/dot) + autoplay 4 detik.
// Autoplay berhenti saat kursor di atas slideshow.

let slideIndex = 1;
let slideInterval = null;
let sedangHover = false;

document.addEventListener("DOMContentLoaded", function () {
    // Halaman tanpa slide: tidak perlu timer sama sekali
    if (document.getElementsByClassName("mySlides").length === 0) return;

    showSlides(slideIndex);
    startAutoSlide();

    const container = document.querySelector(".slideshow-container");
    if (container) {
        container.addEventListener("mouseenter", function () {
            sedangHover = true;
            stopAutoSlide();
        });
        container.addEventListener("mouseleave", function () {
            sedangHover = false;
            startAutoSlide();
        });
    }
});

function showSlides(n) {
    const slides = document.getElementsByClassName("mySlides");
    const dots = document.getElementsByClassName("dot");

    if (slides.length === 0) return;

    if (n > slides.length) slideIndex = 1;
    if (n < 1) slideIndex = slides.length;

    for (const slide of slides) {
        slide.style.display = "none";
        slide.style.opacity = "0";
    }
    for (const dot of dots) {
        dot.classList.remove("active");
    }

    const aktif = slides[slideIndex - 1];
    aktif.style.display = "block";
    setTimeout(() => {
        aktif.style.opacity = "1"; // efek fade
    }, 50);

    if (dots[slideIndex - 1]) {
        dots[slideIndex - 1].classList.add("active");
    }
}

// Panah prev/next
function plusSlides(n) {
    stopAutoSlide();
    showSlides((slideIndex += n));
    if (!sedangHover) startAutoSlide();
}

// Dot
function currentSlide(n) {
    stopAutoSlide();
    showSlides((slideIndex = n));
    if (!sedangHover) startAutoSlide();
}

function startAutoSlide() {
    stopAutoSlide();
    slideInterval = setInterval(() => plusSlides(1), 4000);
}

function stopAutoSlide() {
    clearInterval(slideInterval);
}
