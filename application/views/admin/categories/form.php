<?php
/**
 * @var \app\domain\course\Category|null $category
 */
?>
<div class="container-fluid px-0">
	<!-- Page Header -->
	<div class="page-header animate-fade-up">
		<div class="page-header-info">
			<nav class="page-header-breadcrumb" aria-label="Breadcrumb">
				<a href="<?= base_url('admin/painel') ?>">Painel</a>
				<span class="sep" aria-hidden="true">/</span>
				<a href="<?= base_url('admin/categorias') ?>">Categorias</a>
				<span class="sep" aria-hidden="true">/</span>
				<span class="active" aria-current="page"><?= !empty($category) ? 'Editar' : 'Nova' ?></span>
			</nav>
			<h1 class="page-header-title"><?= !empty($category) ? 'Editar Categoria' : 'Nova Categoria' ?></h1>
			<p class="page-header-subtitle">Preencha os dados da categoria para classificação dos cursos na vitrine.</p>
		</div>
		<div class="page-header-actions">
			<a href="<?= base_url('admin/categorias') ?>" class="edu-btn edu-btn-outline">
				<i class="bi bi-arrow-left me-1" aria-hidden="true"></i> Voltar
			</a>
		</div>
	</div>

	<?php if ($this->session->flashdata('error')): ?>
		<div class="alert alert-danger alert-dismissible fade show animate-fade-up" role="alert">
			<i class="bi bi-exclamation-triangle-fill me-2" aria-hidden="true"></i>
			<?= html_escape($this->session->flashdata('error')) ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
		</div>
	<?php endif; ?>

	<?php if (validation_errors()): ?>
		<div class="alert alert-danger alert-dismissible fade show animate-fade-up" role="alert">
			<i class="bi bi-exclamation-triangle-fill me-2" aria-hidden="true"></i>
			<div class="small"><?= validation_errors() ?></div>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
		</div>
	<?php endif; ?>

	<div class="edu-form-card animate-fade-up animate-delay-1">
		<form action="<?= current_url() ?>" method="POST" id="category-form">
			<div class="edu-form-section">
				<div class="edu-form-section-header">
					<div class="edu-form-section-icon">
						<i class="bi bi-tag" aria-hidden="true"></i>
					</div>
					<div>
						<h2 class="edu-form-section-title">Identificação da Categoria</h2>
						<p class="edu-form-section-desc">Nome de exibição e identificador amigável na URL do catálogo.</p>
					</div>
				</div>

				<div class="row g-3">
					<div class="col-md-6 edu-form-field">
						<label class="edu-form-label edu-label-required" for="category-name">Nome da Categoria</label>
						<input type="text"
						       class="form-control edu-form-control"
						       id="category-name"
						       name="name"
						       value="<?= html_escape(set_value('name', !empty($category) ? $category->get_name() : '')) ?>"
						       placeholder="Ex: Programação & Desenvolvimento"
						       required>
						<span class="edu-form-hint">Nome principal exibido na vitrine e nos filtros de pesquisa.</span>
					</div>

					<div class="col-md-6 edu-form-field">
						<label class="edu-form-label" for="category-slug">Slug na URL</label>
						<input type="text"
						       class="form-control edu-form-control"
						       id="category-slug"
						       name="slug"
						       value="<?= html_escape(set_value('slug', !empty($category) ? $category->get_slug() : '')) ?>"
						       placeholder="Ex: programacao-desenvolvimento">
						<span class="edu-form-hint">Deixe em branco para gerar automaticamente a partir do nome.</span>
					</div>

					<div class="col-md-6 edu-form-field">
						<label class="edu-form-label edu-label-required" for="category-status">Status</label>
						<select class="form-select edu-form-select" id="category-status" name="status" required>
							<?php $curr_status = set_value('status', !empty($category) ? $category->get_status() : 'active'); ?>
							<option value="active" <?= $curr_status === 'active' ? 'selected' : '' ?>>Ativa</option>
							<option value="inactive" <?= $curr_status === 'inactive' ? 'selected' : '' ?>>Inativa</option>
						</select>
						<span class="edu-form-hint small" id="category-status-hint">
							<?= $curr_status === 'inactive' ? 'Oculta do catálogo público e novos cadastros.' : 'Visível para organização e filtros na vitrine.' ?>
						</span>
					</div>
				</div>
			</div>

			<!-- Action Buttons -->
			<div class="edu-form-actions">
				<a href="<?= base_url('admin/categorias') ?>" class="edu-btn edu-btn-secondary">Cancelar</a>
				<button type="submit" class="edu-btn edu-btn-primary" id="btn-submit">
					<i class="bi bi-check-lg me-1" aria-hidden="true"></i> Salvar Categoria
				</button>
			</div>
		</form>
	</div>
</div>
