<!-- Hero Discovery Section -->
<section class="py-5 bg-edu-surface border-bottom border-edu position-relative overflow-hidden" aria-labelledby="catalog-hero-title">
	<div class="container py-4">
		<div class="row align-items-center g-5">
			<div class="col-lg-7 animate-fade-up">
				<span class="edu-badge edu-badge-primary mb-3">
					<i class="bi bi-mortarboard-fill me-1" aria-hidden="true"></i> Formação Profissional de Elite
				</span>
				<h1 class="display-5 fw-bold text-edu-heading mb-3" id="catalog-hero-title" style="letter-spacing: -0.03em;">
					Aprenda com quem constrói o futuro da sua profissão.
				</h1>
				<p class="lead text-edu-muted mb-4 fs-6">
					Cursos estruturados com rigor pedagógico, foco prático e certificações aceitas no mercado corporativo e acadêmico.
				</p>
				<div class="d-flex align-items-center gap-3 flex-wrap">
					<a href="#catalogo" class="edu-btn edu-btn-primary edu-btn-lg">
						<i class="bi bi-compass" aria-hidden="true"></i> Explorar Catálogo
					</a>
					<a href="<?= base_url('aluno/jornadas') ?>" class="edu-btn edu-btn-secondary edu-btn-lg">
						Conhecer Trilhas & Prazos
					</a>
				</div>
			</div>
			<div class="col-lg-5 d-none d-lg-block animate-fade-up animate-delay-1">
				<div class="edu-card p-4 shadow-lg border-edu-strong position-relative" style="background: var(--edu-primary-gradient); color: #ffffff;">
					<div class="d-flex align-items-center gap-3 mb-3">
						<div class="edu-brand-icon" style="background: rgba(255,255,255,0.2);">
							<i class="bi bi-lightning-charge-fill text-white"></i>
						</div>
						<div>
							<h2 class="h6 text-white mb-0 fw-bold">Certificação Oficial</h2>
							<span class="fs-8 text-white-50">Validada em todo território nacional</span>
						</div>
					</div>
					<p class="fs-7 text-white-50 mb-3">
						Estude online com o suporte de mestres e doutores, materiais em PDF e sala de aula virtual com acesso 24h.
					</p>
					<div class="d-flex align-items-center justify-content-between pt-3 border-top border-white-50 fs-7">
						<span><i class="bi bi-people-fill me-1"></i> +5.000 Alunos</span>
						<span><i class="bi bi-star-fill text-warning me-1"></i> Nota 4.9/5</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- Filter & Courses Grid Section -->
