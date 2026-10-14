<?php
/**
 * @var \app\domain\course\Course $course
 * @var array<\app\domain\course\CourseModule> $modules
 * @var array{total_modules: int, total_lessons: int, total_duration_seconds: int} $metrics
 */
$formatted_duration = '0min';
if (!empty($metrics['total_duration_seconds'])) {
	$hours = (int) floor($metrics['total_duration_seconds'] / 3600);
	$minutes = (int) floor(($metrics['total_duration_seconds'] % 3600) / 60);
	$formatted_duration = $hours > 0 ? ($minutes > 0 ? "{$hours}h {$minutes}min" : "{$hours}h") : "{$minutes}min";
}
?>
<div class="container-fluid px-0">
	<!-- Context Header -->
	<header class="page-header animate-fade-up">
		<div class="page-header-info">
			<nav class="page-header-breadcrumb d-flex align-items-center gap-2 mb-2" aria-label="Localização">
				<a href="<?= base_url('admin/painel') ?>" class="text-muted text-decoration-none">Painel</a>
				<span class="sep" aria-hidden="true">/</span>
				<a href="<?= base_url('admin/cursos') ?>" class="text-muted text-decoration-none">Cursos</a>
				<span class="sep" aria-hidden="true">/</span>
				<a href="<?= base_url('admin/cursos/' . $course->get_id()) ?>" class="text-muted text-decoration-none"><?= html_escape($course->get_title()) ?></a>
				<span class="sep" aria-hidden="true">/</span>
				<span class="fw-medium text-body" aria-current="page">Conteúdo Curricular</span>
			</nav>

			<div class="d-flex align-items-center gap-2 mb-1">
				<h1 class="page-header-title mb-0"><?= html_escape($course->get_title()) ?></h1>
				<span class="edu-badge edu-badge-primary">Estrutura Curricular</span>
			</div>
			<p class="page-header-subtitle mb-0">Gerencie os módulos pedagógicos, aulas sequenciais e blocos de aprendizagem deste curso.</p>
		</div>

		<div class="page-header-actions d-flex align-items-center gap-2">
			<a href="<?= base_url('admin/cursos/' . $course->get_id() . '/editar') ?>" class="edu-btn edu-btn-outline">
				<i class="bi bi-pencil me-1" aria-hidden="true"></i> Editar Curso
			</a>
			<button type="button" class="edu-btn edu-btn-primary" data-bs-toggle="modal" data-bs-target="#modal-module" onclick="window.CurriculumApp && window.CurriculumApp.openNewModuleModal()">
				<i class="bi bi-plus-lg me-1" aria-hidden="true"></i> Novo Módulo
			</button>
		</div>
	</header>

	<!-- Context Tabs Navigation -->
	<div class="edu-nav-tabs mt-2 mb-4 animate-fade-up">
		<a href="<?= base_url('admin/cursos/' . $course->get_id()) ?>" class="edu-nav-tab">
			<i class="bi bi-info-circle me-1" aria-hidden="true"></i> Visão Geral
		</a>
		<a href="<?= base_url('admin/cursos/' . $course->get_id() . '/conteudo') ?>" class="edu-nav-tab active">
			<i class="bi bi-layers me-1" aria-hidden="true"></i> Conteúdo Curricular (Hierarquia)
		</a>
		<a href="<?= base_url('admin/avaliacoes') ?>" class="edu-nav-tab">
			<i class="bi bi-patch-check me-1" aria-hidden="true"></i> Avaliações
		</a>
	</div>

	<!-- Curriculum Summary Metrics Bar -->
	<div class="row g-3 mb-4 animate-fade-up animate-delay-1">
		<div class="col-md-4">
			<div class="edu-card p-3 d-flex align-items-center gap-3">
				<div class="edu-stat-icon bg-primary-subtle text-primary p-3 rounded-3">
					<i class="bi bi-folder2 fs-4"></i>
				</div>
				<div>
					<span class="text-muted small d-block">Módulos Cadastrados</span>
					<strong class="fs-5 text-body"><?= (int) $metrics['total_modules'] ?></strong>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="edu-card p-3 d-flex align-items-center gap-3">
				<div class="edu-stat-icon bg-success-subtle text-success p-3 rounded-3">
					<i class="bi bi-play-circle fs-4"></i>
				</div>
				<div>
					<span class="text-muted small d-block">Aulas Estruturadas</span>
					<strong class="fs-5 text-body"><?= (int) $metrics['total_lessons'] ?></strong>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="edu-card p-3 d-flex align-items-center gap-3">
				<div class="edu-stat-icon bg-info-subtle text-info p-3 rounded-3">
					<i class="bi bi-clock-history fs-4"></i>
				</div>
				<div>
					<span class="text-muted small d-block">Duração Total Estimada</span>
					<strong class="fs-5 text-body"><?= html_escape($formatted_duration) ?></strong>
				</div>
			</div>
		</div>
	</div>

	<!-- Curriculum Tree Container -->
	<div id="curriculum-tree-container" class="animate-fade-up animate-delay-1" data-course-id="<?= $course->get_id() ?>">
		<?php if (empty($modules)): ?>
			<div class="edu-card p-5 text-center">
				<div class="py-4">
					<i class="bi bi-layers text-muted fs-1 mb-3 d-block"></i>
					<h2 class="h5 fw-semibold text-body">Nenhum módulo criado ainda</h2>
					<p class="text-muted small mx-auto mb-4" style="max-width: 480px;">
						Estruture a ementa deste curso criando módulos temáticos para agrupar as aulas em vídeo, textos teóricos e exercícios práticos.
					</p>
					<button type="button" class="edu-btn edu-btn-primary" data-bs-toggle="modal" data-bs-target="#modal-module" onclick="window.CurriculumApp && window.CurriculumApp.openNewModuleModal()">
						<i class="bi bi-plus-lg me-1" aria-hidden="true"></i> Criar Primeiro Módulo
					</button>
				</div>
			</div>
		<?php else: ?>
			<div class="d-flex flex-column gap-3" id="modules-sortable-list">
				<?php foreach ($modules as $module_index => $module): ?>
					<?php
					$lessons = $module->get_lessons();
					$module_duration_sec = $module->get_total_duration_in_seconds();
					$mod_hours = (int) floor($module_duration_sec / 3600);
					$mod_mins = (int) floor(($module_duration_sec % 3600) / 60);
					$mod_duration_str = $mod_hours > 0 ? ($mod_mins > 0 ? "{$mod_hours}h {$mod_mins}min" : "{$mod_hours}h") : "{$mod_mins}min";
					?>
					<div class="edu-card edu-module-card border" data-module-id="<?= $module->get_id() ?>">
						<!-- Module Header -->
						<div class="p-3 d-flex align-items-center justify-content-between border-bottom bg-body-tertiary rounded-top">
							<div class="d-flex align-items-center gap-3 min-w-0">
								<span class="edu-module-handle edu-reorder-handle text-muted cursor-grab" title="Arraste para reordenar módulo" aria-label="Reordenar módulo">
									<i class="bi bi-grip-vertical fs-5"></i>
								</span>
								<span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold">
									MÓDULO <?= $module_index + 1 ?>
								</span>
								<div class="min-w-0">
									<h2 class="h6 mb-0 text-body fw-bold text-truncate"><?= html_escape($module->get_title()) ?></h2>
									<?php if ($module->get_description()): ?>
										<small class="text-muted d-block text-truncate"><?= html_escape($module->get_description()) ?></small>
									<?php endif; ?>
								</div>
							</div>

							<div class="d-flex align-items-center gap-3">
								<span class="text-muted small">
									<i class="bi bi-clock me-1"></i><?= html_escape($mod_duration_str) ?> · <?= count($lessons) ?> <?= count($lessons) === 1 ? 'aula' : 'aulas' ?>
								</span>
								<?php if ($module->is_published()): ?>
									<span class="edu-badge edu-badge-success" style="font-size: 0.75rem;">Publicado</span>
								<?php else: ?>
									<span class="edu-badge edu-badge-warning" style="font-size: 0.75rem;">Rascunho</span>
								<?php endif; ?>

								<div class="d-flex align-items-center gap-1">
									<button type="button" class="edu-btn edu-btn-outline edu-btn-sm" title="Adicionar Aula" aria-label="Adicionar aula ao módulo" onclick="window.CurriculumApp && window.CurriculumApp.openNewLessonModal(<?= $module->get_id() ?>)">
										<i class="bi bi-plus-lg me-1" aria-hidden="true"></i> Nova Aula
									</button>
									<button type="button" class="edu-btn edu-btn-ghost edu-btn-sm" title="Editar Módulo" aria-label="Editar módulo" onclick="window.CurriculumApp && window.CurriculumApp.openEditModuleModal(<?= $module->get_id() ?>, '<?= html_escape(addslashes($module->get_title())) ?>', '<?= html_escape(addslashes((string)$module->get_description())) ?>', '<?= $module->get_status() ?>')">
										<i class="bi bi-pencil" aria-hidden="true"></i>
									</button>
									<button type="button" class="edu-btn edu-btn-ghost text-danger edu-btn-sm" title="Excluir Módulo" aria-label="Excluir módulo" onclick="window.CurriculumApp && window.CurriculumApp.confirmDeleteModule(<?= $module->get_id() ?>, '<?= html_escape(addslashes($module->get_title())) ?>')">
										<i class="bi bi-trash" aria-hidden="true"></i>
									</button>
								</div>
							</div>
						</div>

						<!-- Module Lessons Container -->
						<div class="p-3 bg-body">
							<?php if (empty($lessons)): ?>
								<div class="py-3 px-4 text-center border rounded-3 bg-body-tertiary">
									<p class="text-muted small mb-2">Nenhuma aula adicionada neste módulo.</p>
									<button type="button" class="edu-btn edu-btn-outline edu-btn-sm" onclick="window.CurriculumApp && window.CurriculumApp.openNewLessonModal(<?= $module->get_id() ?>)">
										<i class="bi bi-plus-lg me-1" aria-hidden="true"></i> Adicionar Primeira Aula
									</button>
								</div>
							<?php else: ?>
								<div class="d-flex flex-column gap-2 lessons-sortable-list" data-module-id="<?= $module->get_id() ?>">
									<?php foreach ($lessons as $lesson_index => $lesson): ?>
										<?php
										$lesson_blocks = $lesson->get_blocks();
										?>
										<div class="p-2 border rounded-2 d-flex align-items-center justify-content-between bg-body-tertiary edu-lesson-item" data-lesson-id="<?= $lesson->get_id() ?>">
											<div class="d-flex align-items-center gap-3 min-w-0">
												<span class="edu-lesson-handle edu-reorder-handle text-muted cursor-grab" title="Arraste para reordenar aula" aria-label="Reordenar aula">
													<i class="bi bi-grip-vertical"></i>
												</span>
												<div class="edu-lesson-icon text-primary" aria-hidden="true">
													<i class="bi bi-play-btn-fill fs-5"></i>
												</div>
												<div class="min-w-0">
													<div class="d-flex align-items-center gap-2">
														<span class="badge bg-body-secondary text-body-secondary border edu-lesson-index" style="font-size: 0.72rem; min-width: 22px; text-align: center;"><?= $lesson_index + 1 ?></span>
														<span class="text-body fw-medium text-truncate edu-lesson-title-text"><?= html_escape($lesson->get_title()) ?></span>
													</div>
													<span class="text-muted small">
														<?= count($lesson_blocks) ?> <?= count($lesson_blocks) === 1 ? 'bloco de conteúdo' : 'blocos de conteúdo' ?>
													</span>
												</div>
											</div>

											<div class="d-flex align-items-center gap-2">
												<?php if ($lesson->is_published()): ?>
													<span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.7rem;">Publicada</span>
												<?php else: ?>
													<span class="badge bg-warning-subtle text-warning border border-warning-subtle" style="font-size: 0.7rem;">Rascunho</span>
												<?php endif; ?>

												<div class="d-flex align-items-center gap-1">
													<a href="<?= base_url('admin/cursos/' . $course->get_id() . '/aulas/editor/' . $lesson->get_id()) ?>" class="edu-btn edu-btn-outline edu-btn-sm" style="font-size: 0.75rem;" title="Abrir Editor de Conteúdo da Aula" aria-label="Editor da aula <?= html_escape($lesson->get_title()) ?>">
														<i class="bi bi-pencil-square me-1" aria-hidden="true"></i> Editor de Conteúdo
													</a>
													<button type="button" class="edu-btn edu-btn-ghost text-danger edu-btn-sm" title="Remover Aula" aria-label="Remover aula" onclick="window.CurriculumApp && window.CurriculumApp.confirmDeleteLesson(<?= $lesson->get_id() ?>, '<?= html_escape(addslashes($lesson->get_title())) ?>')">
														<i class="bi bi-trash" aria-hidden="true"></i>
													</button>
												</div>
											</div>
										</div>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</div>

