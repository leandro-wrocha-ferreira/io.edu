/**
 * Initialize DataTables on all tables with the .datatable class.
 */
document.addEventListener('DOMContentLoaded', function () {
    var tables = document.querySelectorAll('.datatable');
    if (tables.length > 0 && typeof jQuery !== 'undefined' && jQuery.fn.DataTable) {
        jQuery('.datatable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
            },
            pageLength: 25,
            order: [[0, 'desc']],
            columnDefs: [
                { orderable: false, targets: -1 }
            ]
        });
    }
});
