<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= html_escape($title ?? get_institution_name() . ' — Administração') ?></title>
	<meta name="description" content="Painel Administrativo da Plataforma Educacional">

	<!-- Google Fonts: Inter -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

	<!-- Theme Logic in Head (Prevents FOUC) -->
	<script src="<?= base_url('public/assets/js/theme.js') ?>"></script>

	<!-- Foundation Styles -->
	<link rel="stylesheet" href="<?= base_url('public/assets/css/bootstrap.min.css') ?>">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
	<link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

	<!-- Unified Design System Tokens & Base -->
	<link rel="stylesheet" href="<?= base_url('public/assets/css/shared/tokens.css?v=' . filemtime(FCPATH . 'public/assets/css/shared/tokens.css')) ?>">
	<link rel="stylesheet" href="<?= base_url('public/assets/css/shared/base.css?v=' . filemtime(FCPATH . 'public/assets/css/shared/base.css')) ?>">
	<link rel="stylesheet" href="<?= base_url('public/assets/css/shared/components.css?v=' . filemtime(FCPATH . 'public/assets/css/shared/components.css')) ?>">

	<!-- Admin Shell & Components -->
	<link rel="stylesheet" href="<?= base_url('public/assets/css/admin.css?v=' . filemtime(FCPATH . 'public/assets/css/admin.css')) ?>">
	<link rel="stylesheet" href="<?= base_url('public/assets/css/admin/components/sidebar.css?v=' . filemtime(FCPATH . 'public/assets/css/admin/components/sidebar.css')) ?>">
	<link rel="stylesheet" href="<?= base_url('public/assets/css/admin/components/page-header.css?v=' . filemtime(FCPATH . 'public/assets/css/admin/components/page-header.css')) ?>">
	<link rel="stylesheet" href="<?= base_url('public/assets/css/admin/components/datatable.css?v=' . filemtime(FCPATH . 'public/assets/css/admin/components/datatable.css')) ?>">
	<link rel="stylesheet" href="<?= base_url('public/assets/css/admin/components/dashboard.css?v=' . filemtime(FCPATH . 'public/assets/css/admin/components/dashboard.css')) ?>">

	<!-- White-Label Tenant Overrides (if any) -->
	<?= render_branding_styles() ?>

	<!-- Page-Specific Styles -->
	<?php if (!empty($page_css)): ?>
		<?php foreach ((array)$page_css as $css): ?>
			<link rel="stylesheet" href="<?= base_url('public/assets/css/pages/' . $css) ?>">
		<?php endforeach; ?>
	<?php endif; ?>
