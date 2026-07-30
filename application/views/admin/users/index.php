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
