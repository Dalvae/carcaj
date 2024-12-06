document.addEventListener("alpine:init", () => {
  Alpine.data("progressBar", () => ({
    progress: 0,
    contentFull: null,

    init() {
      this.contentFull = document.querySelector(".content-full");
      if (!this.contentFull) return;

      // Remover barras de progreso existentes
      const existingBars = document.querySelectorAll(".progress-bar");
      existingBars.forEach((bar) => bar.remove());

      this.updateProgress();

      let ticking = false;

      window.addEventListener(
        "scroll",
        () => {
          if (!ticking) {
            requestAnimationFrame(() => {
              this.updateProgress();
              ticking = false;
            });
            ticking = true;
          }
        },
        { passive: true }
      );

      window.addEventListener("resize", () => this.updateProgress());
      window.addEventListener("orientationchange", () => this.updateProgress());
    },

    updateProgress() {
      if (!this.contentFull) return;

      const contentRect = this.contentFull.getBoundingClientRect();
      const contentTop = contentRect.top + window.pageYOffset;
      const contentHeight = contentRect.height;
      const viewportHeight = window.innerHeight;
      const currentScroll = window.pageYOffset;

      let progress = 0;

      if (currentScroll > contentTop) {
        const scrolledContent = currentScroll - contentTop;
        const viewableContentHeight = contentHeight - viewportHeight;
        progress = (scrolledContent / viewableContentHeight) * 100;
        progress = Math.min(Math.max(progress, 0), 100);
      }

      this.progress = progress;
    },
  }));
});
