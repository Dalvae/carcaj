import "./theme.css";

import { initHeader } from "./js/header.js";
import { initSlider } from "./js/slider.js";
import { initProgressBar } from "./js/progressbar.js";
import { initFootnotes } from "./js/footnotes.js";

// Initialize all components when DOM is ready
function init() {
    initHeader();
    initSlider();
    initProgressBar();
    initFootnotes();
    initShareButtons();
}

// Share buttons functionality
function initShareButtons() {
    const shareContainers = document.querySelectorAll('.share-container');
    
    shareContainers.forEach(container => {
        const toggle = container.querySelector('.share-toggle');
        const menu = container.querySelector('.share-menu');
        
        if (!toggle || !menu) return;
        
        toggle.addEventListener('click', () => {
            const isOpen = !menu.classList.contains('opacity-0');
            
            if (isOpen) {
                menu.classList.add('opacity-0', 'pointer-events-none');
                menu.classList.remove('opacity-100', 'pointer-events-auto');
                toggle.setAttribute('aria-expanded', 'false');
            } else {
                menu.classList.remove('opacity-0', 'pointer-events-none');
                menu.classList.add('opacity-100', 'pointer-events-auto');
                toggle.setAttribute('aria-expanded', 'true');
            }
        });
        
        // Close on click outside
        document.addEventListener('click', (e) => {
            if (!container.contains(e.target)) {
                menu.classList.add('opacity-0', 'pointer-events-none');
                menu.classList.remove('opacity-100', 'pointer-events-auto');
                toggle.setAttribute('aria-expanded', 'false');
            }
        });
    });
}

// Run on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}
