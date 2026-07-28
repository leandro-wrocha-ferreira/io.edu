<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= $title ?? 'Inverta Admin' ?></title>
	
	<!-- Fonts -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
	
	<!-- Theme Logic (Before CSS to prevent FOUC) -->
	<script src="<?= base_url('public/assets/js/theme.js') ?>"></script>

	<!-- Styles -->
	<link rel="stylesheet" href="<?= base_url('public/assets/css/bootstrap.min.css') ?>">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
	<link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
	<link rel="stylesheet" href="<?= base_url('public/assets/css/theme.css') ?>">
	<link rel="stylesheet" href="<?= base_url('public/assets/css/admin.css') ?>">
</head>
<body>
	<div class="admin-wrapper">
		<!-- Sidebar -->
		<aside class="admin-sidebar">
			<div class="sidebar-header">
				<div class="brand-logo">
					<i class="bi bi-box-fill text-primary"></i> <span>Inverta</span>
				</div>
			</div>
			
			<div class="sidebar-menu">
				<ul class="nav flex-column">
					<li class="nav-item">
						<a class="nav-link <?= $this->uri->segment(2) == 'painel' ? 'active' : '' ?>" href="<?= base_url('admin/painel') ?>">
							<i class="bi bi-grid-1x2"></i> Dashboard
						</a>
					</li>
					
					<li class="nav-item mt-4 mb-2">
						<span class="nav-section-title">ADMINISTRAÇÃO</span>
					</li>
					<li class="nav-item">
						<a class="nav-link <?= $this->uri->segment(2) == 'usuarios' ? 'active' : '' ?>" href="<?= base_url('admin/usuarios') ?>">
							<i class="bi bi-people"></i> Gestão de Usuários
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link <?= $this->uri->segment(2) == 'perfis' ? 'active' : '' ?>" href="<?= base_url('admin/perfis') ?>">
							<i class="bi bi-shield-lock"></i> Perfis e Permissões
						</a>
					</li>
					
					<li class="nav-item mt-4 mb-2">
						<span class="nav-section-title">ACADÊMICO</span>
					</li>
					<li class="nav-item">
						<a class="nav-link disabled-link" href="#" title="Em desenvolvimento">
							<i class="bi bi-journal-bookmark"></i> Gestão de Cursos 
							<span class="badge badge-dev ms-auto">Em dev</span>
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link disabled-link" href="#" title="Em desenvolvimento">
							<i class="bi bi-star"></i> Gestão de Avaliações 
							<span class="badge badge-dev ms-auto">Em dev</span>
						</a>
					</li>
					
					<li class="nav-item mt-4 mb-2">
						<span class="nav-section-title">INSIGHTS</span>
					</li>
					<li class="nav-item">
						<a class="nav-link disabled-link" href="#" title="Em desenvolvimento">
							<i class="bi bi-bar-chart"></i> Relatórios 
							<span class="badge badge-dev ms-auto">Em dev</span>
						</a>
					</li>
				</ul>
			</div>
			
			<div class="sidebar-footer">
				<div class="user-info">
					<div class="avatar">
						<?= strtoupper(substr($this->session->userdata('user_name') ?? 'U', 0, 1)) ?>
					</div>
					<div class="details">
						<span class="name"><?= htmlspecialchars($this->session->userdata('user_name') ?? 'Usuário') ?></span>
						<span class="role">Administrador</span>
					</div>
				</div>
				<a href="<?= base_url('sair') ?>" class="logout-btn" title="Sair">
					<i class="bi bi-box-arrow-right"></i>
				</a>
			</div>
		</aside>

		<!-- Main Content -->
		<main class="admin-main">
			<!-- Topbar -->
			<header class="admin-header d-flex justify-content-between align-items-center">
				<div class="d-flex align-items-center">
					<button class="btn btn-link btn-toggle-sidebar d-md-none text-theme-main p-0 me-3">
						<i class="bi bi-list fs-3"></i>
					</button>
					<div class="header-breadcrumb text-theme-muted">
						<i class="bi bi-house-door me-1"></i> Painel Administrativo
					</div>
				</div>
				<div class="header-actions">
					<!-- Theme Toggler -->
					<button class="btn btn-link text-theme-main p-0 me-3" id="theme-toggle" title="Alternar Tema">
						<i class="bi bi-moon-stars fs-5"></i>
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
</body>
</html>
