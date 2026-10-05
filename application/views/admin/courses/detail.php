<?php
/**
 * @var \app\domain\course\Course $course
 */
?>
<div class="container-fluid px-0">
	<!-- Context Header with Course Details and Actions -->
	<header class="edu-context-header animate-fade-up">
		<nav class="header-breadcrumb d-flex align-items-center gap-2 mb-2" aria-label="Localização">
			<a href="<?= base_url('admin/painel') ?>" class="text-muted text-decoration-none">Painel</a>
			<i class="bi bi-chevron-right text-muted" style="font-size: 0.7rem;" aria-hidden="true"></i>
			<a href="<?= base_url('admin/cursos') ?>" class="text-muted text-decoration-none">Cursos</a>
			<i class="bi bi-chevron-right text-muted" style="font-size: 0.7rem;" aria-hidden="true"></i>
			<span class="fw-medium text-body"><?= html_escape($course->get_title()) ?></span>
		</nav>

		<div class="edu-context-header-top">
			<div>
				<div class="d-flex align-items-center gap-2 mb-1">
					<h1 class="edu-page-header-title mb-0"><?= html_escape($course->get_title()) ?></h1>
					<?php if ($course->is_active()): ?>
						<span class="edu-badge edu-badge-success">● Ativo</span>
					<?php elseif ($course->is_archived()): ?>
						<span class="edu-badge edu-badge-warning">● Arquivado</span>
					<?php else: ?>
						<span class="edu-badge edu-badge-neutral">● Rascunho</span>
					<?php endif; ?>
				</div>
				<p class="edu-page-header-desc mb-0">
					<code>/<?= html_escape((string) $course->get_slug()) ?></code> — <?= html_escape($course->get_short_description() ?? 'Sem resumo cadastrado.') ?>
				</p>
			</div>

			<div class="d-flex align-items-center gap-2">
				<a href="<?= base_url('admin/cursos/' . $course->get_id() . '/editar') ?>" class="edu-btn edu-btn-primary">
					<i class="bi bi-pencil" aria-hidden="true"></i>
					<span>Editar Curso</span>
				</a>
			</div>
		</div>

		<!-- Course Summary KPIs -->
		<div class="row g-3 mt-1">
			<div class="col-6 col-md-3">
				<div class="p-3 rounded-3 bg-subtle border">
					<span class="text-muted small d-block">Categoria</span>
					<span class="fs-6 fw-bold text-heading text-truncate d-block"><?= html_escape($course->get_category_name() ?? 'Não associada') ?></span>
				</div>
			</div>
			<div class="col-6 col-md-3">
				<div class="p-3 rounded-3 bg-subtle border">
					<span class="text-muted small d-block">Período de Acesso</span>
					<span class="fs-6 fw-bold text-heading">
						<?php if ($course->get_access_period()->is_lifetime()): ?>
							<i class="bi bi-infinity text-info me-1"></i> Vitalício
						<?php else: ?>
							<i class="bi bi-calendar-event text-primary me-1"></i> <?= $course->get_access_period()->get_days() ?> dias
						<?php endif; ?>
					</span>
				</div>
			</div>
			<div class="col-6 col-md-3">
				<div class="p-3 rounded-3 bg-subtle border">
					<span class="text-muted small d-block">Carga Horária</span>
					<span class="fs-6 fw-bold text-heading"><?= $course->get_workload_in_hours() !== null ? $course->get_workload_in_hours() . ' horas' : 'Não definida' ?></span>
				</div>
			</div>
			<div class="col-6 col-md-3">
				<div class="p-3 rounded-3 bg-subtle border">
					<span class="text-muted small d-block">Certificação</span>
					<span class="fs-6 fw-bold text-heading">
						<?= $course->is_certificate_enabled() ? '<span class="text-success"><i class="bi bi-check-circle-fill me-1"></i>Habilitada</span>' : '<span class="text-muted"><i class="bi bi-dash-circle me-1"></i>Desabilitada</span>' ?>
					</span>
				</div>
			</div>
		</div>

		<!-- Context Tabs Navigation -->
		<div class="edu-nav-tabs mt-4">
			<a href="#tab-overview" class="edu-nav-tab active" data-bs-toggle="tab">
				<i class="bi bi-info-circle" aria-hidden="true"></i> Visão Geral & Ementa
			</a>
			<a href="#tab-curriculum" class="edu-nav-tab" data-bs-toggle="tab">
				<i class="bi bi-collection-play" aria-hidden="true"></i> Estrutura Curricular
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
							<h3 class="edu-card-title">Ementa e Descrição Pedagógica</h3>
						</div>
						<div class="edu-card-body">
							<div class="text-body leading-relaxed mb-4">
								<?= nl2br(html_escape($course->get_description() ?? 'Nenhuma ementa detalhada informada.')) ?>
							</div>

							<div class="row g-3 pt-2 border-top">
								<div class="col-sm-6">
									<span class="text-muted small d-block">O que o aluno vai aprender</span>
									<span class="text-body"><?= nl2br(html_escape($course->get_objectives() ?? 'Não especificado.')) ?></span>
								</div>
								<div class="col-sm-6">
									<span class="text-muted small d-block">Público-Alvo</span>
									<span class="text-body"><?= nl2br(html_escape($course->get_target_audience() ?? 'Não especificado.')) ?></span>
								</div>
								<div class="col-sm-6">
									<span class="text-muted small d-block">Pré-requisitos</span>
									<span class="text-body"><?= nl2br(html_escape($course->get_requirements() ?? 'Nenhum pré-requisito.')) ?></span>
								</div>
								<div class="col-sm-6">
									<span class="text-muted small d-block">Cadastrado em</span>
									<span class="text-body"><?= $course->get_created_at() ? $course->get_created_at()->format('d/m/Y H:i') : '—' ?></span>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Right Sidebar Column -->
				<div class="col-lg-4">
					<?php if ($course->get_image()): ?>
						<div class="edu-card mb-4 p-0 overflow-hidden">
							<img src="<?= html_escape($course->get_image()) ?>" alt="<?= html_escape($course->get_title()) ?>" class="img-fluid w-100" style="max-height: 220px; object-fit: cover;">
						</div>
					<?php endif; ?>

					<div class="edu-card mb-4">
						<div class="edu-card-header">
							<h3 class="edu-card-title">Ações Rápidas</h3>
						</div>
						<div class="edu-card-body d-flex flex-column gap-2">
							<a href="<?= base_url('admin/cursos/' . $course->get_id() . '/editar') ?>" class="edu-btn edu-btn-outline w-100 justify-content-start">
								<i class="bi bi-pencil me-2"></i> Editar Informações
							</a>
							<a href="<?= base_url('admin/cursos/' . $course->get_id() . '/conteudo') ?>" class="edu-btn edu-btn-outline w-100 justify-content-start">
								<i class="bi bi-layers me-2"></i> Gerenciador de Conteúdo
							</a>
							<a href="<?= base_url('cursos/detalhes/' . $course->get_slug()) ?>" target="_blank" class="edu-btn edu-btn-ghost w-100 justify-content-start">
								<i class="bi bi-box-arrow-up-right me-2"></i> Visualizar na Vitrine
							</a>
						</div>
					</div>

					<div class="edu-card">
						<div class="edu-card-header">
							<h3 class="edu-card-title">Regras Comerciais</h3>
						</div>
						<div class="edu-card-body">
							<div class="d-flex align-items-center gap-2 mb-2">
								<i class="bi bi-cart-check fs-5 text-primary"></i>
								<span class="fw-semibold text-body">Venda Contínua Avulsa</span>
							</div>
							<p class="text-muted small mb-0">
								O curso fica disponível para compra imediata na vitrine. Ao se matricular, o aluno recebe acesso imediato com validade de 
								<?= $course->get_access_period()->is_lifetime() ? '<strong>acesso vitalício</strong>' : '<strong>' . $course->get_access_period()->get_days() . ' dias</strong>' ?>.
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
					Visualize a árvore hierárquica contendo módulos, seções, aulas e materiais complementares associados.
				</p>
				<a href="<?= base_url('admin/cursos/' . $course->get_id() . '/conteudo') ?>" class="edu-btn edu-btn-primary">
					Abrir Gerenciador de Conteúdo
				</a>
			</div>
		</div>
	</div>
</div>
