<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= $title ?? 'Inverta' ?></title>
	<link rel="stylesheet" href="<?= base_url('public/assets/css/bootstrap.min.css') ?>">
</head>
<body>
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
		<div class="container">
			<a class="navbar-brand" href="<?= base_url() ?>">Inverta</a>
		</div>
	</nav>
	<div class="container mt-4">
		<div class="d-flex justify-content-between align-items-center mb-4">
			<h1>Painel do Aluno</h1>
			<a href="<?= base_url('sair') ?>" class="btn btn-outline-secondary">Sair</a>
		</div>

		<div class="alert alert-info">
			Olá, <strong><?= htmlspecialchars($user_name) ?></strong>! Bem-vindo à sua área.
		</div>

		<div class="row">
			<div class="col-md-6">
				<div class="card mb-3">
					<div class="card-body">
						<h5 class="card-title">Meus Cursos</h5>
						<p class="card-text">Você ainda não está matriculado em nenhum curso.</p>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="card mb-3">
					<div class="card-body">
						<h5 class="card-title">Próximas Aulas</h5>
						<p class="card-text">Nenhuma aula agendada.</p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script src="<?= base_url('public/assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>

