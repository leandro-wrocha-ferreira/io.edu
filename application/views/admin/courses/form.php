<?php
/**
 * @var \app\domain\course\Course|null $course
 * @var array<\app\domain\course\Category> $categories
 */
?>
<div class="container-fluid px-0">
	<!-- Page Header -->
	<div class="page-header animate-fade-up">
		<div class="page-header-info">
			<nav class="page-header-breadcrumb" aria-label="Breadcrumb">
				<a href="<?= base_url('admin/painel') ?>">Painel</a>
				<span class="sep" aria-hidden="true">/</span>
				<a href="<?= base_url('admin/cursos') ?>">Cursos</a>
				<span class="sep" aria-hidden="true">/</span>
				<span class="active" aria-current="page"><?= !empty($course) ? 'Editar' : 'Novo' ?></span>
			</nav>
			<h1 class="page-header-title"><?= !empty($course) ? 'Editar Curso' : 'Novo Curso' ?></h1>
			<p class="page-header-subtitle">Defina as diretrizes pedagógicas, regras de acesso do aluno e parâmetros de certificação.</p>
		</div>
		<div class="page-header-actions">
			<a href="<?= base_url('admin/cursos') ?>" class="edu-btn edu-btn-outline">
				<i class="bi bi-arrow-left me-1" aria-hidden="true"></i> Voltar para Listagem
			</a>
		</div>
	</div>

	<?php if ($this->session->flashdata('error')): ?>
		<div class="alert alert-danger alert-dismissible fade show animate-fade-up" role="alert">
			<i class="bi bi-exclamation-triangle-fill me-2" aria-hidden="true"></i>
			<?= html_escape($this->session->flashdata('error')) ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
		</div>
	<?php endif; ?>

	<?php if (validation_errors()): ?>
		<div class="alert alert-danger alert-dismissible fade show animate-fade-up" role="alert">
			<i class="bi bi-exclamation-triangle-fill me-2" aria-hidden="true"></i>
			<div class="small"><?= validation_errors() ?></div>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
		</div>
	<?php endif; ?>

	<div class="edu-form-card animate-fade-up animate-delay-1">
		<form action="<?= current_url() ?>" method="POST" id="course-form" enctype="multipart/form-data">
			<!-- Section 1: Identificação -->
			<div class="edu-form-section">
				<div class="edu-form-section-header">
					<div class="edu-form-section-icon">
						<i class="bi bi-journal-text" aria-hidden="true"></i>
					</div>
					<div>
						<h2 class="edu-form-section-title">Identificação do Curso</h2>
						<p class="edu-form-section-desc">Título, categoria, endereço URL amigável e imagem de apresentação.</p>
					</div>
				</div>

				<div class="row g-3">
					<div class="col-md-8 edu-form-field">
						<label class="edu-form-label edu-label-required" for="course-title">Título do Curso</label>
						<input type="text"
						       class="form-control edu-form-control"
						       id="course-title"
						       name="title"
						       value="<?= html_escape(set_value('title', !empty($course) ? $course->get_title() : '')) ?>"
						       placeholder="Ex: Arquitetura Limpa e DDD com PHP 8"
						       required>
						<span class="edu-form-hint">Nome formal exibido na vitrine e nos certificados de conclusão.</span>
					</div>

					<div class="col-md-4 edu-form-field">
						<label class="edu-form-label edu-label-required" for="course-category">Categoria Pedagógica</label>
						<select class="form-select edu-form-select" id="course-category" name="category_id" required>
							<option value="">Selecione uma categoria...</option>
							<?php $selected_cat = set_value('category_id', !empty($course) ? $course->get_category_id() : ''); ?>
							<?php if (!empty($categories)): ?>
								<?php foreach ($categories as $cat): ?>
									<option value="<?= $cat->get_id() ?>" <?= (string)$selected_cat === (string)$cat->get_id() ? 'selected' : '' ?>>
										<?= html_escape($cat->get_name()) ?>
									</option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select>
					</div>

					<div class="col-md-6 edu-form-field">
						<label class="edu-form-label" for="course-slug">Slug na URL</label>
						<input type="text"
						       class="form-control edu-form-control"
						       id="course-slug"
						       name="slug"
						       value="<?= html_escape(set_value('slug', !empty($course) ? (string)$course->get_slug() : '')) ?>"
						       placeholder="Ex: arquitetura-limpa-ddd-php-8">
						<span class="edu-form-hint">Deixe em branco para gerar automaticamente a partir do título.</span>
					</div>

					<div class="col-md-6 edu-form-field">
						<label class="edu-form-label edu-label-required" for="course-status">Status</label>
						<select class="form-select edu-form-select" id="course-status" name="status" required>
							<?php $selected_status = set_value('status', !empty($course) ? (string)$course->get_status() : 'draft'); ?>
							<option value="draft" <?= $selected_status === 'draft' ? 'selected' : '' ?>>Rascunho</option>
							<option value="active" <?= $selected_status === 'active' ? 'selected' : '' ?>>Ativo</option>
							<option value="archived" <?= $selected_status === 'archived' ? 'selected' : '' ?>>Arquivado</option>
						</select>
						<span class="edu-form-hint small" id="course-status-hint">
							<?php
								echo match ($selected_status) {
									'active' => 'Disponível para venda contínua e matrículas imediatas.',
									'archived' => 'Vendas encerradas, acessível apenas para alunos matriculados.',
									default => 'Invisível na vitrine, em fase de elaboração pedagógica.',
								};
							?>
						</span>
					</div>

					<div class="col-12 edu-form-field">
						<label class="edu-form-label">Imagem de Apresentação (Capa)</label>
						<div class="edu-card p-3 bg-edu-surface border-edu shadow-none">
							<div class="d-flex align-items-center gap-3 mb-2">
								<div class="form-check form-check-inline mb-0">
									<input class="form-check-input" type="radio" name="image_type" id="img-type-upload" value="upload" checked>
									<label class="form-check-label fw-medium" for="img-type-upload">Upload de Imagem</label>
								</div>
								<div class="form-check form-check-inline mb-0">
									<input class="form-check-input" type="radio" name="image_type" id="img-type-url" value="url" <?= (!empty($course) && filter_var($course->get_image(), FILTER_VALIDATE_URL)) ? 'checked' : '' ?>>
									<label class="form-check-label fw-medium" for="img-type-url">URL Externa</label>
								</div>
							</div>

							<div id="image-upload-box">
								<input type="file"
								       class="form-control edu-form-control"
								       id="course-image-file"
								       name="image_file"
								       accept="image/png,image/jpeg,image/webp,image/jpg">
								<span class="edu-form-hint">Formatos suportados: PNG, JPG, JPEG ou WebP (tamanho máximo: 4MB).</span>
							</div>

							<div id="image-url-box" class="d-none">
								<input type="url"
								       class="form-control edu-form-control"
								       id="course-image"
								       name="image"
								       value="<?= html_escape(set_value('image', !empty($course) ? $course->get_image() : '')) ?>"
								       placeholder="https://exemplo.com/imagem-do-curso.jpg">
								<span class="edu-form-hint">Informe a URL pública direta da imagem.</span>
							</div>

							<?php if (!empty($course) && $course->get_image()): ?>
								<div class="mt-2 pt-2 border-top border-edu d-flex align-items-center gap-2 small text-muted">
									<i class="bi bi-image" aria-hidden="true"></i>
									<span>Imagem atual:</span>
									<a href="<?= html_escape($course->get_image()) ?>" target="_blank" rel="noopener" class="text-primary text-truncate d-inline-block" style="max-width: 320px;">
										<?= html_escape($course->get_image()) ?>
									</a>
								</div>
							<?php endif; ?>
						</div>
					</div>

					<div class="col-12 edu-form-field">
						<label class="edu-form-label" for="course-short-description">Resumo para Vitrine</label>
						<textarea class="form-control edu-form-control"
						          id="course-short-description"
						          name="short_description"
						          rows="2"
						          maxlength="500"
						          placeholder="Breve chamada explicativa para os cards do catálogo..."><?= html_escape(set_value('short_description', !empty($course) ? $course->get_short_description() : '')) ?></textarea>
						<span class="edu-form-hint">Máximo de 500 caracteres.</span>
					</div>
				</div>
			</div>

			<!-- Section 2: Regras de Acesso e Certificação -->
			<div class="edu-form-section">
				<div class="edu-form-section-header">
					<div class="edu-form-section-icon">
						<i class="bi bi-clock-history" aria-hidden="true"></i>
					</div>
					<div>
						<h2 class="edu-form-section-title">Regras de Acesso e Certificação</h2>
						<p class="edu-form-section-desc">Defina a validade da matrícula para o aluno e os parâmetros de emissão do certificado.</p>
					</div>
				</div>

				<div class="row g-3">
					<div class="col-md-6 edu-form-field">
						<label class="edu-form-label edu-label-required" for="access-period-type">Tipo de Período de Acesso</label>
						<?php 
							$current_access_type = set_value(
								'access_period_type', 
								!empty($course) ? $course->get_access_period()->get_type() : 'limited_time'
							); 
						?>
						<select class="form-select edu-form-select" id="access-period-type" name="access_period_type" required>
							<option value="limited_time" <?= $current_access_type === 'limited_time' ? 'selected' : '' ?>>
								Prazo Determinado (ex: 1 ano / 365 dias)
							</option>
							<option value="lifetime" <?= $current_access_type === 'lifetime' ? 'selected' : '' ?>>
								Acesso Vitalício (sem data de expiração)
							</option>
						</select>
						<span class="edu-form-hint">Regra de validade padrão aplicada no momento da matrícula do aluno.</span>
					</div>

					<div class="col-md-6 edu-form-field" id="access-days-container">
						<label class="edu-form-label edu-label-required" for="access-days">Dias de Acesso</label>
						<?php 
							$current_access_days = set_value(
								'access_days', 
								(!empty($course) && $course->get_access_period()->get_days() !== null) 
									? $course->get_access_period()->get_days() 
									: '365'
							); 
						?>
						<div class="input-group">
							<input type="number"
							       class="form-control edu-form-control"
							       id="access-days"
							       name="access_days"
							       min="1"
							       step="1"
							       value="<?= html_escape($current_access_days) ?>"
							       placeholder="365">
							<span class="input-group-text">dias</span>
						</div>
						<span class="edu-form-hint">Exemplos: 365 (1 ano), 180 (6 meses), 730 (2 anos).</span>
					</div>

					<div class="col-12 edu-form-field">
						<div class="form-check form-switch pt-2">
							<?php
								$cert_checked = !empty($course) ? $course->is_certificate_enabled() : true;
								if (set_value('submitted')) {
									$cert_checked = (bool) set_value('certificate_enabled');
								}
							?>
							<input type="hidden" name="submitted" value="1">
							<input class="form-check-input"
							       type="checkbox"
							       role="switch"
							       id="certificate-enabled"
							       name="certificate_enabled"
							       value="1"
							       <?= $cert_checked ? 'checked' : '' ?>>
							<label class="form-check-label fw-semibold" for="certificate-enabled">
								Emitir certificado de conclusão automaticamente após a conclusão das aulas
							</label>
						</div>
					</div>
				</div>
			</div>

			<!-- Section 3: Dados Pedagógicos -->
			<div class="edu-form-section">
				<div class="edu-form-section-header">
					<div class="edu-form-section-icon">
						<i class="bi bi-mortarboard" aria-hidden="true"></i>
					</div>
					<div>
						<h2 class="edu-form-section-title">Dados Pedagógicos</h2>
						<p class="edu-form-section-desc">Carga horária para o certificado, ementa detalhada e pré-requisitos recomendados.</p>
					</div>
				</div>

				<div class="row g-3">
					<div class="col-md-4 edu-form-field">
						<label class="edu-form-label" for="course-workload">Carga Horária Estimada (Certificado)</label>
						<div class="input-group">
							<input type="number"
							       class="form-control edu-form-control"
							       id="course-workload"
							       name="workload_in_hours"
							       min="0"
							       step="1"
							       value="<?= html_escape(set_value('workload_in_hours', !empty($course) ? $course->get_workload_in_hours() : '')) ?>"
							       placeholder="Ex: 40">
							<span class="input-group-text">horas</span>
						</div>
						<span class="edu-form-hint">Carga horária oficial impressa no certificado do aluno.</span>
					</div>

					<div class="col-12 edu-form-field">
						<label class="edu-form-label" for="course-description">Ementa e Detalhes do Curso</label>
						<textarea class="form-control edu-form-control"
						          id="course-description"
						          name="description"
						          data-rich-editor="true"
						          rows="6"
						          placeholder="Apresentação detalhada da disciplina, metodologia e estrutura programática..."><?= html_escape(set_value('description', !empty($course) ? $course->get_description() : '')) ?></textarea>
						<span class="edu-form-hint">Utilize a barra de ferramentas para formatar o texto com negrito, títulos, listas e citações.</span>
					</div>

					<div class="col-md-6 edu-form-field">
						<label class="edu-form-label" for="course-target-audience">Público-Alvo</label>
						<textarea class="form-control edu-form-control"
						          id="course-target-audience"
						          name="target_audience"
						          data-rich-editor="true"
						          rows="3"
						          placeholder="Para quem este curso foi desenhado..."><?= html_escape(set_value('target_audience', !empty($course) ? $course->get_target_audience() : '')) ?></textarea>
						<span class="edu-form-hint">Formate com marcadores para detalhar os perfis profissionais recomendados.</span>
					</div>

					<div class="col-md-6 edu-form-field">
						<label class="edu-form-label" for="course-requirements">Pré-requisitos Recomendados</label>
						<textarea class="form-control edu-form-control"
						          id="course-requirements"
						          name="requirements"
						          data-rich-editor="true"
						          rows="3"
						          placeholder="Conhecimentos prévios ou ferramentas necessárias..."><?= html_escape(set_value('requirements', !empty($course) ? $course->get_requirements() : '')) ?></textarea>
						<span class="edu-form-hint">Formate com marcadores os requisitos técnicos ou conhecimentos prévios.</span>
					</div>
				</div>
			</div>

			<!-- Action Buttons -->
			<div class="edu-form-actions">
				<a href="<?= base_url('admin/cursos') ?>" class="edu-btn edu-btn-secondary">Cancelar</a>
				<button type="submit" class="edu-btn edu-btn-primary" id="btn-submit">
					<i class="bi bi-check-lg me-1" aria-hidden="true"></i> Salvar Curso
				</button>
			</div>
		</form>
	</div>
</div>
