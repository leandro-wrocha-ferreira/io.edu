<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= html_escape($title ?? 'Autenticação — ' . get_institution_name()) ?></title>
	<meta name="description" content="Acesse sua conta na plataforma educacional">

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
		/* Split-Screen Authentication Layout */
		.edu-auth-wrapper {
			min-height: 100vh;
			display: flex;
			background-color: var(--edu-bg-surface);
		}

		/* Left Hero Column */
		.edu-auth-hero {
			flex: 1.1;
			background: var(--edu-primary-gradient);
			color: #ffffff;
			padding: 4rem;
			display: flex;
			flex-direction: column;
			justify-content: space-between;
			position: relative;
			overflow: hidden;
		}

		.edu-auth-hero::before {
			content: '';
			position: absolute;
			top: -15%;
			right: -15%;
			width: 550px;
			height: 550px;
			border-radius: 50%;
			background: radial-gradient(circle, rgba(255, 255, 255, 0.12) 0%, rgba(255, 255, 255, 0) 70%);
			pointer-events: none;
		}

		.edu-auth-hero::after {
			content: '';
			position: absolute;
			bottom: -20%;
			left: -20%;
			width: 600px;
			height: 600px;
			border-radius: 50%;
			background: radial-gradient(circle, rgba(6, 182, 212, 0.15) 0%, rgba(6, 182, 212, 0) 70%);
			pointer-events: none;
		}

		.edu-auth-hero-content {
			position: relative;
			z-index: 2;
			max-width: 520px;
			margin: auto 0;
		}

		.edu-auth-hero-title {
			font-size: 2.75rem;
			font-weight: 800;
			line-height: 1.15;
			color: #ffffff;
			margin-bottom: 1.25rem;
			letter-spacing: -0.03em;
		}

		.edu-auth-hero-text {
			font-size: 1.125rem;
			color: rgba(255, 255, 255, 0.85);
			line-height: 1.6;
			margin-bottom: 2rem;
		}

		.edu-auth-feature-list {
			display: flex;
			flex-direction: column;
			gap: 1rem;
		}

		.edu-auth-feature-item {
			display: flex;
			align-items: center;
			gap: 0.75rem;
			font-size: 0.95rem;
			color: rgba(255, 255, 255, 0.9);
		}

		.edu-auth-feature-icon {
			width: 28px;
			height: 28px;
			border-radius: var(--edu-radius-pill);
			background: rgba(255, 255, 255, 0.2);
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 0.85rem;
			color: #ffffff;
			flex-shrink: 0;
		}

		/* Right Form Column */
		.edu-auth-panel {
			flex: 0.9;
			display: flex;
			flex-direction: column;
			justify-content: space-between;
			padding: 2.5rem 3.5rem;
			background-color: var(--edu-bg-surface);
			overflow-y: auto;
		}

		.edu-auth-form-container {
			max-width: 420px;
			width: 100%;
			margin: auto;
			padding: 2rem 0;
		}

		@media (max-width: 992px) {
			.edu-auth-hero {
				display: none;
			}
			.edu-auth-panel {
				flex: 1;
				padding: 2rem 1.5rem;
			}
		}
	</style>
</head>
<body>
	<!-- Accessibility: Skip Link -->
	<a href="#main-content" class="skip-link">Pular para o conteúdo principal</a>

	<div class="edu-auth-wrapper">
		<!-- Left Editorial Hero Column -->
		<aside class="edu-auth-hero" aria-label="Informações Institucionais">
			<div class="edu-auth-hero-top">
				<a href="<?= base_url() ?>" class="text-white text-decoration-none">
					<span class="d-inline-flex align-items-center gap-2">
						<span class="edu-brand-icon" style="background: rgba(255,255,255,0.2); box-shadow: none;">
							<i class="bi bi-mortarboard-fill text-white"></i>
						</span>
						<span class="edu-brand-text text-white"><?= html_escape(get_institution_name()) ?></span>
					</span>
				</a>
			</div>

			<div class="edu-auth-hero-content animate-fade-up">
				<h1 class="edu-auth-hero-title">Sua jornada de conhecimento começa aqui.</h1>
				<p class="edu-auth-hero-text">
					Acesse aulas em alta resolução, acompanhe seu progresso de aprendizagem em tempo real e conquiste certificados com reconhecimento profissional.
				</p>

				<div class="edu-auth-feature-list">
					<div class="edu-auth-feature-item">
						<span class="edu-auth-feature-icon" aria-hidden="true"><i class="bi bi-check2"></i></span>
						<span>Acesso ilimitado aos conteúdos matriculados</span>
					</div>
					<div class="edu-auth-feature-item">
						<span class="edu-auth-feature-icon" aria-hidden="true"><i class="bi bi-check2"></i></span>
						<span>Trilhas com prazos e emissão imediata de certificado</span>
					</div>
					<div class="edu-auth-feature-item">
						<span class="edu-auth-feature-icon" aria-hidden="true"><i class="bi bi-check2"></i></span>
						<span>Experiência responsiva para estudar em qualquer dispositivo</span>
					</div>
				</div>
			</div>

			<div class="edu-auth-hero-footer">
				<p class="mb-0 text-white-50 fs-7">
					&copy; <?= date('Y') ?> <?= html_escape(get_institution_name()) ?>. Todos os direitos reservados.
				</p>
			</div>
		</aside>

		<!-- Right Form Column -->
		<main class="edu-auth-panel" id="main-content">
			<!-- Topbar Actions -->
			<div class="d-flex align-items-center justify-content-between">
				<!-- Mobile Brand Display -->
				<div class="d-lg-none">
					<?= get_institution_logo() ?>
				</div>

				<div class="ms-auto d-flex align-items-center gap-2">
					<button class="edu-btn edu-btn-ghost p-2" id="theme-toggle" aria-label="Alternar entre tema claro e escuro" title="Alternar Tema">
						<i class="bi bi-moon-stars fs-5" aria-hidden="true"></i>
					</button>
				</div>
			</div>

			<!-- Dynamic Form Injection -->
			<div class="edu-auth-form-container">
				<?php if (isset($page_name)) { $this->load->view($page_name); } ?>
			</div>

			<!-- Form Footer -->
			<div class="text-center text-edu-muted fs-7">
				<span>Precisa de ajuda com o acesso? </span>
				<a href="#" class="text-edu-primary fw-semibold">Suporte ao Aluno</a>
			</div>
		</main>
	</div>

	<!-- Scripts -->
	<script src="<?= base_url('public/assets/js/bootstrap.bundle.min.js') ?>"></script>
	<script src="<?= base_url('public/assets/js/http.js') ?>"></script>
</body>
</html>
