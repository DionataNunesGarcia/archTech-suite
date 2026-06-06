document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".messages__close").forEach(function (button) {
    button.addEventListener("click", function () {
      const message = button.closest(".messages");
      if (message) {
        message.style.transition = "opacity 0.3s ease, transform 0.3s ease";
        message.style.opacity = "0";
        message.style.transform = "translateY(-10px)";

        setTimeout(() => {
          message.remove();
        }, 300);
      }
    });
  });
});
