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

	// 2. Image Source Toggle & Live Preview (Upload vs URL)
	const uploadRadio = document.getElementById('img-type-upload');
	const urlRadio = document.getElementById('img-type-url');
	const uploadBox = document.getElementById('image-upload-box');
	const urlBox = document.getElementById('image-url-box');
	const fileInput = document.getElementById('course-image-file');
	const fileBrowseBtn = document.getElementById('btn-browse-file');
	const fileLabel = document.getElementById('course-image-file-label');
	const urlInput = document.getElementById('course-image-url');
	const previewWrapper = document.getElementById('course-image-preview-wrapper');
	const previewImg = document.getElementById('course-image-preview');
	const previewPlaceholder = document.getElementById('course-image-preview-placeholder');
	const previewInfo = document.getElementById('course-image-preview-info');

	if (uploadRadio && urlRadio && uploadBox && urlBox) {
		const initialImageSrc = previewImg ? previewImg.getAttribute('src') : '';
		const initialInfoHtml = previewInfo ? previewInfo.innerHTML : '';
		let localFileDataUrl = null;

		function showPreview(src, infoText, isHtml) {
			if (!previewWrapper || !previewImg) return;
			if (src) {
				previewImg.src = src;
				previewImg.classList.remove('d-none');
				if (previewPlaceholder) previewPlaceholder.classList.add('d-none');
				previewWrapper.classList.remove('d-none');
				if (previewInfo) {
					if (isHtml) {
						previewInfo.innerHTML = infoText;
					} else {
						previewInfo.textContent = infoText;
					}
				}
			} else {
				previewImg.src = '';
				previewImg.classList.add('d-none');
				if (previewPlaceholder) previewPlaceholder.classList.remove('d-none');
				previewWrapper.classList.add('d-none');
			}
		}

		function syncImageSource() {
			if (urlRadio.checked) {
				urlBox.classList.remove('d-none');
				uploadBox.classList.add('d-none');

				const enteredUrl = urlInput ? urlInput.value.trim() : '';
				if (enteredUrl) {
					showPreview(enteredUrl, 'URL externa: ' + enteredUrl, false);
				} else {
					showPreview('', '', false);
				}
			} else {
				uploadBox.classList.remove('d-none');
				urlBox.classList.add('d-none');

				if (localFileDataUrl && fileInput && fileInput.files && fileInput.files.length > 0) {
					showPreview(localFileDataUrl, 'Arquivo selecionado: ' + fileInput.files[0].name, false);
				} else if (initialImageSrc) {
					showPreview(initialImageSrc, initialInfoHtml, true);
				} else {
					showPreview('', '', false);
				}
			}
		}

		uploadRadio.addEventListener('change', syncImageSource);
		urlRadio.addEventListener('change', syncImageSource);

		// URL input live change on multiple events
		if (urlInput) {
			const handleUrlChange = function () {
				if (urlRadio.checked) {
					const val = urlInput.value.trim();
					if (val) {
						showPreview(val, 'URL externa: ' + val, false);
					} else {
						showPreview('', '', false);
					}
				}
			};

			['input', 'change', 'blur', 'paste', 'keyup'].forEach(function (eventName) {
				urlInput.addEventListener(eventName, handleUrlChange);
			});
		}

		// File picker browse button and change event
		if (fileInput && fileBrowseBtn && fileLabel) {
			fileBrowseBtn.addEventListener('click', function () {
				fileInput.click();
			});

			fileInput.addEventListener('change', function () {
				if (this.files && this.files.length > 0) {
					const file = this.files[0];
					const fileName = file.name;
					const fileSizeKb = Math.round(file.size / 1024);
					fileLabel.textContent = `${fileName} (${fileSizeKb} KB)`;
					fileLabel.classList.add('has-file');

					const reader = new FileReader();
					reader.onload = function (e) {
						localFileDataUrl = e.target.result;
						if (uploadRadio.checked) {
							showPreview(localFileDataUrl, 'Arquivo selecionado: ' + fileName, false);
						}
					};
					reader.readAsDataURL(file);
				} else {
					localFileDataUrl = null;
					fileLabel.textContent = 'Nenhum arquivo selecionado';
					fileLabel.classList.remove('has-file');
					if (uploadRadio.checked) {
						if (initialImageSrc) {
							showPreview(initialImageSrc, initialInfoHtml, true);
						} else {
							showPreview('', '', false);
						}
					}
				}
			});
		}

		// Image preview load & error fallback
		if (previewImg) {
			previewImg.addEventListener('error', function () {
				if (!this.getAttribute('src')) {
					return;
				}
				this.classList.add('d-none');
				if (previewPlaceholder) previewPlaceholder.classList.remove('d-none');
				if (previewInfo) previewInfo.textContent = 'Não foi possível carregar a pré-visualização da imagem.';
			});

			previewImg.addEventListener('load', function () {
				this.classList.remove('d-none');
				if (previewPlaceholder) previewPlaceholder.classList.add('d-none');
			});
		}

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

	// 4. Reactive Auto-Slug Generator (Readonly Preview)
	const titleInput = document.getElementById('course-title');
	const slugInput = document.getElementById('course-slug');

	if (titleInput && slugInput) {
		const courseId = slugInput.dataset.courseId ? slugInput.dataset.courseId.trim() : '';

		function slugify(text) {
			return text
				.toString()
				.normalize('NFD')
				.replace(/[\u0300-\u036f]/g, '')
				.toLowerCase()
				.trim()
				.replace(/[^a-z0-9\s-]/g, '')
				.replace(/[\s-]+/g, '-')
				.replace(/^-+|-+$/g, '');
		}

		function syncSlugPreview() {
			const rawTitle = titleInput.value.trim();
			const cleanSlug = slugify(rawTitle);
			const prefix = courseId ? `${courseId}-` : '[id]-';

			if (!cleanSlug) {
				slugInput.value = '';
				return;
			}

			slugInput.value = `${prefix}${cleanSlug}`;
		}

		titleInput.addEventListener('input', syncSlugPreview);

		// Initialize preview on page load if title exists but slug is empty
		if (titleInput.value && !slugInput.value) {
			syncSlugPreview();
		}
	}
});
