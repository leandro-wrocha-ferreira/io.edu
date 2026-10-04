<!-- Classroom Header Nav -->
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3 animate-fade-up">
	<div>
		<div class="d-flex align-items-center gap-2 mb-1">
			<a href="<?= base_url('aluno/painel') ?>" class="text-edu-muted text-decoration-none fs-7">
				<i class="bi bi-chevron-left" aria-hidden="true"></i> Voltar ao Meu Painel
			</a>
			<span class="text-edu-muted">·</span>
			<span class="edu-badge edu-badge-primary">Módulo 2</span>
		</div>
		<h1 class="h4 fw-bold text-edu-heading mb-0">Desenvolvimento Web Moderno com Arquitetura Limpa</h1>
	</div>
	<div class="d-flex align-items-center gap-3">
		<div class="d-none d-md-block text-end">
			<span class="fs-7 text-edu-muted d-block">Progresso Geral</span>
			<span class="fs-6 fw-bold text-edu-heading">45% Concluído</span>
		</div>
		<button class="edu-btn edu-btn-outline edu-btn-sm" id="btn-toggle-playlist" aria-label="Alternar exibição da playlist">
			<i class="bi bi-layout-sidebar-reverse" aria-hidden="true"></i>
			<span class="d-none d-sm-inline">Playlist</span>
		</button>
	</div>
</div>