<section class="py-5" id="catalogo" aria-labelledby="catalog-courses-title">
	<div class="container">
		<!-- Section Header -->
		<div class="d-flex align-items-end justify-content-between mb-4 flex-wrap gap-3">
			<div>
				<span class="edu-badge edu-badge-neutral mb-2">Vitrine de Cursos</span>
				<h2 class="h3 fw-bold text-edu-heading mb-1" id="catalog-courses-title">Explore Nossas Especializações</h2>
				<p class="text-edu-muted fs-7 mb-0">Selecione uma área de conhecimento para filtrar os cursos disponíveis.</p>
			</div>
			<div class="fs-7 text-edu-muted">
				Mostrando <strong>6</strong> cursos selecionados
			</div>
		</div>

		<!-- Faceted Filter Bar -->
		<div class="edu-filter-bar animate-fade-up animate-delay-1">
			<div class="edu-filter-row-top">
				<!-- Search Input -->
				<div class="edu-search-wrap">
					<i class="bi bi-search edu-search-icon" aria-hidden="true"></i>
					<input type="text"
					       class="edu-form-control edu-search-input"
					       placeholder="Buscar por nome do curso, instrutor ou tema..."
					       aria-label="Buscar cursos">
				</div>

				<!-- Select Filters -->
				<div class="edu-filter-selects">
					<select class="edu-form-select edu-filter-select" aria-label="Filtrar por Nível">
						<option value="">Todos os Níveis</option>
						<option value="iniciante">Iniciante</option>
						<option value="intermediario">Intermediário</option>
						<option value="avancado">Avançado</option>
					</select>

					<select class="edu-form-select edu-filter-select" aria-label="Ordenar por">
						<option value="populares">Mais Populares</option>
						<option value="recentes">Lançamentos</option>
						<option value="menor-preco">Menor Preço</option>
					</select>
				</div>
			</div>

			<!-- Quick Category Pills -->
			<div class="edu-filter-pills" role="tablist" aria-label="Categorias de cursos">
				<a href="#" class="edu-filter-pill active" role="tab" aria-selected="true">
					<i class="bi bi-grid-fill" aria-hidden="true"></i> Todos os Cursos
				</a>
				<a href="#" class="edu-filter-pill" role="tab" aria-selected="false">
					<i class="bi bi-code-slash" aria-hidden="true"></i> Tecnologia & Software
				</a>
				<a href="#" class="edu-filter-pill" role="tab" aria-selected="false">
					<i class="bi bi-shield-check" aria-hidden="true"></i> Direito & Compliance
				</a>
				<a href="#" class="edu-filter-pill" role="tab" aria-selected="false">
					<i class="bi bi-graph-up-arrow" aria-hidden="true"></i> Gestão & Finanças
				</a>
				<a href="#" class="edu-filter-pill" role="tab" aria-selected="false">
					<i class="bi bi-people" aria-hidden="true"></i> Liderança & Pessoas
				</a>
				<a href="#" class="edu-filter-pill" role="tab" aria-selected="false">
					<i class="bi bi-award" aria-hidden="true"></i> Certificações Oficiais
				</a>
			</div>
		</div>

		<!-- Courses Cards Grid -->
		<div class="row g-4 mb-5">
			<!-- Course 1 -->
			<div class="col-lg-4 col-md-6 animate-fade-up animate-delay-1">
				<div class="edu-course-card">
					<div class="edu-course-media">
						<div class="w-100 h-100 d-flex align-items-center justify-content-center text-white" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
							<i class="bi bi-laptop fs-1" aria-hidden="true"></i>
						</div>
						<span class="edu-badge edu-badge-primary edu-course-category-badge">Tecnologia</span>
						<button class="edu-course-wishlist-btn" aria-label="Adicionar aos favoritos" title="Favoritar">
							<i class="bi bi-bookmark" aria-hidden="true"></i>
						</button>
					</div>
					<div class="edu-course-body">
						<div class="edu-course-meta">
							<span class="edu-course-meta-item"><i class="bi bi-clock" aria-hidden="true"></i> 40h</span>
							<span class="edu-course-meta-item"><i class="bi bi-collection-play" aria-hidden="true"></i> 28 aulas</span>
							<span class="edu-course-meta-item ms-auto text-warning"><i class="bi bi-star-fill" aria-hidden="true"></i> 4.9</span>
						</div>
						<h3 class="edu-course-title">
							<a href="<?= base_url('cursos/detalhes/desenvolvimento-web-moderno') ?>">Desenvolvimento Web Moderno com Arquitetura Limpa</a>
						</h3>
						<div class="edu-course-instructor">
							<div class="edu-course-instructor-avatar d-flex align-items-center justify-content-center text-white fw-bold fs-7" style="background: #4f46e5;">
								JS
							</div>
							<span class="edu-course-instructor-name">Prof. João Silva</span>
						</div>
						<div class="edu-course-footer">
							<div class="edu-course-price-box">
								<span class="edu-course-installments">12x de R$ 39,90</span>
								<span class="edu-course-cash-price">ou R$ 399,00 à vista</span>
							</div>
							<a href="<?= base_url('cursos/detalhes/desenvolvimento-web-moderno') ?>" class="edu-btn edu-btn-primary edu-btn-sm">
								Ver Detalhes
							</a>
						</div>
					</div>
				</div>
			</div>

			<!-- Course 2 -->
			<div class="col-lg-4 col-md-6 animate-fade-up animate-delay-2">
				<div class="edu-course-card">
					<div class="edu-course-media">
						<div class="w-100 h-100 d-flex align-items-center justify-content-center text-white" style="background: linear-gradient(135deg, #065f46 0%, #10b981 100%);">
							<i class="bi bi-shield-check fs-1" aria-hidden="true"></i>
						</div>
						<span class="edu-badge edu-badge-accent edu-course-category-badge">Compliance</span>
						<button class="edu-course-wishlist-btn" aria-label="Adicionar aos favoritos" title="Favoritar">
							<i class="bi bi-bookmark" aria-hidden="true"></i>
						</button>
					</div>
					<div class="edu-course-body">
						<div class="edu-course-meta">
							<span class="edu-course-meta-item"><i class="bi bi-clock" aria-hidden="true"></i> 24h</span>
							<span class="edu-course-meta-item"><i class="bi bi-collection-play" aria-hidden="true"></i> 16 aulas</span>
							<span class="edu-course-meta-item ms-auto text-warning"><i class="bi bi-star-fill" aria-hidden="true"></i> 4.8</span>
						</div>
						<h3 class="edu-course-title">
							<a href="<?= base_url('cursos/detalhes/seguranca-informacao') ?>">Segurança da Informação e Proteção de Dados</a>
						</h3>
						<div class="edu-course-instructor">
							<div class="edu-course-instructor-avatar d-flex align-items-center justify-content-center text-white fw-bold fs-7" style="background: #10b981;">
								MC
							</div>
							<span class="edu-course-instructor-name">Dra. Mariana Costa</span>
						</div>
						<div class="edu-course-footer">
							<div class="edu-course-price-box">
								<span class="edu-course-installments">12x de R$ 29,90</span>
								<span class="edu-course-cash-price">ou R$ 299,00 à vista</span>
							</div>
							<a href="<?= base_url('cursos/detalhes/seguranca-informacao') ?>" class="edu-btn edu-btn-primary edu-btn-sm">
								Ver Detalhes
							</a>
						</div>
					</div>
				</div>
			</div>

			<!-- Course 3 -->
			<div class="col-lg-4 col-md-6 animate-fade-up animate-delay-3">
				<div class="edu-course-card">
					<div class="edu-course-media">
						<div class="w-100 h-100 d-flex align-items-center justify-content-center text-white" style="background: linear-gradient(135deg, #7c2d12 0%, #ea580c 100%);">
							<i class="bi bi-briefcase fs-1" aria-hidden="true"></i>
						</div>
						<span class="edu-badge edu-badge-warning edu-course-category-badge">Gestão</span>
						<button class="edu-course-wishlist-btn" aria-label="Adicionar aos favoritos" title="Favoritar">
							<i class="bi bi-bookmark" aria-hidden="true"></i>
						</button>
					</div>
					<div class="edu-course-body">
						<div class="edu-course-meta">
							<span class="edu-course-meta-item"><i class="bi bi-clock" aria-hidden="true"></i> 30h</span>
							<span class="edu-course-meta-item"><i class="bi bi-collection-play" aria-hidden="true"></i> 20 aulas</span>
							<span class="edu-course-meta-item ms-auto text-warning"><i class="bi bi-star-fill" aria-hidden="true"></i> 5.0</span>
						</div>
						<h3 class="edu-course-title">
							<a href="<?= base_url('cursos/detalhes/gestao-riscos') ?>">Gestão de Riscos e Governança Corporativa</a>
						</h3>
						<div class="edu-course-instructor">
							<div class="edu-course-instructor-avatar d-flex align-items-center justify-content-center text-white fw-bold fs-7" style="background: #ea580c;">
								RA
							</div>
							<span class="edu-course-instructor-name">Prof. Roberto Alves</span>
						</div>
						<div class="edu-course-footer">
							<div class="edu-course-price-box">
								<span class="edu-course-installments">12x de R$ 34,90</span>
								<span class="edu-course-cash-price">ou R$ 349,00 à vista</span>
							</div>
							<a href="<?= base_url('cursos/detalhes/gestao-riscos') ?>" class="edu-btn edu-btn-primary edu-btn-sm">
								Ver Detalhes
							</a>
						</div>
					</div>
				</div>
			</div>

			<!-- Course 4 -->
			<div class="col-lg-4 col-md-6 animate-fade-up animate-delay-1">
				<div class="edu-course-card">
					<div class="edu-course-media">
						<div class="w-100 h-100 d-flex align-items-center justify-content-center text-white" style="background: linear-gradient(135deg, #4c1d95 0%, #8b5cf6 100%);">
							<i class="bi bi-award fs-1" aria-hidden="true"></i>
						</div>
						<span class="edu-badge edu-badge-primary edu-course-category-badge">Liderança</span>
						<button class="edu-course-wishlist-btn" aria-label="Adicionar aos favoritos" title="Favoritar">
							<i class="bi bi-bookmark" aria-hidden="true"></i>
						</button>
					</div>
					<div class="edu-course-body">
						<div class="edu-course-meta">
							<span class="edu-course-meta-item"><i class="bi bi-clock" aria-hidden="true"></i> 18h</span>
							<span class="edu-course-meta-item"><i class="bi bi-collection-play" aria-hidden="true"></i> 12 aulas</span>
							<span class="edu-course-meta-item ms-auto text-warning"><i class="bi bi-star-fill" aria-hidden="true"></i> 4.9</span>
						</div>
						<h3 class="edu-course-title">
							<a href="<?= base_url('cursos/detalhes/lideranca-estrategica') ?>">Liderança Estratégica e Gestão de Pessoas</a>
						</h3>
						<div class="edu-course-instructor">
							<div class="edu-course-instructor-avatar d-flex align-items-center justify-content-center text-white fw-bold fs-7" style="background: #8b5cf6;">
								AL
							</div>
							<span class="edu-course-instructor-name">Dra. Ana Luiza Prado</span>
						</div>
						<div class="edu-course-footer">
							<div class="edu-course-price-box">
								<span class="edu-course-installments">12x de R$ 24,90</span>
								<span class="edu-course-cash-price">ou R$ 249,00 à vista</span>
							</div>
							<a href="<?= base_url('cursos/detalhes/lideranca-estrategica') ?>" class="edu-btn edu-btn-primary edu-btn-sm">
								Ver Detalhes
							</a>
						</div>
					</div>
				</div>
			</div>

			<!-- Course 5 -->
			<div class="col-lg-4 col-md-6 animate-fade-up animate-delay-2">
				<div class="edu-course-card">
					<div class="edu-course-media">
						<div class="w-100 h-100 d-flex align-items-center justify-content-center text-white" style="background: linear-gradient(135deg, #0f172a 0%, #334155 100%);">
							<i class="bi bi-diagram-3 fs-1" aria-hidden="true"></i>
						</div>
						<span class="edu-badge edu-badge-neutral edu-course-category-badge">Arquitetura</span>
						<button class="edu-course-wishlist-btn" aria-label="Adicionar aos favoritos" title="Favoritar">
							<i class="bi bi-bookmark" aria-hidden="true"></i>
						</button>
					</div>
					<div class="edu-course-body">
						<div class="edu-course-meta">
							<span class="edu-course-meta-item"><i class="bi bi-clock" aria-hidden="true"></i> 36h</span>
							<span class="edu-course-meta-item"><i class="bi bi-collection-play" aria-hidden="true"></i> 26 aulas</span>
							<span class="edu-course-meta-item ms-auto text-warning"><i class="bi bi-star-fill" aria-hidden="true"></i> 4.7</span>
						</div>
						<h3 class="edu-course-title">
							<a href="<?= base_url('cursos/detalhes/microsservicos-nuvem') ?>">Microsserviços e Sistemas Distribuídos</a>
						</h3>
						<div class="edu-course-instructor">
							<div class="edu-course-instructor-avatar d-flex align-items-center justify-content-center text-white fw-bold fs-7" style="background: #334155;">
								FM
							</div>
							<span class="edu-course-instructor-name">Eng. Felipe Mendes</span>
						</div>
						<div class="edu-course-footer">
							<div class="edu-course-price-box">
								<span class="edu-course-installments">12x de R$ 44,90</span>
								<span class="edu-course-cash-price">ou R$ 449,00 à vista</span>
							</div>
							<a href="<?= base_url('cursos/detalhes/microsservicos-nuvem') ?>" class="edu-btn edu-btn-primary edu-btn-sm">
								Ver Detalhes
							</a>
						</div>
					</div>
				</div>
			</div>

			<!-- Course 6 -->
			<div class="col-lg-4 col-md-6 animate-fade-up animate-delay-3">
				<div class="edu-course-card">
					<div class="edu-course-media">
						<div class="w-100 h-100 d-flex align-items-center justify-content-center text-white" style="background: linear-gradient(135deg, #155e75 0%, #06b6d4 100%);">
							<i class="bi bi-cpu fs-1" aria-hidden="true"></i>
						</div>
						<span class="edu-badge edu-badge-accent edu-course-category-badge">Inovação</span>
						<button class="edu-course-wishlist-btn" aria-label="Adicionar aos favoritos" title="Favoritar">
							<i class="bi bi-bookmark" aria-hidden="true"></i>
						</button>
					</div>
					<div class="edu-course-body">
						<div class="edu-course-meta">
							<span class="edu-course-meta-item"><i class="bi bi-clock" aria-hidden="true"></i> 25h</span>
							<span class="edu-course-meta-item"><i class="bi bi-collection-play" aria-hidden="true"></i> 18 aulas</span>
							<span class="edu-course-meta-item ms-auto text-warning"><i class="bi bi-star-fill" aria-hidden="true"></i> 4.9</span>
						</div>
						<h3 class="edu-course-title">
							<a href="<?= base_url('cursos/detalhes/inteligencia-artificial-negocios') ?>">Inteligência Artificial Aplicada a Negócios</a>
						</h3>
						<div class="edu-course-instructor">
							<div class="edu-course-instructor-avatar d-flex align-items-center justify-content-center text-white fw-bold fs-7" style="background: #06b6d4;">
								CR
							</div>
							<span class="edu-course-instructor-name">Dra. Camila Rocha</span>
						</div>
						<div class="edu-course-footer">
							<div class="edu-course-price-box">
								<span class="edu-course-installments">12x de R$ 38,90</span>
								<span class="edu-course-cash-price">ou R$ 389,00 à vista</span>
							</div>
							<a href="<?= base_url('cursos/detalhes/inteligencia-artificial-negocios') ?>" class="edu-btn edu-btn-primary edu-btn-sm">
								Ver Detalhes
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Learning Tracks Callout Banner -->
		<div class="edu-card p-4 p-md-5 bg-edu-surface border-edu shadow-sm animate-fade-up">
			<div class="row align-items-center g-4">
				<div class="col-lg-8">
					<span class="edu-badge edu-badge-warning mb-2">
						<i class="bi bi-compass" aria-hidden="true"></i> Trilhas & Jornadas
					</span>
					<h3 class="h4 fw-bold text-edu-heading mb-2">Prefere uma formação estruturada com certificação completa?</h3>
					<p class="text-edu-muted mb-0 fs-6">
						Conheça nossas Jornadas Integradas com acompanhamento de prazos, módulos complementares e certificação única de especialista.
					</p>
				</div>
				<div class="col-lg-4 text-lg-end">
					<a href="<?= base_url('aluno/jornadas') ?>" class="edu-btn edu-btn-primary edu-btn-lg">
						Ver Trilhas Disponíveis <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
					</a>
				</div>
			</div>
		</div>
	</div>
</section>
