/**
 * Curriculum & Content Hierarchy Management — io.edu LMS
 * File: public/assets/js/pages/admin/courses/curriculum.js
 */
(function (global) {
	'use strict';

	const rootContainer = document.getElementById('curriculum-tree-container');
	if (!rootContainer) {
		return;
	}

	const courseId = parseInt(rootContainer.dataset.courseId, 10);

	const CurriculumApp = {
		init: function () {
			if (global.CurriculumReorder) {
				global.CurriculumReorder.setupModulesReorder(courseId);
				global.CurriculumReorder.setupLessonsReorder();
			}
		},

		openNewModuleModal: function () {
			const form = document.getElementById('form-module');
			if (!form) return;
			form.reset();
			document.getElementById('modal-module-id').value = '';
			document.getElementById('modal-module-title').textContent = 'Novo Módulo';
			const modalEl = document.getElementById('modal-module');
			if (modalEl && typeof bootstrap !== 'undefined') {
				const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
				modal.show();
			}
		},

		openEditModuleModal: function (moduleId, title, description, status) {
			const form = document.getElementById('form-module');
			if (!form) return;
			form.reset();
			document.getElementById('modal-module-id').value = moduleId;
			document.getElementById('module-input-title').value = title;
			document.getElementById('module-input-description').value = description || '';
			document.getElementById('module-input-status').value = status || 'draft';
			document.getElementById('modal-module-title').textContent = 'Editar Módulo';

			const modalEl = document.getElementById('modal-module');
			if (modalEl && typeof bootstrap !== 'undefined') {
				const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
				modal.show();
			}
		},

		submitModuleForm: function () {
			const form = document.getElementById('form-module');
			const submitBtn = document.getElementById('btn-save-module');
			if (!form) return;

			const formData = new FormData(form);

			Http.post('/admin/cursos/modulos/salvar', formData, { button: submitBtn })
				.then(function () {
					const modalEl = document.getElementById('modal-module');
					if (modalEl && typeof bootstrap !== 'undefined') {
						const modal = bootstrap.Modal.getInstance(modalEl);
						if (modal) modal.hide();
					}
					window.location.reload();
				})
				.catch(function (error) {
					alert(error.message || 'Erro ao salvar módulo.');
				});
		},

		confirmDeleteModule: function (moduleId, moduleTitle) {
			if (!confirm('Deseja realmente excluir o módulo "' + moduleTitle + '" e todas as suas aulas vinculadas?')) {
				return;
			}

			Http.post('/admin/cursos/modulos/excluir/' + moduleId)
				.then(function () {
					window.location.reload();
				})
				.catch(function (error) {
					alert(error.message || 'Erro ao excluir módulo.');
				});
		},

		openNewLessonModal: function (moduleId) {
			const form = document.getElementById('form-lesson');
			if (!form) return;
			form.reset();
			document.getElementById('modal-lesson-module-id').value = moduleId;
			document.getElementById('modal-lesson-id').value = '';
			document.getElementById('modal-lesson-title').textContent = 'Nova Aula';

			const modalEl = document.getElementById('modal-lesson');
			if (modalEl && typeof bootstrap !== 'undefined') {
				const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
				modal.show();
			}
		},

		submitLessonForm: function () {
			const form = document.getElementById('form-lesson');
			const submitBtn = document.getElementById('btn-save-lesson');
			if (!form) return;

			const moduleId = document.getElementById('modal-lesson-module-id').value;
			const lessonId = document.getElementById('modal-lesson-id').value;
			const title = document.getElementById('lesson-input-title').value;
			const description = document.getElementById('lesson-input-description').value;
			const status = document.getElementById('lesson-input-status').value;

			const payload = {
				module_id: moduleId,
				lesson_id: lessonId,
				title: title,
				description: description,
				status: status
			};

			Http.post('/admin/cursos/aulas/salvar', payload, { button: submitBtn })
				.then(function () {
					const modalEl = document.getElementById('modal-lesson');
					if (modalEl && typeof bootstrap !== 'undefined') {
						const modal = bootstrap.Modal.getInstance(modalEl);
						if (modal) modal.hide();
					}
					window.location.reload();
				})
				.catch(function (error) {
					alert(error.message || 'Erro ao salvar aula.');
				});
		},

		confirmDeleteLesson: function (lessonId, lessonTitle) {
			if (!confirm('Deseja realmente excluir a aula "' + lessonTitle + '"?')) {
				return;
			}

			Http.post('/admin/cursos/aulas/excluir/' + lessonId)
				.then(function () {
					window.location.reload();
				})
				.catch(function (error) {
					alert(error.message || 'Erro ao excluir aula.');
				});
		}
	};

	global.CurriculumApp = CurriculumApp;

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () {
			CurriculumApp.init();
		});
	} else {
		CurriculumApp.init();
	}
})(window);
