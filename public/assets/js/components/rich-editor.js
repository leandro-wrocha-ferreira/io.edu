/**
 * Lightweight Rich Text Editor Component (Quill.js Integration) — io.edu LMS
 *
 * Enhances any textarea with [data-rich-editor] into an accessible,
 * themed WYSIWYG editor powered by Quill 2.
 */
(function () {
	'use strict';

	/**
	 * Helper to decode HTML entities if textarea value was encoded
	 *
	 * @param {string} str
	 * @return {string}
	 */
	function decodeHtmlEntities(str) {
		if (!str || !str.includes('&lt;')) {
			return str;
		}
		const temp = document.createElement('textarea');
		temp.innerHTML = str;
		let decoded = temp.value;
		if (decoded.includes('&lt;')) {
			temp.innerHTML = decoded;
			decoded = temp.value;
		}
		return decoded;
	}

	/**
	 * Initialize rich editors for all matching textareas in the DOM.
	 *
	 * @return {void}
	 */
	function initRichEditors() {
		if (typeof Quill === 'undefined') {
			console.warn('[rich-editor] Quill library not loaded.');
			return;
		}

		const textareas = document.querySelectorAll('textarea[data-rich-editor], textarea.edu-rich-editor');

		textareas.forEach(function (textarea) {
			if (textarea.dataset.editorInitialized === 'true') {
				return;
			}

			textarea.dataset.editorInitialized = 'true';
			textarea.style.display = 'none';

			const placeholder = textarea.getAttribute('placeholder') || 'Digite o conteúdo aqui...';
			const minHeight = textarea.getAttribute('rows') && parseInt(textarea.getAttribute('rows'), 10) >= 4 ? '180px' : '110px';

			// Create wrapper
			const wrapper = document.createElement('div');
			wrapper.className = 'edu-rich-editor-wrapper';

			// Create editor container for Quill
			const editorDiv = document.createElement('div');
			editorDiv.className = 'edu-quill-editor';
			wrapper.appendChild(editorDiv);

			textarea.parentNode.insertBefore(wrapper, textarea.nextSibling);

			// Initialize Quill
			const quill = new Quill(editorDiv, {
				theme: 'snow',
				placeholder: placeholder,
				modules: {
					toolbar: [
						[{ 'header': [3, 4, 5, false] }],
						['bold', 'italic', 'underline'],
						[{ 'list': 'ordered' }, { 'list': 'bullet' }],
						[{ 'indent': '-1' }, { 'indent': '+1' }],
						['blockquote'],
						['clean']
					]
				}
			});

			// Set min height on the actual editor surface
			const qlEditor = wrapper.querySelector('.ql-editor');
			if (qlEditor) {
				qlEditor.style.minHeight = minHeight;
			}

			// Initial HTML content
			const initialContent = decodeHtmlEntities(textarea.value || '').trim();
			if (initialContent) {
				quill.clipboard.dangerouslyPasteHTML(initialContent);
			}

			// Synchronize Quill content to textarea
			function syncToTextarea() {
				const html = quill.getSemanticHTML ? quill.getSemanticHTML() : quill.root.innerHTML;
				const text = quill.getText().trim();
				if (text.length === 0 && (html === '<p><br></p>' || html === '<p></p>' || html === '')) {
					textarea.value = '';
				} else {
					textarea.value = html;
				}
			}

			quill.on('text-change', syncToTextarea);

			// Form submit listener as failsafe
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
