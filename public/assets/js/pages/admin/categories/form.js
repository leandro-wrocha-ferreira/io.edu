/**
 * Category Form Script — io.edu LMS
 *
 * Manages reactive UI helpers and dynamic status descriptions.
 */
document.addEventListener('DOMContentLoaded', function () {
	const statusSelect = document.getElementById('category-status');
	const statusHint = document.getElementById('category-status-hint');

	if (statusSelect && statusHint) {
		const statusDescriptions = {
			'active': 'Visível para organização e filtros na vitrine.',
			'inactive': 'Oculta do catálogo público e novos cadastros.'
		};

		statusSelect.addEventListener('change', function () {
			statusHint.textContent = statusDescriptions[this.value] || '';
		});
	}
});
