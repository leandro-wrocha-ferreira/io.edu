<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Page Header -->
<div class="page-header animate-fade-up">
	<div class="page-header-info">
		<p class="page-header-breadcrumb">
			<i class="bi bi-house-door" aria-hidden="true"></i>
			<span class="sep">›</span>
			<span>Dashboard</span>
		</p>
		<h1 class="page-header-title">Bem-vindo, <?= htmlspecialchars($user_name ?? $this->session->userdata('user_name')) ?>!</h1>
		<p class="page-header-subtitle">Acompanhe os números da sua plataforma de educação.</p>
	</div>
</div>

<!-- Stats Row -->
<div class="row g-4 mb-4">
	<!-- Total Alunos -->
	<div class="col-xl-4 col-md-6">
		<div class="dashboard-stat-card animate-fade-up animate-delay-1">
			<div class="stat-icon-wrap stat-icon-primary">
				<i class="bi bi-people-fill" aria-hidden="true"></i>
			</div>
			<div class="stat-body">
				<div class="stat-label">Alunos</div>
				<div class="stat-value"><?= number_format($total_students) ?></div>
				<div class="stat-meta">Total de alunos ativos</div>
			</div>
		</div>
	</div>

	<!-- Cursos Ativos -->
	<div class="col-xl-4 col-md-6">
		<div class="dashboard-stat-card animate-fade-up animate-delay-2">
			<div class="stat-icon-wrap stat-icon-success">
				<i class="bi bi-journal-bookmark-fill" aria-hidden="true"></i>
			</div>
			<div class="stat-body">
				<div class="stat-label">Cursos Ativos</div>
				<div class="stat-value">—</div>
				<div class="stat-meta">Em desenvolvimento</div>
			</div>
		</div>
	</div>

	<!-- Matrículas -->
	<div class="col-xl-4 col-md-6">
		<div class="dashboard-stat-card animate-fade-up animate-delay-3">
			<div class="stat-icon-wrap stat-icon-warning">
				<i class="bi bi-mortarboard-fill" aria-hidden="true"></i>
			</div>
			<div class="stat-body">
				<div class="stat-label">Matrículas Recentes</div>
				<div class="stat-value">—</div>
				<div class="stat-meta">Em desenvolvimento</div>
			</div>
		</div>
	</div>
</div>
