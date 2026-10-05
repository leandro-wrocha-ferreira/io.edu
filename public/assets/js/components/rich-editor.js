/**
 * Lightweight Rich Text Editor Component — io.edu LMS
 *
 * Enhances any textarea with [data-rich-editor] into an accessible,
 * themed WYSIWYG editor with formatting toolbar (Bold, Italic, Lists, Headings, Quotes).
 */
(function () {
	'use strict';

	/**
	 * Initialize rich editors for all matching textareas in the DOM.
	 *
	 * @return {void}
	 */
	function initRichEditors() {
		const textareas = document.querySelectorAll('textarea[data-rich-editor], textarea.edu-rich-editor');

		textareas.forEach(function (textarea) {
			if (textarea.dataset.editorInitialized === 'true') {
				return;
			}

			textarea.dataset.editorInitialized = 'true';
			textarea.style.display = 'none';

			const placeholder = textarea.getAttribute('placeholder') || '';
			const minHeight = textarea.getAttribute('rows') && parseInt(textarea.getAttribute('rows'), 10) >= 4 ? '180px' : '110px';

			// Create wrapper
			const wrapper = document.createElement('div');
			wrapper.className = 'edu-rich-editor-wrapper';

			// Create toolbar
			const toolbar = document.createElement('div');
			toolbar.className = 'edu-rich-editor-toolbar';
			toolbar.innerHTML = `
				<button type="button" class="edu-editor-btn" data-command="bold" title="Negrito (Ctrl+B)">
					<i class="bi bi-type-bold" aria-hidden="true"></i>
				</button>
				<button type="button" class="edu-editor-btn" data-command="italic" title="Itálico (Ctrl+I)">
					<i class="bi bi-type-italic" aria-hidden="true"></i>
				</button>
				<button type="button" class="edu-editor-btn" data-command="underline" title="Sublinhado (Ctrl+U)">
					<i class="bi bi-type-underline" aria-hidden="true"></i>
				</button>
				<div class="edu-editor-divider"></div>
				<button type="button" class="edu-editor-btn" data-command="formatBlock" data-value="h4" title="Título da Seção">
					<i class="bi bi-type-h4" aria-hidden="true"></i>
				</button>
				<button type="button" class="edu-editor-btn" data-command="insertUnorderedList" title="Lista com Marcadores">
					<i class="bi bi-list-ul" aria-hidden="true"></i>
				</button>
				<button type="button" class="edu-editor-btn" data-command="insertOrderedList" title="Lista Numerada">
					<i class="bi bi-list-ol" aria-hidden="true"></i>
				</button>
				<button type="button" class="edu-editor-btn" data-command="formatBlock" data-value="blockquote" title="Destaque / Citação">
					<i class="bi bi-quote" aria-hidden="true"></i>
				</button>
				<div class="edu-editor-divider"></div>
				<button type="button" class="edu-editor-btn" data-command="removeFormat" title="Limpar Formatação">
					<i class="bi bi-eraser" aria-hidden="true"></i>
				</button>
			`;

			// Create contenteditable area
			const content = document.createElement('div');
			content.className = 'edu-rich-editor-content';
			content.contentEditable = 'true';
			content.style.minHeight = minHeight;
			if (placeholder) {
				content.setAttribute('data-placeholder', placeholder);
			}
			content.innerHTML = textarea.value || '';

			wrapper.appendChild(toolbar);
			wrapper.appendChild(content);
			textarea.parentNode.insertBefore(wrapper, textarea.nextSibling);

			// Synchronize content to textarea
			function syncToTextarea() {
				const htmlContent = content.innerHTML.trim();
				if (htmlContent === '<br>' || htmlContent === '<p><br></p>' || htmlContent === '') {
					textarea.value = '';
				} else {
					textarea.value = htmlContent;
				}
			}

			content.addEventListener('input', syncToTextarea);
			content.addEventListener('blur', syncToTextarea);

			// Toolbar command handlers
			const buttons = toolbar.querySelectorAll('.edu-editor-btn');
			buttons.forEach(function (button) {
				button.addEventListener('mousedown', function (event) {
					event.preventDefault(); // Retain selection inside contenteditable
					const command = button.getAttribute('data-command');
					const value = button.getAttribute('data-value') || null;

					content.focus();
					document.execCommand(command, false, value);
					syncToTextarea();
				});
			});

			// Form submission sync
			const form = textarea.closest('form');
			if (form) {
				form.addEventListener('submit', syncToTextarea);
			}
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initRichEditors);
	} else {
		initRichEditors();
	}

	window.initRichEditors = initRichEditors;
})();
