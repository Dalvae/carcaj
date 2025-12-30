import "./theme.css";

// Turbo SPA
import * as Turbo from "@hotwired/turbo";

Turbo.config.drive.progressBarDelay = 500;

// Handle Turbo errors gracefully
document.addEventListener("turbo:frame-missing", (event) => {
    event.preventDefault();
    event.detail.visit(event.detail.response);
});

// Alpine JS
import Alpine from "alpinejs";

// Initialize Alpine store and data before starting
Alpine.store('header', {
    isOpen: false,
    currentCategory: null,
    hasScrolled: false
});

Alpine.data('header', () => ({
    isSearchOpen: false,
    init() {
        this.$watch('$store.header.isOpen', value => {
            document.body.style.overflow = value ? 'hidden' : '';
            if (value) {
                window.dispatchEvent(new Event('stopSlider'));
            } else {
                window.dispatchEvent(new Event('startSlider'));
            }
        });
    }
}));

window.Alpine = Alpine;
Alpine.start();

import "./js/progressbar.js";
import "./js/footnotes.js";

// Page Change - reinitialize Alpine components after navigation
document.addEventListener("turbo:load", function () {
    // Reset header state on navigation
    if (Alpine.store('header')) {
        Alpine.store('header').isOpen = false;
        Alpine.store('header').hasScrolled = window.pageYOffset > 20;
    }
});

// Anchor scroll with offset for sticky header
document.addEventListener("turbo:render", function (event) {
    // Check if newBody exists before accessing its properties
    if (!event.detail?.newBody?.baseURI) {
        return;
    }
    const url = new URL(event.detail.newBody.baseURI);
    if (url.hash) {
        const targetElement = document.getElementById(url.hash.substring(1));
        if (targetElement) {
            const position = targetElement.getBoundingClientRect().top + window.scrollY;
            window.scrollTo({
                top: position - 200, // Adjusted for sticky header
            });
        }
    }
});
