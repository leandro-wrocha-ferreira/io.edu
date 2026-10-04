<div class="container-fluid px-0">
	<!-- Page Header -->
	<header class="edu-page-header animate-fade-up">
		<div>
			<nav class="header-breadcrumb d-flex align-items-center gap-2 mb-1" aria-label="Localização">
				<a href="<?= base_url('admin/painel') ?>" class="text-muted text-decoration-none">Painel</a>
				<i class="bi bi-chevron-right text-muted" style="font-size: 0.7rem;" aria-hidden="true"></i>
				<a href="<?= base_url('admin/turmas') ?>" class="text-muted text-decoration-none">Turmas</a>
				<i class="bi bi-chevron-right text-muted" style="font-size: 0.7rem;" aria-hidden="true"></i>
				<span class="fw-medium text-body">Nova Turma</span>
			</nav>
			<h1 class="edu-page-header-title">Cadastrar Nova Turma</h1>
			<p class="edu-page-header-desc">Defina o período letivo, limite de vagas e docente responsável pela condução pedagógica.</p>
		</div>
		<div class="edu-page-header-actions">
			<a href="<?= base_url('admin/turmas') ?>" class="edu-btn edu-btn-outline">
				<i class="bi bi-arrow-left" aria-hidden="true"></i>
				<span>Voltar para Listagem</span>
			</a>
		</div>
	</header>

	<form id="class-form" onsubmit="event.preventDefault(); alert('Protótipo Visual: Nenhuma alteração foi persistida no banco.');" class="animate-fade-up animate-delay-1">
		<div class="edu-form-card mb-4">
			<!-- Section 1: Identification & Course -->
			<div class="edu-form-section">
				<h2 class="edu-form-section-title">Identificação da Turma</h2>
				<p class="edu-form-section-desc">Vincule a turma ao curso e defina o docente tutor.</p>

				<div class="row g-3">
					<div class="col-md-6 edu-form-field">
						<label for="class-name" class="edu-form-label">
							Nome da Turma <span class="text-danger" aria-hidden="true">*</span>
						</label>
						<input type="text"
						       id="class-name"
						       name="name"
						       class="form-control"
						       placeholder="Ex: Turma Web 2026.2"
						       required>
					</div>

					<div class="col-md-6 edu-form-field">
						<label for="class-course" class="edu-form-label">
							Curso Associado <span class="text-danger" aria-hidden="true">*</span>
						</label>
						<select id="class-course" name="course_id" class="form-select" required>
							<?php foreach ($courses as $course_item): ?>
								<option value="<?= (int) $course_item['id'] ?>"><?= html_escape($course_item['title']) ?></option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="col-md-8 edu-form-field">
						<label for="class-instructor" class="edu-form-label">
							Professor / Tutor da Turma <span class="text-danger" aria-hidden="true">*</span>
						</label>
						<input type="text"
						       id="class-instructor"
						       name="instructor"
						       class="form-control"
						       value="Prof. Marcelo Santos"
						       required>
					</div>

					<div class="col-md-4 edu-form-field">
						<label for="class-max-students" class="edu-form-label">
							Capacidade Máxima (Vagas) <span class="text-danger" aria-hidden="true">*</span>
						</label>
						<input type="number"
						       id="class-max-students"
						       name="max_students"
						       class="form-control"
						       value="35"
						       min="1"
						       required>
					</div>
				</div>
			</div>

			<!-- Section 2: Period & Dates -->
			<div class="edu-form-section border-bottom-0 pb-0">
				<h2 class="edu-form-section-title">Calendário & Modalidade</h2>
				<p class="edu-form-section-desc">Datas de início, término das aulas e modelo de tutoria.</p>

				<div class="row g-3">
					<div class="col-md-4 edu-form-field">
						<label for="start-date" class="edu-form-label">Data de Início das Aulas</label>
						<input type="date" id="start-date" class="form-control" value="2026-10-15">
					</div>

					<div class="col-md-4 edu-form-field">
						<label for="end-date" class="edu-form-label">Data de Conclusão Prevista</label>
						<input type="date" id="end-date" class="form-control" value="2026-12-20">
					</div>

					<div class="col-md-4 edu-form-field">
						<label class="edu-form-label">Modalidade de Formação</label>
						<select class="form-select">
							<option value="online">100% Online com Tutoria</option>
							<option value="hybrid">Semipresencial</option>
							<option value="self_paced">Autoinstrucional</option>
						</select>
					</div>
				</div>
			</div>

			<!-- Actions Footer -->
			<div class="edu-form-actions">
				<a href="<?= base_url('admin/turmas') ?>" class="edu-btn edu-btn-outline">
					Cancelar
				</a>
				<button type="submit" class="edu-btn edu-btn-primary">
					<i class="bi bi-floppy me-1" aria-hidden="true"></i>
					<span>Criar Turma</span>
				</button>
			</div>
		</div>
	</form>
</div>
