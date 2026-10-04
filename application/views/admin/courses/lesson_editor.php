<div class="container-fluid px-0">
	<!-- Page Header -->
	<header class="edu-page-header animate-fade-up">
		<div>
			<nav class="header-breadcrumb d-flex align-items-center gap-2 mb-1" aria-label="Localização">
				<a href="<?= base_url('admin/painel') ?>" class="text-muted text-decoration-none">Painel</a>
				<i class="bi bi-chevron-right text-muted" style="font-size: 0.7rem;" aria-hidden="true"></i>
				<a href="<?= base_url('admin/cursos') ?>" class="text-muted text-decoration-none">Cursos</a>
				<i class="bi bi-chevron-right text-muted" style="font-size: 0.7rem;" aria-hidden="true"></i>
				<a href="<?= base_url('admin/cursos/' . $course['id'] . '/conteudo') ?>" class="text-muted text-decoration-none"><?= html_escape($course['title']) ?></a>
				<i class="bi bi-chevron-right text-muted" style="font-size: 0.7rem;" aria-hidden="true"></i>
				<span class="fw-medium text-body">Editor de Aula</span>
			</nav>
			<h1 class="edu-page-header-title">Editor de Aula</h1>
			<p class="edu-page-header-desc">Edite mídias de vídeo, materiais complementares e parâmetros pedagógicos desta aula.</p>
		</div>
		<div class="edu-page-header-actions">
			<a href="<?= base_url('admin/cursos/' . $course['id'] . '/conteudo') ?>" class="edu-btn edu-btn-outline">
				<i class="bi bi-arrow-left" aria-hidden="true"></i>
				<span>Voltar para Conteúdo</span>
			</a>
		</div>
	</header>

	<form id="lesson-form" onsubmit="event.preventDefault(); alert('Protótipo Visual: Nenhuma alteração foi persistida no banco.');" class="animate-fade-up animate-delay-1">
		<div class="edu-form-card mb-4">
			<!-- Section 1: Lesson Information -->
			<div class="edu-form-section">
				<h2 class="edu-form-section-title">Informações Gerais da Aula</h2>
				<p class="edu-form-section-desc">Identificação, título pedagógico e vinculação na estrutura modular.</p>

				<div class="row g-3">
					<div class="col-md-8 edu-form-field">
						<label for="lesson-title" class="edu-form-label">
							Título da Aula <span class="text-danger" aria-hidden="true">*</span>
						</label>
						<input type="text"
						       id="lesson-title"
						       name="title"
						       class="form-control"
						       value="Configurando Docker e PHP 8.2 Localmente"
						       required>
					</div>

					<div class="col-md-4 edu-form-field">
						<label for="lesson-section" class="edu-form-label">
							Seção / Módulo de Destino <span class="text-danger" aria-hidden="true">*</span>
						</label>
						<select id="lesson-section" name="section" class="form-select">
							<option value="1">Módulo 1 · Introdução e Setup</option>
							<option value="2">Módulo 1 · HTML5 e Acessibilidade</option>
							<option value="3">Módulo 2 · Flexbox e Grid</option>
							<option value="4">Módulo 3 · Backend com PHP 8</option>
						</select>
					</div>

					<div class="col-12 edu-form-field">
						<label for="lesson-desc" class="edu-form-label">Sinopse / Objetivos desta Aula</label>
						<textarea id="lesson-desc"
						          rows="3"
						          class="form-control"
						          placeholder="Explique resumidamente o que o estudante aprenderá neste vídeo...">Nesta aula prática aprenderemos a subir os contêineres Docker do Nginx, PHP-FPM 8.2 e MySQL 8, preparando o ambiente de desenvolvimento local isolado.</textarea>
					</div>
				</div>
			</div>

			<!-- Section 2: Video & Streaming -->
			<div class="edu-form-section">
				<h2 class="edu-form-section-title">Mídia de Vídeo & Streaming</h2>
				<p class="edu-form-section-desc">Integração do player de vídeo, duração e referências de transmissão.</p>

				<div class="row g-3">
					<div class="col-md-6 edu-form-field">
						<label class="edu-form-label">Pré-visualização do Vídeo</label>
						<div class="ratio ratio-16x9 rounded-3 bg-dark d-flex align-items-center justify-content-center text-white text-center border">
							<div class="d-flex flex-column align-items-center justify-content-center p-4">
								<i class="bi bi-play-circle-fill fs-1 text-primary mb-2"></i>
								<span class="small fw-semibold">Pré-visualização do Vídeo (HLS / MP4)</span>
								<span class="text-muted small">Duração detectada: 22min 15s</span>
							</div>
						</div>
					</div>

					<div class="col-md-6 edu-form-field d-flex flex-column justify-content-center">
						<div class="mb-3">
							<label for="video-url" class="edu-form-label">
								URL do Vídeo / ID de Streaming <span class="text-danger" aria-hidden="true">*</span>
							</label>
							<div class="input-group">
								<span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
								<input type="text"
								       id="video-url"
								       class="form-control font-monospace small"
								       value="https://stream.ioedu.internal/vod/2026/lesson_1002.m3u8">
							</div>
							<div class="edu-form-hint">Suporte a streams HLS (.m3u8), MP4 e embeds protegidos.</div>
						</div>

						<div class="row g-2">
							<div class="col-sm-6">
								<label for="video-duration" class="edu-form-label">Duração Manual</label>
								<input type="text" id="video-duration" class="form-control" value="22:15">
							</div>
							<div class="col-sm-6">
								<label class="edu-form-label">Download Permitido?</label>
								<select class="form-select">
									<option value="0">Não (Apenas Streaming)</option>
									<option value="1">Sim (Permitir Offline)</option>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Section 3: Materials & Attachments -->
			<div class="edu-form-section">
				<div class="d-flex justify-content-between align-items-center mb-2">
					<div>
						<h2 class="edu-form-section-title mb-0">Materiais Complementares (4 arquivos)</h2>
						<p class="edu-form-section-desc mb-0">Arquivos disponibilizados na aba de anexos da sala de aula virtual.</p>
					</div>
					<button type="button" class="edu-btn edu-btn-outline edu-btn-sm" onclick="alert('Protótipo: Upload de anexo simulado.');">
						<i class="bi bi-paperclip me-1"></i> Adicionar Arquivo
					</button>
				</div>

				<div class="list-group list-group-flush border rounded-3 mt-3">
					<div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
						<div class="d-flex align-items-center gap-2">
							<i class="bi bi-file-earmark-code text-primary fs-5"></i>
							<div>
								<span class="fw-medium text-body small d-block">docker-compose.yml</span>
								<span class="text-muted" style="font-size: 0.75rem;">Arquivo de configuração · 12 KB</span>
							</div>
						</div>
						<button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="alert('Protótipo: Remoção simulada.');">
							<i class="bi bi-trash"></i>
						</button>
					</div>
					<div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
						<div class="d-flex align-items-center gap-2">
							<i class="bi bi-file-earmark-pdf text-danger fs-5"></i>
							<div>
								<span class="fw-medium text-body small d-block">Guia_Instalacao_PHP82.pdf</span>
								<span class="text-muted" style="font-size: 0.75rem;">Documento PDF · 1.4 MB</span>
							</div>
						</div>
						<button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="alert('Protótipo: Remoção simulada.');">
							<i class="bi bi-trash"></i>
						</button>
					</div>
				</div>
			</div>

			<!-- Section 4: Pedagogical Parameters -->
			<div class="edu-form-section border-bottom-0 pb-0">
				<h2 class="edu-form-section-title">Parâmetros & Regras de Aprendizagem</h2>
				<p class="edu-form-section-desc">Condições para conclusão e desbloqueio de próximas etapas pedagógicas.</p>

				<div class="row g-3">
					<div class="col-md-6 edu-form-field">
						<div class="form-check form-switch mt-1">
							<input class="form-check-input" type="checkbox" id="lesson-required" checked>
							<label class="form-check-label fw-medium text-body" for="lesson-required">
								Aula Obrigatória para Certificado
							</label>
						</div>
						<div class="edu-form-hint ms-4">O aluno deve assistir ao menos 90% da duração para computar conclusão.</div>
					</div>

					<div class="col-md-6 edu-form-field">
						<label for="lesson-status" class="edu-form-label">Status de Publicação</label>
						<select id="lesson-status" class="form-select">
							<option value="published" selected>Publicada (Disponível aos alunos)</option>
							<option value="draft">Rascunho (Oculta)</option>
						</select>
					</div>
				</div>
			</div>

			<!-- Actions Footer -->
			<div class="edu-form-actions">
				<a href="<?= base_url('admin/cursos/' . $course['id'] . '/conteudo') ?>" class="edu-btn edu-btn-outline">
					Cancelar
				</a>
				<button type="submit" class="edu-btn edu-btn-primary">
					<i class="bi bi-floppy me-1" aria-hidden="true"></i>
					<span>Salvar Aula</span>
				</button>
			</div>
		</div>
	</form>
</div>
