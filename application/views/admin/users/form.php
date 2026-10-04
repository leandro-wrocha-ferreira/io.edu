<!-- Operational Page Header -->
<div class="page-header animate-fade-up">
	<div class="page-header-info">
		<nav class="page-header-breadcrumb" aria-label="Navegação estrutural">
			<a href="<?= base_url('admin/painel') ?>"><i class="bi bi-house-door" aria-hidden="true"></i> Início</a>
			<span class="sep" aria-hidden="true">›</span>
			<a href="<?= base_url('admin/usuarios') ?>">Usuários</a>
			<span class="sep" aria-hidden="true">›</span>
			<span class="active" aria-current="page"><?= $user ? 'Editar Usuário' : 'Novo Usuário' ?></span>
		</nav>
		<h1 class="page-header-title"><?= $user ? 'Editar Usuário' : 'Novo Usuário' ?></h1>
		<p class="page-header-subtitle"><?= $user ? 'Atualize as credenciais e o perfil do usuário.' : 'Preencha as informações para registrar uma nova conta no LMS.' ?></p>
	</div>
	<div class="page-header-actions">
		<a href="<?= base_url('admin/usuarios') ?>" class="edu-btn edu-btn-ghost">
			<i class="bi bi-arrow-left" aria-hidden="true"></i> Voltar à Listagem
		</a>
	</div>
</div>

<!-- Flash Notifications -->
<?php if (isset($error)): ?>
	<div class="alert alert-flash alert-flash-danger alert-dismissible fade show mb-4 animate-fade-up" role="alert" aria-live="polite">
		<i class="bi bi-exclamation-circle-fill alert-flash-icon" aria-hidden="true"></i>
		<div class="alert-flash-body"><?= html_escape($error) ?></div>
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar notificação"></button>
	</div>
<?php endif; ?>

<?php if (validation_errors()): ?>
	<div class="alert alert-flash alert-flash-danger alert-dismissible fade show mb-4 animate-fade-up" role="alert" aria-live="polite">
		<i class="bi bi-exclamation-circle-fill alert-flash-icon" aria-hidden="true"></i>
		<div class="alert-flash-body">
			<strong>Atenção:</strong> Por favor, verifique os campos indicados abaixo.
		</div>
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar notificação"></button>
	</div>
<?php endif; ?>

