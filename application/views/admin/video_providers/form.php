<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Page Header -->
<div class="page-header animate-fade-up">
	<div class="page-header-info">
		<p class="page-header-breadcrumb">
			<i class="bi bi-house-door" aria-hidden="true"></i>
			<span class="sep">›</span>
			<a href="<?= base_url('admin/provedores-video') ?>">Provedores de Vídeo</a>
			<span class="sep">›</span>
			<span><?= $provider ? 'Editar Provedor' : 'Novo Provedor' ?></span>
		</p>
		<h1 class="page-header-title"><?= $provider ? 'Editar Provedor' : 'Novo Provedor' ?></h1>
		<p class="page-header-subtitle"><?= $provider ? 'Atualize as chaves de API.' : 'Cadastre um novo provedor de vídeos (ex: Vimeo).' ?></p>
	</div>
	<div class="page-header-actions">
		<a href="<?= base_url('admin/provedores-video') ?>" class="btn-theme-primary-outline">
			<i class="bi bi-arrow-left" aria-hidden="true"></i> Voltar
		</a>
	</div>
</div>

<?php if (validation_errors()): ?>
	<div class="alert alert-flash alert-flash-danger alert-dismissible fade show mb-4 animate-fade-up" role="alert" aria-live="polite">
		<i class="bi bi-exclamation-circle-fill alert-flash-icon" aria-hidden="true"></i>
		<div class="alert-flash-body"><?= validation_errors() ?></div>
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
	</div>
<?php endif; ?>

<form method="post" action="<?= $provider ? base_url('admin/provedores-video/editar/' . $provider->id) : base_url('admin/provedores-video/novo') ?>">
	<div class="card-theme-form animate-fade-up animate-delay-1">

		<div class="form-section">
			<div class="form-section-header">
				<i class="bi bi-camera-video" aria-hidden="true"></i>
				<h2 class="form-section-title">Dados de Integração</h2>
			</div>

			<div class="row mb-3">
				<div class="col-md-6">
					<label for="name" class="form-label">Nome <span class="text-danger" aria-hidden="true">*</span></label>
					<input type="text"
					       class="form-control <?= form_error('name') ? 'is-invalid' : '' ?>"
					       id="name"
					       name="name"
					       value="<?= htmlspecialchars($provider ? $provider->name : set_value('name')) ?>"
					       required>
					<?php if (form_error('name')): ?>
						<div class="invalid-feedback"><?= form_error('name') ?></div>
					<?php endif; ?>
				</div>
				<div class="col-md-6">
					<label for="api_key" class="form-label">API Key / Token</label>
					<input type="text"
					       class="form-control <?= form_error('api_key') ? 'is-invalid' : '' ?>"
					       id="api_key"
					       name="api_key"
					       value="<?= htmlspecialchars($provider ? $provider->api_key : set_value('api_key')) ?>">
					<?php if (form_error('api_key')): ?>
						<div class="invalid-feedback"><?= form_error('api_key') ?></div>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<div class="form-actions">
			<button type="submit" class="btn-theme-primary">
				<i class="bi bi-check-lg" aria-hidden="true"></i>
				<?= $provider ? 'Salvar Alterações' : 'Criar Provedor' ?>
			</button>
			<a href="<?= base_url('admin/provedores-video') ?>" class="btn-theme-secondary">Cancelar</a>
		</div>
	</div>
</form>
