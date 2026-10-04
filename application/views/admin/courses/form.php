<div class="container-fluid px-0">
	<!-- Page Header -->
	<header class="edu-page-header animate-fade-up">
		<div>
			<nav class="header-breadcrumb d-flex align-items-center gap-2 mb-1" aria-label="Localização">
				<a href="<?= base_url('admin/painel') ?>" class="text-muted text-decoration-none">Painel</a>
				<i class="bi bi-chevron-right text-muted" style="font-size: 0.7rem;" aria-hidden="true"></i>
				<a href="<?= base_url('admin/cursos') ?>" class="text-muted text-decoration-none">Cursos</a>
				<i class="bi bi-chevron-right text-muted" style="font-size: 0.7rem;" aria-hidden="true"></i>
				<span class="fw-medium text-body"><?= !empty($course) ? 'Editar Curso' : 'Novo Curso' ?></span>
			</nav>
			<h1 class="edu-page-header-title"><?= !empty($course) ? html_escape($course['title']) : 'Novo Curso' ?></h1>
			<p class="edu-page-header-desc">Defina as diretrizes pedagógicas, carga horária e parâmetros de publicação da disciplina.</p>
		</div>
		<div class="edu-page-header-actions">
			<a href="<?= base_url('admin/cursos') ?>" class="edu-btn edu-btn-outline">
				<i class="bi bi-arrow-left" aria-hidden="true"></i>
				<span>Voltar para Listagem</span>
			</a>
		</div>
	</header>

	<form id="course-form" onsubmit="event.preventDefault(); alert('Protótipo Visual: Nenhuma alteração foi persistida no banco.');" class="animate-fade-up animate-delay-1">
		<div class="edu-form-card mb-4">
			<!-- Section 1: Basic Information -->
			<div class="edu-form-section">
				<h2 class="edu-form-section-title">Informações Básicas</h2>
				<p class="edu-form-section-desc">Identificação principal, titulação e alocação do docente responsável.</p>

				<div class="row g-3">
					<div class="col-md-8 edu-form-field">
						<label for="course-title" class="edu-form-label">
							Título do Curso <span class="text-danger" aria-hidden="true">*</span>
						</label>
						<input type="text"
						       id="course-title"
						       name="title"
						       class="form-control"
						       value="<?= html_escape($course['title'] ?? 'Desenvolvimento Web Fullstack') ?>"
						       required
						       aria-describedby="title-hint">
						<div id="title-hint" class="edu-form-hint">
							<i class="bi bi-info-circle me-1"></i>Nome formal exibido no catálogo público e no certificado de conclusão.
						</div>
					</div>

					<div class="col-md-4 edu-form-field">
						<label for="course-category" class="edu-form-label">
							Categoria Pedagógica <span class="text-danger" aria-hidden="true">*</span>
						</label>
						<select id="course-category" name="category" class="form-select" required>
							<option value="Tecnologia" <?= (!empty($course) && $course['category'] === 'Tecnologia') ? 'selected' : '' ?>>Tecnologia</option>
							<option value="Gestão & Negócios" <?= (!empty($course) && $course['category'] === 'Gestão & Negócios') ? 'selected' : '' ?>>Gestão & Negócios</option>
							<option value="Segurança" <?= (!empty($course) && $course['category'] === 'Segurança') ? 'selected' : '' ?>>Segurança</option>
							<option value="Soft Skills" <?= (!empty($course) && $course['category'] === 'Soft Skills') ? 'selected' : '' ?>>Soft Skills</option>
						</select>
					</div>

					<div class="col-md-8 edu-form-field">
						<label for="course-instructor" class="edu-form-label">
							Professor / Docente Responsável <span class="text-danger" aria-hidden="true">*</span>
						</label>
						<input type="text"
						       id="course-instructor"
						       name="instructor"
						       class="form-control"
						       value="<?= html_escape($course['instructor'] ?? 'Prof. Marcelo Santos') ?>"
						       required>
					</div>

					<div class="col-md-4 edu-form-field">
						<label for="course-workload" class="edu-form-label">
							Carga Horária Total <span class="text-danger" aria-hidden="true">*</span>
						</label>
						<div class="input-group">
							<input type="text"
							       id="course-workload"
							       name="workload"
							       class="form-control"
							       value="<?= html_escape($course['workload'] ?? '120h') ?>"
							       required>
							<span class="input-group-text">horas</span>
						</div>
					</div>

					<div class="col-12 edu-form-field">
						<label for="course-desc" class="edu-form-label">
							Ementa / Descrição Pedagógica
						</label>
						<textarea id="course-desc"
						          name="description"
						          rows="4"
						          class="form-control"><?= html_escape($course['description'] ?? '') ?></textarea>
						<div class="edu-form-hint">
							<i class="bi bi-info-circle me-1"></i>Resumo do conteúdo programático e objetivos de aprendizagem esperados.
						</div>
					</div>
				</div>
			</div>

			<!-- Section 2: Visual Presentation -->
			<div class="edu-form-section">
				<h2 class="edu-form-section-title">Apresentação & Vitrine</h2>
				<p class="edu-form-section-desc">Elementos visuais exibidos nas listagens e no portal de matrículas.</p>

				<div class="row g-3">
					<div class="col-md-6 edu-form-field">
						<label class="edu-form-label">Imagem de Capa (Proporção 16:9)</label>
						<div class="p-4 border rounded-3 bg-subtle text-center">
							<i class="bi bi-image fs-1 text-muted mb-2"></i>
							<div class="small fw-semibold text-heading">Selecione uma imagem de alta resolução</div>
							<div class="text-muted small mb-3">Recomendado: 1280×720px (JPEG ou WebP, máx. 2MB)</div>
							<button type="button" class="edu-btn edu-btn-outline edu-btn-sm" onclick="alert('Protótipo: Seleção de mídia simulada.');">
								<i class="bi bi-cloud-arrow-up me-1"></i> Escolher Imagem
							</button>
						</div>
					</div>

					<div class="col-md-6 edu-form-field">
						<label for="course-highlights" class="edu-form-label">Destaques do Curso</label>
						<textarea id="course-highlights"
						          rows="4"
						          class="form-control"
						          placeholder="Principais benefícios para o aluno (um por linha)...">Aulas 100% práticas
