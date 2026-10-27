<?php
/**
 * @var \app\domain\finance\CostCenter|null $cost_center
 */
?>
<div class="container-fluid px-0">
	<!-- Page Header -->
	<div class="page-header animate-fade-up">
		<div class="page-header-info">
			<nav class="page-header-breadcrumb" aria-label="Breadcrumb">
				<a href="<?= base_url('admin/painel') ?>">Painel</a>
				<span class="sep" aria-hidden="true">/</span>
				<a href="<?= base_url('admin/centros-de-custo') ?>">Centros de Custo</a>
				<span class="sep" aria-hidden="true">/</span>
				<span class="active" aria-current="page"><?= !empty($cost_center) ? 'Editar' : 'Novo' ?></span>
			</nav>
			<h1 class="page-header-title"><?= !empty($cost_center) ? 'Editar Centro de Custo' : 'Novo Centro de Custo' ?></h1>
			<p class="page-header-subtitle">Preencha os dados do centro de custo para associação financeira e contábil aos produtos e cursos.</p>
		</div>
		<div class="page-header-actions">
			<a href="<?= base_url('admin/centros-de-custo') ?>" class="edu-btn edu-btn-outline">
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
		<form action="<?= current_url() ?>" method="POST" id="cost-center-form">
			<div class="edu-form-section">
				<div class="edu-form-section-header">
					<div class="edu-form-section-icon">
						<i class="bi bi-diagram-3" aria-hidden="true"></i>
					</div>
					<div>
						<h2 class="edu-form-section-title">Dados do Centro de Custo</h2>
						<p class="edu-form-section-desc">Nomenclatura do departamento ou projeto para classificação financeira.</p>
					</div>
				</div>

				<div class="row g-3">
					<div class="col-12 edu-form-field">
						<label class="edu-form-label edu-label-required" for="cost-center-name">Nome do Centro de Custo</label>
						<input type="text"
						       class="form-control edu-form-control"
						       id="cost-center-name"
						       name="name"
						       value="<?= html_escape(set_value('name', !empty($cost_center) ? $cost_center->get_name() : '')) ?>"
						       placeholder="Ex: Tecnologia & Produtos Digitais"
						       maxlength="150"
						       required>
						<span class="edu-form-hint">Nome amigável exibido nas configurações comerciais dos cursos.</span>
					</div>

					<div class="col-12 edu-form-field">
						<label class="edu-form-label" for="cost-center-description">Descrição / Finalidade</label>
						<input type="text"
						       class="form-control edu-form-control"
						       id="cost-center-description"
						       name="description"
						       value="<?= html_escape(set_value('description', !empty($cost_center) ? $cost_center->get_description() : '')) ?>"
						       placeholder="Ex: Faturamento e alocação de receitas dos cursos da área de tecnologia."
						       maxlength="255">
						<span class="edu-form-hint">Opcional. Detalhamento adicional sobre a destinação deste centro de custo.</span>
					</div>

					<div class="col-12 edu-form-field mt-3">
						<div class="form-check form-switch">
							<?php
								$is_checked = true;
								if (!empty($cost_center)) {
									$is_checked = $cost_center->is_active();
								}
								if ($this->input->method() === 'post') {
									$is_checked = ($this->input->post('is_active') === '1');
								}
							?>
							<input class="form-check-input" type="checkbox" role="switch" id="cost-center-active" name="is_active" value="1" <?= $is_checked ? 'checked' : '' ?>>
							<label class="form-check-label fw-semibold" for="cost-center-active">Centro de Custo Ativo</label>
						</div>
						<span class="edu-form-hint">Centros de custo inativos não podem ser vinculados a novos cursos ou produtos.</span>
					</div>
				</div>
			</div>

			<!-- Action Buttons -->
			<div class="edu-form-actions">
				<a href="<?= base_url('admin/centros-de-custo') ?>" class="edu-btn edu-btn-secondary">Cancelar</a>
				<button type="submit" class="edu-btn edu-btn-primary" id="btn-submit">
					<i class="bi bi-check-lg me-1" aria-hidden="true"></i> Salvar Centro de Custo
				</button>
			</div>
		</form>
	</div>
</div>
