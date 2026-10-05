<div class="container-fluid px-0">
	<!-- Page Header -->
	<div class="page-header animate-fade-up">
		<div class="page-header-info">
			<nav class="page-header-breadcrumb" aria-label="Breadcrumb">
				<a href="<?= base_url('admin/painel') ?>">Painel</a>
				<span class="sep" aria-hidden="true">/</span>
				<span class="active" aria-current="page">Categorias</span>
			</nav>
			<h1 class="page-header-title">Categorias Pedagógicas</h1>
			<p class="page-header-subtitle">Gerencie as categorias utilizadas para organizar o catálogo e facilitar a navegação dos alunos.</p>
		</div>
		<div class="page-header-actions">
			<a href="<?= base_url('admin/categorias/nova') ?>" class="edu-btn edu-btn-primary">
				<i class="bi bi-plus-lg me-1" aria-hidden="true"></i> Nova Categoria
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
					<input type="search" class="edu-search-input" id="categories-search-input" placeholder="Buscar categoria por nome ou slug..." aria-label="Buscar categorias">
				</div>
			</div>
			<div class="edu-toolbar-end">
				<div class="edu-toolbar-count" id="categories-count" aria-live="polite">
					Carregando contagem...
				</div>
			</div>
		</div>

		<div class="edu-table-responsive">
			<table class="edu-data-table" id="categories-table">
				<thead>
					<tr>
						<th style="width: 80px;">ID</th>
						<th>Nome da Categoria</th>
						<th>Slug Amigável</th>
						<th class="text-center" style="width: 120px;">Status</th>
						<th style="width: 160px;">Cadastrado em</th>
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
