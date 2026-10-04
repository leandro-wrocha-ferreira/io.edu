/**
 * Admin Roles List Page Script — io.edu LMS
 * DataTables Server-Side Initialization with integrated Toolbar
 */
document.addEventListener('DOMContentLoaded', function () {
	const tableEl = document.getElementById('roles-table');
	if (!tableEl || typeof jQuery === 'undefined' || !jQuery.fn.DataTable) {
		return;
	}

	const dt = jQuery(tableEl).DataTable({
		processing: true,
		serverSide: true,
		ajax: {
			url: window.location.origin + '/admin/perfis/dados',
			type: 'GET',
			headers: {
				'X-Requested-With': 'XMLHttpRequest'
			}
		},
		columns: [
			{ data: 'id', width: '70px', className: 'd-none d-md-table-cell ps-3' },
			{ data: 'name', className: 'fw-semibold' },
			{ data: 'slug', className: 'd-none d-md-table-cell' },
			{ data: 'description', className: 'd-none d-lg-table-cell text-muted' },
			{ data: 'created_at', width: '160px', className: 'd-none d-md-table-cell text-muted small' },
			{ data: 'actions', orderable: false, searchable: false, width: '100px', className: 'text-center' }
		],
		language: {
			url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json',
			emptyTable: '<div class="edu-table-empty"><i class="bi bi-shield edu-table-empty-icon" aria-hidden="true"></i><div class="edu-table-empty-title">Nenhum perfil cadastrado</div><div class="edu-table-empty-desc">Cadastre seu primeiro perfil de acesso para atribuir permissões.</div><a href="/admin/perfis/novo" class="edu-btn edu-btn-primary btn-sm"><i class="bi bi-shield-plus me-1"></i> Criar Perfil</a></div>',
			zeroRecords: '<div class="edu-table-empty"><i class="bi bi-search edu-table-empty-icon" aria-hidden="true"></i><div class="edu-table-empty-title">Nenhum perfil encontrado</div><div class="edu-table-empty-desc">Não encontramos registros correspondentes à busca informada.</div></div>',
			processing: '<div class="d-flex align-items-center gap-2"><div class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></div><span>Carregando dados...</span></div>'
		},
		pageLength: 25,
		order: [[1, 'asc']],
		dom: 'rt<"d-flex flex-wrap align-items-center justify-content-between p-3 border-top"ip>'
	});

	// Toolbar Search Integration with Debounce
	const searchInput = document.getElementById('roles-search-input');
	if (searchInput) {
		let debounceTimer;
		searchInput.addEventListener('input', function () {
			clearTimeout(debounceTimer);
			debounceTimer = setTimeout(() => {
				dt.search(this.value).draw();
			}, 300);
		});
	}

	// Update Records Count on Table Redraw
	dt.on('draw.dt', function () {
		const info = dt.page.info();
		const countEl = document.getElementById('roles-count');
		if (countEl) {
			if (info.recordsTotal === 0) {
				countEl.textContent = 'Nenhum registro';
			} else if (info.recordsDisplay < info.recordsTotal) {
				countEl.textContent = `Exibindo ${info.recordsDisplay} de ${info.recordsTotal} perfis (filtrado)`;
			} else {
				countEl.textContent = `Total de ${info.recordsTotal} perfis cadastrados`;
			}
		}
	});
});
