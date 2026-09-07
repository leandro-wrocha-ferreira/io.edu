<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Page Header -->
<div class="page-header animate-fade-up">
	<div class="page-header-info">
		<p class="page-header-breadcrumb">
			<i class="bi bi-house-door" aria-hidden="true"></i>
			<span class="sep">›</span>
			<span>Administração</span>
			<span class="sep">›</span>
			<span>Turmas</span>
		</p>
		<h1 class="page-header-title">Gestão de Turmas</h1>
		<p class="page-header-subtitle">Gerencie as turmas (cohorts) para os cursos.</p>
	</div>
	<div class="page-header-actions">
		<a href="<?= base_url('admin/turmas/nova') ?>" class="btn-theme-primary-outline">
			<i class="bi bi-plus-lg" aria-hidden="true"></i> Nova Turma
		</a>
	</div>
</div>

<?php if ($this->session->flashdata('success')): ?>
	<div class="alert alert-flash alert-flash-success alert-dismissible fade show animate-fade-up" role="alert" aria-live="polite">
		<i class="bi bi-check-circle-fill alert-flash-icon" aria-hidden="true"></i>
		<div class="alert-flash-body"><?= htmlspecialchars($this->session->flashdata('success')) ?></div>
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
	</div>
<?php endif; ?>

<div class="card card-theme animate-fade-up animate-delay-1">
	<div class="card-body">
		<table class="table table-hover align-middle w-100" id="classes-table" aria-label="Lista de turmas">
			<thead class="table-head-brand">
				<tr>
					<th class="d-none d-md-table-cell" scope="col">ID</th>
					<th scope="col">Nome da Turma</th>
					<th scope="col">Curso Relacionado</th>
					<th class="d-none d-md-table-cell" scope="col">Criado em</th>
					<th class="text-center" scope="col">Ações</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan="5" class="text-center text-muted py-3">Nenhuma turma cadastrada (mock).</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
