<div class="container-fluid px-0">
	<!-- Page Header -->
	<header class="edu-page-header animate-fade-up">
		<div>
			<nav class="header-breadcrumb d-flex align-items-center gap-2 mb-1" aria-label="Localização">
				<a href="<?= base_url('admin/painel') ?>" class="text-muted text-decoration-none">Painel</a>
				<i class="bi bi-chevron-right text-muted" style="font-size: 0.7rem;" aria-hidden="true"></i>
				<span class="text-muted">Relatórios</span>
				<i class="bi bi-chevron-right text-muted" style="font-size: 0.7rem;" aria-hidden="true"></i>
				<span class="fw-medium text-body">Financeiros</span>
			</nav>
			<h1 class="edu-page-header-title">Relatórios Financeiros</h1>
			<p class="edu-page-header-desc">Monitore faturamento consolidado, conversão de vendas, modalidades de pagamento e recorrência.</p>
		</div>

		<div class="edu-page-header-actions">
			<button type="button" class="edu-btn edu-btn-outline" onclick="alert('Protótipo: Exportação de extrato financeiro (CSV/XLS) simulada.');">
				<i class="bi bi-file-earmark-spreadsheet" aria-hidden="true"></i>
				<span>Exportar Extrato</span>
			</button>
		</div>
	</header>

	<!-- Filters Toolbar -->
	<div class="edu-data-toolbar animate-fade-up animate-delay-1 mb-4">
		<div class="d-flex align-items-center gap-2 flex-wrap w-100">
			<div class="d-flex align-items-center gap-2">
				<i class="bi bi-funnel text-muted" aria-hidden="true"></i>
				<span class="small fw-semibold text-muted text-uppercase">Filtrar por:</span>
			</div>
			<select class="edu-filter-select" aria-label="Filtrar por Período">
				<option value="current_month">Mês Atual (Outubro/2026)</option>
				<option value="last_quarter">Último Trimestre</option>
				<option value="year">Ano de 2026</option>
			</select>
			<select class="edu-filter-select" aria-label="Filtrar por Curso">
				<option value="">Todos os Cursos</option>
				<option value="1">Desenvolvimento Web Fullstack</option>
				<option value="2">Gestão Ágil e Scrum</option>
			</select>
			<select class="edu-filter-select" aria-label="Filtrar por Método de Pagamento">
				<option value="">Forma de Pagamento: Todas</option>
				<option value="cc">Cartão de Crédito</option>
				<option value="pix">PIX</option>
				<option value="boleto">Boleto Bancário</option>
			</select>
		</div>
	</div>

	<!-- 4 Financial KPIs -->
	<div class="row g-3 mb-4 animate-fade-up animate-delay-1">
		<div class="col-12 col-sm-6 col-xl-3">
			<div class="edu-report-kpi">
				<div class="edu-report-kpi-info">
					<span class="edu-report-kpi-label">Faturamento Total</span>
					<span class="edu-report-kpi-value text-primary"><?= html_escape($reports['kpis']['total_revenue']) ?></span>
					<span class="edu-report-kpi-trend positive">
						<i class="bi bi-arrow-up-short"></i> +18,4% no período
					</span>
				</div>
				<div class="edu-report-kpi-icon primary" aria-hidden="true">
					<i class="bi bi-currency-dollar"></i>
				</div>
			</div>
		</div>

		<div class="col-12 col-sm-6 col-xl-3">
			<div class="edu-report-kpi">
				<div class="edu-report-kpi-info">
					<span class="edu-report-kpi-label">Vendas Aprovadas</span>
					<span class="edu-report-kpi-value"><?= html_escape($reports['kpis']['sales_count']) ?></span>
					<span class="edu-report-kpi-trend positive">
						<i class="bi bi-cart-check"></i> Matrículas confirmadas
					</span>
				</div>
				<div class="edu-report-kpi-icon success" aria-hidden="true">
					<i class="bi bi-cart-check-fill"></i>
				</div>
			</div>
		</div>

		<div class="col-12 col-sm-6 col-xl-3">
			<div class="edu-report-kpi">
				<div class="edu-report-kpi-info">
					<span class="edu-report-kpi-label">Ticket Médio</span>
					<span class="edu-report-kpi-value"><?= html_escape($reports['kpis']['average_ticket']) ?></span>
					<span class="edu-report-kpi-trend positive">
						<i class="bi bi-arrow-up-short"></i> Estável (+1,2%)
					</span>
				</div>
				<div class="edu-report-kpi-icon warning" aria-hidden="true">
					<i class="bi bi-wallet2"></i>
				</div>
			</div>
		</div>

		<div class="col-12 col-sm-6 col-xl-3">
			<div class="edu-report-kpi">
				<div class="edu-report-kpi-info">
					<span class="edu-report-kpi-label">Receita Recorrente</span>
					<span class="edu-report-kpi-value"><?= html_escape($reports['kpis']['recurring_ratio']) ?></span>
					<span class="edu-report-kpi-trend positive">
						<i class="bi bi-arrow-repeat"></i> Assinaturas ativas
					</span>
				</div>
				<div class="edu-report-kpi-icon primary" aria-hidden="true">
					<i class="bi bi-arrow-repeat"></i>
				</div>
			</div>
		</div>
	</div>

	<!-- Payment Breakdown & Recurrence Overview -->
	<div class="row g-4 mb-4 animate-fade-up animate-delay-2">
		<!-- Payment Methods Distribution -->
		<div class="col-lg-6">
			<div class="edu-report-card h-100">
				<div class="edu-report-card-header">
					<div>
						<h2 class="edu-report-card-title">Distribuição por Forma de Pagamento</h2>
						<p class="edu-report-card-desc">Volume financeiro gerado por canal de liquidação.</p>
					</div>
				</div>
				<div class="edu-report-card-body">
					<?php foreach ($reports['payment_methods'] as $method_item): ?>
						<div class="edu-stat-bar-item">
							<div class="edu-stat-bar-header">
								<span class="edu-stat-bar-label"><?= html_escape($method_item['method']) ?></span>
								<span class="edu-stat-bar-value"><?= html_escape($method_item['total']) ?> (<?= (int) $method_item['percentage'] ?>%)</span>
							</div>
							<div class="edu-stat-bar-track" aria-hidden="true">
								<div class="edu-stat-bar-fill <?= $method_item['percentage'] > 50 ? '' : ($method_item['percentage'] > 20 ? 'success' : 'warning') ?>" style="width: <?= (int) $method_item['percentage'] ?>%;"></div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<!-- Recurrence vs Single Payment -->
		<div class="col-lg-6">
			<div class="edu-report-card h-100">
				<div class="edu-report-card-header">
					<div>
						<h2 class="edu-report-card-title">Modalidade de Cobrança</h2>
						<p class="edu-report-card-desc">Proporção entre compras avulsas e planos contínuos.</p>
					</div>
				</div>
				<div class="edu-report-card-body">
					<div class="edu-stat-bar-item">
						<div class="edu-stat-bar-header">
							<span class="edu-stat-bar-label">Pagamento Único (Compra Avulsa de Curso)</span>
							<span class="edu-stat-bar-value">68%</span>
						</div>
						<div class="edu-stat-bar-track" aria-hidden="true">
							<div class="edu-stat-bar-fill" style="width: 68%;"></div>
						</div>
					</div>

					<div class="edu-stat-bar-item mt-4">
						<div class="edu-stat-bar-header">
							<span class="edu-stat-bar-label">Assinatura Recorrente (Acesso a Trilhas)</span>
							<span class="edu-stat-bar-value text-success">32%</span>
						</div>
						<div class="edu-stat-bar-track" aria-hidden="true">
							<div class="edu-stat-bar-fill success" style="width: 32%;"></div>
						</div>
					</div>

					<div class="alert alert-info border-0 rounded-3 mt-4 mb-0 small">
						<i class="bi bi-info-circle me-1"></i> As assinaturas recorrentes apresentaram aumento de retenção de 8% nos últimos 60 dias.
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Recent Transactions Table -->
	<div class="edu-card animate-fade-up animate-delay-3 p-0 overflow-hidden">
		<div class="edu-card-header py-3 px-4 d-flex justify-content-between align-items-center">
			<h2 class="edu-card-title mb-0">Últimas Transações Registradas</h2>
			<span class="text-muted small">Mostrando 5 transações mais recentes</span>
		</div>
		<div class="table-responsive">
			<table class="table edu-data-table mb-0" aria-label="Tabela de Transações Recentes">
				<thead>
					<tr>
						<th scope="col" style="width: 12%;">Transação</th>
						<th scope="col" style="width: 22%;">Aluno</th>
						<th scope="col" style="width: 26%;">Curso Adquirido</th>
						<th scope="col" style="width: 12%;">Data</th>
						<th scope="col" style="width: 14%;">Forma de Pagto</th>
						<th scope="col" style="width: 14%; text-align: right;">Valor</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($reports['recent_transactions'] as $tx): ?>
						<tr>
							<td class="font-monospace small fw-semibold text-body"><?= html_escape($tx['id']) ?></td>
							<td class="fw-medium text-body"><?= html_escape($tx['student']) ?></td>
							<td class="text-muted small"><?= html_escape($tx['course']) ?></td>
							<td class="text-muted small"><?= html_escape($tx['date']) ?></td>
							<td>
								<span class="badge bg-secondary-subtle text-body border fw-normal"><?= html_escape($tx['method']) ?></span>
							</td>
							<td class="text-end fw-bold text-success">
								<?= html_escape($tx['amount']) ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
