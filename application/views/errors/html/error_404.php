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
    <title>404 - Página não encontrada</title>
    
    <!-- Theme Script (must be in head to prevent FOUC) -->
    <script src="<?= $base_url ?>public/assets/js/theme.js"></script>

    <link rel="stylesheet" href="<?= $base_url ?>public/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= $base_url ?>public/assets/css/theme.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body {
            background-color: var(--bg-main, #f4f6f9);
            color: var(--text-main, #334155);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .error-container {
            text-align: center;
            padding: 3rem 2rem;
            background-color: var(--bg-card, #fff);
            border: 1px solid var(--border-color, transparent);
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            max-width: 500px;
            width: 90%;
            border-top: 5px solid var(--brand-primary, #0d6efd) !important;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }
        .error-code {
            font-size: 6rem;
            font-weight: 700;
            color: var(--brand-primary, #0d6efd);
            line-height: 1;
            margin-bottom: 0.5rem;
        }
        .error-heading {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-heading, #1e293b);
            margin-bottom: 1rem;
        }
        .error-text {
            color: var(--text-muted, #64748b);
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">404</div>
        <h1 class="error-heading">Página não encontrada</h1>
        <p class="error-text">Desculpe, mas a página que você está procurando não existe, foi removida ou está temporariamente indisponível.</p>
        <a href="<?= $base_url ?>" class="btn-theme-primary">
            <i class="bi bi-house-door"></i> Voltar para o início
        </a>
    </div>
</body>
</html>