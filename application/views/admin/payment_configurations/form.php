<?php
/**
 * @var array|null $detail
 * @var array<\app\domain\finance\PaymentProvider> $providers
 */
$config = !empty($detail) ? $detail['configuration'] : null;
$account = !empty($detail) ? $detail['account'] : null;
$credential = !empty($detail) ? $detail['credential'] : null;
$provider = !empty($detail) ? $detail['provider'] : null;
$masked_api_key = !empty($detail) ? $detail['masked_api_key'] : '';

$is_edit = !empty($config);
$current_methods = [];
if ($this->input->method() === 'post') {
	$current_methods = (array) ($this->input->post('allowed_methods') ?: []);
} elseif ($is_edit) {
	$current_methods = $config->get_allowed_methods();
} else {
	$current_methods = [];
}
?>
<div class="container-fluid px-0">
	<!-- Page Header -->
	<div class="page-header animate-fade-up">
		<div class="page-header-info">
			<nav class="page-header-breadcrumb" aria-label="Breadcrumb">
				<a href="<?= base_url('admin/painel') ?>">Painel</a>
				<span class="sep" aria-hidden="true">/</span>
				<a href="<?= base_url('admin/configuracoes-pagamento') ?>">Configurações de Pagamento</a>
				<span class="sep" aria-hidden="true">/</span>
				<span class="active" aria-current="page"><?= $is_edit ? 'Editar' : 'Nova' ?></span>
			</nav>
			<h1 class="page-header-title"><?= $is_edit ? 'Editar Configuração de Pagamento' : 'Nova Configuração de Pagamento' ?></h1>
			<p class="page-header-subtitle">Insira as credenciais e dados de conta do seu provedor para habilitar o faturamento de cursos.</p>
		</div>
		<div class="page-header-actions">
			<a href="<?= base_url('admin/configuracoes-pagamento') ?>" class="edu-btn edu-btn-outline">
				<i class="bi bi-arrow-left me-1" aria-hidden="true"></i> Voltar
			</a>
		</div>
	</div>

	<?php if ($this->session->flashdata('error')): ?>
		<div class="alert alert-danger alert-dismissible fade show animate-fade-up" role="alert">
			<i class="bi bi-exclamation-triangle-fill me-2" aria-hidden="true"></i>
			<?= html_escape($this->session->flashdata('error')) ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
		</div>
	<?php endif; ?>

	<?php if (validation_errors()): ?>
		<div class="alert alert-danger alert-dismissible fade show animate-fade-up" role="alert">
			<i class="bi bi-exclamation-triangle-fill me-2" aria-hidden="true"></i>
			<div class="small"><?= validation_errors() ?></div>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
		</div>
	<?php endif; ?>

	<div class="edu-form-card animate-fade-up animate-delay-1">
		<form action="<?= current_url() ?>" method="POST" id="payment-config-form">
			<!-- Seção 1: Identificação do Perfil e Gateway -->
			<div class="edu-form-section">
				<div class="edu-form-section-header">
					<div class="edu-form-section-icon">
						<i class="bi bi-credit-card-2-front" aria-hidden="true"></i>
					</div>
					<div>
						<h2 class="edu-form-section-title">Perfil e Provedor</h2>
						<p class="edu-form-section-desc">Identificação do perfil comercial e seleção do gateway adquirente parceiro.</p>
					</div>
				</div>

				<div class="row g-3">
					<div class="col-md-6 edu-form-field">
						<label class="edu-form-label edu-label-required" for="config-name">Nome do Perfil de Faturamento</label>
						<input type="text"
						       class="form-control edu-form-control"
						       id="config-name"
						       name="name"
						       value="<?= html_escape(set_value('name', $is_edit ? $config->get_name() : '')) ?>"
						       placeholder="Ex: Stripe Brasil — Vendas Diretas"
						       maxlength="150"
						       required>
						<span class="edu-form-hint">Nome de identificação exibido no seletor de faturamento dos cursos.</span>
					</div>

					<div class="col-md-6 edu-form-field">
						<label class="edu-form-label edu-label-required" for="config-provider">Provedor / Adquirente</label>
						<?php if ($is_edit): ?>
							<input type="text" class="form-control edu-form-control" value="<?= html_escape($provider ? $provider->get_name() : 'Provedor') ?>" disabled>
							<input type="hidden" name="provider_id" value="<?= $provider ? $provider->get_id() : '' ?>">
							<span class="edu-form-hint">O provedor não pode ser alterado após a criação da conta.</span>
						<?php else: ?>
							<select class="form-select edu-form-select" id="config-provider" name="provider_id" required>
								<option value="">Selecione um provedor...</option>
								<?php foreach ($providers as $item): ?>
									<option value="<?= $item->get_id() ?>" <?= set_select('provider_id', (string) $item->get_id()) ?>>
										<?= html_escape($item->get_name()) ?>
									</option>
								<?php endforeach; ?>
							</select>
							<span class="edu-form-hint">Selecione o provedor onde você possui conta ativa.</span>
						<?php endif; ?>
					</div>

					<div class="col-md-6 edu-form-field">
						<label class="edu-form-label edu-label-required" for="config-environment">Ambiente de Operação</label>
						<?php
							$current_env = 'production';
							if ($is_edit && $account) {
								$current_env = $account->get_environment();
							}
							$selected_env = set_value('environment', $current_env);
						?>
						<select class="form-select edu-form-select" id="config-environment" name="environment" required>
							<option value="production" <?= $selected_env === 'production' ? 'selected' : '' ?>>Produção (Transações Reais)</option>
							<option value="sandbox" <?= $selected_env === 'sandbox' ? 'selected' : '' ?>>Sandbox / Testes (Homologação)</option>
						</select>
						<span class="edu-form-hint">Certifique-se de usar a chave de API correspondente ao ambiente.</span>
					</div>

					<div class="col-md-6 edu-form-field">
						<label class="edu-form-label" for="config-description">Descrição / Notas Internas</label>
						<input type="text"
						       class="form-control edu-form-control"
						       id="config-description"
						       name="description"
						       value="<?= html_escape(set_value('description', $is_edit ? $config->get_description() : '')) ?>"
						       placeholder="Ex: Conta vinculada à filial de São Paulo."
						       maxlength="255">
						<span class="edu-form-hint">Informação de uso interno da sua equipe financeira.</span>
					</div>
				</div>
			</div>

			<!-- Seção 2: Credenciais de API e Segurança -->
			<div class="edu-form-section">
				<div class="edu-form-section-header">
					<div class="edu-form-section-icon">
						<i class="bi bi-shield-lock" aria-hidden="true"></i>
					</div>
					<div>
						<h2 class="edu-form-section-title">Credenciais de Conexão</h2>
						<p class="edu-form-section-desc">Chaves seguras criptografadas em repouso com algoritmo AES-256-GCM.</p>
					</div>
				</div>

				<div class="row g-3">
					<div class="col-md-6 edu-form-field">
						<label class="edu-form-label <?= $is_edit ? '' : 'edu-label-required' ?>" for="config-api-key">Chave de API Secreta (API Key / Token)</label>
						<div class="input-group">
							<input type="password"
							       class="form-control edu-form-control font-monospace"
							       id="config-api-key"
							       name="api_key"
							       autocomplete="new-password"
							       placeholder="<?= $is_edit ? '••••••••••••••••' : 'Cole sua chave de API secreta' ?>"
							       <?= $is_edit ? '' : 'required' ?>>
							<button class="btn btn-outline-secondary" type="button" id="toggle-api-key" aria-label="Mostrar ou ocultar chave">
								<i class="bi bi-eye" id="toggle-api-key-icon" aria-hidden="true"></i>
							</button>
						</div>
						<span class="edu-form-hint">
							<?php if ($is_edit && !empty($masked_api_key)): ?>
								Chave atual armazenada: <code><?= html_escape($masked_api_key) ?></code>. Deixe em branco para manter a mesma.
							<?php else: ?>
								Chave privada obtida na área de desenvolvedores/integrações do gateway.
							<?php endif; ?>
						</span>
					</div>

					<div class="col-md-6 edu-form-field">
						<label class="edu-form-label" for="config-webhook-secret">Segredo do Webhook (Webhook Secret)</label>
						<input type="password"
						       class="form-control edu-form-control font-monospace"
						       id="config-webhook-secret"
						       name="webhook_secret"
						       autocomplete="new-password"
						       placeholder="<?= $is_edit ? 'Deixe em branco para manter o atual' : 'Ex: whsec_...' ?>"
						       maxlength="255">
						<span class="edu-form-hint">Utilizado para validação de assinaturas das notificações de pagamento recebidas.</span>
					</div>
				</div>
			</div>

			<!-- Seção 3: Formas de Pagamento e Status -->
			<div class="edu-form-section">
				<div class="edu-form-section-header">
					<div class="edu-form-section-icon">
						<i class="bi bi-sliders" aria-hidden="true"></i>
					</div>
					<div>
						<h2 class="edu-form-section-title">Formas de Pagamento e Ativação</h2>
						<p class="edu-form-section-desc">Selecione quais métodos de pagamento serão aceitos nos cursos faturados por este perfil.</p>
					</div>
				</div>

				<div class="row g-3">
					<div class="col-12 edu-form-field">
						<label class="edu-form-label edu-label-required">Métodos de Cobrança Permitidos</label>
						<div class="d-flex flex-wrap gap-4 pt-1">
							<div class="form-check">
								<input class="form-check-input"
								       type="checkbox"
								       name="allowed_methods[]"
								       value="credit_card"
								       id="method-credit-card"
								       <?= in_array('credit_card', $current_methods, true) ? 'checked' : '' ?>>
								<label class="form-check-label fw-semibold" for="method-credit-card">
									<i class="bi bi-credit-card-2-front me-1" aria-hidden="true"></i> Cartão de Crédito
								</label>
							</div>

							<div class="form-check">
								<input class="form-check-input"
								       type="checkbox"
								       name="allowed_methods[]"
								       value="pix"
								       id="method-pix"
								       <?= in_array('pix', $current_methods, true) ? 'checked' : '' ?>>
								<label class="form-check-label fw-semibold" for="method-pix">
									<i class="bi bi-qr-code-scan me-1" aria-hidden="true"></i> PIX
								</label>
							</div>

							<div class="form-check">
								<input class="form-check-input"
								       type="checkbox"
								       name="allowed_methods[]"
								       value="boleto"
								       id="method-boleto"
								       <?= in_array('boleto', $current_methods, true) ? 'checked' : '' ?>>
								<label class="form-check-label fw-semibold" for="method-boleto">
									<i class="bi bi-upc-scan me-1" aria-hidden="true"></i> Boleto Bancário
								</label>
							</div>
						</div>
						<span class="edu-form-hint">Os alunos poderão escolher entre os métodos habilitados durante o checkout do curso.</span>
					</div>

					<div class="col-12 edu-form-field mt-3">
						<div class="form-check form-switch">
							<?php
								$is_active_checked = true;
								if ($is_edit) {
									$is_active_checked = $config->is_active();
								}
								if ($this->input->method() === 'post') {
									$is_active_checked = ($this->input->post('is_active') === '1');
								}
							?>
							<input class="form-check-input" type="checkbox" role="switch" id="config-active" name="is_active" value="1" <?= $is_active_checked ? 'checked' : '' ?>>
							<label class="form-check-label fw-semibold" for="config-active">Configuração Ativa para Vendas</label>
						</div>
						<span class="edu-form-hint">Desmarque para pausar cobranças por esta conta sem remover as configurações.</span>
					</div>
				</div>
			</div>

			<!-- Action Buttons -->
			<div class="edu-form-actions">
				<a href="<?= base_url('admin/configuracoes-pagamento') ?>" class="edu-btn edu-btn-secondary">Cancelar</a>
				<button type="submit" class="edu-btn edu-btn-primary" id="btn-submit">
					<i class="bi bi-check-lg me-1" aria-hidden="true"></i> Salvar Configuração
				</button>
			</div>
		</form>
	</div>
</div>
