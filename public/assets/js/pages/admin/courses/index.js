/**
 * Courses DataTable script — io.edu LMS
 */
document.addEventListener('DOMContentLoaded', function () {
	const tableEl = document.getElementById('courses-table');

	if (!tableEl || typeof jQuery === 'undefined' || !jQuery.fn.DataTable) {
		return;
	}

	const searchInput = document.getElementById('courses-search-input');
	const categoryFilter = document.getElementById('courses-category-filter');
	const statusFilter = document.getElementById('courses-status-filter');

	const dt = jQuery(tableEl).DataTable({
		processing: true,
		serverSide: true,
		ajax: {
			url: window.location.origin + '/admin/cursos/dados',
			type: 'GET',
			data: function (d) {
				if (categoryFilter && categoryFilter.value) {
					d.category_id = categoryFilter.value;
				}
				if (statusFilter && statusFilter.value) {
					d.status = statusFilter.value;
				}
			},
			headers: {
				'X-Requested-With': 'XMLHttpRequest'
			}
		},
		columns: [
			{ data: 'id', width: '70px', className: 'text-muted ps-3' },
			{ data: 'title' },
			{ data: 'category', width: '180px' },
			{ data: 'access', width: '150px' },
			{ data: 'workload', width: '100px', className: 'text-muted' },
			{ data: 'status', orderable: false, searchable: false, width: '110px', className: 'text-center' },
			{ data: 'actions', orderable: false, searchable: false, width: '130px', className: 'text-center' }
		],
		language: {
			emptyTable: `
				<div class="edu-table-empty">
					<i class="bi bi-journal-x edu-table-empty-icon" aria-hidden="true"></i>
					<div class="edu-table-empty-title">Nenhum curso cadastrado</div>
					<div class="edu-table-empty-desc">Cadastre o primeiro curso para iniciar o catálogo pedagógico.</div>
					<a href="/admin/cursos/novo" class="edu-btn edu-btn-primary btn-sm">
						<i class="bi bi-plus-lg me-1"></i> Criar Novo Curso
					</a>
				</div>
			`,
			zeroRecords: `
				<div class="edu-table-empty">
					<i class="bi bi-search edu-table-empty-icon" aria-hidden="true"></i>
					<div class="edu-table-empty-title">Nenhum resultado encontrado</div>
					<div class="edu-table-empty-desc">Não encontramos cursos correspondentes aos filtros selecionados.</div>
				</div>
			`,
			processing: '<div class="d-flex align-items-center justify-content-center gap-2 py-3"><div class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></div><span>Carregando cursos...</span></div>'
		},
		pageLength: 25,
		order: [[0, 'desc']],
		dom: 'rt<"d-flex flex-wrap align-items-center justify-content-between p-3 border-top"ip>',
		drawCallback: function () {
			const api = this.api();
			const info = api.page.info();
			const countEl = document.getElementById('courses-count');
			if (countEl) {
				if (info.recordsTotal === 0) {
					countEl.textContent = 'Nenhum curso';
				} else if (info.recordsDisplay < info.recordsTotal) {
					countEl.textContent = `Exibindo ${info.recordsDisplay} de ${info.recordsTotal} cursos (filtrado)`;
				} else {
					countEl.textContent = `Total de ${info.recordsTotal} cursos cadastrados`;
				}
			}
		}
	});

	// Toolbar search with 300ms debounce
	if (searchInput) {
		let debounceTimer;
		searchInput.addEventListener('input', function () {
			clearTimeout(debounceTimer);
			debounceTimer = setTimeout(() => {
				dt.search(this.value).draw();
			}, 300);
		});
	}

	// Filter by Category
	if (categoryFilter) {
		categoryFilter.addEventListener('change', function () {
			dt.draw();
		});
	}

	// Filter by Status
	if (statusFilter) {
		statusFilter.addEventListener('change', function () {
			dt.draw();
		});
	}
});
