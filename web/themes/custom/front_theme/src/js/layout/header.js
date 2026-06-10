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
