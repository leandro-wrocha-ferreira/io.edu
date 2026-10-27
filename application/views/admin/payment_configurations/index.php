<div class="container-fluid px-0">
	<!-- Page Header -->
	<div class="page-header animate-fade-up">
		<div class="page-header-info">
			<nav class="page-header-breadcrumb" aria-label="Breadcrumb">
				<a href="<?= base_url('admin/painel') ?>">Painel</a>
				<span class="sep" aria-hidden="true">/</span>
				<span class="active" aria-current="page">Configurações de Pagamento</span>
			</nav>
			<h1 class="page-header-title">Configurações de Pagamento</h1>
			<p class="page-header-subtitle">Cadastre e configure suas contas de provedores e adquirentes (Stripe, Asaas, etc.) para faturamento dos cursos.</p>
		</div>
		<div class="page-header-actions">
			<a href="<?= base_url('admin/configuracoes-pagamento/novo') ?>" class="edu-btn edu-btn-primary">
				<i class="bi bi-plus-lg me-1" aria-hidden="true"></i> Nova Configuração
			</a>
		</div>
	</div>

	<?php if ($this->session->flashdata('success')): ?>
		<div class="alert alert-success alert-dismissible fade show animate-fade-up" role="alert">
			<i class="bi bi-check-circle-fill me-2" aria-hidden="true"></i>
			<?= html_escape($this->session->flashdata('success')) ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
		</div>
	<?php endif; ?>

	<?php if ($this->session->flashdata('error')): ?>
		<div class="alert alert-danger alert-dismissible fade show animate-fade-up" role="alert">
			<i class="bi bi-exclamation-triangle-fill me-2" aria-hidden="true"></i>
			<?= html_escape($this->session->flashdata('error')) ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
		</div>
	<?php endif; ?>

	<!-- Data Table Card with Decoupled Toolbar -->
	<div class="edu-card edu-table-card animate-fade-up animate-delay-1">
		<div class="edu-data-toolbar">
			<div class="edu-toolbar-start">
				<div class="edu-search-box">
					<i class="bi bi-search edu-search-icon" aria-hidden="true"></i>
					<input type="search" class="edu-search-input" id="payment-configs-search-input" placeholder="Buscar por perfil, provedor ou conta..." aria-label="Buscar configurações de pagamento">
				</div>
			</div>
			<div class="edu-toolbar-end">
				<div class="edu-toolbar-count" id="payment-configs-count" aria-live="polite">
					Carregando contagem...
				</div>
			</div>
		</div>

		<div class="edu-table-responsive">
			<table class="edu-data-table" id="payment-configs-table">
				<thead>
					<tr>
						<th style="width: 70px;">ID</th>
						<th>Perfil / Configuração</th>
						<th>Provedor & Ambiente</th>
						<th>Formas de Pagamento</th>
						<th class="text-center" style="width: 110px;">Status</th>
						<th class="text-center" style="width: 110px;">Ações</th>
					</tr>
				</thead>
				<tbody>
					<!-- Preenchido via DataTables Server-Side -->
				</tbody>
			</table>
		</div>
	</div>
</div>
