<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Page Header -->
<div class="page-header animate-fade-up">
	<div class="page-header-info">
		<p class="page-header-breadcrumb">
			<i class="bi bi-house-door" aria-hidden="true"></i>
			<span class="sep">›</span>
			<a href="<?= base_url('admin/turmas') ?>">Turmas</a>
			<span class="sep">›</span>
			<span><?= $class_obj ? 'Editar Turma' : 'Nova Turma' ?></span>
		</p>
		<h1 class="page-header-title"><?= $class_obj ? 'Editar Turma' : 'Nova Turma' ?></h1>
		<p class="page-header-subtitle"><?= $class_obj ? 'Atualize as informações da turma.' : 'Crie uma nova turma para um curso.' ?></p>
	</div>
	<div class="page-header-actions">
		<a href="<?= base_url('admin/turmas') ?>" class="btn-theme-primary-outline">
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

<form method="post" action="<?= $class_obj ? base_url('admin/turmas/editar/' . $class_obj->id) : base_url('admin/turmas/nova') ?>">
	<div class="card-theme-form animate-fade-up animate-delay-1">

		<div class="form-section">
			<div class="form-section-header">
				<i class="bi bi-people" aria-hidden="true"></i>
				<h2 class="form-section-title">Dados da Turma</h2>
			</div>

			<div class="row mb-3">
				<div class="col-md-6">
					<label for="name" class="form-label">Nome da Turma <span class="text-danger" aria-hidden="true">*</span></label>
					<input type="text"
					       class="form-control <?= form_error('name') ? 'is-invalid' : '' ?>"
					       id="name"
					       name="name"
					       value="<?= htmlspecialchars($class_obj ? $class_obj->name : set_value('name')) ?>"
					       required>
					<?php if (form_error('name')): ?>
						<div class="invalid-feedback"><?= form_error('name') ?></div>
					<?php endif; ?>
				</div>
				<div class="col-md-6">
					<label for="course_id" class="form-label">Curso <span class="text-danger" aria-hidden="true">*</span></label>
					<select class="form-select <?= form_error('course_id') ? 'is-invalid' : '' ?>"
					        id="course_id"
					        name="course_id"
					        required>
						<option value="">Selecione um curso...</option>
						<?php
						$selected_course = $class_obj ? $class_obj->course_id : set_value('course_id');
						// Mock courses output - replace with actual foreach over $courses
						?>
						<option value="1" <?= $selected_course == 1 ? 'selected' : '' ?>>Curso Teste de PHP</option>
					</select>
					<?php if (form_error('course_id')): ?>
						<div class="invalid-feedback"><?= form_error('course_id') ?></div>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<div class="form-actions">
			<button type="submit" class="btn-theme-primary">
				<i class="bi bi-check-lg" aria-hidden="true"></i>
				<?= $class_obj ? 'Salvar Alterações' : 'Criar Turma' ?>
			</button>
			<a href="<?= base_url('admin/turmas') ?>" class="btn-theme-secondary">Cancelar</a>
		</div>
	</div>
</form>
