<!-- Page Welcome Banner -->
<div class="page-header animate-fade-up">
	<div class="page-header-info">
		<p class="page-header-breadcrumb">
			<i class="bi bi-mortarboard" aria-hidden="true"></i>
			<span class="sep">›</span>
			<span>Área do Aluno</span>
			<span class="sep">›</span>
			<span>Painel Principal</span>
		</p>
		<h1 class="page-header-title">Bem-vindo(a) de volta, <?= html_escape($user_name ?? 'Estudante') ?>!</h1>
		<p class="page-header-subtitle">Pronto para dar o próximo passo na sua formação?</p>
	</div>
	<div class="page-header-actions">
		<a href="<?= base_url('cursos') ?>" class="btn-theme-primary-outline">
			<i class="bi bi-search" aria-hidden="true"></i> Explorar Catálogo
		</a>
	</div>
</div>

<!-- SECTION 1: Continuar de Onde Parou (Top Priority UX) -->
<section class="mb-5 animate-fade-up animate-delay-1" aria-labelledby="section-resume-title">
	<div class="d-flex align-items-center justify-content-between mb-3">
		<div class="d-flex align-items-center gap-2">
			<span class="edu-badge edu-badge-primary">
				<i class="bi bi-play-circle-fill" aria-hidden="true"></i> Em Andamento
			</span>
			<h2 class="h5 fw-bold mb-0 text-edu-heading" id="section-resume-title">Continuar de Onde Parou</h2>
		</div>
	</div>

	<div class="edu-card p-4">
		<div class="row align-items-center g-4">
			<div class="col-lg-3 col-md-4">
				<div class="edu-course-media rounded-3" style="aspect-ratio: 16/9; background: var(--edu-primary-gradient); display: flex; align-items: center; justify-content: center;">
					<i class="bi bi-camera-reels text-white fs-1" aria-hidden="true"></i>
				</div>
			</div>
			<div class="col-lg-6 col-md-5">
				<span class="edu-badge edu-badge-neutral mb-2">Módulo 2 · Aula 4</span>
				<h3 class="h5 fw-bold text-edu-heading mb-1">Fundamentos Avançados e Aplicações Práticas</h3>
				<p class="text-edu-muted fs-7 mb-3">Última aula assistida há 2 dias · 18 min restantes</p>

				<!-- Dual Progress Indicator -->
				<div class="edu-progress edu-progress-sm">
					<div class="edu-progress-header">
						<span class="edu-progress-label">Progresso do curso</span>
						<span class="edu-progress-val">45%</span>
					</div>
					<div class="edu-progress-track" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100">
						<div class="edu-progress-bar" style="width: 45%;"></div>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-3 text-md-end">
				<a href="<?= base_url('aluno/aula/1') ?>" class="edu-btn edu-btn-primary edu-btn-lg w-100">
					<i class="bi bi-play-fill fs-5" aria-hidden="true"></i> Retomar Aula
				</a>
			</div>
		</div>
	</div>
</section>

<!-- SECTION 2: Métricas do Aluno -->
<section class="mb-5 animate-fade-up animate-delay-2" aria-labelledby="section-stats-title">
	<h2 class="visually-hidden" id="section-stats-title">Estatísticas de Aprendizado</h2>
	<div class="row g-4">
		<div class="col-md-4">
			<div class="edu-card p-3 d-flex align-items-center gap-3">
				<div class="stat-icon-wrap stat-icon-primary">
					<i class="bi bi-clock-history" aria-hidden="true"></i>
				</div>
				<div>
					<span class="stat-label">Horas Cursadas</span>
					<div class="stat-value">32<span class="fs-6 text-edu-muted fw-normal">h</span></div>
					<div class="stat-meta">Tempo total dedicado</div>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="edu-card p-3 d-flex align-items-center gap-3">
				<div class="stat-icon-wrap stat-icon-success">
					<i class="bi bi-journal-check" aria-hidden="true"></i>
				</div>
				<div>
					<span class="stat-label">Cursos em Curso</span>
					<div class="stat-value">3</div>
					<div class="stat-meta">Matrículas ativas</div>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="edu-card p-3 d-flex align-items-center gap-3">
				<div class="stat-icon-wrap stat-icon-warning">
					<i class="bi bi-award-fill" aria-hidden="true"></i>
				</div>
				<div>
					<span class="stat-label">Certificados</span>
					<div class="stat-value">2</div>
					<div class="stat-meta">Aptos para download</div>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- SECTION 3: Minhas Jornadas & Formações Contínuas (Modelo Nova ESA) -->
