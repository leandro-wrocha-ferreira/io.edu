<?php
/**
 * @var \app\domain\course\Course $course
 * @var array<\app\domain\course\CourseModule> $modules
 * @var \app\domain\course\CourseLesson|null $lesson
 */
$is_edit = $lesson !== null && $lesson->get_id() !== null;
$lesson_blocks = $is_edit ? $lesson->get_blocks() : [];
$total_blocks = count($lesson_blocks);
?>
<link rel="stylesheet" href="<?= base_url('public/assets/css/plyr.css') ?>">
<script src="<?= base_url('public/assets/js/plyr.js') ?>"></script>

<div class="container-fluid px-0">
	<!-- Page Header -->
	<header class="page-header animate-fade-up">
		<div class="page-header-info">
			<nav class="page-header-breadcrumb d-flex align-items-center gap-2 mb-1" aria-label="Localização">
				<a href="<?= base_url('admin/painel') ?>" class="text-muted text-decoration-none">Painel</a>
				<span class="sep" aria-hidden="true">/</span>
				<a href="<?= base_url('admin/cursos') ?>" class="text-muted text-decoration-none">Cursos</a>
				<span class="sep" aria-hidden="true">/</span>
				<a href="<?= base_url('admin/cursos/' . $course->get_id() . '/conteudo') ?>" class="text-muted text-decoration-none"><?= html_escape($course->get_title()) ?></a>
				<span class="sep" aria-hidden="true">/</span>
				<span class="fw-medium text-body" aria-current="page"><?= $is_edit ? 'Editar Aula' : 'Nova Aula' ?></span>
			</nav>
			<h1 class="page-header-title"><?= $is_edit ? 'Editor de Aula: ' . html_escape($lesson->get_title()) : 'Nova Aula do Curso' ?></h1>
			<p class="page-header-subtitle">Configure parâmetros pedagógicos, mídias em vídeo, textos teóricos e blocos complementares de aprendizagem.</p>
		</div>
		<div class="page-header-actions">
			<a href="<?= base_url('admin/cursos/' . $course->get_id() . '/conteudo') ?>" class="edu-btn edu-btn-outline">
				<i class="bi bi-arrow-left me-1" aria-hidden="true"></i> Voltar ao Conteúdo
			</a>
		</div>
	</header>

	<?php if ($this->session->flashdata('success')): ?>
		<div class="alert alert-success alert-dismissible fade show animate-fade-up" role="alert">
			<i class="bi bi-check-circle-fill me-2" aria-hidden="true"></i>
			<?= html_escape($this->session->flashdata('success')) ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
		</div>
	<?php endif; ?>

	<!-- Card 1: Identificação da Aula (Full Width) -->
	<div class="edu-card p-4 mb-4 animate-fade-up animate-delay-1">
		<div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
			<div>
				<h2 class="h5 fw-bold text-body mb-0">1. Identificação da Aula</h2>
				<small class="text-muted">Parâmetros essenciais, módulo pertencente e status de visibilidade para alunos.</small>
			</div>
		</div>

		<form method="post" action="<?= current_url() ?>" id="lesson-main-form">
			<input type="hidden" name="course_id" value="<?= $course->get_id() ?>">
			<?php if ($is_edit): ?>
				<input type="hidden" name="lesson_id" value="<?= $lesson->get_id() ?>">
			<?php endif; ?>

			<div class="row g-3">
				<div class="col-md-6">
					<label for="lesson-module-id" class="form-label fw-medium">
						Módulo de Destino <span class="text-danger">*</span>
					</label>
					<select id="lesson-module-id" name="module_id" class="form-select" required>
						<?php if (empty($modules)): ?>
							<option value="">Nenhum módulo criado ainda</option>
						<?php else: ?>
							<?php foreach ($modules as $module): ?>
								<option value="<?= $module->get_id() ?>" <?= ($is_edit && $lesson->get_module_id() === $module->get_id()) ? 'selected' : '' ?>>
									<?= html_escape($module->get_title()) ?>
								</option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
				</div>

				<div class="col-md-6">
					<label for="lesson-status" class="form-label fw-medium">
						Status de Publicação
					</label>
					<select id="lesson-status" name="status" class="form-select">
						<option value="draft" <?= ($is_edit && $lesson->is_draft()) ? 'selected' : '' ?>>Rascunho (visível apenas para instrutores)</option>
						<option value="published" <?= ($is_edit && $lesson->is_published()) ? 'selected' : '' ?>>Publicada (liberada para os alunos)</option>
					</select>
				</div>

				<div class="col-12">
					<label for="lesson-title" class="form-label fw-medium">
						Título da Aula <span class="text-danger">*</span>
					</label>
					<input type="text"
					       id="lesson-title"
					       name="title"
					       class="form-control"
					       value="<?= $is_edit ? html_escape($lesson->get_title()) : '' ?>"
					       placeholder="Ex: Introdução ao PHP 8.2 e DDD"
					       required>
				</div>

				<div class="col-12">
					<label for="lesson-desc" class="form-label fw-medium">Sinopse / Objetivos Pedagógicos</label>
					<textarea id="lesson-desc"
					          name="description"
					          rows="3"
					          class="form-control"
					          placeholder="Explique resumidamente o que o aluno aprenderá nesta aula..."><?= $is_edit ? html_escape((string) $lesson->get_description()) : '' ?></textarea>
				</div>
			</div>

			<div class="pt-3 mt-3 border-top d-flex justify-content-end">
				<button type="submit" class="edu-btn edu-btn-primary">
					<i class="bi bi-check2-circle me-1" aria-hidden="true"></i> Salvar Identificação da Aula
				</button>
			</div>
		</form>
	</div>

	<!-- Card 2: Conteúdo e Materiais da Aula (Full Width) -->
	<div class="edu-card p-4 animate-fade-up animate-delay-1" id="lesson-blocks-container" data-lesson-id="<?= $is_edit ? $lesson->get_id() : '' ?>">
		<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3 border-bottom pb-3">
			<div>
				<h2 class="h5 fw-bold text-body mb-0">2. Conteúdo e Materiais da Aula</h2>
				<small class="text-muted">Monte a sequência pedagógica adicionando vídeos, textos de apoio e materiais de estudo.</small>
			</div>
		</div>

		<?php if (!$is_edit): ?>
			<div class="alert alert-info border-0 bg-info-subtle text-info-emphasis p-4 rounded-3 text-center my-4">
				<i class="bi bi-info-circle-fill fs-2 d-block mb-2 text-info"></i>
				<h3 class="h6 fw-bold">Salve a identificação primeiro</h3>
				<p class="small mb-0">Preencha o formulário acima e clique em salvar para habilitar o gerenciamento de blocos de vídeo, texto e anexos.</p>
			</div>
		<?php else: ?>
			<div id="blocks-sortable-list" class="d-flex flex-column gap-3">
				<?php if (empty($lesson_blocks)): ?>
					<div class="p-5 text-center border rounded-3 bg-body-tertiary" id="empty-blocks-notice">
						<i class="bi bi-collection text-muted fs-1 mb-2 d-block"></i>
						<h3 class="h6 fw-semibold text-body">Nenhum bloco de conteúdo adicionado ainda</h3>
						<p class="text-muted small mb-3">Adicione um vídeo explicativo, artigo teórico ou material de apoio em anexo.</p>
						<div class="d-flex justify-content-center gap-2 flex-wrap">
							<button type="button" class="edu-btn edu-btn-outline edu-btn-sm" onclick="window.LessonEditorApp && window.LessonEditorApp.addNewBlockInline('video')">
								<i class="bi bi-play-circle me-1"></i> Adicionar Vídeo
							</button>
							<button type="button" class="edu-btn edu-btn-outline edu-btn-sm" onclick="window.LessonEditorApp && window.LessonEditorApp.addNewBlockInline('text')">
								<i class="bi bi-file-earmark-richtext me-1"></i> Adicionar Texto
							</button>
							<button type="button" class="edu-btn edu-btn-outline edu-btn-sm" onclick="window.LessonEditorApp && window.LessonEditorApp.addNewBlockInline('attachment')">
								<i class="bi bi-paperclip me-1"></i> Adicionar Arquivo
							</button>
							<button type="button" class="edu-btn edu-btn-outline edu-btn-sm" onclick="window.LessonEditorApp && window.LessonEditorApp.showQuizInDevelopmentAlert()">
								<i class="bi bi-question-circle me-1"></i> Adicionar Quiz
							</button>
						</div>
					</div>
				<?php else: ?>
					<?php foreach ($lesson_blocks as $block_index => $block): ?>
						<?php
						$block_type = $block->get_type();
						$payload = $block->get_payload();
						?>
						<div class="edu-block-item is-collapsed" data-block-id="<?= $block->get_id() ?>" data-block-type="<?= $block_type ?>">
							<!-- Collapsed / View Bar -->
							<div class="edu-block-view d-flex align-items-center justify-content-between">
								<div class="d-flex align-items-center gap-2 flex-wrap">
									<span class="edu-block-handle edu-reorder-handle text-muted cursor-grab" title="Arraste para reordenar bloco" aria-label="Reordenar bloco">
										<i class="bi bi-grip-vertical"></i>
									</span>

									<?php if ($block_type === 'video'): ?>
										<span class="badge bg-danger-subtle text-danger border border-danger-subtle d-flex align-items-center gap-1">
											<i class="bi bi-play-circle"></i> Vídeo
										</span>
									<?php elseif ($block_type === 'text'): ?>
										<span class="badge bg-primary-subtle text-primary border border-primary-subtle d-flex align-items-center gap-1">
											<i class="bi bi-file-earmark-richtext"></i> Texto
										</span>
									<?php elseif ($block_type === 'attachment'): ?>
										<span class="badge bg-success-subtle text-success border border-success-subtle d-flex align-items-center gap-1">
											<i class="bi bi-paperclip"></i> Anexo
										</span>
									<?php else: ?>
										<span class="badge bg-warning-subtle text-warning border border-warning-subtle d-flex align-items-center gap-1">
											<i class="bi bi-patch-question-fill"></i> Quiz
										</span>
									<?php endif; ?>

									<?php if ($block->is_required()): ?>
										<span class="badge bg-danger-subtle text-danger border border-danger-subtle small">Obrigatório</span>
									<?php else: ?>
										<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle small">Opcional</span>
									<?php endif; ?>

									<span class="text-muted small fw-medium block-index-label">Bloco #<?= $block_index + 1 ?></span>

									<!-- Inline Summary -->
									<div class="ms-2 d-none d-md-inline-block text-truncate small text-muted" style="max-width: 480px;">
										<?php if ($block_type === 'video'): ?>
											<span><strong><?= html_escape($payload['provider'] === 'youtube' ? 'YouTube' : 'Upload S3') ?>:</strong> <?= html_escape($payload['url'] ?? '') ?></span>
										<?php elseif ($block_type === 'text'): ?>
											<span><?= html_escape(substr(strip_tags((string)($payload['content'] ?? '')), 0, 70)) ?>... (mín: <?= (int)($payload['min_reading_time_in_minutes'] ?? 0) ?> min)</span>
										<?php elseif ($block_type === 'attachment'): ?>
											<span><i class="bi bi-paperclip me-1"></i><?= html_escape($payload['filename'] ?? 'arquivo') ?> (mín: <?= (int)($payload['min_reading_time_in_minutes'] ?? 0) ?> min)</span>
										<?php endif; ?>
									</div>
								</div>

								<div class="d-flex align-items-center gap-1">
									<button type="button" class="edu-btn edu-btn-ghost edu-btn-sm" title="Editar Bloco" aria-label="Editar bloco" onclick="window.LessonEditorApp && window.LessonEditorApp.toggleEditBlock(<?= $block->get_id() ?>)">
										<i class="bi bi-pencil me-1" aria-hidden="true"></i> Editar
									</button>
									<button type="button" class="edu-btn edu-btn-ghost text-danger edu-btn-sm" title="Excluir Bloco" aria-label="Excluir bloco" onclick="window.LessonEditorApp && window.LessonEditorApp.confirmDeleteBlock(<?= $block->get_id() ?>)">
										<i class="bi bi-trash" aria-hidden="true"></i>
									</button>
								</div>
							</div>

							<!-- Expanded / Inline Form -->
							<div class="edu-block-form d-none">
								<form onsubmit="event.preventDefault(); window.LessonEditorApp && window.LessonEditorApp.submitBlockItem(this);">
									<input type="hidden" name="block_id" value="<?= $block->get_id() ?>">
									<input type="hidden" name="type" value="<?= $block_type ?>">

									<div class="mb-3 p-3 border rounded bg-body-tertiary">
										<div class="form-check form-switch mb-0">
											<input class="form-check-input" type="checkbox" role="switch" id="block-req-<?= $block->get_id() ?>" name="is_required" value="1" <?= $block->is_required() ? 'checked' : '' ?>>
											<label class="form-check-label fw-semibold" for="block-req-<?= $block->get_id() ?>">
												Item de Conclusão Obrigatória
											</label>
											<div class="text-muted small">Se desmarcado, o bloco será considerado material complementar/opcional.</div>
										</div>
									</div>

									<?php if ($block_type === 'video'): ?>
										<?php
										$video_provider = ($payload['provider'] ?? '') === 'custom' || ($payload['provider'] ?? '') === 's3' ? 'upload' : 'youtube';
										$video_url = $payload['url'] ?? '';
										$duration_seconds = (int)($payload['duration_in_seconds'] ?? 0);
										?>
										<input type="hidden" name="duration_in_seconds" value="<?= $duration_seconds ?>">
										<input type="hidden" name="url" value="<?= html_escape($video_url) ?>">

										<div class="mb-3">
											<label class="form-label fw-medium">Modelo de Vídeo</label>
											<div class="d-flex gap-2">
												<div class="form-check form-check-inline">
													<input class="form-check-input" type="radio" name="video_mode_choice" id="vmode-yt-<?= $block->get_id() ?>" value="youtube" <?= $video_provider === 'youtube' ? 'checked' : '' ?> onchange="window.LessonEditorApp && window.LessonEditorApp.switchVideoMode(this, 'youtube')">
													<label class="form-check-label" for="vmode-yt-<?= $block->get_id() ?>">YouTube</label>
												</div>
												<div class="form-check form-check-inline">
													<input class="form-check-input" type="radio" name="video_mode_choice" id="vmode-up-<?= $block->get_id() ?>" value="upload" <?= $video_provider === 'upload' ? 'checked' : '' ?> onchange="window.LessonEditorApp && window.LessonEditorApp.switchVideoMode(this, 'upload')">
													<label class="form-check-label" for="vmode-up-<?= $block->get_id() ?>">Upload Direto (AWS S3)</label>
												</div>
											</div>
											<input type="hidden" name="provider" value="<?= $video_provider === 'upload' ? 'custom' : 'youtube' ?>">
										</div>

										<!-- YouTube Section -->
										<div class="video-section-youtube <?= $video_provider === 'youtube' ? '' : 'd-none' ?>">
											<div class="mb-3">
												<label class="form-label fw-medium">Identificador ou URL do Vídeo no YouTube <span class="text-danger">*</span></label>
												<input type="text" class="form-control font-monospace input-youtube-id" placeholder="Ex: dQw4w9WgXcQ ou https://www.youtube.com/watch?v=..." value="<?= $video_provider === 'youtube' ? html_escape($video_url) : '' ?>" oninput="window.LessonEditorApp && window.LessonEditorApp.handleYoutubeInput(this)">
												<small class="text-muted">Insira o ID ou cole o link do YouTube.</small>
											</div>
											<div class="youtube-player-preview-wrapper mt-2 <?= !empty($video_url) && $video_provider === 'youtube' ? '' : 'd-none' ?>">
												<div class="edu-plyr-container is-youtube">
													<div class="js-plyr-embed" data-plyr-provider="youtube" data-plyr-embed-id="<?= $video_provider === 'youtube' ? html_escape($video_url) : '' ?>"></div>
												</div>
											</div>
										</div>

										<!-- Upload Section -->
										<div class="video-section-upload <?= $video_provider === 'upload' ? '' : 'd-none' ?>">
											<?php if ($video_provider === 'upload' && !empty($video_url)): ?>
												<div class="existing-video-wrapper mb-3">
													<div class="edu-plyr-container mb-2">
														<video class="js-uploaded-plyr-video" playsinline controls src="<?= html_escape($video_url) ?>"></video>
													</div>
													<div class="d-flex justify-content-between align-items-center p-2 rounded bg-body-tertiary border">
														<span class="text-truncate small text-muted"><i class="bi bi-file-earmark-play me-1"></i> <?= html_escape(basename($video_url)) ?></span>
														<button type="button" class="edu-btn edu-btn-outline edu-btn-sm" onclick="window.LessonEditorApp && window.LessonEditorApp.toggleReplaceVideo(this)">
															<i class="bi bi-arrow-repeat me-1"></i> Substituir Vídeo
														</button>
													</div>
												</div>
											<?php endif; ?>

											<div class="upload-dropzone-wrapper <?= ($video_provider === 'upload' && !empty($video_url)) ? 'd-none' : '' ?>">
												<div class="edu-dropzone mb-2" onclick="this.nextElementSibling.click()">
													<i class="bi bi-cloud-arrow-up edu-dropzone-icon d-block"></i>
													<div class="fw-semibold">Arraste e solte o vídeo aqui ou clique para selecionar</div>
													<div class="text-muted small">Formatos aceitos: MP4, WebM, MOV. Envio multipart direto para a nuvem.</div>
												</div>
												<input type="file" class="d-none input-file-video" accept="video/mp4,video/webm,video/quicktime" onchange="window.LessonEditorApp && window.LessonEditorApp.handleVideoFileSelect(this)">
											</div>

											<div class="upload-progress-container d-none mb-2">
												<div class="d-flex justify-content-between small text-muted mb-1">
													<span class="upload-status-text">Enviando vídeo...</span>
													<span class="upload-percent-text fw-semibold">0%</span>
												</div>
												<div class="edu-upload-progress">
													<div class="edu-upload-progress-bar" style="width: 0%;"></div>
												</div>
											</div>

											<div class="uploaded-video-preview-dynamic d-none mt-2">
												<div class="edu-plyr-container"></div>
											</div>
										</div>

									<?php elseif ($block_type === 'text'): ?>
										<div class="row g-3">
											<div class="col-12">
												<label class="form-label fw-medium">Conteúdo Formatado <span class="text-danger">*</span></label>
												<textarea class="form-control edu-rich-editor" name="content" data-rich-editor="true" rows="6"><?= html_escape($payload['content'] ?? '') ?></textarea>
											</div>
											<div class="col-md-6">
												<label class="form-label fw-medium">Tempo Mínimo de Leitura (em minutos)</label>
												<input type="number" class="form-control" name="min_reading_time_in_minutes" min="0" value="<?= (int)($payload['min_reading_time_in_minutes'] ?? 0) ?>">
												<small class="text-muted">Tempo mínimo de permanência antes de liberar o avanço (0 para sem restrição).</small>
											</div>
										</div>

									<?php elseif ($block_type === 'attachment'): ?>
										<?php $attach_path = $payload['filepath'] ?? ''; ?>
										<input type="hidden" name="filepath" value="<?= html_escape($attach_path) ?>">
										<input type="hidden" name="file_size" value="<?= (int)($payload['file_size'] ?? 0) ?>">
										<input type="hidden" name="file_type" value="<?= html_escape($payload['file_type'] ?? '') ?>">

										<div class="row g-3">
											<div class="col-12">
												<?php if (!empty($attach_path)): ?>
													<div class="edu-file-info-card mb-3 existing-attachment-card">
														<div class="d-flex align-items-center gap-2 text-truncate">
															<i class="bi bi-file-earmark-check fs-4 text-primary"></i>
															<div>
																<div class="fw-semibold text-body small text-truncate"><?= html_escape($payload['filename'] ?? 'arquivo') ?></div>
																<div class="text-muted small font-monospace"><?= html_escape(basename($attach_path)) ?></div>
															</div>
														</div>
														<div class="d-flex align-items-center gap-2">
															<a href="<?= html_escape($attach_path) ?>" target="_blank" download class="edu-btn edu-btn-outline edu-btn-sm">
																<i class="bi bi-download me-1"></i> Baixar Arquivo
															</a>
															<button type="button" class="edu-btn edu-btn-ghost edu-btn-sm text-primary" onclick="window.LessonEditorApp && window.LessonEditorApp.toggleReplaceAttachment(this)">
																<i class="bi bi-arrow-repeat me-1"></i> Substituir Arquivo
															</button>
														</div>
													</div>
												<?php endif; ?>

												<div class="upload-dropzone-wrapper <?= !empty($attach_path) ? 'd-none' : '' ?>">
													<div class="edu-dropzone mb-2" onclick="this.nextElementSibling.click()">
														<i class="bi bi-file-earmark-arrow-up edu-dropzone-icon d-block"></i>
														<div class="fw-semibold">Arraste e solte o arquivo anexo aqui ou clique para selecionar</div>
														<div class="text-muted small">Documentos em PDF, apostilas, códigos compactados (Zip/Tar).</div>
													</div>
													<input type="file" class="d-none input-file-attachment" onchange="window.LessonEditorApp && window.LessonEditorApp.handleAttachmentFileSelect(this)">
												</div>

												<div class="upload-progress-container d-none mb-3">
													<div class="d-flex justify-content-between small text-muted mb-1">
														<span class="upload-status-text">Enviando anexo...</span>
														<span class="upload-percent-text fw-semibold">0%</span>
													</div>
													<div class="edu-upload-progress">
														<div class="edu-upload-progress-bar" style="width: 0%;"></div>
													</div>
												</div>
											</div>

											<div class="col-md-6">
												<label class="form-label fw-medium">Nome de Exibição do Arquivo <span class="text-danger">*</span></label>
												<input type="text" class="form-control input-attach-filename" name="filename" value="<?= html_escape($payload['filename'] ?? '') ?>" placeholder="Ex: Slides da Aula em PDF" required>
											</div>
											<div class="col-md-6">
												<label class="form-label fw-medium">Tempo Mínimo de Estudo (em minutos)</label>
												<input type="number" class="form-control" name="min_reading_time_in_minutes" min="0" value="<?= (int)($payload['min_reading_time_in_minutes'] ?? 0) ?>">
												<small class="text-muted">Se obrigatório, tempo mínimo antes de registrar como concluído.</small>
											</div>
										</div>
									<?php endif; ?>

									<div class="d-flex justify-content-end gap-2 mt-3 pt-3 border-top">
										<button type="button" class="edu-btn edu-btn-outline edu-btn-sm" onclick="window.LessonEditorApp && window.LessonEditorApp.toggleEditBlock(<?= $block->get_id() ?>)">
											Cancelar
										</button>
										<button type="submit" class="edu-btn edu-btn-primary edu-btn-sm btn-save-block">
											<i class="bi bi-check2 me-1"></i> Salvar Bloco
										</button>
									</div>
								</form>
							</div>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>

			<!-- Bottom Action Toolbar (Only shown when at least one block exists) -->
			<div class="edu-blocks-bottom-toolbar mt-3 <?= $total_blocks === 0 ? 'd-none' : '' ?>" id="blocks-bottom-toolbar">
				<span class="text-muted small fw-medium me-2">Adicionar novo bloco à aula:</span>
				<button type="button" class="edu-btn edu-btn-outline edu-btn-sm" onclick="window.LessonEditorApp && window.LessonEditorApp.addNewBlockInline('video')">
					<i class="bi bi-play-circle me-1"></i> Adicionar Vídeo
				</button>
				<button type="button" class="edu-btn edu-btn-outline edu-btn-sm" onclick="window.LessonEditorApp && window.LessonEditorApp.addNewBlockInline('text')">
					<i class="bi bi-file-earmark-richtext me-1"></i> Adicionar Texto
				</button>
				<button type="button" class="edu-btn edu-btn-outline edu-btn-sm" onclick="window.LessonEditorApp && window.LessonEditorApp.addNewBlockInline('attachment')">
					<i class="bi bi-paperclip me-1"></i> Adicionar Arquivo
				</button>
				<button type="button" class="edu-btn edu-btn-outline edu-btn-sm" onclick="window.LessonEditorApp && window.LessonEditorApp.showQuizInDevelopmentAlert()">
					<i class="bi bi-question-circle me-1"></i> Adicionar Quiz
				</button>
			</div>
		<?php endif; ?>
	</div>
</div>

<!-- Floating Uploads Active Badge -->
<div id="active-uploads-badge" class="position-fixed bottom-0 end-0 m-4 p-3 bg-body border rounded-3 shadow-lg d-none" style="z-index: 1060; min-width: 290px;">
	<div class="d-flex align-items-center justify-content-between mb-2">
		<span class="fw-semibold small"><i class="bi bi-cloud-arrow-up-fill text-primary me-1"></i> Upload em andamento</span>
		<span class="badge bg-primary-subtle text-primary small" id="active-uploads-count">1 arquivo</span>
	</div>
	<div class="edu-upload-progress mb-1">
		<div class="edu-upload-progress-bar" id="active-uploads-bar" style="width: 0%;"></div>
	</div>
	<small class="text-muted d-block" id="active-uploads-status">Aguarde a conclusão antes de sair...</small>
</div>
