<!-- Operational Page Header -->
<div class="page-header animate-fade-up">
	<div class="page-header-info">
		<nav class="page-header-breadcrumb" aria-label="Navegação estrutural">
			<a href="<?= base_url('admin/painel') ?>"><i class="bi bi-house-door" aria-hidden="true"></i> Início</a>
			<span class="sep" aria-hidden="true">›</span>
			<span>Administração</span>
			<span class="sep" aria-hidden="true">›</span>
			<span class="active" aria-current="page">Perfis e Permissões</span>
		</nav>
		<h1 class="page-header-title">Perfis de Acesso</h1>
		<p class="page-header-subtitle">Defina regras e escopos de permissões para grupos de usuários.</p>
	</div>
	<div class="page-header-actions">
		<a href="<?= base_url('admin/perfis/novo') ?>" class="edu-btn edu-btn-primary">
			<i class="bi bi-shield-plus" aria-hidden="true"></i> Novo Perfil
		</a>
	</div>
</div>

<!-- Flash Notifications -->
<?php if ($this->session->flashdata('success')): ?>
	<div class="alert alert-flash alert-flash-success alert-dismissible fade show animate-fade-up" role="alert" aria-live="polite">
		<i class="bi bi-check-circle-fill alert-flash-icon" aria-hidden="true"></i>
		<div class="alert-flash-body"><?= html_escape($this->lang->line($this->session->flashdata('success')) ?? $this->session->flashdata('success')) ?></div>
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar notificação"></button>
	</div>
<?php endif; ?>

<?php if ($this->session->flashdata('error')): ?>
	<div class="alert alert-flash alert-flash-danger alert-dismissible fade show animate-fade-up" role="alert" aria-live="polite">
		<i class="bi bi-exclamation-circle-fill alert-flash-icon" aria-hidden="true"></i>
		<div class="alert-flash-body"><?= html_escape($this->session->flashdata('error')) ?></div>
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar notificação"></button>
	</div>
<?php endif; ?>

<!-- Operational Data Table Container -->
<div class="edu-table-card animate-fade-up animate-delay-1">
	<!-- Integrated Toolbar -->
	<div class="edu-data-toolbar">
		<div class="edu-toolbar-start">
			<div class="edu-search-box">
				<i class="bi bi-search edu-search-icon" aria-hidden="true"></i>
				<input type="text"
				       class="edu-search-input"
				       id="roles-search-input"
				       placeholder="Buscar por nome ou slug..."
				       aria-label="Buscar perfis de acesso">
			</div>
		</div>
		<div class="edu-toolbar-end">
			<span class="edu-toolbar-count" id="roles-count" aria-live="polite">Carregando registros...</span>
		</div>
	</div>

	<!-- Responsive Table -->
	<div class="edu-table-responsive">
		<table class="table edu-data-table align-middle w-100" id="roles-table" aria-label="Lista de perfis de acesso">
			<thead>
				<tr>
					<th class="d-none d-md-table-cell" scope="col" style="width: 70px;">ID</th>
					<th scope="col" style="width: 200px;">Nome</th>
					<th class="d-none d-md-table-cell" scope="col" style="width: 160px;">Slug</th>
					<th class="d-none d-lg-table-cell" scope="col">Descrição</th>
					<th class="d-none d-md-table-cell" scope="col" style="width: 160px;">Criado em</th>
					<th class="text-center" scope="col" style="width: 100px;">Ações</th>
				</tr>
			</thead>
			<tbody>
				<!-- Populated via Server-Side DataTables -->
			</tbody>
		</table>
	</div>
</div>
