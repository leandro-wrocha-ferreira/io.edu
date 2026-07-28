/**
 * Admin Layout Interactions
 *
 * Handles: sidebar toggle (mobile), backdrop, theme toggler callbacks.
 */
document.addEventListener('DOMContentLoaded', function () {
	const toggleBtn  = document.getElementById('btn-toggle-sidebar');
	const sidebar    = document.getElementById('admin-sidebar');
	const backdrop   = document.getElementById('sidebar-backdrop');
	const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	/**
	 * Open the mobile sidebar.
	 */
	function openSidebar() {
		if (!sidebar) return;
		sidebar.classList.add('show');
		if (backdrop) backdrop.classList.add('show');
		if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'true');
		document.body.style.overflow = 'hidden';
	}

	/**
	 * Close the mobile sidebar.
	 */
	function closeSidebar() {
		if (!sidebar) return;
		sidebar.classList.remove('show');
		if (backdrop) backdrop.classList.remove('show');
		if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'false');
		document.body.style.overflow = '';
	}

	// Toggle on hamburger click
	if (toggleBtn) {
		toggleBtn.addEventListener('click', function () {
			const isOpen = sidebar && sidebar.classList.contains('show');
			isOpen ? closeSidebar() : openSidebar();
		});
	}

	// Close on backdrop click
	if (backdrop) {
		backdrop.addEventListener('click', closeSidebar);
	}

	// Close on Escape key
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape' && sidebar && sidebar.classList.contains('show')) {
			closeSidebar();
		}
	});

	// Animate stat cards with IntersectionObserver (respects prefers-reduced-motion)
	if (!prefersReduced) {
		const animatedCards = document.querySelectorAll('.animate-fade-up');
		if ('IntersectionObserver' in window && animatedCards.length > 0) {
			const observer = new IntersectionObserver(function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						entry.target.style.animationPlayState = 'running';
						observer.unobserve(entry.target);
					}
				});
			}, { threshold: 0.1 });

			animatedCards.forEach(function (card) {
				card.style.animationPlayState = 'paused';
				observer.observe(card);
			});
		}
	}
});

// Global DataTables: replace length select with Bootstrap dropdown
if (typeof jQuery !== 'undefined') {
	jQuery(document).on('init.dt', function (e, settings) {
		var api            = new jQuery.fn.dataTable.Api(settings);
		var $wrapper       = jQuery(api.table().container());
		var $lengthContainer = $wrapper.find('.dataTables_length');

		if ($lengthContainer.length > 0 && !$lengthContainer.hasClass('dropdown-initialized')) {
			$lengthContainer.addClass('dropdown-initialized');

			var $select       = $lengthContainer.find('select');
			var currentLength = api.page.len();

			$select.hide();

			var dropdownHtml = `
				<div class="dropdown d-inline-block mx-1">
					<button class="btn btn-sm dropdown-toggle"
					        type="button"
					        data-bs-toggle="dropdown"
					        aria-expanded="false"
					        aria-label="Resultados por página"
					        style="color: var(--text-main); border: 1px solid var(--border-color); background-color: var(--bg-card); border-radius: 0.4rem;">
						<span class="current-len">${currentLength}</span>
					</button>
					<ul class="dropdown-menu shadow-sm" style="min-width: 5rem;">
						<li><a class="dropdown-item" href="#" data-len="10">10</a></li>
						<li><a class="dropdown-item" href="#" data-len="25">25</a></li>
						<li><a class="dropdown-item" href="#" data-len="50">50</a></li>
						<li><a class="dropdown-item" href="#" data-len="100">100</a></li>
					</ul>
				</div>
			`;

			$select.after(dropdownHtml);

			var $dropdown = $lengthContainer.find('.dropdown');
			$dropdown.find('.dropdown-item').on('click', function (ev) {
				ev.preventDefault();
				var len = jQuery(this).data('len');
				api.page.len(len).draw();
				$dropdown.find('.current-len').text(len);
				$select.val(len);
			});
		}
	});
}
