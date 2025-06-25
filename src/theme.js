import "./theme.scss";

// Turbo SPA
import * as Turbo from "@hotwired/turbo";

Turbo.config.drive.progressBarDelay = 500;

// Alpine JS
import Alpine from "alpinejs";

window.Alpine = Alpine;
Alpine.start();

import "./js/mobile-menu.js";
import "./js/slider.js";
import "./js/progressbar.js";
import "./js/footnotes.js";

// Page Change
document.addEventListener("turbo:load", function () {
  // console.log('turbo:load');
});
