// Slider functionality - vanilla JS

export function initSlider() {
    const slider = document.getElementById('slider');
    if (!slider) return;

    const slides = slider.querySelectorAll('.slide');
    const prevBtn = document.getElementById('slider-prev');
    const nextBtn = document.getElementById('slider-next');
    const totalSlides = parseInt(slider.dataset.total, 10);

    let currentSlide = 0;
    let intervalId = null;
    let isInView = false;

    function showSlide(index) {
        slides.forEach((slide, i) => {
            if (i === index) {
                // Show current slide
                slide.classList.remove('opacity-0', 'translate-x-full', '-translate-x-full', 'pointer-events-none');
                slide.classList.add('opacity-100', 'translate-x-0');
                slide.removeAttribute('aria-hidden');
            } else if (i < index) {
                // Hide slides to the left
                slide.classList.remove('opacity-100', 'translate-x-0', 'translate-x-full');
                slide.classList.add('opacity-0', '-translate-x-full', 'pointer-events-none');
                slide.setAttribute('aria-hidden', 'true');
            } else {
                // Hide slides to the right
                slide.classList.remove('opacity-100', 'translate-x-0', '-translate-x-full');
                slide.classList.add('opacity-0', 'translate-x-full', 'pointer-events-none');
                slide.setAttribute('aria-hidden', 'true');
            }
        });
    }

    function next() {
        currentSlide = (currentSlide + 1) % totalSlides;
        showSlide(currentSlide);
    }

    function prev() {
        currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
        showSlide(currentSlide);
    }

    function startAutoPlay() {
        if (isInView && !intervalId) {
            intervalId = setInterval(next, 4000);
        }
    }

    function stopAutoPlay() {
        if (intervalId) {
            clearInterval(intervalId);
            intervalId = null;
        }
    }

    // Event listeners
    if (prevBtn) prevBtn.addEventListener('click', prev);
    if (nextBtn) nextBtn.addEventListener('click', next);

    // Pause on hover
    slider.addEventListener('mouseenter', stopAutoPlay);
    slider.addEventListener('mouseleave', startAutoPlay);

    // Intersection Observer for autoplay
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            isInView = entry.isIntersecting;
            if (isInView) {
                startAutoPlay();
            } else {
                stopAutoPlay();
            }
        });
    }, { threshold: 0.2 });

    observer.observe(slider);
}
