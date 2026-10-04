<!-- Operational Page Header -->
<div class="page-header animate-fade-up">
	<div class="page-header-info">
		<nav class="page-header-breadcrumb" aria-label="Navegação estrutural">
			<a href="<?= base_url('admin/painel') ?>"><i class="bi bi-house-door" aria-hidden="true"></i> Início</a>
			<span class="sep" aria-hidden="true">›</span>
			<span>Administração</span>
			<span class="sep" aria-hidden="true">›</span>
			<span class="active" aria-current="page">Usuários</span>
		</nav>
		<h1 class="page-header-title">Gestão de Usuários</h1>
		<p class="page-header-subtitle">Consulte, filtre e gerencie as contas de acesso da instituição.</p>
	</div>
	<div class="page-header-actions">
		<a href="<?= base_url('admin/usuarios/novo') ?>" class="edu-btn edu-btn-primary">
			<i class="bi bi-person-plus-fill" aria-hidden="true"></i> Novo Usuário
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
				       id="users-search-input"
				       placeholder="Buscar por nome ou email..."
				       aria-label="Buscar usuários cadastrados">
			</div>
			<div class="d-none d-sm-flex align-items-center gap-2">
				<select class="edu-toolbar-filter" id="users-status-filter" aria-label="Filtrar por status">
					<option value="">Todos os status</option>
					<option value="Ativo">Apenas ativos</option>
					<option value="Inativo">Apenas inativos</option>
				</select>
			</div>
		</div>
		<div class="edu-toolbar-end">
			<span class="edu-toolbar-count" id="users-count" aria-live="polite">Carregando registros...</span>
		</div>
	</div>

	<!-- Responsive Table -->
	<div class="edu-table-responsive">
		<table class="table edu-data-table align-middle w-100" id="users-table" aria-label="Lista de usuários cadastrados">
			<thead>
				<tr>
					<th class="d-none d-md-table-cell" scope="col" style="width: 70px;">ID</th>
					<th scope="col">Nome</th>
					<th scope="col">Email</th>
					<th class="d-none d-lg-table-cell" scope="col" style="width: 140px;">Perfil</th>
					<th class="d-none d-md-table-cell" scope="col" style="width: 150px;">Criado em</th>
					<th class="text-center" scope="col" style="width: 110px;">Status</th>
					<th class="text-center" scope="col" style="width: 110px;">Ações</th>
				</tr>
			</thead>
			<tbody>
				<!-- Populated via Server-Side DataTables -->
			</tbody>
		</table>
	</div>
</div>
