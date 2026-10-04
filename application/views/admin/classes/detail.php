<div class="container-fluid px-0">
	<!-- Context Header -->
	<header class="edu-context-header animate-fade-up">
		<nav class="header-breadcrumb d-flex align-items-center gap-2 mb-2" aria-label="Localização">
			<a href="<?= base_url('admin/painel') ?>" class="text-muted text-decoration-none">Painel</a>
			<i class="bi bi-chevron-right text-muted" style="font-size: 0.7rem;" aria-hidden="true"></i>
			<a href="<?= base_url('admin/turmas') ?>" class="text-muted text-decoration-none">Turmas</a>
			<i class="bi bi-chevron-right text-muted" style="font-size: 0.7rem;" aria-hidden="true"></i>
			<span class="fw-medium text-body"><?= html_escape($cohort['name']) ?></span>
		</nav>

		<div class="edu-context-header-top">
			<div>
				<div class="d-flex align-items-center gap-2 mb-1">
					<h1 class="edu-page-header-title mb-0"><?= html_escape($cohort['name']) ?></h1>
					<?php if ($cohort['status'] === 'active'): ?>
						<span class="edu-badge edu-badge-success">● Ativa</span>
					<?php elseif ($cohort['status'] === 'planned'): ?>
						<span class="edu-badge edu-badge-warning">● Prevista</span>
					<?php else: ?>
						<span class="edu-badge edu-badge-neutral">● Concluída</span>
					<?php endif; ?>
				</div>
				<p class="edu-page-header-desc mb-0">
					Curso: <strong class="text-body"><?= html_escape($cohort['course_title']) ?></strong> · Docente: <span class="text-body"><?= html_escape($cohort['instructor']) ?></span>
				</p>
			</div>

			<div class="d-flex align-items-center gap-2">
				<button type="button" class="edu-btn edu-btn-outline" onclick="alert('Protótipo: Matrícula de aluno simulada.');">
					<i class="bi bi-person-plus" aria-hidden="true"></i>
					<span>Enturmar Aluno</span>
				</button>
				<button type="button" class="edu-btn edu-btn-primary" onclick="alert('Protótipo: Diário de classe simulado.');">
					<i class="bi bi-calendar-check" aria-hidden="true"></i>
					<span>Lançar Frequência</span>
				</button>
			</div>
		</div>

		<!-- Cohort Summary KPIs -->
		<div class="row g-3 mt-1">
			<div class="col-6 col-md-3">
				<div class="p-3 rounded-3 bg-subtle border">
					<span class="text-muted small d-block">Alunos Matriculados</span>
					<span class="fs-5 fw-bold text-heading"><?= (int) $cohort['students_count'] ?></span>
				</div>
			</div>
			<div class="col-6 col-md-3">
				<div class="p-3 rounded-3 bg-subtle border">
					<span class="text-muted small d-block">Período de Aulas</span>
					<span class="fs-6 fw-bold text-heading font-monospace"><?= html_escape($cohort['period']) ?></span>
				</div>
			</div>
			<div class="col-6 col-md-3">
				<div class="p-3 rounded-3 bg-subtle border">
					<span class="text-muted small d-block">Progresso Médio</span>
					<span class="fs-5 fw-bold text-primary"><?= (int) $cohort['progress_avg'] ?>%</span>
				</div>
			</div>
			<div class="col-6 col-md-3">
				<div class="p-3 rounded-3 bg-subtle border">
					<span class="text-muted small d-block">Frequência da Turma</span>
					<span class="fs-5 fw-bold text-success"><?= (int) $cohort['attendance_rate'] ?>%</span>
				</div>
			</div>
		</div>

		<!-- Secondary Navigation Tabs -->
		<div class="edu-nav-tabs mt-4">
			<a href="#tab-cohort-students" class="edu-nav-tab active" data-bs-toggle="tab">
				<i class="bi bi-people" aria-hidden="true"></i> Alunos Matriculados (<?= (int) $cohort['students_count'] ?>)
			</a>
			<a href="#tab-cohort-attendance" class="edu-nav-tab" data-bs-toggle="tab">
				<i class="bi bi-calendar-check" aria-hidden="true"></i> Frequência & Presença
			</a>
			<a href="#tab-cohort-evaluations" class="edu-nav-tab" data-bs-toggle="tab">
				<i class="bi bi-patch-check" aria-hidden="true"></i> Avaliações da Turma
			</a>
		</div>
	</header>

	<!-- Tab Contents -->
	<div class="tab-content animate-fade-up animate-delay-1">
		<div class="tab-pane fade show active" id="tab-cohort-students">
			<div class="edu-card p-0 overflow-hidden">
				<div class="table-responsive">
					<table class="table edu-data-table mb-0" aria-label="Alunos da Turma">
						<thead>
							<tr>
								<th scope="col" style="width: 35%;">Aluno</th>
								<th scope="col" style="width: 25%;">E-mail</th>
								<th scope="col" style="width: 15%;">Progresso</th>
								<th scope="col" style="width: 15%;">Frequência</th>
								<th scope="col" style="width: 10%; text-align: right;">Status</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<div class="d-flex align-items-center gap-2">
										<div class="avatar avatar-sm rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
											CS
										</div>
										<span class="fw-semibold text-body">Carlos Silva</span>
									</div>
								</td>
								<td class="text-muted small">carlos.silva@email.com</td>
								<td>
									<div class="d-flex align-items-center gap-2">
										<div class="progress flex-1" style="height: 6px;">
											<div class="progress-bar bg-primary" role="progressbar" style="width: 85%;"></div>
										</div>
										<span class="small fw-semibold">85%</span>
									</div>
								</td>
								<td class="text-body small fw-medium">96%</td>
								<td class="text-end"><span class="edu-badge edu-badge-success">Regular</span></td>
							</tr>
							<tr>
								<td>
									<div class="d-flex align-items-center gap-2">
										<div class="avatar avatar-sm rounded-circle bg-info-subtle text-info fw-bold d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
											MO
										</div>
										<span class="fw-semibold text-body">Mariana Oliveira</span>
									</div>
								</td>
								<td class="text-muted small">mariana.oliveira@email.com</td>
								<td>
									<div class="d-flex align-items-center gap-2">
										<div class="progress flex-1" style="height: 6px;">
											<div class="progress-bar bg-primary" role="progressbar" style="width: 78%;"></div>
										</div>
										<span class="small fw-semibold">78%</span>
									</div>
								</td>
								<td class="text-body small fw-medium">92%</td>
								<td class="text-end"><span class="edu-badge edu-badge-success">Regular</span></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div class="tab-pane fade" id="tab-cohort-attendance">
			<div class="edu-card text-center py-5">
				<i class="bi bi-calendar2-week fs-1 text-primary mb-3"></i>
				<h4 class="h5 fw-bold text-heading">Registro de Frequência dos Alunos</h4>
				<p class="text-muted small mb-3">O diário de classe permite auditar presenças, faltas justificadas e horas assistidas.</p>
				<button type="button" class="edu-btn edu-btn-outline" onclick="alert('Protótipo: Relatório de frequência simulado.');">
					Exportar Frequência da Turma
				</button>
			</div>
		</div>

		<div class="tab-pane fade" id="tab-cohort-evaluations">
			<div class="edu-card text-center py-5">
				<i class="bi bi-award fs-1 text-muted mb-3"></i>
				<h4 class="h5 fw-bold text-heading">Avaliações Aplicadas nesta Turma</h4>
				<p class="text-muted small mb-3">Acompanhe as médias de notas e desempenho coletivo por instrumento avaliativo.</p>
				<a href="<?= base_url('admin/avaliacoes') ?>" class="edu-btn edu-btn-primary">
					Ver Quadro de Avaliações
				</a>
			</div>
		</div>
	</div>
</div>
