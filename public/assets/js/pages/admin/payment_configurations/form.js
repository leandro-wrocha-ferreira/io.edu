/**
 * Payment Configuration Form script — io.edu LMS
 */
document.addEventListener('DOMContentLoaded', function () {
	// Toggle API Key visibility
	const toggleApiKeyBtn = document.getElementById('toggle-api-key');
	const apiKeyInput = document.getElementById('config-api-key');
	const apiKeyIcon = document.getElementById('toggle-api-key-icon');

	if (toggleApiKeyBtn && apiKeyInput && apiKeyIcon) {
		toggleApiKeyBtn.addEventListener('click', function () {
			const isPassword = apiKeyInput.getAttribute('type') === 'password';
			apiKeyInput.setAttribute('type', isPassword ? 'text' : 'password');
			apiKeyIcon.classList.toggle('bi-eye', !isPassword);
			apiKeyIcon.classList.toggle('bi-eye-slash', isPassword);
		});
	}

	// Validate at least one payment method before submission
	const form = document.getElementById('payment-config-form');
	if (form) {
		form.addEventListener('submit', function (event) {
			const checkboxes = form.querySelectorAll('input[name="allowed_methods[]"]:checked');
			if (checkboxes.length === 0) {
				event.preventDefault();
				alert('Por favor, selecione ao menos uma forma de pagamento permitida.');
			}
		});
	}
});
