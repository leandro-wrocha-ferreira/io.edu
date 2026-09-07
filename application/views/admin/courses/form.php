<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Page Header -->
<div class="page-header animate-fade-up">
	<div class="page-header-info">
		<p class="page-header-breadcrumb">
			<i class="bi bi-house-door" aria-hidden="true"></i>
			<span class="sep">›</span>
			<a href="<?= base_url('admin/cursos') ?>">Cursos</a>
			<span class="sep">›</span>
			<span><?= $course ? 'Editar Curso' : 'Novo Curso' ?></span>
		</p>
		<h1 class="page-header-title"><?= $course ? 'Editar Curso' : 'Novo Curso' ?></h1>
		<p class="page-header-subtitle"><?= $course ? 'Atualize as informações do curso.' : 'Preencha os dados do novo curso.' ?></p>
	</div>
	<div class="page-header-actions">
		<a href="<?= base_url('admin/cursos') ?>" class="btn-theme-primary-outline">
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

<form method="post" action="<?= $course ? base_url('admin/cursos/editar/' . $course->id) : base_url('admin/cursos/novo') ?>">
	<div class="card-theme-form animate-fade-up animate-delay-1">

		<div class="form-section">
			<div class="form-section-header">
				<i class="bi bi-journal-bookmark" aria-hidden="true"></i>
				<h2 class="form-section-title">Dados do Curso</h2>
			</div>

			<div class="row mb-3">
				<div class="col-md-12">
					<label for="title" class="form-label">Título <span class="text-danger" aria-hidden="true">*</span></label>
					<input type="text"
					       class="form-control <?= form_error('title') ? 'is-invalid' : '' ?>"
					       id="title"
					       name="title"
					       value="<?= htmlspecialchars($course ? $course->title : set_value('title')) ?>"
					       required>
					<?php if (form_error('title')): ?>
						<div class="invalid-feedback"><?= form_error('title') ?></div>
					<?php endif; ?>
				</div>
			</div>

			<div class="row mb-3">
				<div class="col-md-12">
					<label for="description" class="form-label">Descrição</label>
					<textarea class="form-control <?= form_error('description') ? 'is-invalid' : '' ?>"
					          id="description"
					          name="description"
					          rows="4"><?= htmlspecialchars($course ? $course->description : set_value('description')) ?></textarea>
					<?php if (form_error('description')): ?>
						<div class="invalid-feedback"><?= form_error('description') ?></div>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<div class="form-actions">
			<button type="submit" class="btn-theme-primary">
				<i class="bi bi-check-lg" aria-hidden="true"></i>
				<?= $course ? 'Salvar Alterações' : 'Criar Curso' ?>
			</button>
			<a href="<?= base_url('admin/cursos') ?>" class="btn-theme-secondary">Cancelar</a>
		</div>
	</div>
</form>