<section class="mb-5 animate-fade-up animate-delay-3" aria-labelledby="section-journeys-title">
	<div class="d-flex align-items-center justify-content-between mb-3">
		<div>
			<h2 class="h5 fw-bold text-edu-heading mb-1" id="section-journeys-title">Minhas Trilhas & Jornadas</h2>
			<p class="text-edu-muted fs-7 mb-0">Formações integradas com metas e prazos oficiais</p>
		</div>
		<a href="<?= base_url('aluno/jornadas') ?>" class="edu-btn edu-btn-ghost edu-btn-sm">
			Ver todas as trilhas <i class="bi bi-arrow-right" aria-hidden="true"></i>
		</a>
	</div>

	<div class="row g-4">
		<div class="col-lg-6">
			<div class="edu-journey-card">
				<div class="edu-journey-top">
					<div>
						<span class="edu-badge edu-badge-deadline mb-2">
							<i class="bi bi-hourglass-split" aria-hidden="true"></i> Faltam 45 dias
						</span>
						<h3 class="edu-journey-title">Programa de Qualificação Profissional Contínua</h3>
					</div>
					<span class="edu-badge edu-badge-mandatory">Obrigatório</span>
				</div>
				<p class="edu-journey-desc">
					Trilha de competências regulatórias obrigatória para emissão da certificação anual e renovação de credenciais.
				</p>

				<div class="edu-journey-milestones">
					<div class="edu-journey-milestone-header">
						<span>Progresso da Trilha</span>
						<span class="edu-journey-progress-text">3 de 5 cursos concluídos (60%)</span>
					</div>
					<div class="edu-progress-track" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100">
						<div class="edu-progress-bar" style="width: 60%;"></div>
					</div>
				</div>

				<div class="edu-journey-footer">
					<span class="fs-7 text-edu-muted">Prazo limite: 15/12/2026</span>
					<a href="<?= base_url('aluno/jornadas') ?>" class="edu-btn edu-btn-secondary edu-btn-sm">
						Conferir Situação
					</a>
				</div>
			</div>
		</div>

		<div class="col-lg-6">
			<div class="edu-journey-card">
				<div class="edu-journey-top">
					<div>
						<span class="edu-badge edu-badge-accent mb-2">
							<i class="bi bi-star-fill" aria-hidden="true"></i> Trilha de Carreira
						</span>
						<h3 class="edu-journey-title">Especialização em Gestão e Liderança Estratégica</h3>
					</div>
					<span class="edu-badge edu-badge-primary">Em Andamento</span>
				</div>
				<p class="edu-journey-desc">
					Conjunto de cursos direcionados ao aprimoramento de tomada de decisão, gestão de equipes ágeis e governança.
				</p>

				<div class="edu-journey-milestones">
					<div class="edu-journey-milestone-header">
						<span>Progresso da Trilha</span>
						<span class="edu-journey-progress-text">1 de 4 cursos concluídos (25%)</span>
					</div>
					<div class="edu-progress-track" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
						<div class="edu-progress-bar" style="width: 25%;"></div>
					</div>
				</div>

				<div class="edu-journey-footer">
					<span class="fs-7 text-edu-muted">Carga total: 180 horas</span>
					<a href="<?= base_url('aluno/jornadas') ?>" class="edu-btn edu-btn-secondary edu-btn-sm">
						Continuar Trilha
					</a>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- SECTION 4: Meus Cursos Matriculados -->
