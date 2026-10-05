/**
 * Course Form Script — io.edu LMS
 *
 * Manages reactive visibility for access period days and form helpers.
 */
document.addEventListener('DOMContentLoaded', function () {
	const accessTypeSelect = document.getElementById('access-period-type');
	const accessDaysContainer = document.getElementById('access-days-container');
	const accessDaysInput = document.getElementById('access-days');

	if (!accessTypeSelect || !accessDaysContainer || !accessDaysInput) {
		return;
	}

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
});
