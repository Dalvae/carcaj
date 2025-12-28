import "./theme.css";

// Turbo SPA
import * as Turbo from "@hotwired/turbo";

Turbo.config.drive.progressBarDelay = 500;

// Alpine JS
import Alpine from "alpinejs";

window.Alpine = Alpine;
Alpine.start();

import "./js/slider.js";
import "./js/progressbar.js";
import "./js/footnotes.js";

// Page Change
document.addEventListener("turbo:load", function () {
  // console.log('turbo:load');
});

// Anchor scroll with offset for sticky header
document.addEventListener("turbo:render", function (event) {
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
