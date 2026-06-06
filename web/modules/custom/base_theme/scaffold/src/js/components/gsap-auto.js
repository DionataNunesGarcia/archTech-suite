import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

(function () {
  "use strict";

  // ===============================
  // CONFIGURAÇÕES
  // ===============================
  const animations = {
    "fade-up": { y: 60, opacity: 0 },
    "fade-left": { x: -60, opacity: 0 },
    "zoom": { scale: 0.9, opacity: 0 },
    "scale": { scale: 0.8, opacity: 0 }
  };

  // ===============================
  // AUTO APLICAR (ELEMENTOS PRINCIPAIS)
  // ===============================
  function autoApplyAttributes() {
    const containers = document.querySelectorAll(".auto-animate");

    containers.forEach(container => {
      const elements = container.querySelectorAll(
        "h1, h2, h3, img, button, a"
      );

      elements.forEach((el, i) => {
        if (el.className.includes("animate-")) return;

        let anim = "fade-up";

        if (el.tagName === "H2") anim = "fade-left";
        else if (el.tagName === "IMG") anim = "zoom";
        else if (el.tagName === "BUTTON" || el.tagName === "A") anim = "fade-up";

        el.classList.add(`animate-${anim}`);

        // pequeno stagger visual
        el.style.transitionDelay = `${i * 0.05}s`;
      });
    });
  }

  // ===============================
  // PREPARAR ELEMENTOS
  // ===============================
  function prepareElements() {
    Object.keys(animations).forEach(type => {
      document.querySelectorAll(`.animate-${type}`).forEach(el => {
        gsap.set(el, animations[type]);
      });
    });
  }

  // ===============================
  // ANIMAÇÃO (BATCH)
  // ===============================
  function initBatchAnimations() {
    Object.keys(animations).forEach(type => {
      ScrollTrigger.batch(`.animate-${type}`, {
        start: "top 92%",

        onEnter: batch => {
          gsap.to(batch, {
            x: 0,
            y: 0,
            scale: 1,
            opacity: 1,
            duration: 0.8,
            ease: "power3.out",
            stagger: 0.12,
            overwrite: true
          });
        },

        once: true
      });
    });
  }

  // ===============================
  // FALLBACK (SEGURANÇA)
  // ===============================
  function initObserverFallback() {
    if (!("IntersectionObserver" in window)) return;

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          gsap.to(entry.target, {
            x: 0,
            y: 0,
            scale: 1,
            opacity: 1,
            duration: 0.8,
            ease: "power3.out"
          });

          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.15 });

    document.querySelectorAll("[class*='animate-']").forEach(el => {
      observer.observe(el);
    });
  }

  // ===============================
  // HOVER CARDS
  // ===============================
  function hoverEffects() {
    document.querySelectorAll(".card-hover, [class*='card-hover']").forEach(card => {
      card.addEventListener("mouseenter", () => {
        gsap.to(card, { scale: 1.04, y: -6, duration: 0.3 });
      });

      card.addEventListener("mouseleave", () => {
        gsap.to(card, { scale: 1, y: 0, duration: 0.3 });
      });
    });
  }

  // ===============================
  // PARALLAX
  // ===============================
  function parallax() {
    const heroImg = document.querySelector(".banner img, .hero img");
    if (!heroImg) return;

    gsap.to(heroImg, {
      y: 120,
      ease: "none",
      scrollTrigger: {
        trigger: heroImg,
        start: "top top",
        end: "bottom top",
        scrub: true
      }
    });
  }

  // ===============================
  // LOADER
  // ===============================
  function initLoader() {
    const loader = document.getElementById("page-loader");
    const logo = loader?.querySelector(".logo");

    if (!loader || !logo) return;

    // ✨ animação contínua (brilho suave)
    gsap.fromTo(
      logo,
      { opacity: 0.4 },
      {
        opacity: 1,
        duration: 1.2,
        repeat: -1,
        yoyo: true,
        ease: "power1.inOut"
      }
    );

    function hideLoader() {
      gsap.to(loader, {
        opacity: 0,
        duration: 0.6,
        onComplete: () => loader.remove()
      });
    }

    window.addEventListener("load", hideLoader);

    setTimeout(hideLoader, 3000);
  }

  // ===============================
  // INIT
  // ===============================
  function init() {
    try {
      autoApplyAttributes();
      prepareElements();
      initBatchAnimations();
      initObserverFallback();
      hoverEffects();
      parallax();
      initLoader();

      window.addEventListener("load", () => {
        ScrollTrigger.refresh();
      });

      console.log("✨ GSAP Auto Animate OK");
    } catch (e) {
      console.error("Erro no GSAP Auto:", e);
    }
  }

  // DOM READY
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }

  // API GLOBAL (Drupal AJAX)
  window.AutoAnimate = {
    refresh() {
      ScrollTrigger.refresh();
    }
  };

})();