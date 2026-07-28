<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="mb-4">
    <a href="<?= base_url('admin/perfis') ?>" class="text-decoration-none text-theme-muted">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>
    <h1 class="h3 mt-2 mb-0 text-theme-heading"><?= $role ? 'Editar' : 'Novo' ?> Perfil</h1>
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
        <form method="post" action="<?= $role ? base_url('admin/perfis/editar/' . $role->get_id()) : base_url('admin/perfis/novo') ?>">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label text-theme-main">Nome <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= form_error('name') ? 'is-invalid' : '' ?>" id="name" name="name" value="<?= htmlspecialchars($role ? $role->get_name() : set_value('name')) ?>" required>
                    <?php if (form_error('name')): ?>
                        <div class="invalid-feedback"><?= form_error('name') ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label for="slug" class="form-label text-theme-main">Slug <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= form_error('slug') ? 'is-invalid' : '' ?>" id="slug" name="slug" value="<?= htmlspecialchars($role ? $role->get_slug() : set_value('slug')) ?>" required>
                    <?php if (form_error('slug')): ?>
                        <div class="invalid-feedback"><?= form_error('slug') ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mb-4">
                <label for="description" class="form-label text-theme-main">Descrição</label>
                <textarea class="form-control" id="description" name="description" rows="2"><?= htmlspecialchars($role ? $role->get_description() ?? '' : set_value('description')) ?></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label text-theme-main">Permissões</label>
                <div class="row">
                    <?php foreach ($permissions as $permission): ?>
                        <div class="col-md-4 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="permission_ids[]" id="perm_<?= $permission->get_id() ?>" value="<?= $permission->get_id() ?>"
                                    <?= $role && in_array($permission->get_id(), $role->get_permission_ids()) ? 'checked' : '' ?>>
                                <label class="form-check-label text-theme-main" for="perm_<?= $permission->get_id() ?>">
                                    <?= htmlspecialchars($permission->get_name()) ?>
                                </label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if (empty($permissions)): ?>
                    <p class="text-theme-muted mb-0">Nenhuma permissão disponível. Crie permissões primeiro.</p>
                <?php endif; ?>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn-theme-primary">
                    <i class="bi bi-check-lg"></i> <?= $role ? 'Atualizar' : 'Criar' ?>
                </button>
                <a href="<?= base_url('admin/perfis') ?>" class="btn-theme-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
