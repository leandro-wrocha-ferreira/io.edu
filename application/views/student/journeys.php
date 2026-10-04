<!-- Page Header -->
<div class="page-header animate-fade-up">
	<div class="page-header-info">
		<p class="page-header-breadcrumb">
			<i class="bi bi-mortarboard" aria-hidden="true"></i>
			<span class="sep">›</span>
			<a href="<?= base_url('aluno/painel') ?>">Área do Aluno</a>
			<span class="sep">›</span>
			<span>Trilhas & Jornadas</span>
		</p>
		<h1 class="page-header-title">Trilhas de Formação & Jornadas Contínuas</h1>
		<p class="page-header-subtitle">Acompanhe seu itinerário de especialização, metas de qualificação e prazos regulatórios.</p>
	</div>
	<div class="page-header-actions">
		<a href="<?= base_url('aluno/painel') ?>" class="btn-theme-primary-outline">
			<i class="bi bi-arrow-left" aria-hidden="true"></i> Voltar ao Painel
		</a>
	</div>
</div>

<!-- Overview Stats -->
<div class="row g-4 mb-4 animate-fade-up animate-delay-1">
	<div class="col-md-4">
		<div class="edu-card p-3 d-flex align-items-center gap-3">
			<div class="stat-icon-wrap stat-icon-primary">
				<i class="bi bi-compass" aria-hidden="true"></i>
			</div>
			<div>
				<span class="stat-label">Trilhas em Andamento</span>
				<div class="stat-value">2</div>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="edu-card p-3 d-flex align-items-center gap-3">
			<div class="stat-icon-wrap stat-icon-warning">
				<i class="bi bi-hourglass-split" aria-hidden="true"></i>
			</div>
			<div>
				<span class="stat-label">Prazos Próximos</span>
				<div class="stat-value">1</div>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="edu-card p-3 d-flex align-items-center gap-3">
			<div class="stat-icon-wrap stat-icon-success">
				<i class="bi bi-award-fill" aria-hidden="true"></i>
			</div>
			<div>
				<span class="stat-label">Formações Concluídas</span>
				<div class="stat-value">1</div>
			</div>
		</div>
	</div>
</div>

