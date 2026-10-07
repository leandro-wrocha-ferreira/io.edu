/**
 * Tag Input Component — io.edu LMS
 *
 * Transforms containers with .edu-tag-input-container into an interactive
 * chip/tag input. Synchronizes values as a clean comma-separated list
 * to a hidden input.
 */
(function () {
	'use strict';

	/**
	 * Initialize a tag input container.
	 *
	 * @param {HTMLElement} container
	 * @return {void}
	 */
	function initTagInput(container) {
		if (container.dataset.tagInitialized === 'true') {
			return;
		}
		container.dataset.tagInitialized = 'true';

		const badgesContainer = container.querySelector('.edu-tag-badges');
		const inputField = container.querySelector('.edu-tag-field');
		const hiddenInput = container.querySelector('input[type="hidden"]');

		if (!badgesContainer || !inputField || !hiddenInput) {
			return;
		}

		let tags = [];

		function syncToHidden() {
			hiddenInput.value = tags.join(', ');
		}

		function escapeHtml(text) {
			const div = document.createElement('div');
			div.textContent = text;
			return div.innerHTML;
		}

		function renderBadges() {
			badgesContainer.innerHTML = '';
			tags.forEach(function (tagText, index) {
				const badge = document.createElement('span');
				badge.className = 'edu-tag-badge';
				badge.innerHTML = `
					<span>${escapeHtml(tagText)}</span>
					<button type="button" class="edu-tag-remove" data-index="${index}" aria-label="Remover ${escapeHtml(tagText)}">&times;</button>
				`;
				badgesContainer.appendChild(badge);
			});
			syncToHidden();
		}

		function addTag(text) {
			const cleaned = text.trim().replace(/^[,;\s]+|[,;\s]+$/g, '');
			if (cleaned && !tags.some(function (existing) { return existing.toLowerCase() === cleaned.toLowerCase(); })) {
				tags.push(cleaned);
				renderBadges();
			}
			inputField.value = '';
		}

		function removeTag(index) {
			tags.splice(index, 1);
			renderBadges();
		}

		// Load initial tags from hidden input
		if (hiddenInput.value) {
			let initialValues = [];
			try {
				const parsed = JSON.parse(hiddenInput.value);
				if (Array.isArray(parsed)) {
					initialValues = parsed;
				}
			} catch {
				initialValues = hiddenInput.value.split(/[,;]/);
			}

			initialValues.forEach(function (val) {
				const item = String(val).trim();
				if (item) {
					tags.push(item);
				}
			});
			renderBadges();
		}

		// Event listener on container to focus text input
		container.addEventListener('click', function (event) {
			if (!event.target.closest('.edu-tag-remove')) {
				inputField.focus();
			}
		});

		// Event listener on badge remove buttons
		badgesContainer.addEventListener('click', function (event) {
			const removeBtn = event.target.closest('.edu-tag-remove');
			if (removeBtn) {
				event.stopPropagation();
				const index = parseInt(removeBtn.dataset.index, 10);
				removeTag(index);
				inputField.focus();
			}
		});

		// Input keydown listener
		inputField.addEventListener('keydown', function (event) {
			if (event.key === 'Enter' || event.key === ',') {
				event.preventDefault(); // Prevent form submission!
				addTag(inputField.value);
			} else if (event.key === 'Backspace' && inputField.value === '' && tags.length > 0) {
				removeTag(tags.length - 1);
			}
		});

		// Input blur listener (commit pending text)
		inputField.addEventListener('blur', function () {
			if (inputField.value.trim()) {
				addTag(inputField.value);
			}
		});
	}

	function initAll() {
		const containers = document.querySelectorAll('.edu-tag-input-container');
		containers.forEach(initTagInput);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initAll);
	} else {
		initAll();
	}

	window.initTagInput = initAll;
})();