<section class="mb-5 animate-fade-up animate-delay-4" aria-labelledby="section-enrolled-title">
	<div class="d-flex align-items-center justify-content-between mb-3">
		<div>
			<h2 class="h5 fw-bold text-edu-heading mb-1" id="section-enrolled-title">Meus Cursos Matriculados</h2>
			<p class="text-edu-muted fs-7 mb-0">Acesse seus cursos ativos e continue suas lições</p>
		</div>
	</div>

	<div class="row g-4">
		<!-- Curso 1 -->
		<div class="col-lg-4 col-md-6">
			<div class="edu-course-card">
				<div class="edu-course-media">
					<div class="w-100 h-100 d-flex align-items-center justify-content-center text-white" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
						<i class="bi bi-code-slash fs-1" aria-hidden="true"></i>
					</div>
					<span class="edu-badge edu-badge-primary edu-course-category-badge">Tecnologia</span>
				</div>
				<div class="edu-course-body">
					<div class="edu-course-meta">
						<span class="edu-course-meta-item"><i class="bi bi-clock" aria-hidden="true"></i> 40h</span>
						<span class="edu-course-meta-item"><i class="bi bi-collection-play" aria-hidden="true"></i> 28 aulas</span>
					</div>
					<h3 class="edu-course-title">
						<a href="<?= base_url('aluno/aula/1') ?>">Desenvolvimento Web Moderno com Arquitetura Limpa</a>
					</h3>
					<div class="edu-course-instructor">
						<div class="edu-course-instructor-avatar d-flex align-items-center justify-content-center text-white fw-bold fs-7" style="background: #4f46e5;">
							JS
						</div>
						<span class="edu-course-instructor-name">Prof. João Silva</span>
					</div>

					<div class="edu-course-progress-wrap mt-auto">
						<div class="edu-progress edu-progress-sm">
							<div class="edu-progress-header">
								<span class="edu-progress-label">Progresso</span>
								<span class="edu-progress-val">70%</span>
							</div>
							<div class="edu-progress-track">
								<div class="edu-progress-bar edu-progress-bar-success" style="width: 70%;"></div>
							</div>
						</div>
					</div>

					<div class="edu-course-footer">
						<span class="fs-7 text-edu-success fw-bold">
							<i class="bi bi-check-circle" aria-hidden="true"></i> Matrícula Ativa
						</span>
						<a href="<?= base_url('aluno/aula/1') ?>" class="edu-btn edu-btn-primary edu-btn-sm">
							Acessar Aulas
						</a>
					</div>
				</div>
			</div>
		</div>

		<!-- Curso 2 -->
		<div class="col-lg-4 col-md-6">
			<div class="edu-course-card">
				<div class="edu-course-media">
					<div class="w-100 h-100 d-flex align-items-center justify-content-center text-white" style="background: linear-gradient(135deg, #065f46 0%, #10b981 100%);">
						<i class="bi bi-shield-lock fs-1" aria-hidden="true"></i>
					</div>
					<span class="edu-badge edu-badge-accent edu-course-category-badge">Segurança</span>
				</div>
				<div class="edu-course-body">
					<div class="edu-course-meta">
						<span class="edu-course-meta-item"><i class="bi bi-clock" aria-hidden="true"></i> 20h</span>
						<span class="edu-course-meta-item"><i class="bi bi-collection-play" aria-hidden="true"></i> 14 aulas</span>
					</div>
					<h3 class="edu-course-title">
						<a href="<?= base_url('aluno/aula/1') ?>">Segurança da Informação e Proteção de Dados</a>
					</h3>
					<div class="edu-course-instructor">
						<div class="edu-course-instructor-avatar d-flex align-items-center justify-content-center text-white fw-bold fs-7" style="background: #10b981;">
							MC
						</div>
						<span class="edu-course-instructor-name">Dra. Mariana Costa</span>
					</div>

					<div class="edu-course-progress-wrap mt-auto">
						<div class="edu-progress edu-progress-sm">
							<div class="edu-progress-header">
								<span class="edu-progress-label">Progresso</span>
								<span class="edu-progress-val">25%</span>
							</div>
							<div class="edu-progress-track">
								<div class="edu-progress-bar" style="width: 25%;"></div>
							</div>
						</div>
					</div>

					<div class="edu-course-footer">
						<span class="fs-7 text-edu-success fw-bold">
							<i class="bi bi-check-circle" aria-hidden="true"></i> Matrícula Ativa
						</span>
						<a href="<?= base_url('aluno/aula/1') ?>" class="edu-btn edu-btn-primary edu-btn-sm">
							Acessar Aulas
						</a>
					</div>
				</div>
			</div>
		</div>

		<!-- Curso 3 -->
		<div class="col-lg-4 col-md-6">
			<div class="edu-course-card">
				<div class="edu-course-media">
					<div class="w-100 h-100 d-flex align-items-center justify-content-center text-white" style="background: linear-gradient(135deg, #7c2d12 0%, #ea580c 100%);">
						<i class="bi bi-graph-up-arrow fs-1" aria-hidden="true"></i>
					</div>
					<span class="edu-badge edu-badge-warning edu-course-category-badge">Gestão</span>
				</div>
				<div class="edu-course-body">
					<div class="edu-course-meta">
						<span class="edu-course-meta-item"><i class="bi bi-clock" aria-hidden="true"></i> 30h</span>
						<span class="edu-course-meta-item"><i class="bi bi-collection-play" aria-hidden="true"></i> 20 aulas</span>
					</div>
					<h3 class="edu-course-title">
						<a href="<?= base_url('aluno/aula/1') ?>">Gestão de Riscos e Governança Corporativa</a>
					</h3>
					<div class="edu-course-instructor">
						<div class="edu-course-instructor-avatar d-flex align-items-center justify-content-center text-white fw-bold fs-7" style="background: #ea580c;">
							RA
						</div>
						<span class="edu-course-instructor-name">Prof. Roberto Alves</span>
					</div>

					<div class="edu-course-progress-wrap mt-auto">
						<div class="edu-progress edu-progress-sm">
							<div class="edu-progress-header">
								<span class="edu-progress-label">Progresso</span>
								<span class="edu-progress-val">10%</span>
							</div>
							<div class="edu-progress-track">
								<div class="edu-progress-bar" style="width: 10%;"></div>
							</div>
						</div>
					</div>

					<div class="edu-course-footer">
						<span class="fs-7 text-edu-success fw-bold">
							<i class="bi bi-check-circle" aria-hidden="true"></i> Matrícula Ativa
						</span>
						<a href="<?= base_url('aluno/aula/1') ?>" class="edu-btn edu-btn-primary edu-btn-sm">
							Acessar Aulas
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
