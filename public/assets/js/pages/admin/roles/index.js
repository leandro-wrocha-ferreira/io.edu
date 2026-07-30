/**
 * Admin Roles List Page Script
 * DataTables Server-Side Initialization for Roles
 */
document.addEventListener('DOMContentLoaded', function () {
	const tableEl = document.getElementById('roles-table');
	if (!tableEl || typeof jQuery === 'undefined' || !jQuery.fn.DataTable) {
		return;
	}

	jQuery(tableEl).DataTable({
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
			{ data: 'id', width: '60px', className: 'd-none d-md-table-cell ps-3' },
			{ data: 'name' },
			{ data: 'slug', className: 'd-none d-md-table-cell' },
			{ data: 'description', className: 'd-none d-lg-table-cell' },
			{ data: 'created_at', width: '140px', className: 'd-none d-md-table-cell' },
			{ data: 'actions', orderable: false, searchable: false, width: '100px', className: 'text-center' }
		],
		language: {
			url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
		},
		pageLength: 25,
		order: [[1, 'asc']],
		dom: '<"row"<"col-sm-6"l><"col-sm-6"f>>rtip'
	});
});
