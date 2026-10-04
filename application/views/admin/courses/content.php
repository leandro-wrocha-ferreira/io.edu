<div class="container-fluid px-0">
	<!-- Context Header -->
	<header class="edu-context-header animate-fade-up">
		<nav class="header-breadcrumb d-flex align-items-center gap-2 mb-2" aria-label="Localização">
			<a href="<?= base_url('admin/painel') ?>" class="text-muted text-decoration-none">Painel</a>
			<i class="bi bi-chevron-right text-muted" style="font-size: 0.7rem;" aria-hidden="true"></i>
			<a href="<?= base_url('admin/cursos') ?>" class="text-muted text-decoration-none">Cursos</a>
			<i class="bi bi-chevron-right text-muted" style="font-size: 0.7rem;" aria-hidden="true"></i>
			<a href="<?= base_url('admin/cursos/' . $course['id']) ?>" class="text-muted text-decoration-none"><?= html_escape($course['title']) ?></a>
			<i class="bi bi-chevron-right text-muted" style="font-size: 0.7rem;" aria-hidden="true"></i>
			<span class="fw-medium text-body">Conteúdo Curricular</span>
		</nav>

		<div class="edu-context-header-top">
			<div>
				<div class="d-flex align-items-center gap-2 mb-1">
					<h1 class="edu-page-header-title mb-0"><?= html_escape($course['title']) ?></h1>
					<span class="edu-badge edu-badge-primary">● Estrutura Pedagógica</span>
				</div>
				<p class="edu-page-header-desc mb-0">Organize a hierarquia do curso estruturada em Módulos, Seções temáticas e Aulas.</p>
			</div>

			<div class="d-flex align-items-center gap-2">
				<button type="button" class="edu-btn edu-btn-outline" onclick="alert('Protótipo: Criação de nova seção temática simulada.');">
					<i class="bi bi-folder-plus" aria-hidden="true"></i>
					<span>Nova Seção</span>
				</button>
				<button type="button" class="edu-btn edu-btn-primary" onclick="alert('Protótipo: Criação de novo módulo curricular simulada.');">
					<i class="bi bi-plus-lg" aria-hidden="true"></i>
					<span>Novo Módulo</span>
				</button>
			</div>
		</div>

		<!-- Context Tabs Navigation -->
		<div class="edu-nav-tabs mt-3">
			<a href="<?= base_url('admin/cursos/' . $course['id']) ?>" class="edu-nav-tab">
				<i class="bi bi-info-circle" aria-hidden="true"></i> Visão Geral
			</a>
			<a href="<?= base_url('admin/cursos/' . $course['id'] . '/conteudo') ?>" class="edu-nav-tab active">
				<i class="bi bi-layers" aria-hidden="true"></i> Conteúdo Curricular (Hierarquia)
			</a>
			<a href="<?= base_url('admin/avaliacoes') ?>" class="edu-nav-tab">
				<i class="bi bi-patch-check" aria-hidden="true"></i> Avaliações
			</a>
		</div>
	</header>

	<!-- Curriculum Hierarchy Tree -->
	<div class="edu-curriculum-tree animate-fade-up animate-delay-1">
		<?php foreach ($curriculum as $module): ?>
			<div class="edu-module-card">
				<!-- Module Header -->
				<div class="edu-module-header">
					<div class="edu-module-title-area">
						<span class="edu-reorder-handle" title="Arraste para reordenar módulo (visual)" aria-label="Reordenar módulo">
							<i class="bi bi-grip-vertical fs-5"></i>
						</span>
						<span class="edu-module-index">MÓDULO <?= (int) $module['order'] ?></span>
						<h2 class="edu-module-title"><?= html_escape($module['title']) ?></h2>
					</div>
					<div class="d-flex align-items-center gap-3">
						<span class="edu-module-meta">
							<i class="bi bi-clock me-1"></i><?= html_escape($module['duration']) ?>
						</span>
						<div class="edu-action-group">
							<a href="<?= base_url('admin/cursos/' . $course['id'] . '/aulas/editor') ?>" class="edu-action-btn" title="Adicionar Aula" aria-label="Adicionar aula ao módulo">
								<i class="bi bi-plus-circle text-primary"></i>
							</a>
							<button type="button" class="edu-action-btn" title="Editar Módulo" aria-label="Editar módulo" onclick="alert('Protótipo: Edição de módulo simulada.');">
								<i class="bi bi-pencil"></i>
							</button>
						</div>
					</div>
				</div>

				<!-- Section Blocks -->
				<?php foreach ($module['sections'] as $section): ?>
					<div class="edu-section-block">
						<div class="edu-section-header">
							<div class="edu-section-title">
								<i class="bi bi-folder2-open text-primary fs-6"></i>
								<span><?= html_escape($section['title']) ?></span>
								<span class="badge bg-subtle text-muted border fw-normal ms-2" style="font-size: 0.7rem;">
									<?= count($section['lessons']) ?> aulas
								</span>
							</div>
							<div class="edu-action-group">
								<a href="<?= base_url('admin/cursos/' . $course['id'] . '/aulas/editor') ?>" class="edu-btn edu-btn-ghost edu-btn-sm" style="font-size: 0.75rem;">
									<i class="bi bi-plus-lg me-1"></i> Adicionar Aula
								</a>
							</div>
						</div>

						<!-- Lesson Rows -->
						<div class="edu-lesson-list">
							<?php foreach ($section['lessons'] as $lesson): ?>
								<div class="edu-lesson-item">
									<div class="edu-lesson-info">
										<span class="edu-reorder-handle" title="Arraste para reordenar aula (visual)" aria-label="Reordenar aula">
											<i class="bi bi-grip-vertical"></i>
										</span>
										<div class="edu-lesson-icon" aria-hidden="true">
											<i class="bi bi-play-btn-fill"></i>
										</div>
										<div class="min-w-0">
											<span class="edu-lesson-title d-block">
												<?= html_escape($lesson['title']) ?>
											</span>
											<span class="text-muted small">
												Duração: <?= html_escape($lesson['duration']) ?> · <?= (int) $lesson['materials_count'] ?> materiais anexos
											</span>
										</div>
									</div>

									<div class="edu-lesson-badges">
										<?php if ($lesson['required']): ?>
											<span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-medium" style="font-size: 0.7rem;">
												Obrigatória
											</span>
										<?php endif; ?>
										<span class="edu-badge edu-badge-success" style="font-size: 0.7rem;">
											Publicada
										</span>
										<div class="edu-action-group ms-2">
											<a href="<?= base_url('admin/cursos/' . $course['id'] . '/aulas/editor') ?>"
											   class="edu-action-btn edu-action-btn-edit"
											   title="Editar Aula no Editor de Conteúdo"
											   aria-label="Editar aula <?= html_escape($lesson['title']) ?>">
												<i class="bi bi-pencil" aria-hidden="true"></i>
											</a>
											<button type="button"
											        class="edu-action-btn edu-action-btn-delete"
											        title="Remover Aula"
											        aria-label="Remover aula"
											        onclick="alert('Protótipo: Exclusão de aula simulada.');">
												<i class="bi bi-trash" aria-hidden="true"></i>
											</button>
										</div>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endforeach; ?>
	</div>
</div>