</head>
<body>
	<!-- Accessibility: Skip Link -->
	<a href="#main-content" class="skip-link">Pular para o conteúdo principal</a>

	<div class="admin-wrapper">
		<!-- Sidebar Backdrop (Mobile) -->
		<div class="sidebar-backdrop" id="sidebar-backdrop" aria-hidden="true"></div>

		<!-- Sidebar -->
		<aside class="admin-sidebar" id="admin-sidebar" role="navigation" aria-label="Menu principal administrativo">
			<div class="sidebar-header">
				<a href="<?= base_url('admin/painel') ?>" class="brand-logo" aria-label="<?= html_escape(get_institution_name()) ?> — Início">
					<?= get_institution_logo() ?>
				</a>
			</div>

			<div class="sidebar-menu">
				<?php 
					$seg1 = $this->uri->segment(1);
					$seg2 = $this->uri->segment(2);
					$seg3 = $this->uri->segment(3);
				?>
				<ul class="nav flex-column" role="menubar">
					<li class="nav-item" role="none">
						<a class="nav-link <?= ($seg1 == 'admin' && $seg2 == 'painel') ? 'active' : '' ?>"
						   href="<?= base_url('admin/painel') ?>"
						   role="menuitem"
						   <?= ($seg1 == 'admin' && $seg2 == 'painel') ? 'aria-current="page"' : '' ?>>
							<i class="bi bi-grid-1x2" aria-hidden="true"></i> Dashboard
						</a>
					</li>

					<li class="nav-item nav-section" role="none">
						<span class="nav-section-title">Gestão</span>
					</li>
					<li class="nav-item" role="none">
						<a class="nav-link <?= $seg2 == 'usuarios' ? 'active' : '' ?>"
						   href="<?= base_url('admin/usuarios') ?>"
						   role="menuitem"
						   <?= $seg2 == 'usuarios' ? 'aria-current="page"' : '' ?>>
							<i class="bi bi-people" aria-hidden="true"></i> Usuários
						</a>
					</li>
					<li class="nav-item" role="none">
						<a class="nav-link <?= $seg2 == 'cursos' ? 'active' : '' ?>"
						   href="<?= base_url('admin/cursos') ?>"
						   role="menuitem"
						   <?= $seg2 == 'cursos' ? 'aria-current="page"' : '' ?>>
							<i class="bi bi-journal-bookmark" aria-hidden="true"></i> Cursos
						</a>
					</li>
					<li class="nav-item" role="none">
						<a class="nav-link <?= $seg2 == 'categorias' ? 'active' : '' ?>"
						   href="<?= base_url('admin/categorias') ?>"
						   role="menuitem"
						   <?= $seg2 == 'categorias' ? 'aria-current="page"' : '' ?>>
							<i class="bi bi-tags" aria-hidden="true"></i> Categorias
						</a>
					</li>
					<li class="nav-item" role="none">
						<a class="nav-link <?= $seg2 == 'turmas' ? 'active' : '' ?>"
						   href="<?= base_url('admin/turmas') ?>"
						   role="menuitem"
						   <?= $seg2 == 'turmas' ? 'aria-current="page"' : '' ?>>
							<i class="bi bi-collection" aria-hidden="true"></i> Turmas
						</a>
					</li>

					<li class="nav-item nav-section" role="none">
						<span class="nav-section-title">Avaliações</span>
					</li>
					<li class="nav-item" role="none">
						<a class="nav-link <?= $seg2 == 'avaliacoes' ? 'active' : '' ?>"
						   href="<?= base_url('admin/avaliacoes') ?>"
						   role="menuitem"
						   <?= $seg2 == 'avaliacoes' ? 'aria-current="page"' : '' ?>>
							<i class="bi bi-patch-check" aria-hidden="true"></i> Avaliações
						</a>
					</li>

					<li class="nav-item nav-section" role="none">
						<span class="nav-section-title">Relatórios</span>
					</li>
					<li class="nav-item" role="none">
						<a class="nav-link <?= ($seg2 == 'relatorios' && $seg3 == 'academicos') ? 'active' : '' ?>"
						   href="<?= base_url('admin/relatorios/academicos') ?>"
						   role="menuitem"
						   <?= ($seg2 == 'relatorios' && $seg3 == 'academicos') ? 'aria-current="page"' : '' ?>>
							<i class="bi bi-graph-up" aria-hidden="true"></i> Acadêmicos
						</a>
					</li>
					<li class="nav-item" role="none">
						<a class="nav-link <?= ($seg2 == 'relatorios' && $seg3 == 'financeiros') ? 'active' : '' ?>"
						   href="<?= base_url('admin/relatorios/financeiros') ?>"
						   role="menuitem"
						   <?= ($seg2 == 'relatorios' && $seg3 == 'financeiros') ? 'aria-current="page"' : '' ?>>
							<i class="bi bi-currency-dollar" aria-hidden="true"></i> Financeiros
						</a>
					</li>

					<li class="nav-item nav-section" role="none">
						<span class="nav-section-title">Configuração</span>
					</li>
					<li class="nav-item" role="none">
						<a class="nav-link <?= $seg2 == 'perfis' ? 'active' : '' ?>"
						   href="<?= base_url('admin/perfis') ?>"
						   role="menuitem"
						   <?= $seg2 == 'perfis' ? 'aria-current="page"' : '' ?>>
							<i class="bi bi-shield-check" aria-hidden="true"></i> Perfis e Permissões
						</a>
					</li>

					<li class="nav-item nav-section" role="none">
						<span class="nav-section-title">Externo</span>
					</li>
					<li class="nav-item" role="none">
						<a class="nav-link <?= $seg1 == 'cursos' ? 'active' : '' ?>"
						   href="<?= base_url('cursos') ?>"
						   role="menuitem">
							<i class="bi bi-shop" aria-hidden="true"></i> Catálogo Público
						</a>
					</li>
					<li class="nav-item" role="none">
						<a class="nav-link <?= ($seg2 == 'painel' && $seg1 == 'aluno') ? 'active' : '' ?>"
						   href="<?= base_url('aluno/painel') ?>"
						   role="menuitem">
							<i class="bi bi-mortarboard" aria-hidden="true"></i> Visão do Aluno
						</a>
					</li>
				</ul>
			</div>

			<div class="sidebar-footer">
				<div class="user-info">
					<div class="avatar" aria-hidden="true">
						<?= strtoupper(substr($this->session->userdata('user_name') ?? 'U', 0, 1)) ?>
					</div>
					<div class="details">
						<span class="name"><?= html_escape($this->session->userdata('user_name') ?? 'Administrador') ?></span>
						<span class="role"><?= html_escape(ucfirst(str_replace('-', ' ', $this->session->userdata('user_role') ?? 'Administrador'))) ?></span>
					</div>
				</div>
				<a href="<?= base_url('sair') ?>" class="logout-btn" title="Sair da conta" aria-label="Sair da conta">
					<i class="bi bi-box-arrow-right" aria-hidden="true"></i>
				</a>
			</div>
		</aside>

		<!-- Main Content -->
		<main class="admin-main" id="main-content">
			<!-- Topbar -->
			<header class="admin-header" role="banner">
				<div class="d-flex align-items-center gap-3 flex-1">
					<button class="btn btn-link btn-toggle-sidebar d-md-none p-0"
					        id="btn-toggle-sidebar"
					        aria-label="Abrir menu de navegação"
					        aria-expanded="false"
					        aria-controls="admin-sidebar"
					        style="color: var(--edu-text-muted);">
						<i class="bi bi-list fs-4" aria-hidden="true"></i>
					</button>
					<nav class="header-breadcrumb d-none d-sm-flex align-items-center gap-2" aria-label="Localização atual">
						<i class="bi bi-house-door text-muted" aria-hidden="true"></i>
						<span class="text-muted">Administração</span>
						<i class="bi bi-chevron-right text-muted" style="font-size: 0.7rem;" aria-hidden="true"></i>
						<span class="fw-medium text-body"><?= html_escape($title ?? 'Painel') ?></span>
					</nav>
				</div>
				<div class="header-actions">
					<!-- Theme Toggler -->
					<button class="btn btn-link p-0" id="theme-toggle" aria-label="Alternar entre tema claro e escuro" title="Alternar Tema">
						<i class="bi bi-moon-stars fs-5" aria-hidden="true"></i>
					</button>
				</div>
			</header>

			<!-- Dynamic Content -->
			<div class="admin-content">
				<?php if (isset($page_name)) { $this->load->view($page_name); } ?>
			</div>
		</main>
	</div>

	<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
	<script src="<?= base_url('public/assets/js/bootstrap.bundle.min.js') ?>"></script>
	<script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
	<script src="//cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
	<script src="<?= base_url('public/assets/js/http.js') ?>"></script>
	<script src="<?= base_url('public/assets/js/admin/layout.js') ?>"></script>
	<script src="<?= base_url('public/assets/js/components/rich-editor.js') ?>"></script>

	<!-- Page-Specific Scripts -->
	<?php if (!empty($page_js)): ?>
		<?php foreach ((array)$page_js as $js): ?>
			<script src="<?= base_url('public/assets/js/pages/' . $js) ?>"></script>
		<?php endforeach; ?>
	<?php endif; ?>
</body>
</html>
