document.addEventListener("alpine:init", () => {
  Alpine.data("slider", () => ({
    currentSlide: 0,
    slides: [],

    init() {
      // Convertir los datos del PHP a un array de JavaScript
      this.slides = window.sliderData || [];
    },

    next() {
      this.currentSlide = (this.currentSlide + 1) % this.slides.length;
    },

    prev() {
      this.currentSlide =
        (this.currentSlide - 1 + this.slides.length) % this.slides.length;
    },

    goToSlide(index) {
      this.currentSlide = index;
    },
  }));
});
