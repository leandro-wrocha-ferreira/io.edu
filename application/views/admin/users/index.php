<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Page Header -->
<div class="page-header animate-fade-up">
	<div class="page-header-info">
		<p class="page-header-breadcrumb">
			<i class="bi bi-house-door" aria-hidden="true"></i>
			<span class="sep">›</span>
			<span>Administração</span>
			<span class="sep">›</span>
			<span>Usuários</span>
		</p>
		<h1 class="page-header-title">Gestão de Usuários</h1>
		<p class="page-header-subtitle">Gerencie os usuários cadastrados na plataforma.</p>
	</div>
	<div class="page-header-actions">
		<a href="<?= base_url('admin/usuarios/novo') ?>" class="btn-theme-primary-outline">
			<i class="bi bi-plus-lg" aria-hidden="true"></i> Novo Usuário
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
		<table class="table table-hover align-middle w-100" id="users-table" aria-label="Lista de usuários">
			<thead class="table-head-brand">
				<tr>
					<th class="d-none d-md-table-cell" scope="col">ID</th>
					<th scope="col">Nome</th>
					<th scope="col">Email</th>
					<th class="d-none d-lg-table-cell" scope="col">Perfil</th>
					<th class="d-none d-md-table-cell" scope="col">Criado em</th>
					<th class="text-center" scope="col">Status</th>
					<th class="text-center" scope="col">Ações</th>
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
</script>
