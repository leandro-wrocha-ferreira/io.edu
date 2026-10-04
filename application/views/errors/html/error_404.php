<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$base_url = config_item('base_url');
if (empty($base_url)) {
	$base_url = '/';
} else {
	$base_url = rtrim($base_url, '/') . '/';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>404 — Página não encontrada</title>

	<!-- Theme Script (must be in head to prevent FOUC) -->
	<script src="<?= $base_url ?>public/assets/js/theme.js"></script>

	<link rel="stylesheet" href="<?= $base_url ?>public/assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?= $base_url ?>public/assets/css/shared/tokens.css">
	<link rel="stylesheet" href="<?= $base_url ?>public/assets/css/shared/base.css">
	<link rel="stylesheet" href="<?= $base_url ?>public/assets/css/shared/components.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

	<style>
		body {
			background-color: var(--edu-bg-main);
			color: var(--edu-text-main);
			min-height: 100vh;
			display: flex;
			align-items: center;
			justify-content: center;
			margin: 0;
			padding: 1.5rem;
			transition: background-color var(--edu-transition-theme), color var(--edu-transition-theme);
		}
		.error-container {
			text-align: center;
			padding: 3.5rem 2rem;
			background-color: var(--edu-bg-surface);
			border: 1px solid var(--edu-border);
			border-radius: var(--edu-radius-lg);
			box-shadow: var(--edu-shadow-lg);
			max-width: 520px;
			width: 100%;
			border-top: 5px solid var(--edu-primary) !important;
			transition: background-color var(--edu-transition-theme), border-color var(--edu-transition-theme);
		}
		.error-code {
			font-size: 5.5rem;
			font-weight: 800;
			color: var(--edu-primary);
			line-height: 1;
			margin-bottom: 0.75rem;
			letter-spacing: -0.04em;
		}
		.error-heading {
			font-size: 1.5rem;
			font-weight: 700;
			color: var(--edu-text-heading);
			margin-bottom: 1rem;
		}
		.error-text {
			color: var(--edu-text-muted);
			margin-bottom: 2rem;
			line-height: 1.5;
			font-size: 0.95rem;
		}
	</style>
</head>
<body>
	<main class="error-container animate-fade-up">
		<div class="error-code">404</div>
		<h1 class="error-heading">Página não encontrada</h1>
		<p class="error-text">O endereço solicitado não foi encontrado ou pode ter sido movido permanentemente.</p>
		<a href="<?= $base_url ?>" class="edu-btn edu-btn-primary">
			<i class="bi bi-house-door" aria-hidden="true"></i> Voltar ao Início
		</a>
	</main>
</body>
</html>