<!-- Active & Required Journeys -->
<div class="row g-4 mb-5">
	<!-- Jornada 1: Obrigatória com Deadline -->
	<div class="col-lg-6">
		<div class="edu-journey-card animate-fade-up animate-delay-2">
			<div class="edu-journey-top">
				<div>
					<span class="edu-badge edu-badge-deadline mb-2">
						<i class="bi bi-hourglass-split" aria-hidden="true"></i> Faltam 45 dias
					</span>
					<h2 class="edu-journey-title">Programa de Qualificação Profissional Contínua</h2>
				</div>
				<span class="edu-badge edu-badge-mandatory">Obrigatório</span>
			</div>
			<p class="edu-journey-desc">
				Itinerário formativo estruturado para capacitação continuada em conformidade com as diretrizes do comitê de regulação. Garante a manutenção e validação da certificação anual.
			</p>

			<!-- Cursos Pertencentes -->
			<div class="mb-3">
				<h3 class="fs-7 fw-bold text-edu-heading text-uppercase letter-spacing-1 mb-2">Cursos Integrantes</h3>
				<ul class="list-group list-group-flush rounded-3 border-edu">
					<li class="list-group-item d-flex align-items-center justify-content-between py-2 px-3 bg-edu-surface">
						<span class="fs-7"><i class="bi bi-check-circle-fill text-edu-success me-2" aria-hidden="true"></i> Ética e Compliance Organizacional</span>
						<span class="edu-badge edu-badge-success">Concluído</span>
					</li>
					<li class="list-group-item d-flex align-items-center justify-content-between py-2 px-3 bg-edu-surface">
						<span class="fs-7"><i class="bi bi-check-circle-fill text-edu-success me-2" aria-hidden="true"></i> Proteção de Dados e Governança Digital</span>
						<span class="edu-badge edu-badge-success">Concluído</span>
					</li>
					<li class="list-group-item d-flex align-items-center justify-content-between py-2 px-3 bg-edu-surface">
						<span class="fs-7"><i class="bi bi-check-circle-fill text-edu-success me-2" aria-hidden="true"></i> Prevenção a Fraudes e Segurança da Informação</span>
						<span class="edu-badge edu-badge-success">Concluído</span>
					</li>
					<li class="list-group-item d-flex align-items-center justify-content-between py-2 px-3 bg-edu-surface">
						<span class="fs-7"><i class="bi bi-play-circle-fill text-edu-primary me-2" aria-hidden="true"></i> Gestão de Riscos e Auditoria</span>
						<span class="edu-badge edu-badge-primary">Em Andamento</span>
					</li>
					<li class="list-group-item d-flex align-items-center justify-content-between py-2 px-3 bg-edu-surface opacity-75">
						<span class="fs-7"><i class="bi bi-lock-fill text-edu-muted me-2" aria-hidden="true"></i> Resolução de Conflitos e Mediação</span>
						<span class="edu-badge edu-badge-neutral">Pendente</span>
					</li>
				</ul>
			</div>

			<div class="edu-journey-milestones">
				<div class="edu-journey-milestone-header">
					<span>Progresso Global da Trilha</span>
					<span class="edu-journey-progress-text">3 de 5 cursos concluídos (60%)</span>
				</div>
				<div class="edu-progress-track" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100">
					<div class="edu-progress-bar" style="width: 60%;"></div>
				</div>
			</div>

			<div class="edu-journey-footer">
				<div class="fs-7 text-edu-muted">
					<i class="bi bi-calendar-event me-1" aria-hidden="true"></i> Prazo final: <strong>15/12/2026</strong>
				</div>
				<a href="<?= base_url('aluno/aula/1') ?>" class="edu-btn edu-btn-primary edu-btn-sm">
					Continuar Curso Atual
				</a>
			</div>
		</div>
	</div>

	<!-- Jornada 2: Especialização Livre -->
	<div class="col-lg-6">
		<div class="edu-journey-card animate-fade-up animate-delay-3">
			<div class="edu-journey-top">
				<div>
					<span class="edu-badge edu-badge-accent mb-2">
						<i class="bi bi-mortarboard-fill" aria-hidden="true"></i> Formação Especialista
					</span>
					<h2 class="edu-journey-title">Especialização em Gestão e Liderança Estratégica</h2>
				</div>
				<span class="edu-badge edu-badge-primary">Em Curso</span>
			</div>
			<p class="edu-journey-desc">
				Desenvolvimento aprofundado de visão sistêmica, metodologias ágeis de gestão de times, liderança humanizada e estratégia de inovação sustentável.
			</p>

			<!-- Cursos Pertencentes -->
			<div class="mb-3">
				<h3 class="fs-7 fw-bold text-edu-heading text-uppercase letter-spacing-1 mb-2">Cursos Integrantes</h3>
				<ul class="list-group list-group-flush rounded-3 border-edu">
					<li class="list-group-item d-flex align-items-center justify-content-between py-2 px-3 bg-edu-surface">
						<span class="fs-7"><i class="bi bi-check-circle-fill text-edu-success me-2" aria-hidden="true"></i> Liderança Transformacional e Tomada de Decisão</span>
						<span class="edu-badge edu-badge-success">Concluído</span>
					</li>
					<li class="list-group-item d-flex align-items-center justify-content-between py-2 px-3 bg-edu-surface">
						<span class="fs-7"><i class="bi bi-play-circle-fill text-edu-primary me-2" aria-hidden="true"></i> Gestão Estratégica com OKRs e KPIs</span>
						<span class="edu-badge edu-badge-primary">Em Andamento</span>
					</li>
					<li class="list-group-item d-flex align-items-center justify-content-between py-2 px-3 bg-edu-surface opacity-75">
						<span class="fs-7"><i class="bi bi-lock-fill text-edu-muted me-2" aria-hidden="true"></i> Comunicação Assertiva e Negociação</span>
						<span class="edu-badge edu-badge-neutral">Pendente</span>
					</li>
					<li class="list-group-item d-flex align-items-center justify-content-between py-2 px-3 bg-edu-surface opacity-75">
						<span class="fs-7"><i class="bi bi-lock-fill text-edu-muted me-2" aria-hidden="true"></i> Cultura Ágil e Transformação Organizacional</span>
						<span class="edu-badge edu-badge-neutral">Pendente</span>
					</li>
				</ul>
			</div>

			<div class="edu-journey-milestones">
				<div class="edu-journey-milestone-header">
					<span>Progresso Global da Trilha</span>
					<span class="edu-journey-progress-text">1 de 4 cursos concluídos (25%)</span>
				</div>
				<div class="edu-progress-track" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
					<div class="edu-progress-bar" style="width: 25%;"></div>
				</div>
			</div>

			<div class="edu-journey-footer">
				<div class="fs-7 text-edu-muted">
					<i class="bi bi-award me-1" aria-hidden="true"></i> Carga: <strong>160 horas</strong>
				</div>
				<a href="<?= base_url('aluno/aula/1') ?>" class="edu-btn edu-btn-secondary edu-btn-sm">
					Retomar Formação
				</a>
			</div>
		</div>
	</div>
</div>
