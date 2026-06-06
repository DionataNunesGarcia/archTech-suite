document.addEventListener("DOMContentLoaded", () => {
  const currentUrl = window.location.pathname;

  const nav = document.querySelector("header nav, footer nav");
  if (!nav) return;

  // =========================
  // ACTIVE LINK (APENAS MENU)
  // =========================
  nav.querySelectorAll("a").forEach((link) => {
    if (!link.getAttribute("href") || link.getAttribute("href") === "#") return;

    const linkPath = new URL(link.href).pathname;

    if (link.classList.contains("btn-search")) return;

    if (linkPath === "/") {
      if (currentUrl === "/") {
        link.classList.add("text-orange-500", "font-semibold");
      }
      return;
    }

    if (currentUrl === linkPath || currentUrl.startsWith(linkPath + "/")) {
      link.classList.add("text-orange-500", "font-semibold");

      const li = link.closest("li");
      if (li) li.classList.add("active");
    }
  });

  // =========================
  // HAMBURGER
  // =========================
  const hamburger = document.getElementById("hamburger");
  const menu = document.getElementById("menu");

  if (hamburger && menu) {
    const spans = hamburger.querySelectorAll("span");

    hamburger.addEventListener("click", () => {
      menu.classList.toggle("hidden");

      spans[0].classList.toggle("rotate-45");
      spans[0].classList.toggle("translate-y-1.5");
      spans[1].classList.toggle("opacity-0");
      spans[2].classList.toggle("-rotate-45");
      spans[2].classList.toggle("-translate-y-1.5");
    });
  }

  // =========================
  // DROPDOWN (SÓ MENU)
  // =========================
  nav.querySelectorAll("li").forEach((item) => {
    const submenu = item.querySelector(":scope > ul");
    if (!submenu) return;

    item.classList.add("has-dropdown");

    const link = item.querySelector(":scope > a");

    const arrow = document.createElement("span");
    arrow.innerHTML = `
      <svg xmlns="http://www.w3.org/2000/svg"
        class="w-4 h-4 ml-1 transition-transform duration-300"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round">
        <polyline points="6 9 12 15 18 9"></polyline>
      </svg>
    `;

    arrow.classList.add("flex", "items-center");
    link.appendChild(arrow);

    const svg = arrow.querySelector("svg");

    submenu.classList.add("hidden", "dropdown");

    link.addEventListener("click", (e) => {
      e.preventDefault();

      const isOpen = submenu.classList.contains("open");

      nav.querySelectorAll(".dropdown.open").forEach((el) => {
        el.classList.remove("open");
        el.classList.add("hidden");
      });

      nav.querySelectorAll(".has-dropdown svg").forEach((el) => {
        el.classList.remove("rotate-180");
      });

      if (!isOpen) {
        submenu.classList.remove("hidden");
        submenu.classList.add("open");
        svg.classList.add("rotate-180");
      }
    });

    item.addEventListener("mouseenter", () => {
      if (window.innerWidth > 768) {
        submenu.classList.remove("hidden");
        svg.classList.add("rotate-180");
      }
    });

    item.addEventListener("mouseleave", () => {
      if (window.innerWidth > 768) {
        submenu.classList.add("hidden");
        svg.classList.remove("rotate-180");
      }
    });
  });

  // =========================
  // CLICK OUTSIDE
  // =========================
  document.addEventListener("click", (e) => {
    if (!e.target.closest("#menu")) {
      document.querySelectorAll(".dropdown").forEach((el) => {
        el.classList.add("hidden");
        el.classList.remove("open");
      });

      document.querySelectorAll(".has-dropdown svg").forEach((el) => {
        el.classList.remove("rotate-180");
      });
    }
  });
});