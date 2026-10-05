/**
 * Course Form Script — io.edu LMS
 *
 * Manages reactive visibility for access period days, image upload/URL toggle,
 * and dynamic status descriptions.
 */
document.addEventListener('DOMContentLoaded', function () {
	// 1. Access Period Days Toggle
	const accessTypeSelect = document.getElementById('access-period-type');
	const accessDaysContainer = document.getElementById('access-days-container');
	const accessDaysInput = document.getElementById('access-days');

	if (accessTypeSelect && accessDaysContainer && accessDaysInput) {
		function syncAccessPeriodFields() {
			const isLimitedTime = accessTypeSelect.value === 'limited_time';

			if (isLimitedTime) {
				accessDaysContainer.style.display = '';
				accessDaysInput.setAttribute('required', 'required');
				if (!accessDaysInput.value) {
					accessDaysInput.value = '365';
				}
			} else {
				accessDaysContainer.style.display = 'none';
				accessDaysInput.removeAttribute('required');
			}
		}

		accessTypeSelect.addEventListener('change', syncAccessPeriodFields);
		syncAccessPeriodFields();
	}

	// 2. Image Source Toggle (Upload vs URL)
	const uploadRadio = document.getElementById('img-type-upload');
	const urlRadio = document.getElementById('img-type-url');
	const uploadBox = document.getElementById('image-upload-box');
	const urlBox = document.getElementById('image-url-box');

	if (uploadRadio && urlRadio && uploadBox && urlBox) {
		function syncImageSource() {
			if (urlRadio.checked) {
				urlBox.classList.remove('d-none');
				uploadBox.classList.add('d-none');
			} else {
				uploadBox.classList.remove('d-none');
				urlBox.classList.add('d-none');
			}
		}

		uploadRadio.addEventListener('change', syncImageSource);
		urlRadio.addEventListener('change', syncImageSource);
		syncImageSource();
	}

	// 3. Dynamic Course Status Hint
	const statusSelect = document.getElementById('course-status');
	const statusHint = document.getElementById('course-status-hint');

	if (statusSelect && statusHint) {
		const statusDescriptions = {
			'draft': 'Invisível na vitrine, em fase de elaboração pedagógica.',
			'active': 'Disponível para venda contínua e matrículas imediatas.',
			'archived': 'Vendas encerradas, acessível apenas para alunos matriculados.'
		};

		statusSelect.addEventListener('change', function () {
			statusHint.textContent = statusDescriptions[this.value] || '';
		});
	}
});
