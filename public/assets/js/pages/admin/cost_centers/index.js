/**
 * Cost Centers DataTable script — io.edu LMS
 */
document.addEventListener('DOMContentLoaded', function () {
	const tableEl = document.getElementById('cost-centers-table');

	if (!tableEl || typeof jQuery === 'undefined' || !jQuery.fn.DataTable) {
		return;
	}

	const dt = jQuery(tableEl).DataTable({
		processing: true,
		serverSide: true,
		ajax: {
			url: window.location.origin + '/admin/centros-de-custo/dados',
			type: 'GET',
			headers: {
				'X-Requested-With': 'XMLHttpRequest'
			}
		},
		columns: [
			{ data: 'id', width: '70px', className: 'text-muted ps-3' },
			{ data: 'name', className: 'fw-semibold text-body' },
			{ data: 'description', className: 'text-muted' },
			{ data: 'status', orderable: false, searchable: false, width: '120px', className: 'text-center' },
			{ data: 'created_at', width: '160px', className: 'text-muted' },
			{ data: 'actions', orderable: false, searchable: false, width: '110px', className: 'text-center' }
		],
		language: {
			emptyTable: `
				<div class="edu-table-empty">
					<i class="bi bi-diagram-3 edu-table-empty-icon" aria-hidden="true"></i>
					<div class="edu-table-empty-title">Nenhum centro de custo cadastrado</div>
					<div class="edu-table-empty-desc">Cadastre o primeiro centro de custo para estruturar o faturamento dos cursos.</div>
					<a href="/admin/centros-de-custo/novo" class="edu-btn edu-btn-primary btn-sm">
						<i class="bi bi-plus-lg me-1"></i> Novo Centro de Custo
					</a>
				</div>
			`,
			zeroRecords: `
				<div class="edu-table-empty">
					<i class="bi bi-search edu-table-empty-icon" aria-hidden="true"></i>
					<div class="edu-table-empty-title">Nenhum resultado encontrado</div>
					<div class="edu-table-empty-desc">Nenhum centro de custo encontrado para os termos pesquisados.</div>
				</div>
			`,
			processing: '<div class="d-flex align-items-center justify-content-center gap-2 py-3"><div class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></div><span>Carregando centros de custo...</span></div>'
		},
		pageLength: 25,
		order: [[0, 'desc']],
		dom: 'rt<"d-flex flex-wrap align-items-center justify-content-between p-3 border-top"ip>',
		drawCallback: function () {
			const api = this.api();
			const info = api.page.info();
			const countEl = document.getElementById('cost-centers-count');
			if (countEl) {
				if (info.recordsTotal === 0) {
					countEl.textContent = 'Nenhum centro de custo';
				} else if (info.recordsDisplay < info.recordsTotal) {
					countEl.textContent = `Exibindo ${info.recordsDisplay} de ${info.recordsTotal} centros de custo (filtrado)`;
				} else {
					countEl.textContent = `Total de ${info.recordsTotal} centros de custo cadastrados`;
				}
			}
		}
	});

	// Toolbar search with 300ms debounce
	const searchInput = document.getElementById('cost-centers-search-input');
	if (searchInput) {
		let debounceTimer;
		searchInput.addEventListener('input', function () {
			clearTimeout(debounceTimer);
			debounceTimer = setTimeout(() => {
				dt.search(this.value).draw();
			}, 300);
		});
	}
});
