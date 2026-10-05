/**
 * Categories DataTable script — io.edu LMS
 */
document.addEventListener('DOMContentLoaded', function () {
	const tableEl = document.getElementById('categories-table');

	if (!tableEl || typeof jQuery === 'undefined' || !jQuery.fn.DataTable) {
		return;
	}

	const dt = jQuery(tableEl).DataTable({
		processing: true,
		serverSide: true,
		ajax: {
			url: window.location.origin + '/admin/categorias/dados',
			type: 'GET',
			headers: {
				'X-Requested-With': 'XMLHttpRequest'
			}
		},
		columns: [
			{ data: 'id', width: '80px', className: 'text-muted ps-3' },
			{ data: 'name', className: 'fw-semibold text-body' },
			{ data: 'slug' },
			{ data: 'status', orderable: false, searchable: false, width: '120px', className: 'text-center' },
			{ data: 'created_at', width: '160px', className: 'text-muted' },
			{ data: 'actions', orderable: false, searchable: false, width: '110px', className: 'text-center' }
		],
		language: {
			emptyTable: `
				<div class="edu-table-empty">
					<i class="bi bi-tags edu-table-empty-icon" aria-hidden="true"></i>
					<div class="edu-table-empty-title">Nenhuma categoria cadastrada</div>
					<div class="edu-table-empty-desc">Cadastre a primeira categoria pedagógica para organizar seus cursos.</div>
					<a href="/admin/categorias/nova" class="edu-btn edu-btn-primary btn-sm">
						<i class="bi bi-plus-lg me-1"></i> Nova Categoria
					</a>
				</div>
			`,
			zeroRecords: `
				<div class="edu-table-empty">
					<i class="bi bi-search edu-table-empty-icon" aria-hidden="true"></i>
					<div class="edu-table-empty-title">Nenhum resultado encontrado</div>
					<div class="edu-table-empty-desc">Nenhuma categoria encontrada para o termo pesquisado.</div>
				</div>
			`,
			processing: '<div class="d-flex align-items-center justify-content-center gap-2 py-3"><div class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></div><span>Carregando categorias...</span></div>'
		},
		pageLength: 25,
		order: [[0, 'desc']],
		dom: 'rt<"d-flex flex-wrap align-items-center justify-content-between p-3 border-top"ip>'
	});

	// Toolbar search with 300ms debounce
	const searchInput = document.getElementById('categories-search-input');
	if (searchInput) {
		let debounceTimer;
		searchInput.addEventListener('input', function () {
			clearTimeout(debounceTimer);
			debounceTimer = setTimeout(() => {
				dt.search(this.value).draw();
			}, 300);
		});
	}

	// Dynamic counter update
	dt.on('draw.dt', function () {
		const info = dt.page.info();
		const countEl = document.getElementById('categories-count');
		if (countEl) {
			if (info.recordsTotal === 0) {
				countEl.textContent = 'Nenhuma categoria';
			} else if (info.recordsDisplay < info.recordsTotal) {
				countEl.textContent = `Exibindo ${info.recordsDisplay} de ${info.recordsTotal} categorias (filtrado)`;
			} else {
				countEl.textContent = `Total de ${info.recordsTotal} categorias cadastradas`;
			}
		}
	});
});
