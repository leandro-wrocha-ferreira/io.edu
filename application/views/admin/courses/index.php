<div class="container-fluid px-0">
	<!-- Page Header -->
	<header class="edu-page-header animate-fade-up">
		<div>
			<nav class="header-breadcrumb d-flex align-items-center gap-2 mb-1" aria-label="Localização">
				<a href="<?= base_url('admin/painel') ?>" class="text-muted text-decoration-none">Painel</a>
				<i class="bi bi-chevron-right text-muted" style="font-size: 0.7rem;" aria-hidden="true"></i>
				<span class="fw-medium text-body">Cursos</span>
			</nav>
			<h1 class="edu-page-header-title">Gerenciamento de Cursos</h1>
			<p class="edu-page-header-desc">Gerencie e publique os cursos, formações e ementas disponibilizadas pela instituição.</p>
		</div>
		<div class="edu-page-header-actions">
			<a href="<?= base_url('admin/cursos/novo') ?>" class="edu-btn edu-btn-primary">
				<i class="bi bi-plus-lg" aria-hidden="true"></i>
				<span>Novo Curso</span>
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
				       id="course-search-input"
				       placeholder="Pesquisar por título, categoria ou professor..."
				       aria-label="Pesquisar cursos">
			</div>
		</div>

		<div class="edu-data-toolbar-filters">
			<select class="edu-filter-select" id="filter-status" aria-label="Filtrar por Status">
				<option value="">Status: Todos</option>
				<option value="published">Publicados</option>
				<option value="review">Em Revisão</option>
				<option value="draft">Rascunhos</option>
			</select>

			<select class="edu-filter-select" id="filter-category" aria-label="Filtrar por Categoria">
				<option value="">Categoria: Todas</option>
				<option value="Tecnologia">Tecnologia</option>
				<option value="Gestão">Gestão & Negócios</option>
				<option value="Segurança">Segurança</option>
				<option value="Soft Skills">Soft Skills</option>
			</select>

			<div class="edu-table-counter ms-auto" id="course-counter" aria-live="polite">
				Exibindo <span class="fw-semibold text-body"><?= count($courses) ?></span> de <span class="fw-semibold text-body"><?= count($courses) ?></span> cursos
			</div>
		</div>
	</div>

	<!-- Table Card -->
	<div class="edu-card animate-fade-up animate-delay-2 p-0 overflow-hidden">
		<div class="table-responsive">
			<table class="table edu-data-table mb-0" id="courses-prototype-table" aria-label="Listagem de Cursos">
				<thead>
					<tr>
						<th scope="col" style="width: 32%;">Curso</th>
						<th scope="col" style="width: 14%;">Categoria</th>
						<th scope="col" style="width: 16%;">Docente</th>
						<th scope="col" style="width: 8%;">Carga</th>
						<th scope="col" style="width: 10%;">Status</th>
						<th scope="col" style="width: 8%;">Alunos</th>
						<th scope="col" style="width: 12%; text-align: right;">Ações</th>
					</tr>
				</thead>
				<tbody id="course-rows-container">
					<?php foreach ($courses as $course): ?>
						<tr data-status="<?= html_escape($course['status']) ?>" data-category="<?= html_escape($course['category']) ?>" data-title="<?= html_escape(strtolower($course['title'])) ?>">
							<td>
								<div class="d-flex align-items-center gap-2">
									<div class="avatar avatar-sm rounded-3 bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" aria-hidden="true">
										<i class="bi bi-journal-code fs-5"></i>
									</div>
									<div class="min-w-0">
										<a href="<?= base_url('admin/cursos/' . $course['id']) ?>" class="fw-semibold text-body text-decoration-none d-block text-truncate">
											<?= html_escape($course['title']) ?>
										</a>
										<span class="text-muted small text-truncate d-block" style="max-width: 320px;">
											<?= html_escape($course['description']) ?>
										</span>
									</div>
								</div>
							</td>
							<td>
								<span class="text-body fw-medium small"><?= html_escape($course['category']) ?></span>
							</td>
							<td>
								<span class="text-body small"><?= html_escape($course['instructor']) ?></span>
							</td>
							<td>
								<span class="badge bg-secondary-subtle text-body border fw-normal"><?= html_escape($course['workload']) ?></span>
							</td>
							<td>
								<?php if ($course['status'] === 'published'): ?>
									<span class="edu-badge edu-badge-success">● Publicado</span>
								<?php elseif ($course['status'] === 'review'): ?>
									<span class="edu-badge edu-badge-warning">● Em Revisão</span>
								<?php else: ?>
									<span class="edu-badge edu-badge-neutral">● Rascunho</span>
								<?php endif; ?>
							</td>
							<td>
								<span class="fw-semibold text-body"><?= (int) $course['students_count'] ?></span>
							</td>
							<td class="text-end">
								<div class="edu-action-group justify-content-end">
									<a href="<?= base_url('admin/cursos/' . $course['id']) ?>"
									   class="edu-action-btn"
									   title="Visão Detalhada"
									   aria-label="Ver detalhes de <?= html_escape($course['title']) ?>">
										<i class="bi bi-eye" aria-hidden="true"></i>
									</a>
									<a href="<?= base_url('admin/cursos/' . $course['id'] . '/conteudo') ?>"
									   class="edu-action-btn"
									   title="Conteúdo Curricular"
									   aria-label="Gerenciar conteúdo curricular de <?= html_escape($course['title']) ?>">
										<i class="bi bi-layers" aria-hidden="true"></i>
									</a>
									<a href="<?= base_url('admin/cursos/' . $course['id'] . '/editar') ?>"
									   class="edu-action-btn edu-action-btn-edit"
									   title="Editar Curso"
									   aria-label="Editar <?= html_escape($course['title']) ?>">
										<i class="bi bi-pencil" aria-hidden="true"></i>
									</a>
								</div>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>

		<!-- Empty state placeholder (hidden by default) -->
		<div id="course-empty-state" class="edu-table-empty d-none p-5 text-center">
			<div class="empty-icon mb-3 text-muted">
				<i class="bi bi-search fs-1"></i>
			</div>
			<h3 class="h6 fw-bold text-heading">Nenhum curso encontrado</h3>
			<p class="text-muted small mb-3">Tente ajustar os termos de busca ou remover os filtros aplicados.</p>
			<button type="button" class="edu-btn edu-btn-outline edu-btn-sm" id="btn-reset-filters">
				Limpar Filtros
			</button>
		</div>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
	const searchInput = document.getElementById('course-search-input');
	const filterStatus = document.getElementById('filter-status');
	const filterCategory = document.getElementById('filter-category');
	const counter = document.getElementById('course-counter');
	const emptyState = document.getElementById('course-empty-state');
	const rows = document.querySelectorAll('#course-rows-container tr');
	const totalCount = rows.length;

	const filterTable = () => {
		const term = (searchInput.value || '').toLowerCase().trim();
		const status = filterStatus.value;
		const category = filterCategory.value;
		let visibleCount = 0;

		rows.forEach(function(row) {
			const text = row.textContent.toLowerCase();
			const rowStatus = row.getAttribute('data-status');
			const rowCategory = row.getAttribute('data-category');

			const matchTerm = !term || text.includes(term);
			const matchStatus = !status || rowStatus === status;
			const matchCategory = !category || rowCategory === category;

			if (matchTerm && matchStatus && matchCategory) {
				row.style.display = '';
				visibleCount++;
			} else {
				row.style.display = 'none';
			}
		});

		counter.innerHTML = 'Exibindo <span class="fw-semibold text-body">' + visibleCount + '</span> de <span class="fw-semibold text-body">' + totalCount + '</span> cursos';

		if (visibleCount === 0) {
			emptyState.classList.remove('d-none');
		} else {
			emptyState.classList.add('d-none');
		}
	}

	searchInput.addEventListener('input', filterTable);
	filterStatus.addEventListener('change', filterTable);
	filterCategory.addEventListener('change', filterTable);

	document.getElementById('btn-reset-filters')?.addEventListener('click', function() {
		searchInput.value = '';
		filterStatus.value = '';
		filterCategory.value = '';
		filterTable();
	});
});
</script>