<!-- Main Classroom Grid -->
<div class="row g-4 animate-fade-up animate-delay-1">
	<!-- Left Player Column -->
	<div class="col-lg-8" id="classroom-player-col">
		<div class="edu-card overflow-hidden mb-4">
			<!-- 16:9 Player Viewport -->
			<div class="edu-video-player-frame position-relative">
				<div class="edu-video-placeholder">
					<div class="edu-video-placeholder-icon">
						<i class="bi bi-play-circle-fill" aria-hidden="true"></i>
					</div>
					<h2 class="h5 text-white mb-1">Aula 4: Padrões Resilientes e Arquitetura Limpa</h2>
					<p class="fs-7 text-white-50 mb-0">Clique para iniciar a reprodução em 1080p</p>
				</div>
			</div>

			<!-- Player Control Bar & Next Step -->
			<div class="edu-card-body p-3 bg-edu-subtle d-flex align-items-center justify-content-between flex-wrap gap-2">
				<div class="d-flex align-items-center gap-2">
					<button class="edu-btn edu-btn-secondary edu-btn-sm" aria-label="Aula anterior" title="Aula anterior">
						<i class="bi bi-skip-backward-fill" aria-hidden="true"></i>
					</button>
					<button class="edu-btn edu-btn-primary edu-btn-sm" id="btn-play-trigger">
						<i class="bi bi-play-fill" aria-hidden="true"></i> Reproduzir
					</button>
					<button class="edu-btn edu-btn-secondary edu-btn-sm" aria-label="Próxima aula" title="Próxima aula">
						<i class="bi bi-skip-forward-fill" aria-hidden="true"></i>
					</button>
					<span class="fs-7 text-edu-muted ms-2 tabular-nums">00:00 / 24:18</span>
				</div>
				<div class="d-flex align-items-center gap-2">
					<button class="edu-btn edu-btn-success edu-btn-sm" id="btn-mark-completed">
						<i class="bi bi-check2-circle" aria-hidden="true"></i> Marcar como Concluída
					</button>
				</div>
			</div>
		</div>

		<!-- Lesson Resource Tabs -->
		<div class="edu-card">
			<div class="edu-card-header p-0">
				<ul class="nav nav-tabs border-0 px-3 pt-2" id="lessonTabs" role="tablist">
					<li class="nav-item" role="presentation">
						<button class="nav-link active fw-semibold fs-7 py-2" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab" aria-controls="overview" aria-selected="true">
							<i class="bi bi-info-circle me-1" aria-hidden="true"></i> Visão Geral
						</button>
					</li>
					<li class="nav-item" role="presentation">
						<button class="nav-link fw-semibold fs-7 py-2" id="materials-tab" data-bs-toggle="tab" data-bs-target="#materials" type="button" role="tab" aria-controls="materials" aria-selected="false">
							<i class="bi bi-paperclip me-1" aria-hidden="true"></i> Materiais de Apoio
						</button>
					</li>
					<li class="nav-item" role="presentation">
						<button class="nav-link fw-semibold fs-7 py-2" id="notes-tab" data-bs-toggle="tab" data-bs-target="#notes" type="button" role="tab" aria-controls="notes" aria-selected="false">
							<i class="bi bi-journal-text me-1" aria-hidden="true"></i> Minhas Anotações
						</button>
					</li>
				</ul>
			</div>
			<div class="edu-card-body">
				<div class="tab-content" id="lessonTabsContent">
					<!-- Overview -->
					<div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
						<h3 class="h6 fw-bold text-edu-heading mb-2">Sobre esta lição</h3>
						<p class="text-edu-main fs-7 mb-3 leading-relaxed">
							Nesta aula, exploramos a separação rigorosa de camadas em sistemas corporativos utilizando Domain-Driven Design simplificado (DDD-Lite). Abordaremos o isolamento de entidades de domínio, orquestração desacoplada em Use Cases e o tratamento centralizado de exceções.
						</p>
						<div class="d-flex align-items-center gap-3 fs-7 text-edu-muted">
							<span><i class="bi bi-person me-1" aria-hidden="true"></i> Instrutor: <strong>Prof. João Silva</strong></span>
							<span>·</span>
							<span><i class="bi bi-clock me-1" aria-hidden="true"></i> Duração: 24 minutos</span>
						</div>
					</div>

					<!-- Materials -->
					<div class="tab-pane fade" id="materials" role="tabpanel" aria-labelledby="materials-tab">
						<h3 class="h6 fw-bold text-edu-heading mb-3">Arquivos e Leituras Complementares</h3>
						<div class="d-flex flex-column gap-2">
							<a href="#" class="edu-card p-3 d-flex align-items-center justify-content-between text-decoration-none edu-card-interactive">
								<div class="d-flex align-items-center gap-3">
									<i class="bi bi-file-earmark-pdf text-danger fs-3" aria-hidden="true"></i>
									<div>
										<h4 class="fs-7 fw-bold text-edu-heading mb-0">Apostila Completa do Módulo 2 (PDF)</h4>
										<span class="fs-8 text-edu-muted">4.8 MB · Leitura recomendada</span>
									</div>
								</div>
								<i class="bi bi-download text-edu-primary" aria-hidden="true"></i>
							</a>
							<a href="#" class="edu-card p-3 d-flex align-items-center justify-content-between text-decoration-none edu-card-interactive">
								<div class="d-flex align-items-center gap-3">
									<i class="bi bi-file-earmark-code text-edu-primary fs-3" aria-hidden="true"></i>
									<div>
										<h4 class="fs-7 fw-bold text-edu-heading mb-0">Código-Fonte dos Exemplos Práticos (ZIP)</h4>
										<span class="fs-8 text-edu-muted">1.2 MB · Repositório de apoio</span>
									</div>
								</div>
								<i class="bi bi-download text-edu-primary" aria-hidden="true"></i>
							</a>
						</div>
					</div>

					<!-- Notes -->
					<div class="tab-pane fade" id="notes" role="tabpanel" aria-labelledby="notes-tab">
						<h3 class="h6 fw-bold text-edu-heading mb-2">Caderno Digital do Aluno</h3>
						<p class="text-edu-muted fs-7 mb-3">Suas anotações são salvas automaticamente vinculadas ao timestamp desta aula.</p>
						<div class="edu-form-group mb-3">
							<textarea class="edu-form-control" rows="4" placeholder="Escreva seus apontamentos aqui..."></textarea>
						</div>
						<button class="edu-btn edu-btn-secondary edu-btn-sm">
							<i class="bi bi-save" aria-hidden="true"></i> Salvar Anotações
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Right Playlist Column -->
	<div class="col-lg-4" id="classroom-playlist-col">
		<div class="edu-card h-100">
			<div class="edu-card-header">
				<h2 class="h6 fw-bold text-edu-heading mb-0">Grade de Conteúdo</h2>
				<span class="fs-8 text-edu-muted">8 de 18 concluídas</span>
			</div>
			<div class="edu-card-body p-0">
				<!-- Module 1 -->
				<div class="p-3 border-bottom border-edu bg-edu-subtle">
					<div class="d-flex align-items-center justify-content-between">
						<span class="fw-bold fs-7 text-edu-heading">Módulo 1: Fundamentos & Setup</span>
						<span class="edu-badge edu-badge-success fs-8">100%</span>
					</div>
				</div>
				<ul class="edu-curriculum-list mb-0">
					<li>
						<a href="#" class="edu-curriculum-item">
							<div class="edu-lesson-info">
								<i class="bi bi-check-circle-fill edu-lesson-status-completed" aria-hidden="true"></i>
								<span>1. Apresentação e Objetivos</span>
							</div>
							<span class="edu-lesson-duration">12 min</span>
						</a>
					</li>
					<li>
						<a href="#" class="edu-curriculum-item">
							<div class="edu-lesson-info">
								<i class="bi bi-check-circle-fill edu-lesson-status-completed" aria-hidden="true"></i>
								<span>2. Setup do Ambiente com Docker</span>
							</div>
							<span class="edu-lesson-duration">18 min</span>
						</a>
					</li>
				</ul>

				<!-- Module 2 (Active) -->
				<div class="p-3 border-bottom border-top border-edu bg-edu-subtle">
					<div class="d-flex align-items-center justify-content-between">
						<span class="fw-bold fs-7 text-edu-heading">Módulo 2: Arquitetura & Domínio</span>
						<span class="edu-badge edu-badge-primary fs-8">Em Curso</span>
					</div>
				</div>
				<ul class="edu-curriculum-list mb-0">
					<li>
						<a href="#" class="edu-curriculum-item">
							<div class="edu-lesson-info">
								<i class="bi bi-check-circle-fill edu-lesson-status-completed" aria-hidden="true"></i>
								<span>3. Entities e Value Objects</span>
							</div>
							<span class="edu-lesson-duration">22 min</span>
						</a>
					</li>
					<li>
						<a href="#" class="edu-curriculum-item active">
							<div class="edu-lesson-info">
								<i class="bi bi-play-circle-fill edu-lesson-status-active" aria-hidden="true"></i>
								<span>4. Padrões Resilientes em Nuvem</span>
							</div>
							<span class="edu-lesson-duration">24 min</span>
						</a>
					</li>
					<li>
						<a href="#" class="edu-curriculum-item">
							<div class="edu-lesson-info">
								<i class="bi bi-circle edu-lesson-status-active text-edu-muted" aria-hidden="true"></i>
								<span>5. Mappers e DTOs de Infraestrutura</span>
							</div>
							<span class="edu-lesson-duration">19 min</span>
						</a>
					</li>
				</ul>

				<!-- Module 3 (Locked) -->
				<div class="p-3 border-bottom border-top border-edu bg-edu-subtle opacity-75">
					<div class="d-flex align-items-center justify-content-between">
						<span class="fw-bold fs-7 text-edu-heading">Módulo 3: Testes & Homologação</span>
						<span class="edu-badge edu-badge-neutral fs-8">Bloqueado</span>
					</div>
				</div>
				<ul class="edu-curriculum-list mb-0">
					<li>
						<a href="#" class="edu-curriculum-item locked">
							<div class="edu-lesson-info">
								<i class="bi bi-lock-fill edu-lesson-status-locked" aria-hidden="true"></i>
								<span>6. Testes Unitários de Use Cases</span>
							</div>
							<span class="edu-lesson-duration">25 min</span>
						</a>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>