<!-- Operational Form Container -->
<form method="post" action="<?= $user ? base_url('admin/usuarios/editar/' . $user->get_id()) : base_url('admin/usuarios/novo') ?>">
	<div class="edu-form-card animate-fade-up animate-delay-1">

		<!-- Seção 1: Dados Pessoais e Identificação -->
		<div class="edu-form-section">
			<div class="edu-form-section-header">
				<i class="bi bi-person-badge" aria-hidden="true"></i>
				<div>
					<h2 class="edu-form-section-title">Dados de Identificação</h2>
					<p class="edu-form-section-desc">Informações básicas da conta e comunicação.</p>
				</div>
			</div>

			<div class="row g-3">
				<div class="col-md-6 edu-form-field">
					<label for="name" class="edu-form-label">Nome Completo <span class="text-danger" aria-hidden="true">*</span></label>
					<input type="text"
					       class="form-control <?= form_error('name') ? 'is-invalid' : '' ?>"
					       id="name"
					       name="name"
					       value="<?= html_escape($user ? $user->get_name() : set_value('name')) ?>"
					       autocomplete="name"
					       aria-describedby="<?= form_error('name') ? 'name-error' : 'name-hint' ?>"
					       required>
					<?php if (form_error('name')): ?>
						<div class="invalid-feedback" id="name-error"><?= form_error('name') ?></div>
					<?php else: ?>
						<span class="edu-form-hint" id="name-hint">Nome exibido no certificado e no perfil.</span>
					<?php endif; ?>
				</div>

				<div class="col-md-6 edu-form-field">
					<label for="email" class="edu-form-label">Endereço de E-mail <span class="text-danger" aria-hidden="true">*</span></label>
					<input type="email"
					       class="form-control <?= form_error('email') ? 'is-invalid' : '' ?>"
					       id="email"
					       name="email"
					       value="<?= html_escape($user ? (string) $user->get_email() : set_value('email')) ?>"
					       autocomplete="email"
					       spellcheck="false"
					       aria-describedby="<?= form_error('email') ? 'email-error' : 'email-hint' ?>"
					       required>
					<?php if (form_error('email')): ?>
						<div class="invalid-feedback" id="email-error"><?= form_error('email') ?></div>
					<?php else: ?>
						<span class="edu-form-hint" id="email-hint">Utilizado para autenticação e notificações operacionais.</span>
					<?php endif; ?>
				</div>
			</div>

			<?php if (!$user): ?>
				<div class="row g-3 mt-1">
					<div class="col-md-6 edu-form-field">
						<label for="password" class="edu-form-label">Senha Inicial <span class="text-danger" aria-hidden="true">*</span></label>
						<input type="password"
						       class="form-control <?= form_error('password') ? 'is-invalid' : '' ?>"
						       id="password"
						       name="password"
						       autocomplete="new-password"
						       aria-describedby="<?= form_error('password') ? 'password-error' : 'password-hint' ?>"
						       required
						       minlength="6">
						<?php if (form_error('password')): ?>
							<div class="invalid-feedback" id="password-error"><?= form_error('password') ?></div>
						<?php else: ?>
							<span class="edu-form-hint" id="password-hint">Mínimo de 6 caracteres.</span>
						<?php endif; ?>
					</div>
				</div>
			<?php else: ?>
				<div class="row g-3 mt-1">
					<div class="col-md-6 edu-form-field">
						<label class="edu-form-label">Senha</label>
						<div class="d-flex align-items-center gap-2">
							<button type="button" class="edu-btn edu-btn-secondary btn-sm" disabled title="Funcionalidade em desenvolvimento">
								<i class="bi bi-key" aria-hidden="true"></i> Redefinir Senha
							</button>
							<span class="edu-form-hint mb-0 text-muted">A senha do usuário não é exibida por motivos de segurança.</span>
						</div>
					</div>
				</div>
			<?php endif; ?>
		</div>

		<!-- Seção 2: Perfil de Acesso e Permissões -->
		<div class="edu-form-section">
			<div class="edu-form-section-header">
				<i class="bi bi-shield-check" aria-hidden="true"></i>
				<div>
					<h2 class="edu-form-section-title">Perfil de Acesso</h2>
					<p class="edu-form-section-desc">Selecione o nível de privilégio e escopo deste usuário.</p>
				</div>
			</div>

			<div class="row g-3">
				<?php foreach ($roles as $role): ?>
					<div class="col-md-4">
						<div class="p-3 rounded-3 border h-100" style="background-color: var(--edu-bg-subtle);">
							<div class="form-check">
								<input class="form-check-input"
								       type="radio"
								       name="role_ids[]"
								       id="role_<?= $role->get_id() ?>"
								       value="<?= $role->get_id() ?>"
								       <?= (!empty($user_role_ids) && in_array($role->get_id(), $user_role_ids)) ? 'checked' : '' ?>>
								<label class="form-check-label fw-bold text-heading" for="role_<?= $role->get_id() ?>">
									<?= html_escape($role->get_name()) ?>
								</label>
							</div>
							<p class="small text-muted mb-0 mt-1 ps-4">
								<?= html_escape($role->get_description() ?: 'Acesso padrão do perfil.') ?>
							</p>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<!-- Ações de Formulário -->
		<div class="edu-form-actions">
			<a href="<?= base_url('admin/usuarios') ?>" class="edu-btn edu-btn-secondary">Cancelar</a>
			<button type="submit" class="edu-btn edu-btn-primary">
				<i class="bi bi-check-lg" aria-hidden="true"></i>
				<?= $user ? 'Salvar Alterações' : 'Criar Usuário' ?>
			</button>
		</div>
	</div>
</form>
