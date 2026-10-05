<?php
/**
 * @var array<\app\domain\course\Category> $categories
 */
?>
<div class="container-fluid px-0">
	<!-- Page Header -->
	<div class="page-header animate-fade-up">
		<div class="page-header-info">
			<nav class="page-header-breadcrumb" aria-label="Breadcrumb">
				<a href="<?= base_url('admin/painel') ?>">Painel</a>
				<span class="sep" aria-hidden="true">/</span>
				<span class="active" aria-current="page">Cursos</span>
			</nav>
			<h1 class="page-header-title">Gerenciamento de Cursos</h1>
			<p class="page-header-subtitle">Gerencie o catálogo de cursos, regras de acesso do aluno e diretrizes de certificação.</p>
		</div>
		<div class="page-header-actions">
			<a href="<?= base_url('admin/cursos/novo') ?>" class="edu-btn edu-btn-primary">
				<i class="bi bi-plus-lg me-1" aria-hidden="true"></i> Novo Curso
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
					<input type="search" class="edu-search-input" id="courses-search-input" placeholder="Buscar curso por título, slug ou categoria..." aria-label="Buscar cursos">
				</div>
			</div>
			<div class="edu-toolbar-end">
				<select class="edu-toolbar-filter" id="courses-category-filter" aria-label="Filtrar por categoria">
					<option value="">Todas as categorias</option>
					<?php if (!empty($categories)): ?>
						<?php foreach ($categories as $category): ?>
							<option value="<?= $category->get_id() ?>"><?= html_escape($category->get_name()) ?></option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select>

				<select class="edu-toolbar-filter" id="courses-status-filter" aria-label="Filtrar por status">
					<option value="">Todos os status</option>
					<option value="draft">Rascunhos</option>
					<option value="active">Ativos</option>
					<option value="archived">Arquivados</option>
				</select>

				<div class="edu-toolbar-count" id="courses-count" aria-live="polite">
					Carregando contagem...
				</div>
			</div>
		</div>

		<div class="edu-table-responsive">
			<table class="edu-data-table" id="courses-table">
				<thead>
					<tr>
						<th style="width: 70px;">ID</th>
						<th>Curso</th>
						<th style="width: 180px;">Categoria</th>
						<th style="width: 150px;">Acesso</th>
						<th style="width: 100px;">Carga</th>
						<th class="text-center" style="width: 110px;">Status</th>
						<th class="text-center" style="width: 130px;">Ações</th>
					</tr>
				</thead>
				<tbody>
					<!-- Preenchido via DataTables Server-Side -->
				</tbody>
			</table>
		</div>
	</div>
</div>
