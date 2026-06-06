// layout
import "./layout/header";
import "./layout/messages";

// components
import "./components/accordions";
import "./components/gsap-auto";


/**
 * @file
 * front_theme — Bootstrap 5 JavaScript entry point.
 */

// Bootstrap JS (includes Popper.js via @popperjs/core)
import * as bootstrap from 'bootstrap';

// AOS — Animate on Scroll
import AOS from 'aos';
import 'aos/dist/aos.css';

// GLightbox
import GLightbox from 'glightbox';
import 'glightbox/dist/css/glightbox.css';

// Drupal behaviors
(function (Drupal) {
  'use strict';

  Drupal.behaviors.frontTheme = {
    attach(context) {
      // AOS init
      AOS.init({ once: true, duration: 600 });

      // Bootstrap tooltips
      const tooltipEls = context.querySelectorAll('[data-bs-toggle="tooltip"]');
      tooltipEls.forEach(el => new bootstrap.Tooltip(el));

      // Bootstrap popovers
      const popoverEls = context.querySelectorAll('[data-bs-toggle="popover"]');
      popoverEls.forEach(el => new bootstrap.Popover(el));

      // GLightbox
      GLightbox({ selector: '.glightbox' });
    },
  };

})(Drupal);
