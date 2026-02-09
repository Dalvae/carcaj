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

    // Cache layout values — only recalculate on resize
    let contentTop = 0;
    let contentHeight = 0;
    let viewportHeight = 0;

    function measureLayout() {
        const rect = contentFull.getBoundingClientRect();
        contentTop = rect.top + window.pageYOffset;
        contentHeight = rect.height;
        viewportHeight = window.innerHeight;
    }

    let ticking = false;

    function updateProgress() {
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

    window.addEventListener('resize', () => {
        measureLayout();
        updateProgress();
    });
    window.addEventListener('orientationchange', () => {
        measureLayout();
        updateProgress();
    });

    // Initial measurement + update
    measureLayout();
    updateProgress();
}
