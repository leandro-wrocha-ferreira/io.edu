/**
 * Lesson Content Blocks Reordering & Visibility Manager — io.edu LMS
 * File: public/assets/js/pages/admin/courses/lesson-blocks-reorder.js
 */
(function (global) {
	'use strict';

	/**
	 * Extract ordered block integer IDs from container.
	 *
	 * @param {HTMLElement} container
	 * @returns {number[]}
	 */
	function getBlockIds(container) {
		const items = container.querySelectorAll(':scope > .edu-block-item');
		return Array.from(items)
			.map(function (item) {
				return parseInt(item.dataset.blockId, 10);
			})
			.filter(function (identifier) {
				return !isNaN(identifier) && identifier > 0;
			});
	}

	/**
	 * Update numbered indicators for lesson blocks.
	 *
	 * @param {HTMLElement} blocksContainer
	 */
	function updateBlockNumbers(blocksContainer) {
		const blockItems = blocksContainer.querySelectorAll(':scope > .edu-block-item');
		blockItems.forEach(function (item, index) {
			const labelSpan = item.querySelector('.text-muted.small.fw-medium');
			if (labelSpan && labelSpan.textContent.indexOf('Bloco #') !== -1) {
				labelSpan.textContent = 'Bloco #' + (index + 1);
			}
			const blockIndexLabel = item.querySelector('.block-index-label');
			if (blockIndexLabel) {
				blockIndexLabel.textContent = 'Bloco #' + (index + 1);
			}
		});
	}

	/**
	 * Synchronize empty notice and bottom toolbar visibility based on blocks count.
	 */
	function updateBlocksVisibilityState() {
		const container = document.getElementById('blocks-sortable-list');
		const bottomToolbar = document.getElementById('blocks-bottom-toolbar');
		if (!container) return;

		const totalBlocks = container.querySelectorAll(':scope > .edu-block-item').length;
		let emptyNotice = document.getElementById('empty-blocks-notice');

		if (totalBlocks === 0) {
			if (bottomToolbar) {
				bottomToolbar.classList.add('d-none');
			}
			if (!emptyNotice) {
				emptyNotice = document.createElement('div');
				emptyNotice.id = 'empty-blocks-notice';
				emptyNotice.className = 'p-5 text-center border rounded-3 bg-body-tertiary';
				emptyNotice.innerHTML = `
					<i class="bi bi-collection text-muted fs-1 mb-2 d-block"></i>
					<h3 class="h6 fw-semibold text-body">Nenhum bloco de conteúdo adicionado ainda</h3>
					<p class="text-muted small mb-3">Adicione um vídeo explicativo, artigo teórico ou material de apoio em anexo.</p>
					<div class="d-flex justify-content-center gap-2 flex-wrap">
						<button type="button" class="edu-btn edu-btn-outline edu-btn-sm" onclick="window.LessonEditorApp && window.LessonEditorApp.addNewBlockInline('video')">
							<i class="bi bi-play-circle me-1"></i> Adicionar Vídeo
						</button>
						<button type="button" class="edu-btn edu-btn-outline edu-btn-sm" onclick="window.LessonEditorApp && window.LessonEditorApp.addNewBlockInline('text')">
							<i class="bi bi-file-earmark-richtext me-1"></i> Adicionar Texto
						</button>
						<button type="button" class="edu-btn edu-btn-outline edu-btn-sm" onclick="window.LessonEditorApp && window.LessonEditorApp.addNewBlockInline('attachment')">
							<i class="bi bi-paperclip me-1"></i> Adicionar Arquivo
						</button>
					</div>
				`;
				container.appendChild(emptyNotice);
			}
		} else {
			if (emptyNotice) {
				emptyNotice.remove();
			}
			if (bottomToolbar) {
				bottomToolbar.classList.remove('d-none');
			}
		}
	}

	/**
	 * Setup Smooth Floating Drag & Drop for content blocks.
	 *
	 * @param {number} lessonId
	 */
	function setupBlocksReorder(lessonId) {
		const container = document.getElementById('blocks-sortable-list');
		if (!container || !lessonId) {
			return;
		}

		container.addEventListener('pointerdown', function (pointerEvent) {
			const handle = pointerEvent.target.closest('.edu-block-handle');
			if (!handle || !container.contains(handle)) {
				return;
			}

			const blockItem = handle.closest('.edu-block-item');
			if (!blockItem || blockItem.parentElement !== container) {
				return;
			}

			if (blockItem.classList.contains('is-editing')) {
				return;
			}

			pointerEvent.preventDefault();
			pointerEvent.stopPropagation();

			const startOrder = getBlockIds(container);
			const boundingRectangle = blockItem.getBoundingClientRect();
			const offsetX = pointerEvent.clientX - boundingRectangle.left;
			const offsetY = pointerEvent.clientY - boundingRectangle.top;
			let hasMoved = false;

			const placeholder = document.createElement('div');
			placeholder.className = 'edu-drag-placeholder';
			placeholder.style.height = boundingRectangle.height + 'px';
			placeholder.style.width = '100%';

			container.insertBefore(placeholder, blockItem);

			blockItem.classList.add('edu-drag-floating');
			blockItem.style.width = boundingRectangle.width + 'px';
			blockItem.style.height = boundingRectangle.height + 'px';
			blockItem.style.left = boundingRectangle.left + 'px';
			blockItem.style.top = boundingRectangle.top + 'px';
			document.body.appendChild(blockItem);
			document.body.style.userSelect = 'none';
			document.body.style.cursor = 'grabbing';

			function onPointerMove(moveEvent) {
				hasMoved = true;
				blockItem.style.left = (moveEvent.clientX - offsetX) + 'px';
				blockItem.style.top = (moveEvent.clientY - offsetY) + 'px';

				const siblings = Array.from(container.querySelectorAll(':scope > .edu-block-item:not(.edu-drag-floating)'));
				let nextSibling = null;
				let smallestOffset = Number.NEGATIVE_INFINITY;

				siblings.forEach(function (sibling) {
					const box = sibling.getBoundingClientRect();
					const calculatedOffset = moveEvent.clientY - box.top - (box.height / 2);
					if (calculatedOffset < 0 && calculatedOffset > smallestOffset) {
						smallestOffset = calculatedOffset;
						nextSibling = sibling;
					}
				});

				if (nextSibling === null) {
					container.appendChild(placeholder);
				} else {
					container.insertBefore(placeholder, nextSibling);
				}
			}

			function onPointerUp() {
				document.removeEventListener('pointermove', onPointerMove);
				document.removeEventListener('pointerup', onPointerUp);
				document.removeEventListener('pointercancel', onPointerUp);

				container.insertBefore(blockItem, placeholder);
				if (placeholder.parentElement) {
					placeholder.remove();
				}

				blockItem.classList.remove('edu-drag-floating');
				blockItem.removeAttribute('style');
				document.body.style.userSelect = '';
				document.body.style.cursor = '';

				updateBlockNumbers(container);

				if (!hasMoved) {
					return;
				}

				const endOrder = getBlockIds(container);
				const hasChanged = endOrder.length !== startOrder.length || endOrder.some(function (orderedId, orderIndex) {
					return orderedId !== startOrder[orderIndex];
				});

				if (hasChanged) {
					Http.post('/admin/cursos/blocos/reordenar', {
						lesson_id: lessonId,
						block_ids: endOrder
					}).catch(function (error) {
						alert(error.message || 'Erro ao reordenar blocos de conteúdo.');
					});
				}
			}

			document.addEventListener('pointermove', onPointerMove);
			document.addEventListener('pointerup', onPointerUp);
			document.addEventListener('pointercancel', onPointerUp);
		});
	}

	global.LessonBlocksReorder = {
		getBlockIds: getBlockIds,
		updateBlockNumbers: updateBlockNumbers,
		updateBlocksVisibilityState: updateBlocksVisibilityState,
		setupBlocksReorder: setupBlocksReorder
	};
})(window);
