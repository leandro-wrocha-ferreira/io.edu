<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="mb-4">
    <a href="<?= base_url('admin/usuarios') ?>" class="text-decoration-none text-theme-muted">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>
    <h1 class="h3 mt-2 mb-0 text-theme-heading"><?= $user ? 'Editar' : 'Novo' ?> Usuário</h1>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (validation_errors()): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= validation_errors() ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card card-theme">
    <div class="card-body">
        <form method="post" action="<?= $user ? base_url('admin/usuarios/editar/' . $user->get_id()) : base_url('admin/usuarios/novo') ?>">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label text-theme-main">Nome <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= form_error('name') ? 'is-invalid' : '' ?>" id="name" name="name" value="<?= htmlspecialchars($user ? $user->get_name() : set_value('name')) ?>" required>
                    <?php if (form_error('name')): ?>
                        <div class="invalid-feedback"><?= form_error('name') ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label text-theme-main">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control <?= form_error('email') ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= htmlspecialchars($user ? (string) $user->get_email() : set_value('email')) ?>" required>
                    <?php if (form_error('email')): ?>
                        <div class="invalid-feedback"><?= form_error('email') ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!$user): ?>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label text-theme-main">Senha <span class="text-danger">*</span></label>
                        <input type="password" class="form-control <?= form_error('password') ? 'is-invalid' : '' ?>" id="password" name="password" required minlength="6">
                        <?php if (form_error('password')): ?>
                            <div class="invalid-feedback"><?= form_error('password') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-theme-main">Senha</label>
                        <div>
                            <button type="button" class="btn-theme-secondary btn-sm" disabled title="Em desenvolvimento">
                                <i class="bi bi-key"></i> Redefinir Senha
                            </button>
                            <small class="text-theme-muted ms-2">(Em desenvolvimento)</small>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label text-theme-main">Perfil de Acesso</label>
                    <?php foreach ($roles as $role): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="role_ids[]" id="role_<?= $role->get_id() ?>" value="<?= $role->get_id() ?>"
                                <?= $user && in_array($role->get_id(), $user->get_role_ids()) ? 'checked' : '' ?>>
                            <label class="form-check-label text-theme-main" for="role_<?= $role->get_id() ?>">
                                <?= htmlspecialchars($role->get_name()) ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn-theme-primary">
                    <i class="bi bi-check-lg"></i> <?= $user ? 'Atualizar' : 'Criar' ?>
                </button>
                <a href="<?= base_url('admin/usuarios') ?>" class="btn-theme-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
