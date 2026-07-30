<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= $title ?? 'Inverta Admin' ?></title>
	<meta name="description" content="Painel Administrativo da Plataforma de Educação Inverta">

	<!-- Fonts -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

	<!-- Theme Logic (Before CSS to prevent FOUC) -->
	<script src="<?= base_url('public/assets/js/theme.js') ?>"></script>

	<!-- Base Styles & Theme Tokens -->
	<link rel="stylesheet" href="<?= base_url('public/assets/css/bootstrap.min.css') ?>">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
	<link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
	<link rel="stylesheet" href="<?= base_url('public/assets/css/theme.css') ?>">
	<link rel="stylesheet" href="<?= base_url('public/assets/css/admin.css') ?>">

	<!-- Component Styles -->
	<link rel="stylesheet" href="<?= base_url('public/assets/css/admin/components/sidebar.css') ?>">
	<link rel="stylesheet" href="<?= base_url('public/assets/css/admin/components/page-header.css') ?>">
	<link rel="stylesheet" href="<?= base_url('public/assets/css/admin/components/card.css') ?>">
	<link rel="stylesheet" href="<?= base_url('public/assets/css/admin/components/buttons.css') ?>">
	<link rel="stylesheet" href="<?= base_url('public/assets/css/admin/components/datatable.css') ?>">
	<link rel="stylesheet" href="<?= base_url('public/assets/css/admin/components/form.css') ?>">
	<link rel="stylesheet" href="<?= base_url('public/assets/css/admin/components/badges.css') ?>">
	<link rel="stylesheet" href="<?= base_url('public/assets/css/admin/components/alerts.css') ?>">
	<link rel="stylesheet" href="<?= base_url('public/assets/css/admin/components/dashboard.css') ?>">

	<!-- Page-Specific Styles -->
	<?php if (!empty($page_css)): ?>
		<?php foreach ((array)$page_css as $css): ?>
			<link rel="stylesheet" href="<?= base_url('public/assets/css/pages/' . $css) ?>">
		<?php endforeach; ?>
	<?php endif; ?>
</head>
<body>
	<div class="admin-wrapper">
		<!-- Sidebar Backdrop (Mobile) -->
		<div class="sidebar-backdrop" id="sidebar-backdrop" aria-hidden="true"></div>

		<!-- Sidebar -->
		<aside class="admin-sidebar" id="admin-sidebar" role="navigation" aria-label="Menu principal">
			<div class="sidebar-header">
				<a href="<?= base_url('admin/painel') ?>" class="brand-logo">
					<div class="brand-logo-icon" aria-hidden="true">
						<i class="bi bi-lightning-fill"></i>
					</div>
					<span class="brand-logo-text">Inver<span>ta</span></span>
				</a>
			</div>

			<div class="sidebar-menu">
				<ul class="nav flex-column" role="menubar">
					<li class="nav-item" role="none">
						<a class="nav-link <?= $this->uri->segment(2) == 'painel' ? 'active' : '' ?>"
						   href="<?= base_url('admin/painel') ?>"
						   role="menuitem"
						   <?= $this->uri->segment(2) == 'painel' ? 'aria-current="page"' : '' ?>>
							<i class="bi bi-grid-1x2" aria-hidden="true"></i> Dashboard
						</a>
					</li>

					<li class="nav-item nav-section" role="none">
						<span class="nav-section-title">Administração</span>
					</li>

					<li class="nav-item" role="none">
						<a class="nav-link <?= $this->uri->segment(2) == 'usuarios' ? 'active' : '' ?>"
						   href="<?= base_url('admin/usuarios') ?>"
						   role="menuitem"
						   <?= $this->uri->segment(2) == 'usuarios' ? 'aria-current="page"' : '' ?>>
							<i class="bi bi-people" aria-hidden="true"></i> Gestão de Usuários
						</a>
					</li>
					<li class="nav-item" role="none">
						<a class="nav-link <?= $this->uri->segment(2) == 'perfis' ? 'active' : '' ?>"
						   href="<?= base_url('admin/perfis') ?>"
						   role="menuitem"
						   <?= $this->uri->segment(2) == 'perfis' ? 'aria-current="page"' : '' ?>>
							<i class="bi bi-shield-check" aria-hidden="true"></i> Perfis e Permissões
						</a>
					</li>

					<li class="nav-item nav-section" role="none">
						<span class="nav-section-title">Acadêmico</span>
					</li>
					<li class="nav-item" role="none">
						<a class="nav-link disabled-link" href="#" role="menuitem" aria-disabled="true" tabindex="-1">
							<i class="bi bi-journal-bookmark" aria-hidden="true"></i> Gestão de Cursos
							<span class="badge badge-dev">Em dev</span>
						</a>
					</li>
					<li class="nav-item" role="none">
						<a class="nav-link disabled-link" href="#" role="menuitem" aria-disabled="true" tabindex="-1">
							<i class="bi bi-star" aria-hidden="true"></i> Gestão de Avaliações
							<span class="badge badge-dev">Em dev</span>
						</a>
					</li>

					<li class="nav-item nav-section" role="none">
						<span class="nav-section-title">Insights</span>
					</li>
					<li class="nav-item" role="none">
						<a class="nav-link disabled-link" href="#" role="menuitem" aria-disabled="true" tabindex="-1">
							<i class="bi bi-bar-chart-line" aria-hidden="true"></i> Relatórios
							<span class="badge badge-dev">Em dev</span>
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
						<span class="name"><?= htmlspecialchars($this->session->userdata('user_name') ?? 'Usuário') ?></span>
						<span class="role">Administrador</span>
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
					        style="color: var(--text-muted);">
						<i class="bi bi-list fs-4" aria-hidden="true"></i>
					</button>
					<div class="header-breadcrumb" aria-label="Localização atual">
						<i class="bi bi-house-door" aria-hidden="true"></i>
						<span>Painel Administrativo</span>
					</div>
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
				<?php $this->load->view($page_name); ?>
			</div>
		</main>
	</div>

	<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
	<script src="<?= base_url('public/assets/js/bootstrap.bundle.min.js') ?>"></script>
	<script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
	<script src="//cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
	<script src="<?= base_url('public/assets/js/admin/layout.js') ?>"></script>

	<!-- Page-Specific Scripts -->
	<?php if (!empty($page_js)): ?>
		<?php foreach ((array)$page_js as $js): ?>
			<script src="<?= base_url('public/assets/js/pages/' . $js) ?>"></script>
		<?php endforeach; ?>
	<?php endif; ?>
</body>
</html>
