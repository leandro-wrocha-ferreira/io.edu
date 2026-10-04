<div class="container-fluid px-0">
	<!-- Page Header -->
	<header class="edu-page-header animate-fade-up">
		<div>
			<nav class="header-breadcrumb d-flex align-items-center gap-2 mb-1" aria-label="Localização">
				<a href="<?= base_url('admin/painel') ?>" class="text-muted text-decoration-none">Painel</a>
				<i class="bi bi-chevron-right text-muted" style="font-size: 0.7rem;" aria-hidden="true"></i>
				<span class="fw-medium text-body">Turmas</span>
			</nav>
			<h1 class="edu-page-header-title">Gerenciamento de Turmas</h1>
			<p class="edu-page-header-desc">Organize turmas, participantes enturmados e períodos letivos de formação.</p>
		</div>
		<div class="edu-page-header-actions">
			<a href="<?= base_url('admin/turmas/novo') ?>" class="edu-btn edu-btn-primary">
				<i class="bi bi-plus-lg" aria-hidden="true"></i>
				<span>Nova Turma</span>
			</a>
		</div>
	</header>

	<!-- Data Toolbar -->
	<div class="edu-data-toolbar animate-fade-up animate-delay-1">
		<div class="edu-data-toolbar-search">
			<div class="edu-search-box">
				<i class="bi bi-search edu-search-icon" aria-hidden="true"></i>
				<input type="search"
				       class="edu-search-input"
				       id="class-search-input"
				       placeholder="Pesquisar por turma, curso ou professor..."
				       aria-label="Pesquisar turmas">
			</div>
		</div>

		<div class="edu-data-toolbar-filters">
			<select class="edu-filter-select" id="filter-class-status" aria-label="Filtrar por Status">
				<option value="">Status: Todos</option>
				<option value="active">Ativas</option>
				<option value="planned">Previstas</option>
				<option value="finished">Concluídas</option>
			</select>

			<div class="edu-table-counter ms-auto" id="class-counter" aria-live="polite">
				Exibindo <span class="fw-semibold text-body"><?= count($classes) ?></span> de <span class="fw-semibold text-body"><?= count($classes) ?></span> turmas
			</div>
		</div>
	</div>

	<!-- Table Card -->
	<div class="edu-card animate-fade-up animate-delay-2 p-0 overflow-hidden">
		<div class="table-responsive">
			<table class="table edu-data-table mb-0" id="classes-table" aria-label="Listagem de Turmas">
				<thead>
					<tr>
						<th scope="col" style="width: 25%;">Turma</th>
						<th scope="col" style="width: 20%;">Curso Associado</th>
						<th scope="col" style="width: 15%;">Docente</th>
						<th scope="col" style="width: 15%;">Período Letivo</th>
						<th scope="col" style="width: 8%;">Alunos</th>
						<th scope="col" style="width: 8%;">Status</th>
						<th scope="col" style="width: 9%; text-align: right;">Ações</th>
					</tr>
				</thead>
				<tbody id="class-rows-container">
					<?php foreach ($classes as $cohort): ?>
						<tr data-status="<?= html_escape($cohort['status']) ?>">
							<td>
								<div class="d-flex align-items-center gap-2">
									<div class="avatar avatar-sm rounded-3 bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" aria-hidden="true">
										<i class="bi bi-people-fill fs-5"></i>
									</div>
									<div class="min-w-0">
										<a href="<?= base_url('admin/turmas/' . $cohort['id']) ?>" class="fw-semibold text-body text-decoration-none d-block">
											<?= html_escape($cohort['name']) ?>
										</a>
										<span class="text-muted small">Progresso médio: <?= (int) $cohort['progress_avg'] ?>%</span>
									</div>
								</div>
							</td>
							<td>
								<span class="text-body fw-medium small"><?= html_escape($cohort['course_title']) ?></span>
							</td>
							<td>
								<span class="text-body small"><?= html_escape($cohort['instructor']) ?></span>
							</td>
							<td>
								<span class="text-muted small font-monospace"><?= html_escape($cohort['period']) ?></span>
							</td>
							<td>
								<span class="fw-semibold text-body"><?= (int) $cohort['students_count'] ?></span>
							</td>
							<td>
								<?php if ($cohort['status'] === 'active'): ?>
									<span class="edu-badge edu-badge-success">● Ativa</span>
								<?php elseif ($cohort['status'] === 'planned'): ?>
									<span class="edu-badge edu-badge-warning">● Prevista</span>
								<?php else: ?>
									<span class="edu-badge edu-badge-neutral">● Concluída</span>
								<?php endif; ?>
							</td>
							<td class="text-end">
								<div class="edu-action-group justify-content-end">
									<a href="<?= base_url('admin/turmas/' . $cohort['id']) ?>"
									   class="edu-action-btn"
									   title="Visão Detalhada da Turma"
									   aria-label="Ver detalhes da turma <?= html_escape($cohort['name']) ?>">
										<i class="bi bi-eye" aria-hidden="true"></i>
									</a>
									<button type="button"
									        class="edu-action-btn edu-action-btn-edit"
									        title="Editar Turma"
									        onclick="alert('Protótipo: Edição de turma simulada.');">
										<i class="bi bi-pencil" aria-hidden="true"></i>
									</button>
								</div>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
	const searchInput = document.getElementById('class-search-input');
	const filterStatus = document.getElementById('filter-class-status');
	const counter = document.getElementById('class-counter');
	const rows = document.querySelectorAll('#class-rows-container tr');
	const totalCount = rows.length;

	const filterClasses = () => {
		const term = (searchInput.value || '').toLowerCase().trim();
		const status = filterStatus.value;
		let visibleCount = 0;

		rows.forEach(function(row) {
			const text = row.textContent.toLowerCase();
			const rowStatus = row.getAttribute('data-status');
			const matchTerm = !term || text.includes(term);
			const matchStatus = !status || rowStatus === status;

			if (matchTerm && matchStatus) {
				row.style.display = '';
				visibleCount++;
			} else {
				row.style.display = 'none';
			}
		});

		counter.innerHTML = 'Exibindo <span class="fw-semibold text-body">' + visibleCount + '</span> de <span class="fw-semibold text-body">' + totalCount + '</span> turmas';
	}

	searchInput.addEventListener('input', filterClasses);
	filterStatus.addEventListener('change', filterClasses);
});
</script>
