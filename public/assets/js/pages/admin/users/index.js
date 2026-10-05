/**
 * Admin Users List Page Script — io.edu LMS
 * DataTables Server-Side Initialization with integrated Toolbar
 */
document.addEventListener('DOMContentLoaded', function () {
	const tableEl = document.getElementById('users-table');
	if (!tableEl || typeof jQuery === 'undefined' || !jQuery.fn.DataTable) {
		return;
	}

	const dt = jQuery(tableEl).DataTable({
		processing: true,
		serverSide: true,
		ajax: {
			url: window.location.origin + '/admin/usuarios/dados',
			type: 'GET',
			headers: {
				'X-Requested-With': 'XMLHttpRequest'
			}
		},
		columns: [
			{ data: 'id', width: '70px', className: 'd-none d-md-table-cell ps-3' },
			{ data: 'name', className: 'fw-semibold' },
			{ data: 'email', className: 'text-muted' },
			{ data: 'role', orderable: false, searchable: false, className: 'd-none d-lg-table-cell' },
			{ data: 'created_at', width: '150px', className: 'd-none d-md-table-cell text-muted small' },
			{ data: 'status', orderable: false, searchable: false, width: '110px', className: 'text-center' },
			{ data: 'actions', orderable: false, searchable: false, width: '110px', className: 'text-center' }
		],
		language: {
			emptyTable: '<div class="edu-table-empty"><i class="bi bi-people edu-table-empty-icon" aria-hidden="true"></i><div class="edu-table-empty-title">Nenhum usuário cadastrado</div><div class="edu-table-empty-desc">Cadastre seu primeiro usuário para começar a gerenciar acessos.</div><a href="/admin/usuarios/novo" class="edu-btn edu-btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i> Criar Usuário</a></div>',
			zeroRecords: '<div class="edu-table-empty"><i class="bi bi-search edu-table-empty-icon" aria-hidden="true"></i><div class="edu-table-empty-title">Nenhum usuário encontrado</div><div class="edu-table-empty-desc">Não encontramos registros correspondentes à busca informada.</div></div>',
			processing: '<div class="d-flex align-items-center gap-2"><div class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></div><span>Carregando dados...</span></div>',
			paginate: {
				first: 'Primeiro',
				previous: 'Anterior',
				next: 'Próximo',
				last: 'Último'
			},
			info: 'Exibindo _START_ até _END_ de _TOTAL_ usuários',
			infoEmpty: 'Nenhum usuário encontrado',
			infoFiltered: '(filtrado de _MAX_ no total)'
		},
		pageLength: 25,
		order: [[0, 'desc']],
		dom: 'rt<"d-flex flex-wrap align-items-center justify-content-between p-3 border-top"ip>',
		drawCallback: function () {
			const api = this.api();
			const info = api.page.info();
			const countEl = document.getElementById('users-count');
			if (countEl) {
				if (info.recordsTotal === 0) {
					countEl.textContent = 'Nenhum registro';
				} else if (info.recordsDisplay < info.recordsTotal) {
					countEl.textContent = `Exibindo ${info.recordsDisplay} de ${info.recordsTotal} usuários (filtrado)`;
				} else {
					countEl.textContent = `Total de ${info.recordsTotal} usuários cadastrados`;
				}
			}
		}
	});

	// Toolbar Search Integration with Debounce
	const searchInput = document.getElementById('users-search-input');
	if (searchInput) {
		let debounceTimer;
		searchInput.addEventListener('input', function () {
			clearTimeout(debounceTimer);
			debounceTimer = setTimeout(() => {
				dt.search(this.value).draw();
			}, 300);
		});
	}

	// Toolbar Status Filter Integration
	const statusFilter = document.getElementById('users-status-filter');
	if (statusFilter) {
		statusFilter.addEventListener('change', function () {
			dt.search(searchInput ? searchInput.value : '').draw();
		});
	}
});
