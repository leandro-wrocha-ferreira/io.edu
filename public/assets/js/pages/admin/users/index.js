/**
 * Admin Users List Page Script
 * DataTables Server-Side Initialization for Users
 */
document.addEventListener('DOMContentLoaded', function () {
	const tableEl = document.getElementById('users-table');
	if (!tableEl || typeof jQuery === 'undefined' || !jQuery.fn.DataTable) {
		return;
	}

	jQuery(tableEl).DataTable({
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
			{ data: 'id', width: '60px', className: 'd-none d-md-table-cell ps-3' },
			{ data: 'name' },
			{ data: 'email' },
			{ data: 'role', orderable: false, searchable: false, className: 'd-none d-lg-table-cell' },
			{ data: 'created_at', width: '140px', className: 'd-none d-md-table-cell' },
			{ data: 'status', orderable: false, searchable: false, width: '90px', className: 'text-center' },
			{ data: 'actions', orderable: false, searchable: false, width: '100px', className: 'text-center' }
		],
		language: {
			url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
		},
		pageLength: 25,
		order: [[0, 'desc']],
		dom: '<"row"<"col-sm-6"l><"col-sm-6"f>>rtip'
	});
});
