<div class="container-fluid px-0">
	<!-- Page Header -->
	<header class="edu-page-header animate-fade-up">
		<div>
			<nav class="header-breadcrumb d-flex align-items-center gap-2 mb-1" aria-label="Localização">
				<a href="<?= base_url('admin/painel') ?>" class="text-muted text-decoration-none">Painel</a>
				<i class="bi bi-chevron-right text-muted" style="font-size: 0.7rem;" aria-hidden="true"></i>
				<span class="text-muted">Relatórios</span>
				<i class="bi bi-chevron-right text-muted" style="font-size: 0.7rem;" aria-hidden="true"></i>
				<span class="fw-medium text-body">Acadêmicos</span>
			</nav>
			<h1 class="edu-page-header-title">Relatórios Acadêmicos</h1>
			<p class="edu-page-header-desc">Acompanhe métricas pedagógicas, taxas de conclusão, retenção e evolução do aprendizado.</p>
		</div>

		<div class="edu-page-header-actions">
			<button type="button" class="edu-btn edu-btn-outline" onclick="alert('Protótipo: Exportação de relatório acadêmico em PDF simulada.');">
				<i class="bi bi-download" aria-hidden="true"></i>
				<span>Exportar Relatório</span>
			</button>
		</div>
	</header>

	<!-- Filters Toolbar -->
	<div class="edu-data-toolbar animate-fade-up animate-delay-1 mb-4">
		<div class="d-flex align-items-center gap-2 flex-wrap w-100">
			<div class="d-flex align-items-center gap-2">
				<i class="bi bi-funnel text-muted" aria-hidden="true"></i>
				<span class="small fw-semibold text-muted text-uppercase">Filtrar por:</span>
			</div>
			<select class="edu-filter-select" aria-label="Filtrar por Período">
				<option value="year">Ano Letivo: 2026</option>
				<option value="semester">Semestre Atual (2026.2)</option>
				<option value="30days">Últimos 30 dias</option>
			</select>
			<select class="edu-filter-select" aria-label="Filtrar por Curso">
				<option value="">Todos os Cursos</option>
				<option value="1">Desenvolvimento Web Fullstack</option>
				<option value="2">Gestão Ágil e Scrum</option>
				<option value="3">Segurança da Informação</option>
			</select>
			<div class="ms-auto text-muted small">
				<i class="bi bi-check-circle me-1 text-success"></i>Base consolidada em tempo real
			</div>
		</div>
	</div>

	<!-- 4 Main Academic KPIs -->
	<div class="row g-3 mb-4 animate-fade-up animate-delay-1">
		<div class="col-12 col-sm-6 col-xl-3">
			<div class="edu-report-kpi">
				<div class="edu-report-kpi-info">
					<span class="edu-report-kpi-label">Alunos Ativos</span>
					<span class="edu-report-kpi-value"><?= html_escape($reports['kpis']['active_students']) ?></span>
					<span class="edu-report-kpi-trend positive">
						<i class="bi bi-arrow-up-short"></i> +12% vs. mês anterior
					</span>
				</div>
				<div class="edu-report-kpi-icon primary" aria-hidden="true">
					<i class="bi bi-mortarboard-fill"></i>
				</div>
			</div>
		</div>

		<div class="col-12 col-sm-6 col-xl-3">
			<div class="edu-report-kpi">
				<div class="edu-report-kpi-info">
					<span class="edu-report-kpi-label">Cursos Ativos</span>
					<span class="edu-report-kpi-value"><?= html_escape($reports['kpis']['active_courses']) ?></span>
					<span class="edu-report-kpi-trend positive">
						<i class="bi bi-check2"></i> Todas as formações
					</span>
				</div>
				<div class="edu-report-kpi-icon success" aria-hidden="true">
					<i class="bi bi-journal-bookmark-fill"></i>
				</div>
			</div>
		</div>

		<div class="col-12 col-sm-6 col-xl-3">
			<div class="edu-report-kpi">
				<div class="edu-report-kpi-info">
					<span class="edu-report-kpi-label">Taxa de Conclusão</span>
					<span class="edu-report-kpi-value"><?= html_escape($reports['kpis']['avg_completion_rate']) ?></span>
					<span class="edu-report-kpi-trend positive">
						<i class="bi bi-arrow-up-short"></i> +4,5% na média geral
					</span>
				</div>
				<div class="edu-report-kpi-icon success" aria-hidden="true">
					<i class="bi bi-award-fill"></i>
				</div>
			</div>
		</div>

		<div class="col-12 col-sm-6 col-xl-3">
			<div class="edu-report-kpi">
				<div class="edu-report-kpi-info">
					<span class="edu-report-kpi-label">Taxa de Abandono</span>
					<span class="edu-report-kpi-value"><?= html_escape($reports['kpis']['dropout_rate']) ?></span>
					<span class="edu-report-kpi-trend warning">
						<i class="bi bi-exclamation-triangle"></i> Atenção necessária
					</span>
				</div>
				<div class="edu-report-kpi-icon danger" aria-hidden="true">
					<i class="bi bi-person-x-fill"></i>
				</div>
			</div>
		</div>
	</div>

	<!-- Course Completion & Dropout Analysis -->
	<div class="row g-4 mb-4 animate-fade-up animate-delay-2">
		<!-- Course Completion Rates (Visual Bars & Accessible Alternative) -->
		<div class="col-lg-6">
			<div class="edu-report-card h-100">
				<div class="edu-report-card-header">
					<div>
						<h2 class="edu-report-card-title">Conclusão Média por Curso</h2>
						<p class="edu-report-card-desc">Percentual de estudantes que concluem 100% da carga horária.</p>
					</div>
				</div>
				<div class="edu-report-card-body">
					<?php foreach ($reports['course_completion'] as $completion_item): ?>
						<div class="edu-stat-bar-item">
							<div class="edu-stat-bar-header">
								<span class="edu-stat-bar-label"><?= html_escape($completion_item['course']) ?></span>
								<span class="edu-stat-bar-value"><?= (int) $completion_item['rate'] ?>%</span>
							</div>
							<div class="edu-stat-bar-track" aria-hidden="true">
								<div class="edu-stat-bar-fill <?= $completion_item['rate'] >= 80 ? 'success' : ($completion_item['rate'] >= 70 ? '' : 'warning') ?>" style="width: <?= (int) $completion_item['rate'] ?>%;"></div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<!-- Dropout Points Analysis -->
		<div class="col-lg-6">
			<div class="edu-report-card h-100">
				<div class="edu-report-card-header">
					<div>
						<h2 class="edu-report-card-title">Pontos Críticos de Abandono</h2>
						<p class="edu-report-card-desc">Mapeamento das etapas modulares com maior evasão de estudantes.</p>
					</div>
				</div>
				<div class="edu-report-card-body">
					<?php foreach ($reports['dropout_analysis']['dropout_points'] as $dropout_point): ?>
						<div class="edu-stat-bar-item">
							<div class="edu-stat-bar-header">
								<span class="edu-stat-bar-label"><?= html_escape($dropout_point['module']) ?></span>
								<span class="edu-stat-bar-value text-danger"><?= html_escape($dropout_point['rate']) ?></span>
							</div>
							<div class="edu-stat-bar-track" aria-hidden="true">
								<div class="edu-stat-bar-fill danger" style="width: <?= (int) $dropout_point['rate'] * 3 ?>%;"></div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>

	<!-- Cohort Performance Table -->
	<div class="edu-card animate-fade-up animate-delay-3 p-0 overflow-hidden">
		<div class="edu-card-header py-3 px-4">
			<h2 class="edu-card-title mb-0">Desempenho Geral por Turma</h2>
		</div>
		<div class="table-responsive">
			<table class="table edu-data-table mb-0" aria-label="Tabela de Desempenho por Turma">
				<thead>
					<tr>
						<th scope="col" style="width: 35%;">Turma</th>
						<th scope="col" style="width: 20%;">Alunos Enturmados</th>
						<th scope="col" style="width: 25%;">Taxa de Conclusão</th>
						<th scope="col" style="width: 20%; text-align: right;">Média de Notas</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($reports['cohort_performance'] as $cohort): ?>
						<tr>
							<td class="fw-semibold text-body"><?= html_escape($cohort['cohort']) ?></td>
							<td><?= (int) $cohort['students'] ?> participantes</td>
							<td>
								<div class="d-flex align-items-center gap-2">
									<div class="progress flex-1" style="height: 6px;">
										<div class="progress-bar bg-success" role="progressbar" style="width: <?= html_escape($cohort['completion']) ?>;"></div>
									</div>
									<span class="small fw-semibold"><?= html_escape($cohort['completion']) ?></span>
								</div>
							</td>
							<td class="text-end fw-bold text-success"><?= html_escape($cohort['grade_avg']) ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
