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
						<label class="edu-form-label" for="course-slug">Slug na URL (Automático)</label>
						<input type="text"
						       class="form-control edu-form-control bg-edu-subtle text-muted"
						       id="course-slug"
						       name="slug"
						       readonly
						       tabindex="-1"
						       data-course-id="<?= !empty($course) ? $course->get_id() : '' ?>"
						       value="<?= html_escape(set_value('slug', !empty($course) ? (string)$course->get_slug() : '')) ?>"
						       placeholder="[id]-titulo-do-curso">
						<span class="edu-form-hint">
							<?= !empty($course) ? 'Identificador único permanente na vitrine pública.' : 'Gerado automaticamente a partir do título. O ID do curso será prefixado ao salvar.' ?>
						</span>
					</div>

					<div class="col-md-6 edu-form-field">
						<label class="edu-form-label edu-label-required" for="course-status">Status</label>
						<select class="form-select edu-form-select" id="course-status" name="status" required>
							<?php $selected_status = set_value('status', !empty($course) ? (string)$course->get_status() : 'draft'); ?>
							<option value="draft" <?= $selected_status === 'draft' ? 'selected' : '' ?> <?= (!empty($course) && !$course->is_draft()) ? 'disabled' : '' ?>>
								Rascunho <?= (!empty($course) && !$course->is_draft()) ? '(Não é permitido retornar para rascunho)' : '' ?>
							</option>
							<option value="active" <?= $selected_status === 'active' ? 'selected' : '' ?>>Ativo</option>
							<option value="inactive" <?= $selected_status === 'inactive' ? 'selected' : '' ?>>Inativo</option>
							<option value="archived" <?= $selected_status === 'archived' ? 'selected' : '' ?>>Arquivado</option>
						</select>
						<span class="edu-form-hint small" id="course-status-hint">
							<?php
								echo match ($selected_status) {
									'active' => 'Disponível para venda contínua e matrículas imediatas.',
									'inactive' => 'Inativo, temporariamente indisponível para novas matrículas.',
									'archived' => 'Vendas encerradas, acessível apenas para alunos já matriculados.',
									default => 'Invisível na vitrine, em fase de elaboração pedagógica.',
								};
							?>
						</span>
					</div>

					<div class="col-12 edu-form-field">
						<label class="edu-form-label">Imagem de Apresentação (Capa)</label>
						<div class="edu-card p-3 bg-edu-surface border-edu shadow-none">
							<div class="d-flex align-items-center gap-3 mb-2">
								<?php
									$is_url_selected = (!empty($course) && !empty($course->get_image_url()) && empty($course->get_image())) 
										|| (set_value('image_type') === 'url');
								?>
								<div class="form-check form-check-inline mb-0">
									<input class="form-check-input" type="radio" name="image_type" id="img-type-upload" value="upload" <?= !$is_url_selected ? 'checked' : '' ?>>
									<label class="form-check-label fw-medium" for="img-type-upload">Upload de Imagem</label>
								</div>
								<div class="form-check form-check-inline mb-0">
									<input class="form-check-input" type="radio" name="image_type" id="img-type-url" value="url" <?= $is_url_selected ? 'checked' : '' ?>>
									<label class="form-check-label fw-medium" for="img-type-url">URL Externa</label>
								</div>
							</div>

							<div id="image-upload-box" class="<?= $is_url_selected ? 'd-none' : '' ?>">
								<div class="edu-file-picker">
									<input type="file"
									       class="edu-file-picker-input"
									       id="course-image-file"
									       name="image_file"
									       accept="image/png,image/jpeg,image/webp,image/jpg">
									<button type="button" class="edu-file-picker-btn" id="btn-browse-file">
										<i class="bi bi-cloud-arrow-up" aria-hidden="true"></i> Selecionar Arquivo
									</button>
									<span class="edu-file-picker-label" id="course-image-file-label">Nenhum arquivo selecionado</span>
								</div>
								<span class="edu-form-hint">Formatos suportados: PNG, JPG, JPEG ou WebP (tamanho máximo: 4MB). O arquivo será salvo como <code>image.[ext]</code>.</span>
							</div>

							<div id="image-url-box" class="<?= !$is_url_selected ? 'd-none' : '' ?>">
								<input type="url"
								       class="form-control edu-form-control"
								       id="course-image-url"
								       name="image_url"
								       value="<?= html_escape(set_value('image_url', !empty($course) ? $course->get_image_url() : '')) ?>"
								       placeholder="https://exemplo.com/imagem-do-curso.jpg">
								<span class="edu-form-hint">Informe a URL pública direta da imagem compartilhada.</span>
							</div>

							<!-- Image Preview Box -->
							<?php 
								$current_display_image = !empty($course) ? $course->get_display_image() : null; 
							?>
							<div id="course-image-preview-wrapper" class="mt-3 pt-3 border-top border-edu <?= empty($current_display_image) ? 'd-none' : '' ?>">
								<div class="d-flex align-items-center gap-3">
									<div class="edu-image-preview-thumbnail border border-edu rounded overflow-hidden bg-edu-surface-alt d-flex align-items-center justify-content-center" style="width: 140px; height: 80px; flex-shrink: 0;">
										<img id="course-image-preview" 
										     src="<?= html_escape($current_display_image ?? '') ?>" 
										     alt="Pré-visualização da imagem do curso" 
										     class="w-100 h-100 object-fit-cover <?= empty($current_display_image) ? 'd-none' : '' ?>">
										<span id="course-image-preview-placeholder" class="text-muted small <?= !empty($current_display_image) ? 'd-none' : '' ?>">
											<i class="bi bi-image fs-4"></i>
										</span>
									</div>
									<div class="flex-grow-1">
										<div class="fw-semibold text-edu-heading small mb-1">Pré-visualização da Imagem</div>
										<div class="text-muted small" id="course-image-preview-info">
											<?php if (!empty($course) && !empty($course->get_image())): ?>
												Arquivo local: <code><?= html_escape($course->get_image()) ?></code>
											<?php elseif (!empty($course) && !empty($course->get_image_url())): ?>
												URL externa: <span class="text-truncate d-inline-block align-bottom" style="max-width: 250px;"><?= html_escape($course->get_image_url()) ?></span>
											<?php else: ?>
												Imagem selecionada
											<?php endif; ?>
										</div>
									</div>
								</div>
							</div>
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
								Prazo Determinado
							</option>
							<option value="lifetime" <?= $current_access_type === 'lifetime' ? 'selected' : '' ?>>
								Vitalício
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
						<span class="edu-form-hint">Carga horária oficial impressa no certificado.</span>
					</div>

					<div class="col-md-8 edu-form-field">
						<label class="edu-form-label" for="target-audience-tag-input">Público-Alvo (Perfis e Personas)</label>
						<div class="edu-tag-input-container" id="target-audience-tag-container">
							<div class="edu-tag-badges" id="target-audience-badges"></div>
							<input type="text"
							       class="edu-tag-field"
							       id="target-audience-tag-input"
							       placeholder="Digite um perfil e pressione Enter ou vírgula...">
							<input type="hidden"
							       id="course-target-audience"
							       name="target_audience"
							       value="<?= html_escape(set_value('target_audience', !empty($course) ? $course->get_target_audience() : '')) ?>">
						</div>
						<span class="edu-form-hint">Adicione personas ou perfis recomendados (Ex: Desenvolvedores Backend, Estudantes de TI).</span>
					</div>

					<div class="col-12 edu-form-field">
						<label class="edu-form-label" for="course-description">Ementa e Detalhes do Curso</label>
						<textarea class="form-control edu-form-control"
						          id="course-description"
						          name="description"
						          data-rich-editor="true"
						          rows="6"
						          placeholder="Apresentação detalhada da disciplina, metodologia e estrutura programática..."><?= set_value('description', !empty($course) ? $course->get_description() : '') ?></textarea>
						<span class="edu-form-hint">Utilize a barra de ferramentas para formatar o texto com títulos, negrito, listas e citações.</span>
					</div>

					<div class="col-12 edu-form-field">
						<label class="edu-form-label" for="course-requirements">Pré-requisitos Recomendados</label>
						<textarea class="form-control edu-form-control"
						          id="course-requirements"
						          name="requirements"
						          data-rich-editor="true"
						          rows="4"
						          placeholder="Conhecimentos prévios, ferramentas necessárias ou perfil acadêmico requerido..."><?= set_value('requirements', !empty($course) ? $course->get_requirements() : '') ?></textarea>
						<span class="edu-form-hint">Formate com marcadores os requisitos técnicos, experiências prévias ou ferramentas recomendadas.</span>
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
