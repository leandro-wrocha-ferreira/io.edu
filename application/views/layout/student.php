<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= html_escape($title ?? 'Área do Aluno — ' . get_institution_name()) ?></title>
	<meta name="description" content="Ambiente Virtual de Aprendizagem do Aluno">

	<!-- Google Fonts: Inter -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

	<!-- Theme Logic in Head (Prevents FOUC) -->
	<script src="<?= base_url('public/assets/js/theme.js') ?>"></script>

	<!-- Foundation Styles -->
	<link rel="stylesheet" href="<?= base_url('public/assets/css/bootstrap.min.css') ?>">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

	<!-- Unified Design System Tokens & Base -->
	<link rel="stylesheet" href="<?= base_url('public/assets/css/shared/tokens.css') ?>">
	<link rel="stylesheet" href="<?= base_url('public/assets/css/shared/base.css') ?>">
	<link rel="stylesheet" href="<?= base_url('public/assets/css/shared/components.css') ?>">

	<!-- White-Label Tenant Overrides -->
	<?= render_branding_styles() ?>

	<style>
		/* Student Header Styles */
		.edu-student-navbar {
			background-color: var(--edu-bg-surface);
			border-bottom: 1px solid var(--edu-border);
			padding: 0.75rem 0;
			position: sticky;
			top: 0;
			z-index: 1000;
			transition: background-color var(--edu-transition-theme), border-color var(--edu-transition-theme);
		}
		.edu-student-nav-link {
			color: var(--edu-text-main);
			font-weight: 600;
			font-size: 0.9375rem;
			padding: 0.5rem 0.85rem;
			border-radius: var(--edu-radius-md);
			transition: all var(--edu-transition-fast);
			text-decoration: none;
			display: inline-flex;
			align-items: center;
			gap: 0.4rem;
		}
		.edu-student-nav-link:hover,
		.edu-student-nav-link.active {
			color: var(--edu-primary);
			background-color: var(--edu-primary-ghost);
		}
		.edu-student-avatar {
			width: 38px;
			height: 38px;
			border-radius: var(--edu-radius-pill);
			background: var(--edu-primary-gradient);
			color: #ffffff;
			display: flex;
			align-items: center;
			justify-content: center;
			font-weight: 700;
			font-size: 0.9rem;
			box-shadow: 0 2px 6px rgba(79, 70, 229, 0.3);
		}
		.edu-student-footer {
			background-color: var(--edu-bg-surface);
			border-top: 1px solid var(--edu-border);
			padding: 2.5rem 0 1.5rem;
			margin-top: 4rem;
			transition: background-color var(--edu-transition-theme), border-color var(--edu-transition-theme);
		}
	</style>

	<!-- Page-Specific Styles -->
	<?php if (!empty($page_css)): ?>
		<?php foreach ((array)$page_css as $css): ?>
			<link rel="stylesheet" href="<?= base_url('public/assets/css/pages/' . $css) ?>">
		<?php endforeach; ?>
	<?php endif; ?>
