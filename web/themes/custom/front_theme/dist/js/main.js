(() => {
	// src/js/layout/header.js
	(function () {
		const header = document.getElementById('site-header');
		if (!header) return;
		const onScroll = () => {
			if (window.scrollY > 50) {
				header.classList.add('scrolled');
			} else {
				header.classList.remove('scrolled');
			}
		};
		window.addEventListener('scroll', onScroll, { passive: true });
		onScroll();
		const hamburger = document.getElementById('hamburger');
		const menu = document.getElementById('menu');
		if (hamburger && menu) {
			hamburger.addEventListener('click', () => {
				menu.classList.toggle('hidden');
				menu.classList.toggle('flex');
				menu.classList.toggle('flex-col');
				menu.classList.toggle('absolute');
				menu.classList.toggle('top-full');
				menu.classList.toggle('left-0');
				menu.classList.toggle('w-full');
				menu.classList.toggle('bg-white');
				menu.classList.toggle('shadow-lg');
				menu.classList.toggle('p-6');
			});
		}
	})();

	// src/js/layout/messages.js
	document.addEventListener('DOMContentLoaded', function () {
		document.querySelectorAll('.messages__close').forEach(function (button) {
			button.addEventListener('click', function () {
				const message = button.closest('.messages');
				if (message) {
					message.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
					message.style.opacity = '0';
					message.style.transform = 'translateY(-10px)';
					setTimeout(() => {
						message.remove();
					}, 300);
				}
			});
		});
	});

	// src/js/components/accordions.js
	document.addEventListener('DOMContentLoaded', () => {
		document.querySelectorAll('.accordion-trigger').forEach(btn => {
			btn.addEventListener('click', () => {
				const content = btn.nextElementSibling;
				const icon = btn.querySelector('.accordion-icon');
				content.classList.toggle('hidden');
				if (content.classList.contains('hidden')) {
					icon.textContent = '+';
				} else {
					icon.textContent = '\u2212';
				}
			});
		});
	});

	// src/js/components/gsap-auto.js
	(function () {
		const revealElements = document.querySelectorAll('.reveal');
		if (!revealElements.length || !('IntersectionObserver' in window)) return;
		const observer = new IntersectionObserver(
			entries => {
				entries.forEach(entry => {
					if (entry.isIntersecting) {
						entry.target.classList.add('visible');
						observer.unobserve(entry.target);
					}
				});
			},
			{
				threshold: 0.1,
				rootMargin: '0px 0px -50px 0px',
			}
		);
		revealElements.forEach(el => observer.observe(el));
	})();
})();
