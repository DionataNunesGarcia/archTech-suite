document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".accordion-trigger").forEach((btn) => {
    btn.addEventListener("click", () => {
      const content = btn.nextElementSibling;
      const icon = btn.querySelector(".accordion-icon");

      content.classList.toggle("hidden");

      if (content.classList.contains("hidden")) {
        icon.textContent = "+";
      } else {
        icon.textContent = "−";
      }
    });
  });
});
