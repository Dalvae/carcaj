// Progress bar - vanilla JS

export function initProgressBar() {
    const contentFull = document.querySelector('.content-full');
    if (!contentFull) return;

    // Create progress bar element
    const progressBar = document.createElement('div');
    progressBar.id = 'reading-progress';
    progressBar.className = 'fixed top-0 left-0 h-1.5 bg-rojo transform-gpu z-50';
    progressBar.style.width = '0%';
    document.body.prepend(progressBar);

    let ticking = false;

    function updateProgress() {
        const contentRect = contentFull.getBoundingClientRect();
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

        progressBar.style.width = `${progress}%`;
    }

    window.addEventListener('scroll', () => {
        if (!ticking) {
            requestAnimationFrame(() => {
                updateProgress();
                ticking = false;
            });
            ticking = true;
        }
    }, { passive: true });

    window.addEventListener('resize', updateProgress);
    window.addEventListener('orientationchange', updateProgress);

    // Initial update
    updateProgress();
}