<!-- Modal Module (Create / Edit) -->
<div class="modal fade" id="modal-module" tabindex="-1" aria-labelledby="modal-module-title" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<form id="form-module" onsubmit="event.preventDefault(); window.CurriculumApp && window.CurriculumApp.submitModuleForm();">
				<input type="hidden" name="course_id" value="<?= $course->get_id() ?>">
				<input type="hidden" name="module_id" id="modal-module-id" value="">
				<div class="modal-header">
					<h2 class="modal-title fs-5" id="modal-module-title">Novo Módulo</h2>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
				</div>
				<div class="modal-body">
					<div class="mb-3">
						<label for="module-input-title" class="form-label fw-medium">Título do Módulo <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="module-input-title" name="title" required placeholder="Ex: Módulo 1: Fundamentos de Arquitetura">
					</div>
					<div class="mb-3">
						<label for="module-input-description" class="form-label fw-medium">Descrição Pedagógica / Sinopse</label>
						<textarea class="form-control" id="module-input-description" name="description" rows="3" placeholder="Resumo do que será abordado neste módulo..."></textarea>
					</div>
					<div class="mb-3">
						<label for="module-input-status" class="form-label fw-medium">Status de Publicação</label>
						<select class="form-select" id="module-input-status" name="status">
							<option value="draft">Rascunho (visível apenas para instrutores)</option>
							<option value="published">Publicado (liberado aos alunos)</option>
						</select>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="edu-btn edu-btn-outline" data-bs-dismiss="modal">Cancelar</button>
					<button type="submit" class="edu-btn edu-btn-primary" id="btn-save-module">Salvar Módulo</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Modal Quick Lesson (Create / Edit) -->
