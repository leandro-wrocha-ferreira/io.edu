<!-- Operational Page Header -->
<div class="page-header animate-fade-up">
	<div class="page-header-info">
		<nav class="page-header-breadcrumb" aria-label="Navegação estrutural">
			<i class="bi bi-house-door" aria-hidden="true"></i>
			<span class="sep" aria-hidden="true">›</span>
			<span class="active" aria-current="page">Painel Operacional</span>
		</nav>
		<h1 class="page-header-title">
			Olá, <?= html_escape($user_name ?? 'Administrador') ?>
		</h1>
		<p class="page-header-subtitle">
			Visão geral da operação, gestão de acessos e parametrizações do LMS <?= html_escape(get_institution_name()) ?>.
		</p>
	</div>

	<div class="page-header-actions">
		<a href="<?= base_url('admin/usuarios/novo') ?>" class="edu-btn edu-btn-primary">
			<i class="bi bi-person-plus-fill" aria-hidden="true"></i> Novo Usuário
		</a>
		<a href="<?= base_url('admin/usuarios') ?>" class="edu-btn edu-btn-secondary">
			<i class="bi bi-people" aria-hidden="true"></i> Gestão de Usuários
		</a>
		<a href="<?= base_url('cursos') ?>" class="edu-btn edu-btn-ghost" target="_blank" rel="noopener">
			<i class="bi bi-box-arrow-up-right" aria-hidden="true"></i> Catálogo
		</a>
	</div>
</div>