Certificado com validação digital
Suporte a dúvidas com tutoria especializada</textarea>
					</div>
				</div>
			</div>

			<!-- Section 3: Publication & Certifications -->
			<div class="edu-form-section border-bottom-0 pb-0">
				<h2 class="edu-form-section-title">Publicação & Certificação</h2>
				<p class="edu-form-section-desc">Defina a visibilidade do curso e emissão de certificados automáticos.</p>

				<div class="row g-3">
					<div class="col-md-6 edu-form-field">
						<label for="course-status" class="edu-form-label">
							Status de Publicação <span class="text-danger" aria-hidden="true">*</span>
						</label>
						<select id="course-status" name="status" class="form-select">
							<option value="published" <?= (!empty($course) && $course['status'] === 'published') ? 'selected' : '' ?>>Publicado (Visível no catálogo)</option>
							<option value="review" <?= (!empty($course) && $course['status'] === 'review') ? 'selected' : '' ?>>Em Revisão (Apenas administradores)</option>
							<option value="draft" <?= (!empty($course) && $course['status'] === 'draft') ? 'selected' : '' ?>>Rascunho (Não publicado)</option>
						</select>
					</div>

					<div class="col-md-6 edu-form-field">
						<label class="edu-form-label">Emissão de Certificado Digital</label>
						<div class="form-check form-switch mt-2">
							<input class="form-check-input" type="checkbox" id="cert-toggle" checked>
							<label class="form-check-label fw-medium text-body" for="cert-toggle">
								Emitir certificado automaticamente após 100% de conclusão
							</label>
						</div>
					</div>
				</div>
			</div>

			<!-- Actions Footer -->
			<div class="edu-form-actions">
				<a href="<?= base_url('admin/cursos') ?>" class="edu-btn edu-btn-outline">
					Cancelar
				</a>
				<button type="submit" class="edu-btn edu-btn-primary">
					<i class="bi bi-floppy me-1" aria-hidden="true"></i>
					<span>Salvar Alterações</span>
				</button>
			</div>
		</div>
	</form>
</div>
