<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Page Header -->
<div class="page-header animate-fade-up">
	<div class="page-header-info">
		<p class="page-header-breadcrumb">
			<i class="bi bi-house-door" aria-hidden="true"></i>
			<span class="sep">›</span>
			<a href="<?= base_url('admin/perfis') ?>">Perfis e Permissões</a>
			<span class="sep">›</span>
			<span><?= $role ? 'Editar Perfil' : 'Novo Perfil' ?></span>
		</p>
		<h1 class="page-header-title"><?= $role ? 'Editar Perfil' : 'Novo Perfil' ?></h1>
		<p class="page-header-subtitle"><?= $role ? 'Atualize as informações e permissões do perfil.' : 'Defina o nome, identificador e permissões do novo perfil.' ?></p>
	</div>
	<div class="page-header-actions">
		<a href="<?= base_url('admin/perfis') ?>" class="btn-theme-primary-outline">
			<i class="bi bi-arrow-left" aria-hidden="true"></i> Voltar
		</a>
	</div>
</div>

<?php if (isset($error)): ?>
	<div class="alert-flash alert-flash-danger alert-dismissible fade show mb-4 animate-fade-up" role="alert" aria-live="polite">
		<i class="bi bi-exclamation-circle-fill alert-flash-icon" aria-hidden="true"></i>
		<div class="alert-flash-body"><?= htmlspecialchars($error) ?></div>
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
	</div>
<?php endif; ?>

<?php if (validation_errors()): ?>
	<div class="alert-flash alert-flash-danger alert-dismissible fade show mb-4 animate-fade-up" role="alert" aria-live="polite">
		<i class="bi bi-exclamation-circle-fill alert-flash-icon" aria-hidden="true"></i>
		<div class="alert-flash-body"><?= validation_errors() ?></div>
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
	</div>
<?php endif; ?>

<form method="post" action="<?= $role ? base_url('admin/perfis/editar/' . $role->get_id()) : base_url('admin/perfis/novo') ?>">
	<div class="card-theme-form animate-fade-up animate-delay-1">

		<!-- Seção: Dados do Perfil -->
		<div class="form-section">
			<div class="form-section-header">
				<i class="bi bi-shield-check" aria-hidden="true"></i>
				<h2 class="form-section-title">Dados do Perfil</h2>
			</div>

			<div class="row mb-3">
				<div class="col-md-6">
					<label for="name" class="form-label">Nome <span class="text-danger" aria-hidden="true">*</span></label>
					<input type="text"
					       class="form-control <?= form_error('name') ? 'is-invalid' : '' ?>"
					       id="name"
					       name="name"
					       value="<?= htmlspecialchars($role ? $role->get_name() : set_value('name')) ?>"
					       autocomplete="off"
					       required>
					<?php if (form_error('name')): ?>
						<div class="invalid-feedback"><?= form_error('name') ?></div>
					<?php endif; ?>
				</div>
				<div class="col-md-6">
					<label for="slug" class="form-label">Slug <span class="text-danger" aria-hidden="true">*</span></label>
					<input type="text"
					       class="form-control <?= form_error('slug') ? 'is-invalid' : '' ?>"
					       id="slug"
					       name="slug"
					       value="<?= htmlspecialchars($role ? $role->get_slug() : set_value('slug')) ?>"
					       autocomplete="off"
					       spellcheck="false"
					       required>
					<?php if (form_error('slug')): ?>
						<div class="invalid-feedback"><?= form_error('slug') ?></div>
					<?php endif; ?>
				</div>
			</div>

			<div class="row">
				<div class="col-md-12">
					<label for="description" class="form-label">Descrição</label>
					<textarea class="form-control"
					          id="description"
					          name="description"
					          rows="2"><?= htmlspecialchars($role ? $role->get_description() ?? '' : set_value('description')) ?></textarea>
				</div>
			</div>
		</div>

		<!-- Seção: Permissões -->
		<div class="form-section">
			<div class="form-section-header">
				<i class="bi bi-key" aria-hidden="true"></i>
				<h2 class="form-section-title">Permissões</h2>
			</div>

			<?php if (empty($permissions)): ?>
				<p class="text-theme-muted mb-0">Nenhuma permissão disponível. Crie permissões primeiro.</p>
			<?php else: ?>
				<div class="row">
					<?php foreach ($permissions as $permission): ?>
						<div class="col-md-4 mb-2">
							<div class="form-check">
								<input class="form-check-input"
								       type="checkbox"
								       name="permission_ids[]"
								       id="perm_<?= $permission->get_id() ?>"
								       value="<?= $permission->get_id() ?>"
								       <?= $role && in_array($permission->get_id(), $role->get_permission_ids()) ? 'checked' : '' ?>>
								<label class="form-check-label" for="perm_<?= $permission->get_id() ?>">
									<?= htmlspecialchars($permission->get_name()) ?>
								</label>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<!-- Ações -->
		<div class="form-actions">
			<button type="submit" class="btn-theme-primary">
				<i class="bi bi-check-lg" aria-hidden="true"></i>
				<?= $role ? 'Salvar Alterações' : 'Criar Perfil' ?>
			</button>
			<a href="<?= base_url('admin/perfis') ?>" class="btn-theme-secondary">Cancelar</a>
		</div>
	</div>
</form>