<!-- Operational Metrics Row (Real Database Data) -->
<div class="row g-3 mb-4">
	<!-- Alunos Ativos -->
	<div class="col-xl-3 col-sm-6">
		<div class="dashboard-stat-card animate-fade-up animate-delay-1">
			<div class="stat-icon-wrap stat-icon-primary">
				<i class="bi bi-mortarboard-fill" aria-hidden="true"></i>
			</div>
			<div class="stat-body">
				<div class="stat-label">Alunos Ativos</div>
				<div class="stat-value"><?= number_format($metrics['total_students'] ?? 0) ?></div>
				<div class="stat-meta text-success">
					<i class="bi bi-check-circle" aria-hidden="true"></i> Perfil estudante no banco
				</div>
			</div>
		</div>
	</div>

	<!-- Administradores -->
	<div class="col-xl-3 col-sm-6">
		<div class="dashboard-stat-card animate-fade-up animate-delay-2">
			<div class="stat-icon-wrap stat-icon-success">
				<i class="bi bi-shield-lock-fill" aria-hidden="true"></i>
			</div>
			<div class="stat-body">
				<div class="stat-label">Administradores</div>
				<div class="stat-value"><?= number_format($metrics['total_admins'] ?? 0) ?></div>
				<div class="stat-meta text-muted">
					<i class="bi bi-people" aria-hidden="true"></i> Gestores com permissão
				</div>
			</div>
		</div>
	</div>

	<!-- Total de Usuários -->
	<div class="col-xl-3 col-sm-6">
		<div class="dashboard-stat-card animate-fade-up animate-delay-3">
			<div class="stat-icon-wrap stat-icon-purple" style="background-color: rgba(6, 182, 212, 0.1); color: var(--edu-accent);">
				<i class="bi bi-person-lines-fill" aria-hidden="true"></i>
			</div>
			<div class="stat-body">
				<div class="stat-label">Total de Contas</div>
				<div class="stat-value"><?= number_format($metrics['total_users'] ?? 0) ?></div>
				<div class="stat-meta text-muted">
					<i class="bi bi-database-check" aria-hidden="true"></i> Usuários cadastrados
				</div>
			</div>
		</div>
	</div>

	<!-- Perfis e Permissões -->
	<div class="col-xl-3 col-sm-6">
		<div class="dashboard-stat-card animate-fade-up animate-delay-4">
			<div class="stat-icon-wrap stat-icon-warning">
				<i class="bi bi-key-fill" aria-hidden="true"></i>
			</div>
			<div class="stat-body">
				<div class="stat-label">Perfis Configurados</div>
				<div class="stat-value"><?= number_format($metrics['total_roles'] ?? 0) ?></div>
				<div class="stat-meta text-success">
					<i class="bi bi-shield-check" aria-hidden="true"></i> RBAC operacional
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Main Operational Layout (8 cols + 4 cols) -->
<div class="row g-4 mb-4">
	<!-- Left Column: Atenção Necessária & Cursos em Oferta -->
	<div class="col-xl-8">
		<!-- Atenção Necessária (Pendências Operacionais) -->
		<div class="edu-card mb-4 animate-fade-up animate-delay-1">
			<div class="edu-card-header d-flex justify-content-between align-items-center">
				<div>
					<h2 class="edu-card-title h6 mb-0 d-flex align-items-center gap-2">
						<i class="bi bi-exclamation-triangle-fill text-warning" aria-hidden="true"></i>
						Atenção Necessária
					</h2>
					<small class="text-muted">Demandas operacionais que requerem acompanhamento</small>
				</div>
				<span class="badge bg-light text-muted border">Monitoramento Operacional</span>
			</div>
			<div class="edu-card-body p-0">
				<ul class="list-group list-group-flush" style="font-size: 0.875rem;">
					<li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
						<div class="d-flex align-items-center gap-3">
							<div class="rounded-circle d-flex align-items-center justify-content-center text-primary flex-shrink-0"
							     style="width: 36px; height: 36px; background-color: var(--edu-primary-ghost);">
								<i class="bi bi-person-check-fill" aria-hidden="true"></i>
							</div>
							<div>
								<div class="fw-semibold text-heading">Gestão de Acessos e Perfis</div>
								<small class="text-muted">Revise os perfis atribuídos e valide status de contas inativas na base.</small>
							</div>
						</div>
						<a href="<?= base_url('admin/usuarios') ?>" class="edu-btn edu-btn-ghost btn-sm">
							Verificar Usuários <i class="bi bi-chevron-right" aria-hidden="true"></i>
						</a>
					</li>

					<li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
						<div class="d-flex align-items-center gap-3">
							<div class="rounded-circle d-flex align-items-center justify-content-center text-warning flex-shrink-0"
							     style="width: 36px; height: 36px; background-color: rgba(245, 158, 11, 0.1);">
								<i class="bi bi-mortarboard" aria-hidden="true"></i>
							</div>
							<div>
								<div class="fw-semibold text-heading d-flex align-items-center gap-2">
									<span>Módulo de Professores & Enturmação</span>
									<span class="badge rounded-pill bg-secondary-subtle text-muted border" style="font-size: 0.7rem; font-weight: 500;">
										Dependência de Backend
									</span>
								</div>
								<small class="text-muted">Aguardando modelagem de turmas e atribuição docente no banco de dados.</small>
							</div>
						</div>
						<span class="text-muted small">Planejado</span>
					</li>

					<li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
						<div class="d-flex align-items-center gap-3">
							<div class="rounded-circle d-flex align-items-center justify-content-center text-info flex-shrink-0"
							     style="width: 36px; height: 36px; background-color: rgba(6, 182, 212, 0.1);">
								<i class="bi bi-journal-check" aria-hidden="true"></i>
							</div>
							<div>
								<div class="fw-semibold text-heading d-flex align-items-center gap-2">
									<span>Avaliações e Correções Pendentes</span>
									<span class="badge rounded-pill bg-secondary-subtle text-muted border" style="font-size: 0.7rem; font-weight: 500;">
										Dependência de Backend
									</span>
								</div>
								<small class="text-muted">Fluxo de submissão de trabalhos e notas para o perfil do professor.</small>
							</div>
						</div>
						<span class="text-muted small">Planejado</span>
					</li>
				</ul>
			</div>
		</div>

		<!-- Courses Catalog Overview -->
		<div class="edu-card animate-fade-up animate-delay-2">
			<div class="edu-card-header d-flex justify-content-between align-items-center">
				<div>
					<h2 class="edu-card-title h6 mb-0">Cursos Publicados no Catálogo LMS</h2>
					<small class="text-muted">Grade curricular em oferta aberta a matrículas no portal público</small>
				</div>
				<a href="<?= base_url('cursos') ?>" class="edu-btn edu-btn-ghost btn-sm" target="_blank" rel="noopener">
					Ver todos <i class="bi bi-arrow-right" aria-hidden="true"></i>
				</a>
			</div>
			<div class="edu-table-responsive">
				<table class="table edu-data-table align-middle mb-0">
					<thead>
						<tr>
							<th scope="col" class="ps-4">Curso / Disciplina</th>
							<th scope="col">Área / Nível</th>
							<th scope="col">Carga Horária</th>
							<th scope="col">Preço</th>
							<th scope="col" class="text-end pe-4" style="min-width: 120px;">Ação</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="ps-4">
								<div class="d-flex align-items-center gap-3">
									<div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
									     style="width: 38px; height: 38px; background: #4f46e5; font-size: 1rem;">
										<i class="bi bi-code-slash" aria-hidden="true"></i>
									</div>
									<div>
										<div class="fw-bold text-truncate" style="max-width: 260px;">Desenvolvimento Web Moderno com Arquitetura Limpa</div>
										<small class="text-muted">Prof. Dr. Marcelo Santos • 28 aulas</small>
									</div>
								</div>
							</td>
							<td>
								<span class="edu-badge edu-badge-primary">Tecnologia</span>
								<span class="edu-badge edu-badge-outline">Avançado</span>
							</td>
							<td><i class="bi bi-clock text-muted me-1" aria-hidden="true"></i>40 horas</td>
							<td class="fw-bold text-success">12x R$ 49,90</td>
							<td class="text-end pe-4">
								<a href="<?= base_url('cursos/detalhes/desenvolvimento-web-moderno') ?>" class="edu-btn edu-btn-ghost btn-sm" target="_blank">
									Visualizar
								</a>
							</td>
						</tr>
						<tr>
							<td class="ps-4">
								<div class="d-flex align-items-center gap-3">
									<div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
									     style="width: 38px; height: 38px; background: #06b6d4; font-size: 1rem;">
										<i class="bi bi-palette-fill" aria-hidden="true"></i>
									</div>
									<div>
										<div class="fw-bold text-truncate" style="max-width: 260px;">Design System e Arquitetura White-Label</div>
										<small class="text-muted">Profa. Mariana Costa • 32 aulas</small>
									</div>
								</div>
							</td>
							<td>
								<span class="edu-badge edu-badge-info">Design</span>
								<span class="edu-badge edu-badge-outline">Intermediário</span>
							</td>
							<td><i class="bi bi-clock text-muted me-1" aria-hidden="true"></i>36 horas</td>
							<td class="fw-bold text-success">12x R$ 39,90</td>
							<td class="text-end pe-4">
								<a href="<?= base_url('cursos/detalhes/design-system-white-label') ?>" class="edu-btn edu-btn-ghost btn-sm" target="_blank">
									Visualizar
								</a>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<!-- Right Column: Institutional Branding & Quick Actions -->
	<div class="col-xl-4">
		<!-- White-Label Status Card -->
		<div class="edu-card mb-4 animate-fade-up animate-delay-2">
			<div class="edu-card-header d-flex justify-content-between align-items-center">
				<h2 class="edu-card-title h6 mb-0">Instituição & White-Label</h2>
				<span class="badge-status-active">
					<i class="bi bi-circle-fill" style="font-size: 0.45rem;" aria-hidden="true"></i> Ativo
				</span>
			</div>
			<div class="edu-card-body">
				<div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
					<div class="p-2 rounded-3 border bg-surface flex-shrink-0" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
						<?= get_institution_logo() ?>
					</div>
					<div>
						<div class="fw-bold text-heading"><?= html_escape(get_institution_name()) ?></div>
						<small class="text-muted"><?= html_escape(get_institution_short()) ?> • Plataforma White-Label</small>
					</div>
				</div>

				<div class="small">
					<div class="d-flex justify-content-between mb-2">
						<span class="text-muted">Cor Primária:</span>
						<span class="d-inline-flex align-items-center gap-1 font-monospace">
							<span class="d-inline-block rounded-circle" style="width: 12px; height: 12px; background: var(--edu-primary);"></span>
							<code>Indigo</code>
						</span>
					</div>
					<div class="d-flex justify-content-between mb-2">
						<span class="text-muted">Cor de Destaque:</span>
						<span class="d-inline-flex align-items-center gap-1 font-monospace">
							<span class="d-inline-block rounded-circle" style="width: 12px; height: 12px; background: var(--edu-accent);"></span>
							<code>Cyan</code>
						</span>
					</div>
					<div class="d-flex justify-content-between">
						<span class="text-muted">Tema Ativo:</span>
						<span class="badge bg-light text-muted border">Automático (Light/Dark)</span>
					</div>
				</div>
			</div>
		</div>

		<!-- Quick Actions Card -->
		<div class="edu-card animate-fade-up animate-delay-3">
			<div class="edu-card-header">
				<h2 class="edu-card-title h6 mb-0">Atalhos Operacionais</h2>
			</div>
			<div class="edu-card-body p-2">
				<div class="d-flex flex-column gap-1">
					<a href="<?= base_url('admin/usuarios') ?>" class="edu-btn edu-btn-ghost justify-content-start py-2.5 px-3">
						<i class="bi bi-people text-primary me-2" aria-hidden="true"></i>
						<span>Gestão de Usuários</span>
					</a>
					<a href="<?= base_url('admin/perfis') ?>" class="edu-btn edu-btn-ghost justify-content-start py-2.5 px-3">
						<i class="bi bi-shield-check text-success me-2" aria-hidden="true"></i>
						<span>Perfis e Permissões</span>
					</a>
					<a href="<?= base_url('cursos') ?>" class="edu-btn edu-btn-ghost justify-content-start py-2.5 px-3" target="_blank" rel="noopener">
						<i class="bi bi-journal-bookmark text-info me-2" aria-hidden="true"></i>
						<span>Catálogo de Cursos</span>
					</a>
					<a href="<?= base_url('aluno/painel') ?>" class="edu-btn edu-btn-ghost justify-content-start py-2.5 px-3">
						<i class="bi bi-mortarboard text-warning me-2" aria-hidden="true"></i>
						<span>Visão do Estudante</span>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
