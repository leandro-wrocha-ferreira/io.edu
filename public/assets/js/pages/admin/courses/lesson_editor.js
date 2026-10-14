/**
 * Lesson & Content Blocks Inline Editor with Plyr and Quill — io.edu LMS
 * File: public/assets/js/pages/admin/courses/lesson_editor.js
 */
(function (global) {
	'use strict';

	const rootContainer = document.getElementById('lesson-blocks-container');
	if (!rootContainer) {
		return;
	}

	const lessonId = parseInt(rootContainer.dataset.lessonId, 10);

	const LessonEditorApp = {
		plyrInstances: new Map(),

		init: function () {
			if (global.LessonBlocksReorder) {
				global.LessonBlocksReorder.setupBlocksReorder(lessonId);
				global.LessonBlocksReorder.updateBlocksVisibilityState();
			}

			this.setupDropzones();

			// Listen for user interaction on blocks to keep only the last active one highlighted
			const blocksContainer = document.getElementById('blocks-sortable-list');
			if (blocksContainer) {
				blocksContainer.addEventListener('focusin', function (event) {
					const blockItem = event.target.closest('.edu-block-item');
					if (blockItem) {
						LessonEditorApp.setActiveBlock(blockItem);
					}
				});
				blocksContainer.addEventListener('click', function (event) {
					const blockItem = event.target.closest('.edu-block-item');
					if (blockItem) {
						LessonEditorApp.setActiveBlock(blockItem);
					}
				});
			}

			// Initialize Plyr on pre-rendered videos
			document.querySelectorAll('.js-uploaded-plyr-video').forEach(function (videoEl) {
				if (typeof Plyr !== 'undefined') {
					new Plyr(videoEl, {
						controls: ['play-large', 'play', 'progress', 'current-time', 'mute', 'volume', 'fullscreen']
					});
				}
			});
		},

		setActiveBlock: function (targetItem) {
			if (!targetItem) return;
			const container = document.getElementById('blocks-sortable-list');
			if (!container) return;
			container.querySelectorAll('.edu-block-item').forEach(function (item) {
				if (item !== targetItem) {
					item.classList.remove('is-active-block');
				}
			});
			targetItem.classList.add('is-active-block');
		},

		renumberBlocks: function () {
			const container = document.getElementById('blocks-sortable-list');
			if (!container) return;
			if (global.LessonBlocksReorder) {
				global.LessonBlocksReorder.updateBlockNumbers(container);
			}
		},

		updateBlockViewBar: function (blockItem, savedBlock) {
			const viewBar = blockItem.querySelector('.edu-block-view');
			if (!viewBar) return;

			let badgeHtml = '';
			if (savedBlock.type === 'video') {
				badgeHtml = '<span class="badge bg-danger-subtle text-danger border border-danger-subtle d-flex align-items-center gap-1"><i class="bi bi-play-circle"></i> Vídeo</span>';
			} else if (savedBlock.type === 'text') {
				badgeHtml = '<span class="badge bg-primary-subtle text-primary border border-primary-subtle d-flex align-items-center gap-1"><i class="bi bi-file-earmark-richtext"></i> Texto</span>';
			} else if (savedBlock.type === 'attachment') {
				badgeHtml = '<span class="badge bg-success-subtle text-success border border-success-subtle d-flex align-items-center gap-1"><i class="bi bi-paperclip"></i> Anexo</span>';
			} else {
				badgeHtml = '<span class="badge bg-warning-subtle text-warning border border-warning-subtle d-flex align-items-center gap-1"><i class="bi bi-patch-question-fill"></i> Quiz</span>';
			}

			const reqBadgeHtml = savedBlock.is_required
				? '<span class="badge bg-danger-subtle text-danger border border-danger-subtle small">Obrigatório</span>'
				: '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle small">Opcional</span>';

			let summaryHtml = '';
			const payload = savedBlock.payload || {};
			if (savedBlock.type === 'video') {
				const providerLabel = payload.provider === 'youtube' ? 'YouTube' : 'Upload S3';
				summaryHtml = `<span><strong>${providerLabel}:</strong> ${payload.url || ''}</span>`;
			} else if (savedBlock.type === 'text') {
				const plainText = (payload.content || '').replace(/<[^>]*>/g, '').trim().substring(0, 70);
				const minTime = parseInt(payload.min_reading_time_in_minutes || '0', 10);
				summaryHtml = `<span>${plainText}... (mín: ${minTime} min)</span>`;
			} else if (savedBlock.type === 'attachment') {
				const minTime = parseInt(payload.min_reading_time_in_minutes || '0', 10);
				summaryHtml = `<span><i class="bi bi-paperclip me-1"></i>${payload.filename || 'arquivo'} (mín: ${minTime} min)</span>`;
			}

			viewBar.innerHTML = `
				<div class="d-flex align-items-center gap-2 flex-wrap">
					<span class="edu-block-handle edu-reorder-handle text-muted cursor-grab" title="Arraste para reordenar bloco" aria-label="Reordenar bloco">
						<i class="bi bi-grip-vertical"></i>
					</span>
					${badgeHtml}
					${reqBadgeHtml}
					<span class="text-muted small fw-medium block-index-label">Bloco #1</span>
					<div class="ms-2 d-none d-md-inline-block text-truncate small text-muted" style="max-width: 480px;">
						${summaryHtml}
					</div>
				</div>
				<div class="d-flex align-items-center gap-1">
					<button type="button" class="edu-btn edu-btn-ghost edu-btn-sm" title="Editar Bloco" aria-label="Editar bloco" onclick="window.LessonEditorApp && window.LessonEditorApp.toggleEditBlock(${savedBlock.id})">
						<i class="bi bi-pencil me-1" aria-hidden="true"></i> Editar
					</button>
					<button type="button" class="edu-btn edu-btn-ghost text-danger edu-btn-sm" title="Excluir Bloco" aria-label="Excluir bloco" onclick="window.LessonEditorApp && window.LessonEditorApp.confirmDeleteBlock(${savedBlock.id})">
						<i class="bi bi-trash" aria-hidden="true"></i>
					</button>
				</div>
			`;
		},

		setupDropzones: function () {
			document.addEventListener('dragover', function (dragEvent) {
				const dropzone = dragEvent.target.closest('.edu-dropzone');
				if (dropzone) {
					dragEvent.preventDefault();
					dropzone.classList.add('is-dragover');
				}
			});

			document.addEventListener('dragleave', function (dragEvent) {
				const dropzone = dragEvent.target.closest('.edu-dropzone');
				if (dropzone && !dropzone.contains(dragEvent.relatedTarget)) {
					dropzone.classList.remove('is-dragover');
				}
			});

			document.addEventListener('drop', function (dragEvent) {
				const dropzone = dragEvent.target.closest('.edu-dropzone');
				if (dropzone) {
					dragEvent.preventDefault();
					dropzone.classList.remove('is-dragover');
					const fileInput = dropzone.parentElement.querySelector('input[type="file"]');
					if (fileInput && dragEvent.dataTransfer && dragEvent.dataTransfer.files.length > 0) {
						fileInput.files = dragEvent.dataTransfer.files;
						fileInput.dispatchEvent(new Event('change', { bubbles: true }));
					}
				}
			});
		},

		switchVideoMode: function (radioElement, mode) {
			const form = radioElement.closest('form');
			if (!form) return;

			const providerInput = form.querySelector('input[name="provider"]');
			const sectionYoutube = form.querySelector('.video-section-youtube');
			const sectionUpload = form.querySelector('.video-section-upload');

			if (mode === 'youtube') {
				providerInput.value = 'youtube';
				sectionYoutube.classList.remove('d-none');
				sectionUpload.classList.add('d-none');
			} else {
				providerInput.value = 'custom';
				sectionUpload.classList.remove('d-none');
				sectionYoutube.classList.add('d-none');
			}
		},

		toggleReplaceVideo: function (buttonElement) {
			const form = buttonElement.closest('form');
			if (!form) return;

			const dropzoneWrapper = form.querySelector('.upload-dropzone-wrapper');
			if (dropzoneWrapper) {
				dropzoneWrapper.classList.toggle('d-none');
			}
		},

		toggleReplaceAttachment: function (buttonElement) {
			const form = buttonElement.closest('form');
			if (!form) return;

			const dropzoneWrapper = form.querySelector('.upload-dropzone-wrapper');
			if (dropzoneWrapper) {
				dropzoneWrapper.classList.toggle('d-none');
			}
		},

		handleYoutubeInput: function (inputElement) {
			const form = inputElement.closest('form');
			if (!form) return;

			const rawValue = inputElement.value;
			const videoId = global.LessonUploader ? global.LessonUploader.extractYoutubeId(rawValue) : rawValue.trim();
			const urlHiddenInput = form.querySelector('input[name="url"]');
			const durationHiddenInput = form.querySelector('input[name="duration_in_seconds"]');
			const previewWrapper = form.querySelector('.youtube-player-preview-wrapper');

			if (!videoId) {
				urlHiddenInput.value = '';
				previewWrapper.classList.add('d-none');
				return;
			}

			urlHiddenInput.value = videoId;
			previewWrapper.classList.remove('d-none');

			const container = previewWrapper.querySelector('.edu-plyr-container');
			container.className = 'edu-plyr-container is-youtube';
			container.innerHTML = `<div class="js-plyr-embed" data-plyr-provider="youtube" data-plyr-embed-id="${videoId}"></div>`;

			const embedElement = container.querySelector('.js-plyr-embed');
			if (typeof Plyr !== 'undefined' && embedElement) {
				const playerInstance = new Plyr(embedElement, {
					controls: ['play-large', 'play', 'progress', 'current-time', 'mute', 'volume', 'fullscreen'],
					youtube: {
						noCookie: true,
						rel: 0,
						showinfo: 0,
						iv_load_policy: 3,
						modestbranding: 1
					}
				});

				playerInstance.on('ready', function () {
					const detectedDuration = Math.round(playerInstance.duration) || 0;
					if (detectedDuration > 0 && durationHiddenInput) {
						durationHiddenInput.value = detectedDuration;
					}
				});
			}
		},

		handleVideoFileSelect: async function (inputElement) {
			const form = inputElement.closest('form');
			if (!form || !inputElement.files || inputElement.files.length === 0) return;

			const file = inputElement.files[0];
			const urlHiddenInput = form.querySelector('input[name="url"]');
			const durationHiddenInput = form.querySelector('input[name="duration_in_seconds"]');
			const progressContainer = form.querySelector('.upload-progress-container');
			const progressBar = form.querySelector('.edu-upload-progress-bar');
			const progressPercent = form.querySelector('.upload-percent-text');
			const progressStatus = form.querySelector('.upload-status-text');
			const submitButton = form.querySelector('.btn-save-block');
			const previewDynamic = form.querySelector('.uploaded-video-preview-dynamic');
			const existingVideoWrapper = form.querySelector('.existing-video-wrapper');

			if (global.LessonUploader) {
				const detectedDuration = await global.LessonUploader.extractVideoDuration(file);
				if (durationHiddenInput) {
					durationHiddenInput.value = detectedDuration;
				}
			}

			progressContainer.classList.remove('d-none');
			if (submitButton) submitButton.disabled = true;

			try {
				if (!global.LessonUploader) {
					throw new Error('Módulo de upload não disponível.');
				}

				const uploadResult = await global.LessonUploader.uploadMultipartToS3(file, 'videos', function (percent, statusText) {
					progressBar.style.width = percent + '%';
					progressPercent.textContent = percent + '%';
					progressStatus.textContent = statusText;
				});

				const videoUrl = uploadResult.location || uploadResult.key;
				urlHiddenInput.value = videoUrl;

				if (existingVideoWrapper) {
					existingVideoWrapper.classList.add('d-none');
				}

				// Render instant Plyr preview for uploaded video
				if (previewDynamic) {
					previewDynamic.classList.remove('d-none');
					const plyrContainer = previewDynamic.querySelector('.edu-plyr-container');
					plyrContainer.innerHTML = `<video class="js-new-video" playsinline controls src="${videoUrl}"></video>`;
					const videoTag = plyrContainer.querySelector('.js-new-video');
					if (typeof Plyr !== 'undefined' && videoTag) {
						new Plyr(videoTag, {
							controls: ['play-large', 'play', 'progress', 'current-time', 'mute', 'volume', 'fullscreen']
						});
					}
				}
			} catch (uploadError) {
				alert(uploadError.message || 'Falha no upload do vídeo.');
				progressContainer.classList.add('d-none');
			} finally {
				if (submitButton) submitButton.disabled = false;
			}
		},

		handleAttachmentFileSelect: async function (inputElement) {
			const form = inputElement.closest('form');
			if (!form || !inputElement.files || inputElement.files.length === 0) return;

			const file = inputElement.files[0];
			const filepathHiddenInput = form.querySelector('input[name="filepath"]');
			const filesizeHiddenInput = form.querySelector('input[name="file_size"]');
			const filetypeHiddenInput = form.querySelector('input[name="file_type"]');
			const filenameInput = form.querySelector('.input-attach-filename');

			const progressContainer = form.querySelector('.upload-progress-container');
			const progressBar = form.querySelector('.edu-upload-progress-bar');
			const progressPercent = form.querySelector('.upload-percent-text');
			const progressStatus = form.querySelector('.upload-status-text');
			const submitButton = form.querySelector('.btn-save-block');

			if (filenameInput && !filenameInput.value.trim()) {
				filenameInput.value = file.name;
			}
			if (filesizeHiddenInput) filesizeHiddenInput.value = file.size;
			if (filetypeHiddenInput) filetypeHiddenInput.value = file.type || 'application/octet-stream';

			progressContainer.classList.remove('d-none');
			if (submitButton) submitButton.disabled = true;

			try {
				if (!global.LessonUploader) {
					throw new Error('Módulo de upload não disponível.');
				}

				const uploadResult = await global.LessonUploader.uploadMultipartToS3(file, 'attachments', function (percent, statusText) {
					progressBar.style.width = percent + '%';
					progressPercent.textContent = percent + '%';
					progressStatus.textContent = statusText;
				});

				filepathHiddenInput.value = uploadResult.location || uploadResult.key;
			} catch (uploadError) {
				alert(uploadError.message || 'Falha no upload do arquivo anexo.');
				progressContainer.classList.add('d-none');
			} finally {
				if (submitButton) submitButton.disabled = false;
			}
		},

		toggleEditBlock: function (blockId) {
			const blockItem = document.querySelector(`.edu-block-item[data-block-id="${blockId}"]`);
			if (!blockItem) return;

			const formContainer = blockItem.querySelector('.edu-block-form');
			if (!formContainer) return;

			const isCurrentlyEditing = blockItem.classList.contains('is-editing');
			if (isCurrentlyEditing) {
				blockItem.classList.remove('is-editing', 'is-active-block');
				blockItem.classList.add('is-collapsed');
				formContainer.classList.add('d-none');

				const otherEditing = document.querySelector('#blocks-sortable-list .edu-block-item.is-editing');
				if (otherEditing) {
					this.setActiveBlock(otherEditing);
				}
			} else {
				blockItem.classList.add('is-editing');
				blockItem.classList.remove('is-collapsed');
				formContainer.classList.remove('d-none');
				this.setActiveBlock(blockItem);

				const blockType = blockItem.dataset.blockType;
				if (blockType === 'text') {
					if (window.initRichEditors) {
						window.initRichEditors();
					}
				} else if (blockType === 'video') {
					const inputYoutube = formContainer.querySelector('.input-youtube-id');
					if (inputYoutube && inputYoutube.value.trim()) {
						this.handleYoutubeInput(inputYoutube);
					}
				}

				const firstInput = formContainer.querySelector('input:not([type="hidden"]), select, textarea');
				if (firstInput) {
					firstInput.focus();
				}
			}
		},

		showQuizInDevelopmentAlert: function () {
			if (typeof Swal !== 'undefined') {
				Swal.fire({
					icon: 'info',
					title: 'Recurso em Desenvolvimento',
					text: 'O Bloco de Quiz / Avaliação está em desenvolvimento e será disponibilizado em uma próxima atualização.',
					confirmButtonText: 'Entendido',
					customClass: {
						confirmButton: 'edu-btn edu-btn-primary'
					}
				});
			} else {
				alert('O Bloco de Quiz / Avaliação está em desenvolvimento e será disponibilizado em uma próxima atualização.');
			}
		},

		addNewBlockInline: function (type) {
			if (type === 'quiz') {
				this.showQuizInDevelopmentAlert();
				return;
			}

			const container = document.getElementById('blocks-sortable-list');
			if (!container) return;

			const totalBlocks = container.querySelectorAll(':scope > .edu-block-item').length;
			const newBlockIndex = totalBlocks + 1;

			const blockItem = document.createElement('div');
			blockItem.className = 'edu-block-item is-editing is-new is-active-block';
			blockItem.dataset.blockId = 'new';
			blockItem.dataset.blockType = type;

			let badgeHtml = '';
			if (type === 'video') {
				badgeHtml = '<span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="bi bi-play-circle me-1"></i> Vídeo</span>';
			} else if (type === 'text') {
				badgeHtml = '<span class="badge bg-primary-subtle text-primary border border-primary-subtle"><i class="bi bi-file-earmark-richtext me-1"></i> Texto</span>';
			} else if (type === 'attachment') {
				badgeHtml = '<span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-paperclip me-1"></i> Anexo</span>';
			}

			let fieldsHtml = '';
			if (type === 'video') {
				fieldsHtml = `
					<input type="hidden" name="duration_in_seconds" value="0">
					<input type="hidden" name="url" value="">
					<div class="mb-3">
						<label class="form-label fw-medium">Modelo de Vídeo</label>
						<div class="d-flex gap-2">
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" name="video_mode_choice" id="vmode-yt-new-${Date.now()}" value="youtube" checked onchange="window.LessonEditorApp && window.LessonEditorApp.switchVideoMode(this, 'youtube')">
								<label class="form-check-label" for="vmode-yt-new-${Date.now()}">YouTube</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" name="video_mode_choice" id="vmode-up-new-${Date.now()}" value="upload" onchange="window.LessonEditorApp && window.LessonEditorApp.switchVideoMode(this, 'upload')">
								<label class="form-check-label" for="vmode-up-new-${Date.now()}">Upload Direto (AWS S3)</label>
							</div>
						</div>
						<input type="hidden" name="provider" value="youtube">
					</div>

					<div class="video-section-youtube">
						<div class="mb-3">
							<label class="form-label fw-medium">Identificador ou URL do Vídeo no YouTube <span class="text-danger">*</span></label>
							<input type="text" class="form-control font-monospace input-youtube-id" placeholder="Ex: dQw4w9WgXcQ ou https://www.youtube.com/watch?v=..." oninput="window.LessonEditorApp && window.LessonEditorApp.handleYoutubeInput(this)">
							<small class="text-muted">Insira o ID ou cole o link do YouTube.</small>
						</div>
						<div class="youtube-player-preview-wrapper mt-2 d-none">
							<div class="edu-plyr-container is-youtube"></div>
						</div>
					</div>

					<div class="video-section-upload d-none">
						<div class="upload-dropzone-wrapper">
							<div class="edu-dropzone mb-2" onclick="this.nextElementSibling.click()">
								<i class="bi bi-cloud-arrow-up edu-dropzone-icon d-block"></i>
								<div class="fw-semibold">Arraste e solte o vídeo aqui ou clique para selecionar</div>
								<div class="text-muted small">Formatos aceitos: MP4, WebM, MOV. Envio multipart direto para a nuvem.</div>
							</div>
							<input type="file" class="d-none input-file-video" accept="video/mp4,video/webm,video/quicktime" onchange="window.LessonEditorApp && window.LessonEditorApp.handleVideoFileSelect(this)">
						</div>
						<div class="upload-progress-container d-none mb-2">
							<div class="d-flex justify-content-between small text-muted mb-1">
								<span class="upload-status-text">Enviando vídeo...</span>
								<span class="upload-percent-text fw-semibold">0%</span>
							</div>
							<div class="edu-upload-progress">
								<div class="edu-upload-progress-bar" style="width: 0%;"></div>
							</div>
						</div>
						<div class="uploaded-video-preview-dynamic d-none mt-2">
							<div class="edu-plyr-container"></div>
						</div>
					</div>
				`;
			} else if (type === 'text') {
				fieldsHtml = `
					<div class="row g-3">
						<div class="col-12">
							<label class="form-label fw-medium">Conteúdo Formatado <span class="text-danger">*</span></label>
							<textarea class="form-control edu-rich-editor" name="content" data-rich-editor="true" rows="6"></textarea>
						</div>
						<div class="col-md-6">
							<label class="form-label fw-medium">Tempo Mínimo de Leitura (em minutos)</label>
							<input type="number" class="form-control" name="min_reading_time_in_minutes" min="0" value="0">
							<small class="text-muted">Tempo mínimo de permanência antes de liberar o avanço (0 para sem restrição).</small>
						</div>
					</div>
				`;
			} else if (type === 'attachment') {
				fieldsHtml = `
					<input type="hidden" name="filepath" value="">
					<input type="hidden" name="file_size" value="0">
					<input type="hidden" name="file_type" value="">
					<div class="row g-3">
						<div class="col-12">
							<div class="upload-dropzone-wrapper">
								<div class="edu-dropzone mb-2" onclick="this.nextElementSibling.click()">
									<i class="bi bi-file-earmark-arrow-up edu-dropzone-icon d-block"></i>
									<div class="fw-semibold">Arraste e solte o arquivo anexo aqui ou clique para selecionar</div>
									<div class="text-muted small">Documentos em PDF, apostilas, códigos compactados (Zip/Tar).</div>
								</div>
								<input type="file" class="d-none input-file-attachment" onchange="window.LessonEditorApp && window.LessonEditorApp.handleAttachmentFileSelect(this)">
							</div>
							<div class="upload-progress-container d-none mb-3">
								<div class="d-flex justify-content-between small text-muted mb-1">
									<span class="upload-status-text">Enviando anexo...</span>
									<span class="upload-percent-text fw-semibold">0%</span>
								</div>
								<div class="edu-upload-progress">
									<div class="edu-upload-progress-bar" style="width: 0%;"></div>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<label class="form-label fw-medium">Nome de Exibição do Arquivo <span class="text-danger">*</span></label>
							<input type="text" class="form-control input-attach-filename" name="filename" placeholder="Ex: Slides da Aula em PDF" required>
						</div>
						<div class="col-md-6">
							<label class="form-label fw-medium">Tempo Mínimo de Estudo (em minutos)</label>
							<input type="number" class="form-control" name="min_reading_time_in_minutes" min="0" value="0">
							<small class="text-muted">Se obrigatório, tempo mínimo antes de registrar como concluído.</small>
						</div>
					</div>
				`;
			}

			blockItem.innerHTML = `
				<div class="edu-block-view d-flex align-items-center justify-content-between mb-2">
					<div class="d-flex align-items-center gap-2">
						<span class="edu-block-handle text-muted"><i class="bi bi-grip-vertical"></i></span>
						${badgeHtml}
						<span class="badge bg-primary-subtle text-primary border border-primary-subtle small">Novo</span>
						<span class="text-muted small fw-medium block-index-label">Bloco #${newBlockIndex}</span>
					</div>
				</div>

				<div class="edu-block-form">
					<form onsubmit="event.preventDefault(); window.LessonEditorApp && window.LessonEditorApp.submitBlockItem(this);">
						<input type="hidden" name="block_id" value="">
						<input type="hidden" name="type" value="${type}">

						<div class="mb-3 p-3 border rounded bg-body-tertiary">
							<div class="form-check form-switch mb-0">
								<input class="form-check-input" type="checkbox" role="switch" id="block-req-new-${Date.now()}" name="is_required" value="1" checked>
								<label class="form-check-label fw-semibold" for="block-req-new-${Date.now()}">
									Item de Conclusão Obrigatória
								</label>
								<div class="text-muted small">Se desmarcado, o bloco será considerado material complementar/opcional.</div>
							</div>
						</div>

						${fieldsHtml}

						<div class="d-flex justify-content-end gap-2 mt-3 pt-3 border-top">
							<button type="button" class="edu-btn edu-btn-outline edu-btn-sm" onclick="this.closest('.edu-block-item').remove(); window.LessonEditorApp && window.LessonEditorApp.onBlockRemoved();">
								Cancelar
							</button>
							<button type="submit" class="edu-btn edu-btn-primary edu-btn-sm btn-save-block">
								<i class="bi bi-check2 me-1"></i> Salvar Bloco
							</button>
						</div>
					</form>
				</div>
			`;

			container.appendChild(blockItem);
			this.setActiveBlock(blockItem);
			if (global.LessonBlocksReorder) {
				global.LessonBlocksReorder.updateBlocksVisibilityState();
			}

			if (type === 'text') {
				if (window.initRichEditors) {
					window.initRichEditors();
				}
			}

			const firstInput = blockItem.querySelector('input:not([type="hidden"]), select, textarea');
			if (firstInput) {
				firstInput.focus();
			}
		},

		onBlockRemoved: function () {
			this.renumberBlocks();
			if (global.LessonBlocksReorder) {
				global.LessonBlocksReorder.updateBlocksVisibilityState();
			}
			const otherEditing = document.querySelector('#blocks-sortable-list .edu-block-item.is-editing');
			if (otherEditing) {
				this.setActiveBlock(otherEditing);
			}
		},

		submitBlockItem: function (formElement) {
			const submitButton = formElement.querySelector('.btn-save-block');
			const blockItem = formElement.closest('.edu-block-item');
			const blockId = formElement.querySelector('input[name="block_id"]').value;
			const type = formElement.querySelector('input[name="type"]').value;
			const isRequiredInput = formElement.querySelector('input[name="is_required"]');
			const isRequired = isRequiredInput ? isRequiredInput.checked : true;

			let payload = {};
			if (type === 'video') {
				const providerInput = formElement.querySelector('input[name="provider"]');
				const urlInput = formElement.querySelector('input[name="url"]');
				const durationInput = formElement.querySelector('input[name="duration_in_seconds"]');

				const finalUrl = (urlInput ? urlInput.value.trim() : '');
				if (!finalUrl) {
					alert('Por favor, informe o identificador do YouTube ou envie um vídeo.');
					return;
				}

				payload = {
					provider: providerInput ? providerInput.value : 'youtube',
					url: finalUrl,
					duration_in_seconds: durationInput ? parseInt(durationInput.value || '0', 10) : 0
				};
			} else if (type === 'text') {
				const contentTextarea = formElement.querySelector('textarea[name="content"]');
				const minReadingInput = formElement.querySelector('input[name="min_reading_time_in_minutes"]');

				const finalContent = contentTextarea ? contentTextarea.value.trim() : '';
				if (!finalContent) {
					alert('O conteúdo do texto não pode estar vazio.');
					return;
				}

				payload = {
					content: finalContent,
					min_reading_time_in_minutes: minReadingInput ? parseInt(minReadingInput.value || '0', 10) : 0
				};
			} else if (type === 'attachment') {
				const filenameInput = formElement.querySelector('.input-attach-filename');
				const filepathInput = formElement.querySelector('input[name="filepath"]');
				const minReadingInput = formElement.querySelector('input[name="min_reading_time_in_minutes"]');
				const filesizeInput = formElement.querySelector('input[name="file_size"]');
				const filetypeInput = formElement.querySelector('input[name="file_type"]');

				const finalFilepath = filepathInput ? filepathInput.value.trim() : '';
				if (!finalFilepath) {
					alert('Por favor, envie um arquivo anexo antes de salvar.');
					return;
				}

				payload = {
					filename: filenameInput ? filenameInput.value.trim() : '',
					filepath: finalFilepath,
					file_size: filesizeInput ? parseInt(filesizeInput.value || '0', 10) : 0,
					file_type: filetypeInput ? filetypeInput.value : 'application/octet-stream',
					min_reading_time_in_minutes: minReadingInput ? parseInt(minReadingInput.value || '0', 10) : 0
				};
			}

			const requestData = {
				lesson_id: lessonId,
				block_id: blockId ? parseInt(blockId, 10) : null,
				type: type,
				is_required: isRequired,
				payload: payload
			};

			Http.post('/admin/cursos/blocos/salvar', requestData, { button: submitButton })
				.then(function (response) {
					const savedBlock = response && response.block ? response.block : null;
					if (!savedBlock || !blockItem) {
						window.location.reload();
						return;
					}

					// 1. Assign block ID if new
					blockItem.dataset.blockId = savedBlock.id;
					blockItem.classList.remove('is-new');
					const blockIdInput = formElement.querySelector('input[name="block_id"]');
					if (blockIdInput) blockIdInput.value = savedBlock.id;

					const switchInput = formElement.querySelector('input[name="is_required"]');
					if (switchInput) {
						switchInput.id = 'block-req-' + savedBlock.id;
						const switchLabel = formElement.querySelector('label[for^="block-req-"]');
						if (switchLabel) switchLabel.htmlFor = 'block-req-' + savedBlock.id;
					}

					const cancelBtn = formElement.querySelector('button[onclick*="onBlockRemoved"], button[onclick*="toggleEditBlock"]');
					if (cancelBtn) {
						cancelBtn.setAttribute('onclick', `window.LessonEditorApp && window.LessonEditorApp.toggleEditBlock(${savedBlock.id})`);
					}

					// 2. Update view bar display
					LessonEditorApp.updateBlockViewBar(blockItem, savedBlock);

					// 3. Collapse only this specific saved block
					blockItem.classList.remove('is-editing', 'is-active-block');
					blockItem.classList.add('is-collapsed');
					const formContainer = blockItem.querySelector('.edu-block-form');
					if (formContainer) formContainer.classList.add('d-none');

					// 4. Subtle success feedback pulse
					blockItem.classList.add('is-saved-flash');
					setTimeout(function () {
						blockItem.classList.remove('is-saved-flash');
					}, 1600);

					// 5. If any other block remains in editing mode, set that one as active
					const otherEditing = document.querySelector('#blocks-sortable-list .edu-block-item.is-editing');
					if (otherEditing) {
						LessonEditorApp.setActiveBlock(otherEditing);
					}

					// 6. Refresh sequential index labels and visibility
					LessonEditorApp.renumberBlocks();
					if (global.LessonBlocksReorder) {
						global.LessonBlocksReorder.updateBlocksVisibilityState();
					}
				})
				.catch(function (error) {
					alert(error.message || 'Erro ao salvar bloco de conteúdo.');
				});
		},

		confirmDeleteBlock: function (blockId) {
			if (!confirm('Deseja realmente remover este bloco de conteúdo?')) {
				return;
			}

			Http.post('/admin/cursos/blocos/excluir/' + blockId)
				.then(function () {
					const blockItem = document.querySelector(`.edu-block-item[data-block-id="${blockId}"]`);
					if (blockItem) {
						blockItem.remove();
					}
					LessonEditorApp.renumberBlocks();
					if (global.LessonBlocksReorder) {
						global.LessonBlocksReorder.updateBlocksVisibilityState();
					}
				})
				.catch(function (error) {
					alert(error.message || 'Erro ao excluir bloco.');
				});
		}
	};

	global.LessonEditorApp = LessonEditorApp;

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () {
			LessonEditorApp.init();
		});
	} else {
		LessonEditorApp.init();
	}
})(window);
