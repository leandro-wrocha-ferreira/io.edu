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
					reader.onload = function (event) {
						localFileDataUrl = event.target.result;
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

		// Initialize preview on page load if title exists but slug is empty,
		// or if in edit mode with courseId and slug contains an 'id-' or '[id]-' placeholder
		if (titleInput.value && !slugInput.value) {
			syncSlugPreview();
		} else if (courseId && slugInput.value && /^(\[id\]-|id-)/i.test(slugInput.value)) {
			const cleanExisting = slugInput.value.replace(/^(\[id\]-|id-)/i, '');
			slugInput.value = `${courseId}-${cleanExisting}`;
		}
	}

	// 5. Commercial Conditions Reactive Controls
	const isFreeCheckbox = document.getElementById('is-free');
	const commercialWrapper = document.getElementById('commercial-fields-wrapper');
	const billingModelSelect = document.getElementById('billing-model');
	const billingOneTimeFields = document.getElementById('billing-one-time-fields');
	const billingIntervalField = document.getElementById('billing-subscription-interval-field');
	const billingCyclesField = document.getElementById('billing-subscription-cycles-field');
	const coursePriceInput = document.getElementById('course-price');
	const changeReasonContainer = document.getElementById('change-reason-container');

	if (isFreeCheckbox && commercialWrapper) {
		function syncCommercialVisibility() {
			if (isFreeCheckbox.checked) {
				commercialWrapper.classList.add('d-none');
			} else {
				commercialWrapper.classList.remove('d-none');
			}
		}

		isFreeCheckbox.addEventListener('change', syncCommercialVisibility);
		syncCommercialVisibility();
	}

	if (billingModelSelect && billingOneTimeFields && billingIntervalField && billingCyclesField) {
		function syncBillingModelFields() {
			const selectedModel = billingModelSelect.value;
			if (selectedModel === 'subscription') {
				billingOneTimeFields.classList.add('d-none');
				billingIntervalField.classList.remove('d-none');
				billingCyclesField.classList.remove('d-none');
			} else {
				billingOneTimeFields.classList.remove('d-none');
				billingIntervalField.classList.add('d-none');
				billingCyclesField.classList.add('d-none');
			}
		}

		billingModelSelect.addEventListener('change', syncBillingModelFields);
		syncBillingModelFields();
	}

	if (coursePriceInput && changeReasonContainer) {
		const initialPrice = coursePriceInput.dataset.initialPrice ? coursePriceInput.dataset.initialPrice.trim() : '';

		function checkPriceChange() {
			const currentPrice = coursePriceInput.value.trim();
			if (initialPrice !== '' && currentPrice !== initialPrice) {
				changeReasonContainer.classList.remove('d-none');
			} else {
				changeReasonContainer.classList.add('d-none');
			}
		}

		['input', 'change', 'blur'].forEach(function (eventName) {
			coursePriceInput.addEventListener(eventName, checkPriceChange);
		});
		checkPriceChange();
	}

	// Simple Currency Input Mask for BRL (e.g. 199,90)
	if (coursePriceInput) {
		coursePriceInput.addEventListener('input', function () {
			let rawDigits = this.value.replace(/\D/g, '');
			if (!rawDigits) {
				this.value = '';
				return;
			}
			const centsNumber = parseInt(rawDigits, 10);
			const formattedUnits = Math.floor(centsNumber / 100);
			const formattedCents = String(centsNumber % 100).padStart(2, '0');
			const formattedWithThousands = formattedUnits.toLocaleString('pt-BR');
			this.value = `${formattedWithThousands},${formattedCents}`;
		});
	}
});
