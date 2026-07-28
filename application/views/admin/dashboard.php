<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-theme-heading">Dashboard</h1>
</div>

<div class="alert alert-primary bg-theme-card border-0 shadow-sm border-start border-primary border-4 mb-4 py-3">
    <div class="d-flex align-items-center">
        <i class="bi bi-info-circle-fill text-primary fs-4 me-3"></i>
        <div class="text-theme-main">
            Olá, <strong><?= htmlspecialchars($user_name ?? $this->session->userdata('user_name')) ?></strong>! Bem-vindo ao novo painel administrativo da Plataforma de Educação.
        </div>
    </div>
</div>

<div class="row">
    <!-- Alunos (Total) Card -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card bg-theme-card border-theme shadow-sm h-100 dashboard-card border-left-primary">
            <div class="card-body py-4">
                <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">Alunos (Total)</div>
                        <div class="h3 mb-0 fw-bold text-theme-heading"><?= $total_students ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-people text-theme-muted fs-1 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cursos Card -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card bg-theme-card border-theme shadow-sm h-100 dashboard-card border-left-success">
            <div class="card-body py-4">
                <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-success text-uppercase mb-1">Cursos Ativos</div>
                        <div class="h3 mb-0 fw-bold text-theme-heading">-</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-journal-bookmark text-theme-muted fs-1 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Matrículas Card -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card bg-theme-card border-theme shadow-sm h-100 dashboard-card border-left-warning">
            <div class="card-body py-4">
                <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-warning text-uppercase mb-1">Matrículas Recentes</div>
                        <div class="h3 mb-0 fw-bold text-theme-heading">-</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-mortarboard text-theme-muted fs-1 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
