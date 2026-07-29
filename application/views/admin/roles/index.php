<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Page Header -->
<div class="page-header animate-fade-up">
	<div class="page-header-info">
		<p class="page-header-breadcrumb">
			<i class="bi bi-house-door" aria-hidden="true"></i>
			<span class="sep">›</span>
			<span>Administração</span>
			<span class="sep">›</span>
			<span>Perfis e Permissões</span>
		</p>
		<h1 class="page-header-title">Perfis de Acesso</h1>
		<p class="page-header-subtitle">Gerencie os perfis e permissões do sistema.</p>
	</div>
	<div class="page-header-actions">
		<a href="<?= base_url('admin/perfis/novo') ?>" class="btn-theme-primary-outline">
			<i class="bi bi-plus-lg" aria-hidden="true"></i> Novo Perfil
		</a>
	</div>
</div>

<?php if ($this->session->flashdata('success')): ?>
	<div class="alert-flash alert-flash-success alert-dismissible fade show animate-fade-up" role="alert" aria-live="polite">
		<i class="bi bi-check-circle-fill alert-flash-icon" aria-hidden="true"></i>
		<div class="alert-flash-body"><?= htmlspecialchars($this->session->flashdata('success')) ?></div>
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
	</div>
<?php endif; ?>

<?php if ($this->session->flashdata('error')): ?>
	<div class="alert-flash alert-flash-danger alert-dismissible fade show animate-fade-up" role="alert" aria-live="polite">
		<i class="bi bi-exclamation-circle-fill alert-flash-icon" aria-hidden="true"></i>
		<div class="alert-flash-body"><?= htmlspecialchars($this->session->flashdata('error')) ?></div>
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
	</div>
<?php endif; ?>

<div class="card card-theme animate-fade-up animate-delay-1">
	<div class="card-body">
		<table class="table table-hover align-middle w-100" id="roles-table" aria-label="Lista de perfis de acesso">
			<thead class="table-head-brand">
				<tr>
					<th class="d-none d-md-table-cell" scope="col">ID</th>
					<th scope="col">Nome</th>
					<th class="d-none d-md-table-cell" scope="col">Slug</th>
					<th class="d-none d-lg-table-cell" scope="col">Descrição</th>
					<th class="d-none d-md-table-cell" scope="col">Criado em</th>
					<th class="text-center" scope="col">Ações</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
	jQuery('#roles-table').DataTable({
		processing: true,
		serverSide: true,
		ajax: {
			url: '<?= site_url('admin/perfis/dados') ?>',
			type: 'GET'
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
</script>
