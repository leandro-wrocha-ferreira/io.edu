<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-theme-heading">Usuários</h1>
    <a href="<?= base_url('admin/usuarios/novo') ?>" class="btn-theme-primary">
        <i class="bi bi-plus-lg"></i> Novo Usuário
    </a>
</div>

<?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($this->session->flashdata('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($this->session->flashdata('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card card-theme">
    <div class="card-body">
        <table class="table table-hover align-middle w-100" id="users-table">
            <thead>
                <tr>
                    <th class="d-none d-md-table-cell">ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th class="d-none d-lg-table-cell">Perfil</th>
                    <th class="d-none d-md-table-cell">Criado em</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    jQuery('#users-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= site_url('admin/usuarios/dados') ?>',
            type: 'GET'
        },
        columns: [
            { data: 'id', width: '60px', className: 'd-none d-md-table-cell' },
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
        dom: '<"row"<"col-sm-6"l><"col-sm-6"f>>rtip',
        drawCallback: function () {
            var theme = document.documentElement.getAttribute('data-theme') || 'light';
            var wrapper = this.closest('.dataTables_wrapper')[0];
            if (wrapper) {
                wrapper.style.setProperty('--dt-bg', 'var(--bg-card)');
                wrapper.style.setProperty('--dt-color', 'var(--text-main)');
            }
        }
    });
});
</script>
