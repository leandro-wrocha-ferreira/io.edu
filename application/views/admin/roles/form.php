<!-- Operational Page Header -->
<div class="page-header animate-fade-up">
	<div class="page-header-info">
		<nav class="page-header-breadcrumb" aria-label="Navegação estrutural">
			<a href="<?= base_url('admin/painel') ?>"><i class="bi bi-house-door" aria-hidden="true"></i> Início</a>
			<span class="sep" aria-hidden="true">›</span>
			<a href="<?= base_url('admin/perfis') ?>">Perfis e Permissões</a>
			<span class="sep" aria-hidden="true">›</span>
			<span class="active" aria-current="page"><?= $role ? 'Editar Perfil' : 'Novo Perfil' ?></span>
		</nav>
		<h1 class="page-header-title"><?= $role ? 'Editar Perfil' : 'Novo Perfil' ?></h1>
		<p class="page-header-subtitle"><?= $role ? 'Atualize as permissões e parâmetros do perfil de acesso.' : 'Defina identificador e matriz de permissões para o novo perfil.' ?></p>
	</div>
	<div class="page-header-actions">
		<a href="<?= base_url('admin/perfis') ?>" class="edu-btn edu-btn-ghost">
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
<form method="post" action="<?= $role ? base_url('admin/perfis/editar/' . $role->get_id()) : base_url('admin/perfis/novo') ?>">
	<div class="edu-form-card animate-fade-up animate-delay-1">

		<!-- Seção 1: Dados do Perfil -->
		<div class="edu-form-section">
			<div class="edu-form-section-header">
				<i class="bi bi-shield-check" aria-hidden="true"></i>
				<div>
					<h2 class="edu-form-section-title">Identificação do Perfil</h2>
					<p class="edu-form-section-desc">Nome amigável, identificador de sistema e descrição do papel.</p>
				</div>
			</div>

			<div class="row g-3">
				<div class="col-md-6 edu-form-field">
					<label for="name" class="edu-form-label">Nome do Perfil <span class="text-danger" aria-hidden="true">*</span></label>
					<input type="text"
					       class="form-control <?= form_error('name') ? 'is-invalid' : '' ?>"
					       id="name"
					       name="name"
					       value="<?= html_escape($role ? $role->get_name() : set_value('name')) ?>"
					       autocomplete="off"
					       aria-describedby="<?= form_error('name') ? 'name-error' : 'name-hint' ?>"
					       required>
					<?php if (form_error('name')): ?>
						<div class="invalid-feedback" id="name-error"><?= form_error('name') ?></div>
					<?php else: ?>
						<span class="edu-form-hint" id="name-hint">Ex: Coordenador Pedagógico, Tutor, Secretário.</span>
					<?php endif; ?>
				</div>

				<div class="col-md-6 edu-form-field">
					<label for="slug" class="edu-form-label">Slug (Identificador) <span class="text-danger" aria-hidden="true">*</span></label>
					<input type="text"
					       class="form-control <?= form_error('slug') ? 'is-invalid' : '' ?>"
					       id="slug"
					       name="slug"
					       value="<?= html_escape($role ? $role->get_slug() : set_value('slug')) ?>"
					       autocomplete="off"
					       spellcheck="false"
					       aria-describedby="<?= form_error('slug') ? 'slug-error' : 'slug-hint' ?>"
					       required>
					<?php if (form_error('slug')): ?>
						<div class="invalid-feedback" id="slug-error"><?= form_error('slug') ?></div>
					<?php else: ?>
						<span class="edu-form-hint" id="slug-hint">Identificador único em minúsculas (ex: <code>coordenador</code>).</span>
					<?php endif; ?>
				</div>
			</div>

			<div class="row g-3 mt-1">
				<div class="col-md-12 edu-form-field">
					<label for="description" class="edu-form-label">Descrição Operacional</label>
					<textarea class="form-control"
					          id="description"
					          name="description"
					          rows="2"
					          aria-describedby="desc-hint"><?= html_escape($role ? $role->get_description() ?? '' : set_value('description')) ?></textarea>
					<span class="edu-form-hint" id="desc-hint">Resumo das responsabilidades atribuídas a este perfil na plataforma.</span>
				</div>
			</div>
		</div>

		<!-- Seção 2: Matriz de Permissões -->
		<div class="edu-form-section">
			<div class="edu-form-section-header">
				<i class="bi bi-key-fill" aria-hidden="true"></i>
				<div>
					<h2 class="edu-form-section-title">Permissões do Sistema</h2>
					<p class="edu-form-section-desc">Selecione as ações autorizadas para os usuários com este perfil.</p>
				</div>
			</div>

			<?php if (empty($permissions)): ?>
				<div class="p-3 text-muted border rounded-3 bg-light">
					Nenhuma permissão disponível para seleção no momento.
				</div>
			<?php else: ?>
				<div class="row g-3">
					<?php foreach ($permissions as $permission): ?>
						<div class="col-md-6 col-lg-4">
							<div class="p-3 rounded-3 border h-100" style="background-color: var(--edu-bg-subtle);">
								<div class="form-check">
									<input class="form-check-input"
									       type="checkbox"
									       name="permission_ids[]"
									       id="perm_<?= $permission->get_id() ?>"
									       value="<?= $permission->get_id() ?>"
									       <?= (!empty($role_permission_ids) && in_array($permission->get_id(), $role_permission_ids)) ? 'checked' : '' ?>>
									<label class="form-check-label fw-bold text-heading" for="perm_<?= $permission->get_id() ?>">
										<?= html_escape($permission->get_name()) ?>
									</label>
								</div>
								<p class="small text-muted mb-0 mt-1 ps-4">
									<?= html_escape($permission->get_description() ?: 'Ação autorizada no sistema.') ?>
								</p>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<!-- Ações de Formulário -->
		<div class="edu-form-actions">
			<a href="<?= base_url('admin/perfis') ?>" class="edu-btn edu-btn-secondary">Cancelar</a>
			<button type="submit" class="edu-btn edu-btn-primary">
				<i class="bi bi-check-lg" aria-hidden="true"></i>
				<?= $role ? 'Salvar Alterações' : 'Criar Perfil' ?>
			</button>
		</div>
	</div>
</form>
