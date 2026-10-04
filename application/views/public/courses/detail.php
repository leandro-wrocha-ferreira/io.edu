<!-- Course Detail Banner -->
<section class="py-5 bg-edu-surface border-bottom border-edu" aria-labelledby="course-detail-title">
	<div class="container">
		<nav aria-label="breadcrumb" class="mb-3">
			<ol class="breadcrumb fs-7 mb-0">
				<li class="breadcrumb-item"><a href="<?= base_url('cursos') ?>" class="text-edu-muted text-decoration-none">Catálogo</a></li>
				<li class="breadcrumb-item"><a href="<?= base_url('cursos?cat=tecnologia') ?>" class="text-edu-muted text-decoration-none">Tecnologia</a></li>
				<li class="breadcrumb-item active text-edu-heading" aria-current="page">Desenvolvimento Web Moderno</li>
			</ol>
		</nav>

		<div class="row g-5 align-items-start">
			<!-- Main Left Column -->
			<div class="col-lg-8 animate-fade-up">
				<div class="d-flex align-items-center gap-2 mb-3">
					<span class="edu-badge edu-badge-primary">Tecnologia & Software</span>
					<span class="edu-badge edu-badge-accent">Nível Intermediário</span>
					<span class="edu-badge edu-badge-success"><i class="bi bi-patch-check-fill me-1" aria-hidden="true"></i> Certificado Reconhecido</span>
				</div>

				<h1 class="display-6 fw-bold text-edu-heading mb-3" id="course-detail-title">
					Desenvolvimento Web Moderno com Arquitetura Limpa
				</h1>

				<p class="lead text-edu-muted fs-6 mb-4">
					Aprenda a projetar, desenvolver e testar aplicações robustas e seguras utilizando práticas modernas de Domain-Driven Design (DDD-Lite), testes automatizados e clean architecture.
				</p>

				<!-- Instructor Bio Card -->
				<div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-edu-subtle border-edu mb-4">
					<div class="edu-course-instructor-avatar d-flex align-items-center justify-content-center text-white fw-bold fs-6" style="width: 48px; height: 48px; background: var(--edu-primary-gradient);">
						JS
					</div>
					<div>
						<span class="fs-8 text-edu-muted text-uppercase fw-bold letter-spacing-1 d-block">Especialista & Docente</span>
						<h2 class="h6 fw-bold text-edu-heading mb-0">Prof. João Silva, MsC</h2>
						<span class="fs-7 text-edu-muted">Arquiteto de Software sênior com 15+ anos de experiência corporativa.</span>
					</div>
				</div>

				<!-- Quick Highlights Grid -->
				<div class="row g-3 mb-5 text-edu-heading">
					<div class="col-sm-4 col-6">
						<div class="p-3 border-edu rounded-3 bg-edu-surface">
							<span class="fs-8 text-edu-muted d-block"><i class="bi bi-clock me-1 text-edu-primary" aria-hidden="true"></i> Carga Horária</span>
							<strong class="fs-6">40 Horas de Estudo</strong>
						</div>
					</div>
					<div class="col-sm-4 col-6">
						<div class="p-3 border-edu rounded-3 bg-edu-surface">
							<span class="fs-8 text-edu-muted d-block"><i class="bi bi-collection-play me-1 text-edu-primary" aria-hidden="true"></i> Total de Lições</span>
							<strong class="fs-6">28 Videoaulas em HD</strong>
						</div>
					</div>
					<div class="col-sm-4 col-6">
						<div class="p-3 border-edu rounded-3 bg-edu-surface">
							<span class="fs-8 text-edu-muted d-block"><i class="bi bi-infinity me-1 text-edu-primary" aria-hidden="true"></i> Período de Acesso</span>
							<strong class="fs-6">Acesso Vitalício</strong>
						</div>
					</div>
				</div>

				<!-- What You'll Learn Section -->
				<section class="mb-5" aria-labelledby="learning-outcomes-title">
					<h3 class="h5 fw-bold text-edu-heading mb-3" id="learning-outcomes-title">O que você vai dominar neste curso:</h3>
					<div class="row g-3">
						<div class="col-md-6">
							<div class="d-flex align-items-start gap-2">
								<i class="bi bi-check-circle-fill text-edu-success mt-1" aria-hidden="true"></i>
								<span class="fs-7 text-edu-main">Isolamento rigoroso de regras de negócio em entidades puras.</span>
							</div>
						</div>
						<div class="col-md-6">
							<div class="d-flex align-items-start gap-2">
								<i class="bi bi-check-circle-fill text-edu-success mt-1" aria-hidden="true"></i>
								<span class="fs-7 text-edu-main">Construção de Use Cases desacoplados de frameworks e persistência.</span>
							</div>
						</div>
						<div class="col-md-6">
							<div class="d-flex align-items-start gap-2">
								<i class="bi bi-check-circle-fill text-edu-success mt-1" aria-hidden="true"></i>
								<span class="fs-7 text-edu-main">Design Tokens e interfaces acessíveis com conformidade WCAG AA.</span>
							</div>
						</div>
						<div class="col-md-6">
							<div class="d-flex align-items-start gap-2">
								<i class="bi bi-check-circle-fill text-edu-success mt-1" aria-hidden="true"></i>
								<span class="fs-7 text-edu-main">Criação de suítes de testes unitários com mais de 80% de cobertura.</span>
							</div>
						</div>
					</div>
				</section>

				<!-- Syllabus Curriculum Section (Accordion) -->
				<section class="mb-5" aria-labelledby="syllabus-title">
					<div class="d-flex align-items-center justify-content-between mb-3">
						<h3 class="h5 fw-bold text-edu-heading mb-0" id="syllabus-title">Ementa Curricular Completa</h3>
						<span class="fs-7 text-edu-muted">4 Módulos · 28 Aulas</span>
					</div>

					<div class="edu-curriculum" id="courseCurriculum">
						<!-- Module 1 -->
						<div class="edu-curriculum-module">
							<button class="edu-curriculum-header" type="button" data-bs-toggle="collapse" data-bs-target="#mod1Collapse" aria-expanded="true" aria-controls="mod1Collapse">
								<span class="edu-curriculum-module-title">Módulo 1: Fundamentos & Princípios de Engenharia</span>
								<span class="edu-curriculum-module-meta">
									<span>6 aulas</span>
									<span>·</span>
									<span>8h 30min</span>
									<i class="bi bi-chevron-down" aria-hidden="true"></i>
								</span>
							</button>
							<div id="mod1Collapse" class="collapse show" data-bs-parent="#courseCurriculum">
								<ul class="edu-curriculum-list">
									<li class="edu-curriculum-item">
										<div class="edu-lesson-info">
											<i class="bi bi-play-circle text-edu-primary" aria-hidden="true"></i>
											<span>1. Introdução à arquitetura de software limpa</span>
										</div>
										<span class="edu-lesson-duration">22 min</span>
									</li>
									<li class="edu-curriculum-item">
										<div class="edu-lesson-info">
											<i class="bi bi-play-circle text-edu-primary" aria-hidden="true"></i>
											<span>2. Por que arquiteturas convencionais quebram em escala</span>
										</div>
										<span class="edu-lesson-duration">35 min</span>
									</li>
									<li class="edu-curriculum-item">
										<div class="edu-lesson-info">
											<i class="bi bi-play-circle text-edu-primary" aria-hidden="true"></i>
											<span>3. Configuração de containers e orquestração Docker</span>
										</div>
										<span class="edu-lesson-duration">40 min</span>
									</li>
								</ul>
							</div>
						</div>

						<!-- Module 2 -->
						<div class="edu-curriculum-module">
							<button class="edu-curriculum-header collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#mod2Collapse" aria-expanded="false" aria-controls="mod2Collapse">
								<span class="edu-curriculum-module-title">Módulo 2: Domínio e Casos de Uso (DDD-Lite)</span>
								<span class="edu-curriculum-module-meta">
									<span>8 aulas</span>
									<span>·</span>
									<span>12h 15min</span>
									<i class="bi bi-chevron-down" aria-hidden="true"></i>
								</span>
							</button>
							<div id="mod2Collapse" class="collapse" data-bs-parent="#courseCurriculum">
								<ul class="edu-curriculum-list">
									<li class="edu-curriculum-item">
										<div class="edu-lesson-info">
											<i class="bi bi-lock text-edu-muted" aria-hidden="true"></i>
											<span>4. Modelando Entidades e Value Objects imutáveis</span>
										</div>
										<span class="edu-lesson-duration">28 min</span>
									</li>
									<li class="edu-curriculum-item">
										<div class="edu-lesson-info">
											<i class="bi bi-lock text-edu-muted" aria-hidden="true"></i>
											<span>5. Use Cases: orquestração desacoplada de dados</span>
										</div>
										<span class="edu-lesson-duration">45 min</span>
									</li>
								</ul>
							</div>
						</div>

						<!-- Module 3 -->
						<div class="edu-curriculum-module">
							<button class="edu-curriculum-header collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#mod3Collapse" aria-expanded="false" aria-controls="mod3Collapse">
								<span class="edu-curriculum-module-title">Módulo 3: Infraestrutura, Mappers e DTOs</span>
								<span class="edu-curriculum-module-meta">
									<span>8 aulas</span>
									<span>·</span>
									<span>11h 45min</span>
									<i class="bi bi-chevron-down" aria-hidden="true"></i>
								</span>
							</button>
							<div id="mod3Collapse" class="collapse" data-bs-parent="#courseCurriculum">
								<ul class="edu-curriculum-list">
									<li class="edu-curriculum-item">
										<div class="edu-lesson-info">
											<i class="bi bi-lock text-edu-muted" aria-hidden="true"></i>
											<span>6. Tradução limpa de persistência com Data Transfer Objects</span>
										</div>
										<span class="edu-lesson-duration">32 min</span>
									</li>
								</ul>
							</div>
						</div>

						<!-- Module 4 -->
						<div class="edu-curriculum-module">
							<button class="edu-curriculum-header collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#mod4Collapse" aria-expanded="false" aria-controls="mod4Collapse">
								<span class="edu-curriculum-module-title">Módulo 4: Testes Automatizados & Homologação</span>
								<span class="edu-curriculum-module-meta">
									<span>6 aulas</span>
									<span>·</span>
									<span>7h 30min</span>
									<i class="bi bi-chevron-down" aria-hidden="true"></i>
								</span>
							</button>
							<div id="mod4Collapse" class="collapse" data-bs-parent="#courseCurriculum">
								<ul class="edu-curriculum-list">
									<li class="edu-curriculum-item">
										<div class="edu-lesson-info">
											<i class="bi bi-lock text-edu-muted" aria-hidden="true"></i>
											<span>7. Testes unitários com Mocks e métricas de cobertura</span>
										</div>
										<span class="edu-lesson-duration">40 min</span>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</section>
			</div>

			<!-- Floating Enrollment Sticky Card (Right Column) -->
			<div class="col-lg-4 animate-fade-up animate-delay-1">
				<div class="edu-card shadow-lg border-edu sticky-top" style="top: 90px;">
					<!-- 16:9 Thumbnail Trailer -->
					<div class="edu-course-media">
						<div class="w-100 h-100 d-flex align-items-center justify-content-center text-white" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
							<i class="bi bi-play-circle-fill fs-1" aria-hidden="true"></i>
						</div>
					</div>

					<div class="edu-card-body p-4">
						<div class="mb-4">
							<span class="fs-8 text-edu-muted text-uppercase fw-bold letter-spacing-1 d-block mb-1">Investimento Acadêmico</span>
							<div class="display-6 fw-bold text-edu-heading lh-1 mb-1 tabular-nums">
								12x R$ 39,90
							</div>
							<span class="fs-7 text-edu-muted">ou R$ 399,00 à vista no PIX ou Boleto</span>
						</div>

						<a href="<?= base_url('entrar') ?>" class="edu-btn edu-btn-primary edu-btn-lg w-100 mb-3">
							<i class="bi bi-lightning-charge-fill me-1" aria-hidden="true"></i> Matricular-se Agora
						</a>

						<p class="text-center text-edu-muted fs-8 mb-4">
							<i class="bi bi-shield-check text-edu-success me-1" aria-hidden="true"></i> Garantia incondicional de 7 dias com reembolso integral
						</p>

						<div class="border-top border-edu pt-3">
							<h4 class="fs-7 fw-bold text-edu-heading mb-2">Este curso inclui:</h4>
							<ul class="list-unstyled d-flex flex-column gap-2 fs-7 text-edu-muted mb-0">
								<li><i class="bi bi-check2 text-edu-primary me-2"></i> 40 horas de conteúdo em vídeo</li>
								<li><i class="bi bi-check2 text-edu-primary me-2"></i> Materiais em PDF e códigos para download</li>
								<li><i class="bi bi-check2 text-edu-primary me-2"></i> Certificado de conclusão digital</li>
								<li><i class="bi bi-check2 text-edu-primary me-2"></i> Acesso em smartphone, tablet e desktop</li>
								<li><i class="bi bi-check2 text-edu-primary me-2"></i> Suporte direto com a tutoria</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
