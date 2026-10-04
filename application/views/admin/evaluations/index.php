<div class="container-fluid px-0">
	<!-- Page Header -->
	<header class="edu-page-header animate-fade-up">
		<div>
			<nav class="header-breadcrumb d-flex align-items-center gap-2 mb-1" aria-label="Localização">
				<a href="<?= base_url('admin/painel') ?>" class="text-muted text-decoration-none">Painel</a>
				<i class="bi bi-chevron-right text-muted" style="font-size: 0.7rem;" aria-hidden="true"></i>
				<span class="fw-medium text-body">Avaliações</span>
			</nav>
			<h1 class="edu-page-header-title">Gerenciamento de Avaliações</h1>
			<p class="edu-page-header-desc">Gerencie provas, atividades, simulados e instrumentos de acompanhamento da aprendizagem.</p>
		</div>
		<div class="edu-page-header-actions">
			<a href="<?= base_url('admin/avaliacoes/novo') ?>" class="edu-btn edu-btn-primary">
				<i class="bi bi-plus-lg" aria-hidden="true"></i>
				<span>Nova Avaliação</span>
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
				       id="eval-search-input"
				       placeholder="Pesquisar por título, curso ou módulo..."
				       aria-label="Pesquisar avaliações">
			</div>
		</div>

		<div class="edu-data-toolbar-filters">
			<select class="edu-filter-select" id="filter-eval-status" aria-label="Filtrar por Status">
				<option value="">Status: Todos</option>
				<option value="published">Publicadas</option>
				<option value="review">Em Revisão</option>
			</select>

			<select class="edu-filter-select" id="filter-eval-type" aria-label="Filtrar por Tipo">
				<option value="">Tipo: Todos</option>
				<option value="multiple_choice">Múltipla Escolha</option>
				<option value="mixed">Mista / Prática</option>
			</select>

			<div class="edu-table-counter ms-auto" id="eval-counter" aria-live="polite">
				Exibindo <span class="fw-semibold text-body"><?= count($evaluations) ?></span> de <span class="fw-semibold text-body"><?= count($evaluations) ?></span> avaliações
			</div>
		</div>
	</div>

	<!-- Table Card -->
	<div class="edu-card animate-fade-up animate-delay-2 p-0 overflow-hidden">
		<div class="table-responsive">
			<table class="table edu-data-table mb-0" id="evaluations-table" aria-label="Listagem de Avaliações">
				<thead>
					<tr>
						<th scope="col" style="width: 28%;">Avaliação</th>
						<th scope="col" style="width: 22%;">Curso / Módulo</th>
						<th scope="col" style="width: 14%;">Tipo</th>
						<th scope="col" style="width: 8%;">Questões</th>
						<th scope="col" style="width: 8%;">Tentativas</th>
						<th scope="col" style="width: 8%;">Média</th>
						<th scope="col" style="width: 12%; text-align: right;">Ações</th>
					</tr>
				</thead>
				<tbody id="eval-rows-container">
					<?php foreach ($evaluations as $item): ?>
						<tr data-status="<?= html_escape($item['status']) ?>" data-type="<?= html_escape($item['type']) ?>">
							<td>
								<div class="d-flex align-items-center gap-2">
									<div class="avatar avatar-sm rounded-3 bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" aria-hidden="true">
										<i class="bi bi-patch-question fs-5"></i>
									</div>
									<div class="min-w-0">
										<a href="<?= base_url('admin/avaliacoes/' . $item['id']) ?>" class="fw-semibold text-body text-decoration-none d-block">
											<?= html_escape($item['title']) ?>
										</a>
										<span class="text-muted small">Tempo limite: <?= html_escape($item['time_limit']) ?></span>
									</div>
								</div>
							</td>
							<td>
								<span class="text-body fw-medium small d-block"><?= html_escape($item['course_title']) ?></span>
								<span class="text-muted small"><?= html_escape($item['module_title']) ?></span>
							</td>
							<td>
								<span class="badge bg-secondary-subtle text-body border fw-normal"><?= html_escape($item['type_label']) ?></span>
							</td>
							<td>
								<span class="fw-semibold text-body"><?= (int) $item['questions_count'] ?></span>
							</td>
							<td>
								<span class="text-body"><?= (int) $item['attempts_count'] ?></span>
							</td>
							<td>
								<span class="fw-bold text-success"><?= html_escape($item['average_score']) ?></span>
							</td>
							<td class="text-end">
								<div class="edu-action-group justify-content-end">
									<a href="<?= base_url('admin/avaliacoes/' . $item['id']) ?>"
									   class="edu-action-btn"
									   title="Visão Geral da Avaliação"
									   aria-label="Ver detalhes da avaliação">
										<i class="bi bi-eye" aria-hidden="true"></i>
									</a>
									<a href="<?= base_url('admin/avaliacoes/' . $item['id'] . '/editar') ?>"
									   class="edu-action-btn edu-action-btn-edit"
									   title="Editar Avaliação e Questões"
									   aria-label="Editar avaliação">
										<i class="bi bi-pencil" aria-hidden="true"></i>
									</a>
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
	const searchInput = document.getElementById('eval-search-input');
	const filterStatus = document.getElementById('filter-eval-status');
	const filterType = document.getElementById('filter-eval-type');
	const counter = document.getElementById('eval-counter');
	const rows = document.querySelectorAll('#eval-rows-container tr');
	const totalCount = rows.length;

	const filterEvals = () => {
		const term = (searchInput.value || '').toLowerCase().trim();
		const status = filterStatus.value;
		const type = filterType.value;
		let visibleCount = 0;

		rows.forEach(function(row) {
			const text = row.textContent.toLowerCase();
			const rowStatus = row.getAttribute('data-status');
			const rowType = row.getAttribute('data-type');

			const matchTerm = !term || text.includes(term);
			const matchStatus = !status || rowStatus === status;
			const matchType = !type || rowType === type;

			if (matchTerm && matchStatus && matchType) {
				row.style.display = '';
				visibleCount++;
			} else {
				row.style.display = 'none';
			}
		});

		counter.innerHTML = 'Exibindo <span class="fw-semibold text-body">' + visibleCount + '</span> de <span class="fw-semibold text-body">' + totalCount + '</span> avaliações';
	}

	searchInput.addEventListener('input', filterEvals);
	filterStatus.addEventListener('change', filterEvals);
	filterType.addEventListener('change', filterEvals);
});
</script>
