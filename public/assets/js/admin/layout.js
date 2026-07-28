/**
 * Admin Layout Interactions
 */
document.addEventListener('DOMContentLoaded', function() {
    // Mobile sidebar toggle logic
    const toggleBtn = document.querySelector('.btn-toggle-sidebar');
    const sidebar = document.querySelector('.admin-sidebar');
    
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });
    }
});

// Global DataTables configuration to replace length menu with Bootstrap dropdown
if (typeof jQuery !== 'undefined') {
    jQuery(document).on('init.dt', function(e, settings) {
        var api = new jQuery.fn.dataTable.Api(settings);
        var $wrapper = jQuery(api.table().container());
        
        var $lengthContainer = $wrapper.find('.dataTables_length');
        if ($lengthContainer.length > 0 && !$lengthContainer.hasClass('dropdown-initialized')) {
            $lengthContainer.addClass('dropdown-initialized');
            
            var $select = $lengthContainer.find('select');
            var currentLength = api.page.len();
            
            $select.hide();
            
            var dropdownHtml = `
                <div class="dropdown d-inline-block mx-1">
                    <button class="btn btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: var(--text-main); border: 1px solid var(--border-color); background-color: var(--bg-card);">
                        <span class="current-len">${currentLength}</span>
                    </button>
                    <ul class="dropdown-menu shadow-sm" style="min-width: 5rem;">
                        <li><a class="dropdown-item" href="#" data-len="10">10</a></li>
                        <li><a class="dropdown-item" href="#" data-len="25">25</a></li>
                        <li><a class="dropdown-item" href="#" data-len="50">50</a></li>
                        <li><a class="dropdown-item" href="#" data-len="100">100</a></li>
                    </ul>
                </div>
            `;
            
            $select.after(dropdownHtml);
            
            var $dropdown = $lengthContainer.find('.dropdown');
            $dropdown.find('.dropdown-item').on('click', function(ev) {
                ev.preventDefault();
                var len = jQuery(this).data('len');
                api.page.len(len).draw();
                $dropdown.find('.current-len').text(len);
                $select.val(len);
            });
        }
    });
}