</head>
<body class="d-flex flex-column min-vh-100">
	<!-- Accessibility: Skip Link -->
	<a href="#main-content" class="skip-link">Pular para o conteúdo principal</a>

	<!-- Top Navigation -->
	<header class="edu-student-navbar" role="banner">
		<div class="container">
			<div class="d-flex align-items-center justify-content-between">
				<!-- Brand Logo -->
				<a href="<?= base_url('aluno/painel') ?>" class="text-decoration-none" aria-label="<?= html_escape(get_institution_name()) ?> — Início do Aluno">
					<?= get_institution_logo() ?>
				</a>

				<!-- Desktop Nav Links -->
				<nav class="d-none d-md-flex align-items-center gap-2" role="navigation" aria-label="Navegação do Aluno">
					<a href="<?= base_url('aluno/painel') ?>"
					   class="edu-student-nav-link <?= ($this->uri->segment(1) == 'aluno' && $this->uri->segment(2) == 'painel') ? 'active' : '' ?>"
					   <?= ($this->uri->segment(1) == 'aluno' && $this->uri->segment(2) == 'painel') ? 'aria-current="page"' : '' ?>>
						<i class="bi bi-grid-1x2" aria-hidden="true"></i> Meu Painel
					</a>
					<a href="<?= base_url('aluno/jornadas') ?>"
					   class="edu-student-nav-link <?= ($this->uri->segment(2) == 'jornadas') ? 'active' : '' ?>"
					   <?= ($this->uri->segment(2) == 'jornadas') ? 'aria-current="page"' : '' ?>>
						<i class="bi bi-compass" aria-hidden="true"></i> Trilhas & Jornadas
					</a>
					<a href="<?= base_url('cursos') ?>"
					   class="edu-student-nav-link <?= ($this->uri->segment(1) == 'cursos') ? 'active' : '' ?>">
						<i class="bi bi-journal-bookmark" aria-hidden="true"></i> Catálogo
					</a>
				</nav>

				<!-- User Actions & Theme Toggle -->
				<div class="d-flex align-items-center gap-2">
					<!-- Theme Toggler -->
					<button class="edu-btn edu-btn-ghost p-2" id="theme-toggle" aria-label="Alternar entre tema claro e escuro" title="Alternar Tema">
						<i class="bi bi-moon-stars fs-5" aria-hidden="true"></i>
					</button>

					<!-- User Dropdown -->
					<div class="dropdown">
						<button class="btn p-0 border-0 d-flex align-items-center gap-2"
						        type="button"
						        id="userMenuButton"
						        data-bs-toggle="dropdown"
						        aria-expanded="false"
						        aria-label="Menu do usuário">
							<div class="edu-student-avatar" aria-hidden="true">
								<?= strtoupper(substr($this->session->userdata('user_name') ?? 'A', 0, 1)) ?>
							</div>
							<span class="d-none d-lg-inline-block text-start">
								<span class="d-block fw-bold fs-6 text-edu-heading lh-1">
									<?= html_escape($this->session->userdata('user_name') ?? 'Aluno') ?>
								</span>
								<span class="d-block text-edu-muted fs-7">Área do Aluno</span>
							</span>
							<i class="bi bi-chevron-down text-edu-muted fs-7 d-none d-sm-inline-block" aria-hidden="true"></i>
						</button>
						<ul class="dropdown-menu dropdown-menu-end shadow-sm border-edu" aria-labelledby="userMenuButton">
							<li><h6 class="dropdown-header text-edu-muted">Conta</h6></li>
							<li>
								<a class="dropdown-item d-flex align-items-center gap-2" href="<?= base_url('aluno/painel') ?>">
									<i class="bi bi-mortarboard text-edu-primary" aria-hidden="true"></i> Meus Cursos
								</a>
							</li>
							<li>
								<a class="dropdown-item d-flex align-items-center gap-2" href="<?= base_url('aluno/jornadas') ?>">
									<i class="bi bi-award text-edu-primary" aria-hidden="true"></i> Minhas Certificações
								</a>
							</li>
							<?php if ($this->session->userdata('is_admin')): ?>
								<li><hr class="dropdown-divider border-edu"></li>
								<li>
									<a class="dropdown-item d-flex align-items-center gap-2 text-primary" href="<?= base_url('admin/painel') ?>">
										<i class="bi bi-speedometer2" aria-hidden="true"></i> Painel Admin
									</a>
								</li>
							<?php endif; ?>
							<li><hr class="dropdown-divider border-edu"></li>
							<li>
								<a class="dropdown-item d-flex align-items-center gap-2 text-danger" href="<?= base_url('sair') ?>">
									<i class="bi bi-box-arrow-right" aria-hidden="true"></i> Sair da Plataforma
								</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</header>

	<!-- Main Student Content -->
	<main class="flex-grow-1 py-4" id="main-content">
		<div class="container">
			<?php if (isset($page_name)) { $this->load->view($page_name); } ?>
		</div>
	</main>

	<!-- Footer -->
	<footer class="edu-student-footer" role="contentinfo">
		<div class="container">
			<div class="row align-items-center gy-3">
				<div class="col-md-6 text-center text-md-start">
					<p class="mb-0 text-edu-muted fs-7">
						&copy; <?= date('Y') ?> <strong><?= html_escape(get_institution_name()) ?></strong>. Todos os direitos reservados.
					</p>
				</div>
				<div class="col-md-6 text-center text-md-end">
					<div class="d-inline-flex gap-3 text-edu-muted fs-7">
						<a href="<?= base_url('cursos') ?>" class="text-edu-muted">Catálogo de Cursos</a>
						<span>·</span>
						<a href="<?= base_url('aluno/jornadas') ?>" class="text-edu-muted">Trilhas de Formação</a>
						<span>·</span>
						<a href="#" class="text-edu-muted">Termos & Privacidade</a>
					</div>
				</div>
			</div>
		</div>
	</footer>

	<!-- Floating Support Button -->
	<a href="#" class="edu-support-btn" aria-label="Canal de Dúvidas e Atendimento" title="Precisa de Ajuda?">
		<i class="bi bi-question-circle-fill" aria-hidden="true"></i>
	</a>

	<!-- Scripts -->
	<script src="<?= base_url('public/assets/js/bootstrap.bundle.min.js') ?>"></script>
	<script src="<?= base_url('public/assets/js/http.js') ?>"></script>

	<!-- Page-Specific Scripts -->
	<?php if (!empty($page_js)): ?>
		<?php foreach ((array)$page_js as $js): ?>
			<script src="<?= base_url('public/assets/js/pages/' . $js) ?>"></script>
		<?php endforeach; ?>
	<?php endif; ?>
</body>
</html>
