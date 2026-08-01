<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Page Header -->
<div class="page-header animate-fade-up">
	<div class="page-header-info">
		<p class="page-header-breadcrumb">
			<i class="bi bi-house-door" aria-hidden="true"></i>
			<span class="sep">›</span>
			<a href="<?= base_url('admin/usuarios') ?>">Usuários</a>
			<span class="sep">›</span>
			<span><?= $user ? 'Editar Usuário' : 'Novo Usuário' ?></span>
		</p>
		<h1 class="page-header-title"><?= $user ? 'Editar Usuário' : 'Novo Usuário' ?></h1>
		<p class="page-header-subtitle"><?= $user ? 'Atualize os dados do usuário.' : 'Preencha os dados para criar um novo usuário.' ?></p>
	</div>
	<div class="page-header-actions">
		<a href="<?= base_url('admin/usuarios') ?>" class="btn-theme-primary-outline">
			<i class="bi bi-arrow-left" aria-hidden="true"></i> Voltar
		</a>
	</div>
</div>

<?php if (isset($error)): ?>
	<div class="alert alert-flash alert-flash-danger alert-dismissible fade show mb-4 animate-fade-up" role="alert" aria-live="polite">
		<i class="bi bi-exclamation-circle-fill alert-flash-icon" aria-hidden="true"></i>
		<div class="alert-flash-body"><?= htmlspecialchars($error) ?></div>
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
	</div>
<?php endif; ?>

<?php if (validation_errors()): ?>
	<div class="alert alert-flash alert-flash-danger alert-dismissible fade show mb-4 animate-fade-up" role="alert" aria-live="polite">
		<i class="bi bi-exclamation-circle-fill alert-flash-icon" aria-hidden="true"></i>
		<div class="alert-flash-body"><?= validation_errors() ?></div>
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
	</div>
<?php endif; ?>

<form method="post" action="<?= $user ? base_url('admin/usuarios/editar/' . $user->get_id()) : base_url('admin/usuarios/novo') ?>">
	<div class="card-theme-form animate-fade-up animate-delay-1">

		<!-- Seção: Dados do Usuário -->
		<div class="form-section">
			<div class="form-section-header">
				<i class="bi bi-person" aria-hidden="true"></i>
				<h2 class="form-section-title">Dados do Usuário</h2>
			</div>

			<div class="row mb-3">
				<div class="col-md-6">
					<label for="name" class="form-label">Nome <span class="text-danger" aria-hidden="true">*</span></label>
					<input type="text"
					       class="form-control <?= form_error('name') ? 'is-invalid' : '' ?>"
					       id="name"
					       name="name"
					       value="<?= htmlspecialchars($user ? $user->get_name() : set_value('name')) ?>"
					       autocomplete="name"
					       required>
					<?php if (form_error('name')): ?>
						<div class="invalid-feedback"><?= form_error('name') ?></div>
					<?php endif; ?>
				</div>
				<div class="col-md-6">
					<label for="email" class="form-label">Email <span class="text-danger" aria-hidden="true">*</span></label>
					<input type="email"
					       class="form-control <?= form_error('email') ? 'is-invalid' : '' ?>"
					       id="email"
					       name="email"
					       value="<?= htmlspecialchars($user ? (string) $user->get_email() : set_value('email')) ?>"
					       autocomplete="email"
					       spellcheck="false"
					       required>
					<?php if (form_error('email')): ?>
						<div class="invalid-feedback"><?= form_error('email') ?></div>
					<?php endif; ?>
				</div>
			</div>

			<?php if (!$user): ?>
				<div class="row">
					<div class="col-md-6">
						<label for="password" class="form-label">Senha <span class="text-danger" aria-hidden="true">*</span></label>
						<input type="password"
						       class="form-control <?= form_error('password') ? 'is-invalid' : '' ?>"
						       id="password"
						       name="password"
						       autocomplete="new-password"
						       required
						       minlength="6">
						<?php if (form_error('password')): ?>
							<div class="invalid-feedback"><?= form_error('password') ?></div>
						<?php endif; ?>
					</div>
				</div>
			<?php else: ?>
				<div class="row">
					<div class="col-md-6">
						<label class="form-label">Senha</label>
						<div class="d-flex align-items-center gap-2">
							<button type="button" class="btn-theme-secondary" disabled title="Em desenvolvimento">
								<i class="bi bi-key" aria-hidden="true"></i> Redefinir Senha
							</button>
							<small class="text-theme-muted">Em desenvolvimento</small>
						</div>
					</div>
				</div>
			<?php endif; ?>
		</div>

		<!-- Seção: Perfil de Acesso -->
		<div class="form-section">
			<div class="form-section-header">
				<i class="bi bi-shield-check" aria-hidden="true"></i>
				<h2 class="form-section-title">Perfil de Acesso</h2>
			</div>

			<div class="row">
				<?php foreach ($roles as $role): ?>
					<div class="col-md-4 mb-2">
						<div class="form-check">
							<input class="form-check-input"
							       type="radio"
							       name="role_ids[]"
							       id="role_<?= $role->get_id() ?>"
							       value="<?= $role->get_id() ?>"
							       <?= $user && in_array($role->get_id(), $user->get_role_ids()) ? 'checked' : '' ?>>
							<label class="form-check-label" for="role_<?= $role->get_id() ?>">
								<?= htmlspecialchars($role->get_name()) ?>
							</label>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<!-- Ações -->
		<div class="form-actions">
			<button type="submit" class="btn-theme-primary">
				<i class="bi bi-check-lg" aria-hidden="true"></i>
				<?= $user ? 'Salvar Alterações' : 'Criar Usuário' ?>
			</button>
			<a href="<?= base_url('admin/usuarios') ?>" class="btn-theme-secondary">Cancelar</a>
		</div>
	</div>
</form>
