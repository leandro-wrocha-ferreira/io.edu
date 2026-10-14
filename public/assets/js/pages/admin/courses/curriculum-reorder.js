/**
 * Curriculum Reordering Engine (Modules & Lessons) — io.edu LMS
 * File: public/assets/js/pages/admin/courses/curriculum-reorder.js
 */
(function (global) {
	'use strict';

	/**
	 * Extract ordered module integer IDs from container.
	 *
	 * @param {HTMLElement} modulesContainer
	 * @returns {number[]}
	 */
	function getModuleIds(modulesContainer) {
		const cards = modulesContainer.querySelectorAll(':scope > .edu-module-card');
		return Array.from(cards).map(function (card) {
			return parseInt(card.dataset.moduleId, 10);
		});
	}

	/**
	 * Extract ordered lesson integer IDs from container.
	 *
	 * @param {HTMLElement} lessonsContainer
	 * @returns {number[]}
	 */
	function getLessonIds(lessonsContainer) {
		const items = lessonsContainer.querySelectorAll(':scope > .edu-lesson-item');
		return Array.from(items).map(function (item) {
			return parseInt(item.dataset.lessonId, 10);
		});
	}

	/**
	 * Update numbered badge indicators for modules.
	 *
	 * @param {HTMLElement} modulesContainer
	 */
	function updateModuleBadges(modulesContainer) {
		const moduleCards = modulesContainer.querySelectorAll(':scope > .edu-module-card');
		moduleCards.forEach(function (card, index) {
			const badge = card.querySelector('.badge.bg-primary-subtle');
			if (badge) {
				badge.textContent = 'MÓDULO ' + (index + 1);
			}
		});
	}

	/**
	 * Update position number badge for lessons without touching the title.
	 *
	 * @param {HTMLElement} lessonsContainer
	 */
	function updateLessonBadges(lessonsContainer) {
		const lessonItems = lessonsContainer.querySelectorAll(':scope > .edu-lesson-item');
		lessonItems.forEach(function (item, index) {
			const indexBadge = item.querySelector('.edu-lesson-index');
			if (indexBadge) {
				indexBadge.textContent = String(index + 1);
			}
		});
	}

	/**
	 * Setup Smooth Floating Drag & Drop for Course Modules.
	 *
	 * @param {number} courseId
	 */
	function setupModulesReorder(courseId) {
		const modulesContainer = document.getElementById('modules-sortable-list');
		if (!modulesContainer) {
			return;
		}

		modulesContainer.addEventListener('pointerdown', function (event) {
			const handle = event.target.closest('.edu-module-handle');
			if (!handle || !modulesContainer.contains(handle)) {
				return;
			}

			const card = handle.closest('.edu-module-card');
			if (!card || card.parentElement !== modulesContainer) {
				return;
			}

			event.preventDefault();
			event.stopPropagation();

			const startOrder = getModuleIds(modulesContainer);
			const boundingRectangle = card.getBoundingClientRect();
			const offsetX = event.clientX - boundingRectangle.left;
			const offsetY = event.clientY - boundingRectangle.top;
			let hasMoved = false;

			// Create visual placeholder
			const placeholder = document.createElement('div');
			placeholder.className = 'edu-drag-placeholder';
			placeholder.style.height = boundingRectangle.height + 'px';
			placeholder.style.width = '100%';

			// Insert placeholder in current position
			modulesContainer.insertBefore(placeholder, card);

			// Transform dragged card into floating element
			card.classList.add('edu-drag-floating');
			card.style.width = boundingRectangle.width + 'px';
			card.style.height = boundingRectangle.height + 'px';
			card.style.left = boundingRectangle.left + 'px';
			card.style.top = boundingRectangle.top + 'px';
			document.body.appendChild(card);
			document.body.style.userSelect = 'none';
			document.body.style.cursor = 'grabbing';

			function onPointerMove(moveEvent) {
				hasMoved = true;
				card.style.left = (moveEvent.clientX - offsetX) + 'px';
				card.style.top = (moveEvent.clientY - offsetY) + 'px';

				const siblings = Array.from(modulesContainer.querySelectorAll(':scope > .edu-module-card:not(.edu-drag-floating)'));
				let nextSibling = null;
				let smallestOffset = Number.NEGATIVE_INFINITY;

				siblings.forEach(function (sibling) {
					const box = sibling.getBoundingClientRect();
					const offset = moveEvent.clientY - box.top - (box.height / 2);
					if (offset < 0 && offset > smallestOffset) {
						smallestOffset = offset;
						nextSibling = sibling;
					}
				});

				if (nextSibling === null) {
					modulesContainer.appendChild(placeholder);
				} else {
					modulesContainer.insertBefore(placeholder, nextSibling);
				}
			}

			function onPointerUp() {
				document.removeEventListener('pointermove', onPointerMove);
				document.removeEventListener('pointerup', onPointerUp);
				document.removeEventListener('pointercancel', onPointerUp);

				// Re-insert card at placeholder location
				modulesContainer.insertBefore(card, placeholder);
				if (placeholder.parentElement) {
					placeholder.remove();
				}

				card.classList.remove('edu-drag-floating');
				card.removeAttribute('style');
				document.body.style.userSelect = '';
				document.body.style.cursor = '';

				updateModuleBadges(modulesContainer);

				if (!hasMoved) {
					return;
				}

				const endOrder = getModuleIds(modulesContainer);
				const hasChanged = endOrder.length !== startOrder.length || endOrder.some(function (orderedId, orderIndex) {
					return orderedId !== startOrder[orderIndex];
				});

				if (hasChanged && courseId) {
					Http.post('/admin/cursos/modulos/reordenar', {
						course_id: courseId,
						module_ids: endOrder
					}).catch(function (error) {
						alert(error.message || 'Erro ao reordenar módulos.');
					});
				}
			}

			document.addEventListener('pointermove', onPointerMove);
			document.addEventListener('pointerup', onPointerUp);
			document.addEventListener('pointercancel', onPointerUp);
		});
	}

	/**
	 * Setup Smooth Floating Drag & Drop for Lessons within each Module.
	 */
	function setupLessonsReorder() {
		const lessonContainers = document.querySelectorAll('.lessons-sortable-list');
		lessonContainers.forEach(function (container) {
			container.addEventListener('pointerdown', function (event) {
				const handle = event.target.closest('.edu-lesson-handle');
				if (!handle || !container.contains(handle)) {
					return;
				}

				const lessonItem = handle.closest('.edu-lesson-item');
				if (!lessonItem || lessonItem.parentElement !== container) {
					return;
				}

				event.preventDefault();
				event.stopPropagation();

				const startOrder = getLessonIds(container);
				const boundingRectangle = lessonItem.getBoundingClientRect();
				const offsetX = event.clientX - boundingRectangle.left;
				const offsetY = event.clientY - boundingRectangle.top;
				let hasMoved = false;

				// Create visual placeholder
				const placeholder = document.createElement('div');
				placeholder.className = 'edu-drag-placeholder';
				placeholder.style.height = boundingRectangle.height + 'px';
				placeholder.style.width = '100%';

				// Insert placeholder in place of item
				container.insertBefore(placeholder, lessonItem);

				// Make item float smoothly above layout
				lessonItem.classList.add('edu-drag-floating');
				lessonItem.style.width = boundingRectangle.width + 'px';
				lessonItem.style.height = boundingRectangle.height + 'px';
				lessonItem.style.left = boundingRectangle.left + 'px';
				lessonItem.style.top = boundingRectangle.top + 'px';
				document.body.appendChild(lessonItem);
				document.body.style.userSelect = 'none';
				document.body.style.cursor = 'grabbing';

				function onPointerMove(moveEvent) {
					hasMoved = true;
					lessonItem.style.left = (moveEvent.clientX - offsetX) + 'px';
					lessonItem.style.top = (moveEvent.clientY - offsetY) + 'px';

					const siblings = Array.from(container.querySelectorAll(':scope > .edu-lesson-item:not(.edu-drag-floating)'));
					let nextSibling = null;
					let smallestOffset = Number.NEGATIVE_INFINITY;

					siblings.forEach(function (sibling) {
						const box = sibling.getBoundingClientRect();
						const offset = moveEvent.clientY - box.top - (box.height / 2);
						if (offset < 0 && offset > smallestOffset) {
							smallestOffset = offset;
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

					// Re-insert item at placeholder position
					container.insertBefore(lessonItem, placeholder);
					if (placeholder.parentElement) {
						placeholder.remove();
					}

					lessonItem.classList.remove('edu-drag-floating');
					lessonItem.removeAttribute('style');
					document.body.style.userSelect = '';
					document.body.style.cursor = '';

					updateLessonBadges(container);

					if (!hasMoved) {
						return;
					}

					const endOrder = getLessonIds(container);
					const hasChanged = endOrder.length !== startOrder.length || endOrder.some(function (orderedId, orderIndex) {
						return orderedId !== startOrder[orderIndex];
					});

					if (hasChanged) {
						const targetModuleId = parseInt(container.dataset.moduleId, 10);
						Http.post('/admin/cursos/aulas/reordenar', {
							module_id: targetModuleId,
							lesson_ids: endOrder
						}).catch(function (error) {
							alert(error.message || 'Erro ao reordenar aulas.');
						});
					}
				}

				document.addEventListener('pointermove', onPointerMove);
				document.addEventListener('pointerup', onPointerUp);
				document.addEventListener('pointercancel', onPointerUp);
			});
		});
	}

	global.CurriculumReorder = {
		setupModulesReorder: setupModulesReorder,
		setupLessonsReorder: setupLessonsReorder,
		updateModuleBadges: updateModuleBadges,
		updateLessonBadges: updateLessonBadges
	};
})(window);
