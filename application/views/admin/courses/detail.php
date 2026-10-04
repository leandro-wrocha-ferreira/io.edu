<div class="container-fluid px-0">
	<!-- Context Header with Course Details and Actions -->
	<header class="edu-context-header animate-fade-up">
		<nav class="header-breadcrumb d-flex align-items-center gap-2 mb-2" aria-label="Localização">
			<a href="<?= base_url('admin/painel') ?>" class="text-muted text-decoration-none">Painel</a>
			<i class="bi bi-chevron-right text-muted" style="font-size: 0.7rem;" aria-hidden="true"></i>
			<a href="<?= base_url('admin/cursos') ?>" class="text-muted text-decoration-none">Cursos</a>
			<i class="bi bi-chevron-right text-muted" style="font-size: 0.7rem;" aria-hidden="true"></i>
			<span class="fw-medium text-body"><?= html_escape($course['title']) ?></span>
		</nav>

		<div class="edu-context-header-top">
			<div>
				<div class="d-flex align-items-center gap-2 mb-1">
					<h1 class="edu-page-header-title mb-0"><?= html_escape($course['title']) ?></h1>
					<?php if ($course['status'] === 'published'): ?>
						<span class="edu-badge edu-badge-success">● Publicado</span>
					<?php elseif ($course['status'] === 'review'): ?>
						<span class="edu-badge edu-badge-warning">● Em Revisão</span>
					<?php else: ?>
						<span class="edu-badge edu-badge-neutral">● Rascunho</span>
					<?php endif; ?>
				</div>
				<p class="edu-page-header-desc mb-0"><?= html_escape($course['description']) ?></p>
			</div>

			<div class="d-flex align-items-center gap-2">
				<a href="<?= base_url('admin/cursos/' . $course['id'] . '/conteudo') ?>" class="edu-btn edu-btn-outline">
					<i class="bi bi-layers" aria-hidden="true"></i>
					<span>Conteúdo</span>
				</a>
				<a href="<?= base_url('admin/cursos/' . $course['id'] . '/editar') ?>" class="edu-btn edu-btn-primary">
					<i class="bi bi-pencil" aria-hidden="true"></i>
					<span>Editar Curso</span>
				</a>
			</div>
		</div>

		<!-- Course Summary KPIs -->
		<div class="row g-3 mt-1">
			<div class="col-6 col-md-4 col-lg-2">
				<div class="p-3 rounded-3 bg-subtle border">
					<span class="text-muted small d-block">Carga Horária</span>
					<span class="fs-5 fw-bold text-heading"><?= html_escape($course['workload']) ?></span>
				</div>
			</div>
			<div class="col-6 col-md-4 col-lg-2">
				<div class="p-3 rounded-3 bg-subtle border">
					<span class="text-muted small d-block">Alunos</span>
					<span class="fs-5 fw-bold text-heading"><?= (int) $course['students_count'] ?></span>
				</div>
			</div>
			<div class="col-6 col-md-4 col-lg-2">
				<div class="p-3 rounded-3 bg-subtle border">
					<span class="text-muted small d-block">Módulos</span>
					<span class="fs-5 fw-bold text-heading"><?= (int) $course['modules_count'] ?></span>
				</div>
			</div>
			<div class="col-6 col-md-4 col-lg-2">
				<div class="p-3 rounded-3 bg-subtle border">
					<span class="text-muted small d-block">Aulas</span>
					<span class="fs-5 fw-bold text-heading"><?= (int) $course['lessons_count'] ?></span>
				</div>
			</div>
			<div class="col-6 col-md-4 col-lg-2">
				<div class="p-3 rounded-3 bg-subtle border">
					<span class="text-muted small d-block">Avaliações</span>
					<span class="fs-5 fw-bold text-heading"><?= (int) $course['evaluations_count'] ?></span>
				</div>
			</div>
			<div class="col-6 col-md-4 col-lg-2">
				<div class="p-3 rounded-3 bg-subtle border">
					<span class="text-muted small d-block">Docente</span>
					<span class="fs-6 fw-semibold text-heading text-truncate d-block"><?= html_escape($course['instructor']) ?></span>
				</div>
			</div>
		</div>

		<!-- Context Tabs Navigation -->
		<div class="edu-nav-tabs mt-4">
			<a href="#tab-overview" class="edu-nav-tab active" data-bs-toggle="tab">
				<i class="bi bi-info-circle" aria-hidden="true"></i> Visão Geral
			</a>
			<a href="#tab-curriculum" class="edu-nav-tab" data-bs-toggle="tab">
				<i class="bi bi-collection-play" aria-hidden="true"></i> Módulos & Aulas (<?= (int) $course['lessons_count'] ?>)
			</a>
			<a href="#tab-students" class="edu-nav-tab" data-bs-toggle="tab">
				<i class="bi bi-people" aria-hidden="true"></i> Alunos Matriculados (<?= (int) $course['students_count'] ?>)
			</a>
		</div>
	</header>

	<!-- Tab Contents -->
	<div class="tab-content animate-fade-up animate-delay-1">
		<!-- Tab 1: Overview -->
		<div class="tab-pane fade show active" id="tab-overview">
			<div class="row g-4">
				<div class="col-lg-8">
					<div class="edu-card mb-4">
						<div class="edu-card-header">
							<h3 class="edu-card-title">Sobre a Disciplina</h3>
						</div>
						<div class="edu-card-body">
							<p class="text-body leading-relaxed mb-3">
								<?= html_escape($course['description']) ?>
							</p>
							<div class="row g-3 pt-2">
								<div class="col-sm-6">
									<span class="text-muted small d-block">Categoria Pedagógica</span>
									<span class="fw-medium text-body"><?= html_escape($course['category']) ?></span>
								</div>
								<div class="col-sm-6">
									<span class="text-muted small d-block">Docente Responsável</span>
									<span class="fw-medium text-body"><?= html_escape($course['instructor']) ?></span>
								</div>
								<div class="col-sm-6">
									<span class="text-muted small d-block">Última Atualização Curricular</span>
									<span class="fw-medium text-body"><?= html_escape($course['updated_at']) ?></span>
								</div>
								<div class="col-sm-6">
									<span class="text-muted small d-block">Certificação Emitida</span>
									<span class="fw-medium text-success"><i class="bi bi-award me-1"></i>Certificado Digital Incluso</span>
								</div>
							</div>
						</div>
					</div>

					<!-- Content Preview List -->
					<div class="edu-card">
						<div class="edu-card-header d-flex justify-content-between align-items-center">
							<h3 class="edu-card-title">Estrutura Curricular Resumida</h3>
							<a href="<?= base_url('admin/cursos/' . $course['id'] . '/conteudo') ?>" class="small text-primary text-decoration-none fw-semibold">
								Gerenciar Aulas <i class="bi bi-arrow-right"></i>
							</a>
						</div>
						<div class="edu-card-body p-0">
							<ul class="list-group list-group-flush">
								<?php foreach ($curriculum as $module): ?>
									<li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
										<div>
											<span class="badge bg-primary-subtle text-primary me-2">Módulo <?= (int) $module['order'] ?></span>
											<span class="fw-medium text-heading"><?= html_escape($module['title']) ?></span>
										</div>
										<div class="text-muted small">
											<i class="bi bi-play-circle me-1"></i><?= (int) $module['lessons_count'] ?> aulas · <?= html_escape($module['duration']) ?>
										</div>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					</div>
				</div>

				<!-- Right Sidebar Column -->
				<div class="col-lg-4">
					<div class="edu-card mb-4">
						<div class="edu-card-header">
							<h3 class="edu-card-title">Ações Rápidas</h3>
						</div>
						<div class="edu-card-body d-flex flex-column gap-2">
							<a href="<?= base_url('admin/cursos/' . $course['id'] . '/conteudo') ?>" class="edu-btn edu-btn-outline w-100 justify-content-start">
								<i class="bi bi-plus-circle me-2"></i> Adicionar Módulo ou Aula
							</a>
							<a href="<?= base_url('admin/avaliacoes/novo') ?>" class="edu-btn edu-btn-outline w-100 justify-content-start">
								<i class="bi bi-patch-question me-2"></i> Criar Nova Avaliação
							</a>
							<a href="<?= base_url('cursos/detalhes/' . $course['id']) ?>" target="_blank" class="edu-btn edu-btn-ghost w-100 justify-content-start">
								<i class="bi bi-box-arrow-up-right me-2"></i> Pré-visualizar Catálogo Público
							</a>
						</div>
					</div>

					<div class="edu-card">
						<div class="edu-card-header">
							<h3 class="edu-card-title">Status e Publicação</h3>
						</div>
						<div class="edu-card-body">
							<div class="d-flex align-items-center gap-2 mb-3">
								<div class="status-indicator active"></div>
								<span class="fw-medium text-body">Visível para Matrícula</span>
							</div>
							<p class="text-muted small mb-0">
								Este curso está ativo no catálogo institucional com matriculados ativos.
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Tab 2: Curriculum Link -->
		<div class="tab-pane fade" id="tab-curriculum">
			<div class="edu-card text-center py-5">
				<i class="bi bi-layers fs-1 text-primary mb-3"></i>
				<h4 class="h5 fw-bold text-heading">Gestão Completa de Conteúdo Curricular</h4>
				<p class="text-muted small mb-4" style="max-width: 480px; margin: 0 auto;">
					Visualize a árvore hierárquica completa contendo módulos, seções, aulas e materiais complementares associados.
				</p>
				<a href="<?= base_url('admin/cursos/' . $course['id'] . '/conteudo') ?>" class="edu-btn edu-btn-primary">
					Abrir Gerenciador de Conteúdo
				</a>
			</div>
		</div>

		<!-- Tab 3: Students -->
		<div class="tab-pane fade" id="tab-students">
			<div class="edu-card text-center py-5">
				<i class="bi bi-people fs-1 text-muted mb-3"></i>
				<h4 class="h5 fw-bold text-heading"><?= (int) $course['students_count'] ?> Alunos Matriculados</h4>
				<p class="text-muted small mb-4">
					Consulte e gerencie a lista nominal de participantes enturmados e histórico de aprendizagem.
				</p>
				<a href="<?= base_url('admin/turmas') ?>" class="edu-btn edu-btn-outline">
					Ver Turmas do Curso
				</a>
			</div>
		</div>
	</div>
</div>
