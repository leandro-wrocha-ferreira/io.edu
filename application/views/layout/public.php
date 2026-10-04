<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= html_escape($title ?? get_institution_name() . ' — Cursos e Formações Online') ?></title>
	<meta name="description" content="Explore cursos online de excelência, trilhas práticas e certificações profissionais.">

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
		/* Public Header */
		.edu-public-header {
			background-color: var(--edu-bg-surface);
			border-bottom: 1px solid var(--edu-border);
			padding: 0.85rem 0;
			position: sticky;
			top: 0;
			z-index: 1000;
			box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
			transition: background-color var(--edu-transition-theme), border-color var(--edu-transition-theme);
		}
		.edu-public-nav-link {
			color: var(--edu-text-main);
			font-weight: 600;
			font-size: 0.9375rem;
			padding: 0.5rem 0.85rem;
			border-radius: var(--edu-radius-md);
			transition: all var(--edu-transition-fast);
			text-decoration: none;
		}
		.edu-public-nav-link:hover,
		.edu-public-nav-link.active {
			color: var(--edu-primary);
			background-color: var(--edu-primary-ghost);
		}
		.edu-public-footer {
			background-color: var(--edu-bg-surface);
			border-top: 1px solid var(--edu-border);
			padding: 4rem 0 2rem;
			margin-top: 5rem;
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

	<!-- Public Top Navigation -->
	<header class="edu-public-header" role="banner">
		<div class="container">
			<div class="d-flex align-items-center justify-content-between">
				<!-- Brand Logo -->
				<a href="<?= base_url() ?>" class="text-decoration-none" aria-label="<?= html_escape(get_institution_name()) ?> — Início">
					<?= get_institution_logo() ?>
				</a>

				<!-- Desktop Nav Links -->
				<nav class="d-none d-md-flex align-items-center gap-2" role="navigation" aria-label="Navegação Principal">
					<a href="<?= base_url('cursos') ?>" class="edu-public-nav-link <?= $this->uri->segment(1) == 'cursos' ? 'active' : '' ?>">
						Catálogo de Cursos
					</a>
					<a href="<?= base_url('aluno/jornadas') ?>" class="edu-public-nav-link">
						Trilhas & Jornadas
					</a>
					<a href="#sobre" class="edu-public-nav-link">
						Sobre Nós
					</a>
				</nav>

				<!-- Right Actions -->
				<div class="d-flex align-items-center gap-2">
					<!-- Theme Toggler -->
					<button class="edu-btn edu-btn-ghost p-2" id="theme-toggle" aria-label="Alternar entre tema claro e escuro" title="Alternar Tema">
						<i class="bi bi-moon-stars fs-5" aria-hidden="true"></i>
					</button>

					<?php if ($this->session->userdata('user_id')): ?>
						<a href="<?= base_url('aluno/painel') ?>" class="edu-btn edu-btn-primary edu-btn-sm">
							<i class="bi bi-speedometer2" aria-hidden="true"></i> Meu Painel
						</a>
					<?php else: ?>
						<a href="<?= base_url('entrar') ?>" class="edu-btn edu-btn-outline edu-btn-sm">
							<i class="bi bi-box-arrow-in-right" aria-hidden="true"></i> Entrar
						</a>
						<a href="<?= base_url('cursos') ?>" class="edu-btn edu-btn-primary edu-btn-sm d-none d-sm-inline-flex">
							Começar Agora
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</header>

	<!-- Main Public Content -->
	<main class="flex-grow-1" id="main-content">
		<?php if (isset($page_name)) { $this->load->view($page_name); } ?>
	</main>

	<!-- Public Footer -->
	<footer class="edu-public-footer" role="contentinfo">
		<div class="container">
			<div class="row g-4 mb-4">
				<div class="col-lg-4 col-md-6">
					<div class="mb-3">
						<?= get_institution_logo() ?>
					</div>
					<p class="text-edu-muted fs-6 mb-3">
						Plataforma de educação continuada com foco em excelência pedagógica, autonomia de aprendizado e desenvolvimento profissional acelerado.
					</p>
					<div class="d-flex gap-3 text-edu-primary fs-5">
						<a href="#" class="text-edu-primary" aria-label="Instagram"><i class="bi bi-instagram" aria-hidden="true"></i></a>
						<a href="#" class="text-edu-primary" aria-label="LinkedIn"><i class="bi bi-linkedin" aria-hidden="true"></i></a>
						<a href="#" class="text-edu-primary" aria-label="YouTube"><i class="bi bi-youtube" aria-hidden="true"></i></a>
					</div>
				</div>

				<div class="col-lg-2 col-md-3 col-6">
					<h6 class="text-edu-heading fw-bold mb-3">Cursos</h6>
					<ul class="list-unstyled d-flex flex-direction-column flex-column gap-2 fs-7 mb-0">
						<li><a href="<?= base_url('cursos') ?>" class="text-edu-muted">Todos os Cursos</a></li>
						<li><a href="<?= base_url('cursos?cat=tecnologia') ?>" class="text-edu-muted">Tecnologia</a></li>
						<li><a href="<?= base_url('cursos?cat=gestao') ?>" class="text-edu-muted">Gestão & Negócios</a></li>
						<li><a href="<?= base_url('cursos?cat=direito') ?>" class="text-edu-muted">Direito & Compliance</a></li>
					</ul>
				</div>

				<div class="col-lg-2 col-md-3 col-6">
					<h6 class="text-edu-heading fw-bold mb-3">Jornadas</h6>
					<ul class="list-unstyled d-flex flex-column gap-2 fs-7 mb-0">
						<li><a href="<?= base_url('aluno/jornadas') ?>" class="text-edu-muted">Trilhas de Carreira</a></li>
						<li><a href="#" class="text-edu-muted">Certificação Oficial</a></li>
						<li><a href="#" class="text-edu-muted">Prazos e Requisitos</a></li>
					</ul>
				</div>

				<div class="col-lg-4 col-md-12">
					<h6 class="text-edu-heading fw-bold mb-3">Garantia & Suporte</h6>
					<p class="text-edu-muted fs-7 mb-3">
						Plataforma segura com emissão imediata de certificados verificáveis via QR Code.
					</p>
					<div class="d-flex align-items-center gap-3">
						<span class="edu-badge edu-badge-success p-2">
							<i class="bi bi-shield-check fs-6" aria-hidden="true"></i> Ambiente 100% Seguro
						</span>
					</div>
				</div>
			</div>

			<hr class="border-edu my-4">

			<div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3 text-edu-muted fs-7">
				<p class="mb-0">
					&copy; <?= date('Y') ?> <strong><?= html_escape(get_institution_name()) ?></strong>. Todos os direitos reservados.
				</p>
				<div class="d-flex gap-3">
					<a href="#" class="text-edu-muted">Termos de Uso</a>
					<span>·</span>
					<a href="#" class="text-edu-muted">Política de Privacidade</a>
					<span>·</span>
					<a href="#" class="text-edu-muted">Segurança da Informação</a>
				</div>
			</div>
		</div>
	</footer>

	<!-- Floating Support Button -->
	<a href="#" class="edu-support-btn" aria-label="Atendimento ao Aluno e Dúvidas" title="Precisa de Ajuda?">
		<i class="bi bi-chat-dots-fill" aria-hidden="true"></i>
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