<div class="modal fade" id="modal-lesson" tabindex="-1" aria-labelledby="modal-lesson-title" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<form id="form-lesson" onsubmit="event.preventDefault(); window.CurriculumApp && window.CurriculumApp.submitLessonForm();">
				<input type="hidden" name="module_id" id="modal-lesson-module-id" value="">
				<input type="hidden" name="lesson_id" id="modal-lesson-id" value="">
				<div class="modal-header">
					<h2 class="modal-title fs-5" id="modal-lesson-title">Nova Aula</h2>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
				</div>
				<div class="modal-body">
					<div class="mb-3">
						<label for="lesson-input-title" class="form-label fw-medium">Título da Aula <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="lesson-input-title" name="title" required placeholder="Ex: Aula 1: Visão Geral e Setup">
					</div>
					<div class="mb-3">
						<label for="lesson-input-description" class="form-label fw-medium">Sinopse da Aula</label>
						<textarea class="form-control" id="lesson-input-description" name="description" rows="2" placeholder="Resumo do conteúdo desta aula..."></textarea>
					</div>
					<div class="mb-3">
						<label for="lesson-input-status" class="form-label fw-medium">Status de Publicação</label>
						<select class="form-select" id="lesson-input-status" name="status">
							<option value="draft">Rascunho</option>
							<option value="published">Publicada</option>
						</select>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="edu-btn edu-btn-outline" data-bs-dismiss="modal">Cancelar</button>
					<button type="submit" class="edu-btn edu-btn-primary" id="btn-save-lesson">Salvar Aula</button>
				</div>
			</form>
		</div>
	</div>
</div>
