<?php
/**
 * @var \app\domain\course\Course|null $course
 * @var \app\domain\commercial\Product|null $product
 * @var array<\app\domain\commercial\ProductPriceVersion> $price_versions
 * @var array<\app\domain\course\Category> $categories
 * @var array<\app\domain\finance\PaymentConfiguration> $payment_configurations
 * @var array<\app\domain\finance\CostCenter> $cost_centers
 */
?>
<div class="container-fluid px-0">
	<!-- Page Header -->
	<div class="page-header animate-fade-up">
		<div class="page-header-info">
			<nav class="page-header-breadcrumb" aria-label="Breadcrumb">
				<a href="<?= base_url('admin/painel') ?>">Painel</a>
				<span class="sep" aria-hidden="true">/</span>
				<a href="<?= base_url('admin/cursos') ?>">Cursos</a>
				<span class="sep" aria-hidden="true">/</span>
				<span class="active" aria-current="page"><?= !empty($course) ? 'Editar' : 'Novo' ?></span>
			</nav>
			<h1 class="page-header-title"><?= !empty($course) ? 'Editar Curso' : 'Novo Curso' ?></h1>
			<p class="page-header-subtitle">Defina as diretrizes pedagógicas, regras de acesso do aluno e parâmetros de certificação.</p>
		</div>
		<div class="page-header-actions">
			<a href="<?= base_url('admin/cursos') ?>" class="edu-btn edu-btn-outline">
				<i class="bi bi-arrow-left me-1" aria-hidden="true"></i> Voltar para Listagem
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
		<form action="<?= current_url() ?>" method="POST" id="course-form" enctype="multipart/form-data">
			<!-- Section 1: Identificação -->
			<div class="edu-form-section">
				<div class="edu-form-section-header">
					<div class="edu-form-section-icon">
						<i class="bi bi-journal-text" aria-hidden="true"></i>
					</div>
					<div>
						<h2 class="edu-form-section-title">Identificação do Curso</h2>
						<p class="edu-form-section-desc">Título, categoria, endereço URL amigável e imagem de apresentação.</p>
					</div>
				</div>

				<div class="row g-3">
					<div class="col-md-8 edu-form-field">
						<label class="edu-form-label edu-label-required" for="course-title">Título do Curso</label>
						<input type="text"
						       class="form-control edu-form-control"
						       id="course-title"
						       name="title"
						       value="<?= html_escape(set_value('title', !empty($course) ? $course->get_title() : '')) ?>"
						       placeholder="Ex: Arquitetura Limpa e DDD com PHP 8"
						       required>
						<span class="edu-form-hint">Nome formal exibido na vitrine e nos certificados de conclusão.</span>
					</div>

					<div class="col-md-4 edu-form-field">
						<label class="edu-form-label edu-label-required" for="course-category">Categoria Pedagógica</label>
						<select class="form-select edu-form-select" id="course-category" name="category_id" required>
							<option value="">Selecione uma categoria...</option>
							<?php $selected_cat = set_value('category_id', !empty($course) ? $course->get_category_id() : ''); ?>
							<?php if (!empty($categories)): ?>
								<?php foreach ($categories as $cat): ?>
									<option value="<?= $cat->get_id() ?>" <?= (string)$selected_cat === (string)$cat->get_id() ? 'selected' : '' ?>>
										<?= html_escape($cat->get_name()) ?>
									</option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select>
					</div>

					<div class="col-md-6 edu-form-field">
						<label class="edu-form-label" for="course-slug">Slug na URL (Automático)</label>
						<input type="text"
						       class="form-control edu-form-control bg-edu-subtle text-muted"
						       id="course-slug"
						       name="slug"
						       readonly
						       tabindex="-1"
						       data-course-id="<?= !empty($course) ? $course->get_id() : '' ?>"
						       value="<?= html_escape(set_value('slug', !empty($course) ? (string)$course->get_slug() : '')) ?>"
						       placeholder="[id]-titulo-do-curso">
						<span class="edu-form-hint">
							<?= !empty($course) ? 'Identificador único permanente na vitrine pública.' : 'Gerado automaticamente a partir do título. O ID do curso será prefixado ao salvar.' ?>
						</span>
					</div>

					<div class="col-md-6 edu-form-field">
						<label class="edu-form-label edu-label-required" for="course-status">Status</label>
						<select class="form-select edu-form-select" id="course-status" name="status" required>
							<?php $selected_status = set_value('status', !empty($course) ? (string)$course->get_status() : 'draft'); ?>
							<option value="draft" <?= $selected_status === 'draft' ? 'selected' : '' ?> <?= (!empty($course) && !$course->is_draft()) ? 'disabled' : '' ?>>
								Rascunho <?= (!empty($course) && !$course->is_draft()) ? '(Não é permitido retornar para rascunho)' : '' ?>
							</option>
							<option value="active" <?= $selected_status === 'active' ? 'selected' : '' ?>>Ativo</option>
							<option value="inactive" <?= $selected_status === 'inactive' ? 'selected' : '' ?>>Inativo</option>
							<option value="archived" <?= $selected_status === 'archived' ? 'selected' : '' ?>>Arquivado</option>
						</select>
						<span class="edu-form-hint small" id="course-status-hint">
							<?php
								echo match ($selected_status) {
									'active' => 'Disponível para venda contínua e matrículas imediatas.',
									'inactive' => 'Inativo, temporariamente indisponível para novas matrículas.',
									'archived' => 'Vendas encerradas, acessível apenas para alunos já matriculados.',
									default => 'Invisível na vitrine, em fase de elaboração pedagógica.',
								};
							?>
						</span>
					</div>

					<div class="col-12 edu-form-field">
						<div class="form-check form-switch pt-1">
							<?php
								$sale_checked = false;
								if (!empty($course)) {
									$sale_checked = $course->is_available_for_sale();
								}
								if (set_value('submitted')) {
									$sale_checked = (bool) set_value('is_available_for_sale');
								}
							?>
							<input class="form-check-input"
							       type="checkbox"
							       role="switch"
							       id="is-available-for-sale"
							       name="is_available_for_sale"
							       value="1"
							       <?= $sale_checked ? 'checked' : '' ?>>
							<label class="form-check-label fw-semibold" for="is-available-for-sale">
								Disponibilizar para Venda Imediata no Catálogo
							</label>
							<span class="edu-form-hint d-block">
								Quando ativado, o curso é listado para novas aquisições e matrículas na vitrine pública. Requer status 'Ativo' e configuração comercial completa (para cursos pagos).
							</span>
						</div>
					</div>

					<div class="col-12 edu-form-field">
						<label class="edu-form-label">Imagem de Apresentação (Capa)</label>
						<div class="edu-card p-3 bg-edu-surface border-edu shadow-none">
							<div class="d-flex align-items-center gap-3 mb-2">
								<?php
									$is_url_selected = (!empty($course) && !empty($course->get_image_url()) && empty($course->get_image())) 
										|| (set_value('image_type') === 'url');
								?>
								<div class="form-check form-check-inline mb-0">
									<input class="form-check-input" type="radio" name="image_type" id="img-type-upload" value="upload" <?= !$is_url_selected ? 'checked' : '' ?>>
									<label class="form-check-label fw-medium" for="img-type-upload">Upload de Imagem</label>
								</div>
								<div class="form-check form-check-inline mb-0">
									<input class="form-check-input" type="radio" name="image_type" id="img-type-url" value="url" <?= $is_url_selected ? 'checked' : '' ?>>
									<label class="form-check-label fw-medium" for="img-type-url">URL Externa</label>
								</div>
							</div>

							<div id="image-upload-box" class="<?= $is_url_selected ? 'd-none' : '' ?>">
								<div class="edu-file-picker">
									<input type="file"
									       class="edu-file-picker-input"
									       id="course-image-file"
									       name="image_file"
									       accept="image/png,image/jpeg,image/webp,image/jpg">
									<button type="button" class="edu-file-picker-btn" id="btn-browse-file">
										<i class="bi bi-cloud-arrow-up" aria-hidden="true"></i> Selecionar Arquivo
									</button>
									<span class="edu-file-picker-label" id="course-image-file-label">Nenhum arquivo selecionado</span>
								</div>
								<span class="edu-form-hint">Formatos suportados: PNG, JPG, JPEG ou WebP (tamanho máximo: 4MB). O arquivo será salvo como <code>image.[ext]</code>.</span>
							</div>

							<div id="image-url-box" class="<?= !$is_url_selected ? 'd-none' : '' ?>">
								<input type="url"
								       class="form-control edu-form-control"
								       id="course-image-url"
								       name="image_url"
								       value="<?= html_escape(set_value('image_url', !empty($course) ? $course->get_image_url() : '')) ?>"
								       placeholder="https://exemplo.com/imagem-do-curso.jpg">
								<span class="edu-form-hint">Informe a URL pública direta da imagem compartilhada.</span>
							</div>

							<!-- Image Preview Box -->
							<?php 
								$current_display_image = !empty($course) ? $course->get_display_image() : null; 
							?>
							<div id="course-image-preview-wrapper" class="mt-3 pt-3 border-top border-edu <?= empty($current_display_image) ? 'd-none' : '' ?>">
								<div class="d-flex align-items-center gap-3">
									<div class="edu-image-preview-thumbnail border border-edu rounded overflow-hidden bg-edu-surface-alt d-flex align-items-center justify-content-center" style="width: 140px; height: 80px; flex-shrink: 0;">
										<img id="course-image-preview" 
										     src="<?= html_escape($current_display_image ?? '') ?>" 
										     alt="Pré-visualização da imagem do curso" 
										     class="w-100 h-100 object-fit-cover <?= empty($current_display_image) ? 'd-none' : '' ?>">
										<span id="course-image-preview-placeholder" class="text-muted small <?= !empty($current_display_image) ? 'd-none' : '' ?>">
											<i class="bi bi-image fs-4"></i>
										</span>
									</div>
									<div class="flex-grow-1">
										<div class="fw-semibold text-edu-heading small mb-1">Pré-visualização da Imagem</div>
										<div class="text-muted small" id="course-image-preview-info">
											<?php if (!empty($course) && !empty($course->get_image())): ?>
												Arquivo local: <code><?= html_escape($course->get_image()) ?></code>
											<?php elseif (!empty($course) && !empty($course->get_image_url())): ?>
												URL externa: <span class="text-truncate d-inline-block align-bottom" style="max-width: 250px;"><?= html_escape($course->get_image_url()) ?></span>
											<?php else: ?>
												Imagem selecionada
											<?php endif; ?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-12 edu-form-field">
						<label class="edu-form-label" for="course-short-description">Resumo para Vitrine</label>
						<textarea class="form-control edu-form-control"
						          id="course-short-description"
						          name="short_description"
						          rows="2"
						          maxlength="500"
						          placeholder="Breve chamada explicativa para os cards do catálogo..."><?= html_escape(set_value('short_description', !empty($course) ? $course->get_short_description() : '')) ?></textarea>
						<span class="edu-form-hint">Máximo de 500 caracteres.</span>
					</div>
				</div>
			</div>

			<!-- Section 2: Condições Comerciais e Precificação -->
			<div class="edu-form-section">
				<div class="edu-form-section-header">
					<div class="edu-form-section-icon">
						<i class="bi bi-tag" aria-hidden="true"></i>
					</div>
					<div>
						<h2 class="edu-form-section-title">Condições Comerciais e Precificação</h2>
						<p class="edu-form-section-desc">Defina o valor do curso, modelo de faturamento, opções de parcelamento e credenciamento financeiro.</p>
					</div>
				</div>

				<?php
					$is_free_checked = true;
					if (!empty($product)) {
						$is_free_checked = $product->is_free();
					}
					if (set_value('submitted')) {
						$is_free_checked = (bool) set_value('is_free');
					}

					$current_billing_model = 'one_time';
					if (!empty($product)) {
						$current_billing_model = (string) $product->get_billing_model();
					}
					$selected_billing_model = set_value('billing_model', $current_billing_model);

					$current_price = '';
					if (!empty($product) && !$product->is_free()) {
						$current_price = number_format($product->get_price()->get_cents() / 100, 2, ',', '.');
					}
					$price_value = set_value('price', $current_price);

					$current_installments = 1;
					if (!empty($product)) {
						$current_installments = $product->get_max_installments()->get_limit();
					}
					$selected_installments = (int) set_value('max_installments', $current_installments);

					$current_interval = 'monthly';
					if (!empty($product) && $product->get_subscription_interval() !== null) {
						$current_interval = (string) $product->get_subscription_interval();
					}
					$selected_interval = set_value('subscription_interval', $current_interval);

					$current_cycles = '';
					if (!empty($product) && $product->get_max_billing_cycles() !== null) {
						$current_cycles = (string) $product->get_max_billing_cycles();
					}
					$cycles_value = set_value('max_billing_cycles', $current_cycles);

					$enabled_methods = [];
					if (!empty($product)) {
						$enabled_methods = $product->get_payment_methods()->to_array();
					}
					if (set_value('submitted')) {
						$enabled_methods = (array) $this->input->post('payment_methods');
					}

					$current_config_id = !empty($product) ? $product->get_payment_configuration_id() : null;
					$selected_config_id = set_value('payment_configuration_id', $current_config_id);

					$current_cc_id = !empty($product) ? $product->get_cost_center_id() : null;
					$selected_cc_id = set_value('cost_center_id', $current_cc_id);
				?>

				<div class="row g-3">
					<div class="col-12 edu-form-field">
						<div class="form-check form-switch pt-1">
							<input class="form-check-input"
							       type="checkbox"
							       role="switch"
							       id="is-free"
							       name="is_free"
							       value="1"
							       <?= $is_free_checked ? 'checked' : '' ?>>
							<label class="form-check-label fw-semibold" for="is-free">
								Curso Gratuito (Sem Cobrança)
							</label>
							<span class="edu-form-hint d-block">
								Cursos gratuitos possuem valor R$ 0,00 e não exigem métodos de pagamento, gateways ou regras de parcelamento.
							</span>
						</div>
					</div>

					<div id="commercial-fields-wrapper" class="col-12 <?= $is_free_checked ? 'd-none' : '' ?>">
						<div class="row g-3 p-3 rounded-3 bg-edu-subtle border border-edu">
							<div class="col-md-4 edu-form-field">
								<label class="edu-form-label edu-label-required" for="course-price">Preço de Venda (R$)</label>
								<div class="input-group">
									<span class="input-group-text">R$</span>
									<input type="text"
									       class="form-control edu-form-control money-mask"
									       id="course-price"
									       name="price"
									       value="<?= html_escape($price_value) ?>"
									       placeholder="0,00"
									       data-initial-price="<?= html_escape($current_price) ?>">
								</div>
								<span class="edu-form-hint">Valor cobrado na finalização da matrícula.</span>
							</div>

							<div class="col-md-4 edu-form-field">
								<label class="edu-form-label edu-label-required" for="billing-model">Modelo de Cobrança</label>
								<select class="form-select edu-form-select" id="billing-model" name="billing_model">
									<option value="one_time" <?= $selected_billing_model === 'one_time' ? 'selected' : '' ?>>Pagamento Único (Avulso)</option>
									<option value="subscription" <?= $selected_billing_model === 'subscription' ? 'selected' : '' ?>>Assinatura Recorrente</option>
								</select>
								<span class="edu-form-hint">Transação única ou cobrança periódica automática.</span>
							</div>

							<!-- One Time: Installments -->
							<div class="col-md-4 edu-form-field <?= $selected_billing_model === 'subscription' ? 'd-none' : '' ?>" id="billing-one-time-fields">
								<label class="edu-form-label edu-label-required" for="max-installments">Parcelamento Máximo</label>
								<select class="form-select edu-form-select" id="max-installments" name="max_installments">
									<option value="1" <?= $selected_installments === 1 ? 'selected' : '' ?>>1x (Somente à vista)</option>
									<?php for ($inst = 2; $inst <= 24; $inst++): ?>
										<option value="<?= $inst ?>" <?= $selected_installments === $inst ? 'selected' : '' ?>><?= $inst ?>x no cartão</option>
									<?php endfor; ?>
								</select>
								<span class="edu-form-hint">Número máximo de parcelas permitidas no cartão.</span>
							</div>

							<!-- Subscription fields -->
							<div class="col-md-4 edu-form-field <?= $selected_billing_model !== 'subscription' ? 'd-none' : '' ?>" id="billing-subscription-interval-field">
								<label class="edu-form-label edu-label-required" for="subscription-interval">Intervalo de Cobrança</label>
								<select class="form-select edu-form-select" id="subscription-interval" name="subscription_interval">
									<option value="monthly" <?= $selected_interval === 'monthly' ? 'selected' : '' ?>>Mensal</option>
									<option value="quarterly" <?= $selected_interval === 'quarterly' ? 'selected' : '' ?>>Trimestral</option>
									<option value="semiannual" <?= $selected_interval === 'semiannual' ? 'selected' : '' ?>>Semestral</option>
									<option value="yearly" <?= $selected_interval === 'yearly' ? 'selected' : '' ?>>Anual</option>
								</select>
								<span class="edu-form-hint">Frequência da cobrança recorrente.</span>
							</div>

							<div class="col-md-4 edu-form-field <?= $selected_billing_model !== 'subscription' ? 'd-none' : '' ?>" id="billing-subscription-cycles-field">
								<label class="edu-form-label" for="max-billing-cycles">Limite de Ciclos (Opcional)</label>
								<input type="number"
								       class="form-control edu-form-control"
								       id="max-billing-cycles"
								       name="max_billing_cycles"
								       min="1"
								       step="1"
								       value="<?= html_escape($cycles_value) ?>"
								       placeholder="Ex: 12 (Em branco para indeterminado)">
								<span class="edu-form-hint">Deixe em branco para assinatura por tempo indeterminado.</span>
							</div>

							<!-- Payment Methods -->
							<div class="col-12 edu-form-field">
								<label class="edu-form-label edu-label-required">Métodos de Pagamento Permitidos</label>
								<div class="d-flex flex-wrap gap-4 pt-1">
									<div class="form-check">
										<input class="form-check-input"
										       type="checkbox"
										       name="payment_methods[]"
										       id="method-credit-card"
										       value="credit_card"
										       <?= in_array('credit_card', $enabled_methods, true) ? 'checked' : '' ?>>
										<label class="form-check-label fw-medium" for="method-credit-card">
											<i class="bi bi-credit-card-2-front me-1" aria-hidden="true"></i> Cartão de Crédito
										</label>
									</div>
									<div class="form-check">
										<input class="form-check-input"
										       type="checkbox"
										       name="payment_methods[]"
										       id="method-pix"
										       value="pix"
										       <?= in_array('pix', $enabled_methods, true) ? 'checked' : '' ?>>
										<label class="form-check-label fw-medium" for="method-pix">
											<i class="bi bi-qr-code-scan me-1" aria-hidden="true"></i> PIX
										</label>
									</div>
									<div class="form-check">
										<input class="form-check-input"
										       type="checkbox"
										       name="payment_methods[]"
										       id="method-boleto"
										       value="boleto"
										       <?= in_array('boleto', $enabled_methods, true) ? 'checked' : '' ?>>
										<label class="form-check-label fw-medium" for="method-boleto">
											<i class="bi bi-upc-scan me-1" aria-hidden="true"></i> Boleto Bancário
										</label>
									</div>
								</div>
								<span class="edu-form-hint">Selecione ao menos um método para produtos comercializados pagos.</span>
							</div>

							<!-- Financial Integration -->
							<div class="col-md-6 edu-form-field">
								<label class="edu-form-label edu-label-required" for="payment-configuration-id">Perfil Financeiro (Gateway)</label>
								<select class="form-select edu-form-select" id="payment-configuration-id" name="payment_configuration_id">
									<option value="">Selecione uma configuração de pagamento...</option>
									<?php if (!empty($payment_configurations)): ?>
										<?php foreach ($payment_configurations as $p_cfg): ?>
											<option value="<?= $p_cfg->get_id() ?>" <?= (string)$selected_config_id === (string)$p_cfg->get_id() ? 'selected' : '' ?>>
												<?= html_escape($p_cfg->get_name()) ?>
											</option>
										<?php endforeach; ?>
									<?php endif; ?>
								</select>
								<span class="edu-form-hint">Define a adquirente e subconta recebedora das transações.</span>
							</div>

							<div class="col-md-6 edu-form-field">
								<label class="edu-form-label" for="cost-center-id">Centro de Custo Gerencial</label>
								<select class="form-select edu-form-select" id="cost-center-id" name="cost_center_id">
									<option value="">Nenhum centro de custo associado</option>
									<?php if (!empty($cost_centers)): ?>
										<?php foreach ($cost_centers as $cc_item): ?>
											<option value="<?= $cc_item->get_id() ?>" <?= (string)$selected_cc_id === (string)$cc_item->get_id() ? 'selected' : '' ?>>
												<?= html_escape($cc_item->get_name()) ?>
											</option>
										<?php endforeach; ?>
									<?php endif; ?>
								</select>
								<span class="edu-form-hint">Opcional para agrupamento contábil e orçamentário.</span>
							</div>

							<!-- Change Reason (Only in Edit mode) -->
							<?php if (!empty($course)): ?>
								<div class="col-12 edu-form-field d-none" id="change-reason-container">
									<label class="edu-form-label" for="change-reason">Justificativa da Alteração de Preço</label>
									<input type="text"
									       class="form-control edu-form-control"
									       id="change-reason"
									       name="change_reason"
									       maxlength="255"
									       placeholder="Ex: Reajuste contratual, promoção especial de lançamento, etc.">
									<span class="edu-form-hint">Registrado no histórico de versões caso o valor seja alterado.</span>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>

			<!-- Section 3: Regras de Acesso e Certificação -->
			<div class="edu-form-section">
				<div class="edu-form-section-header">
					<div class="edu-form-section-icon">
						<i class="bi bi-clock-history" aria-hidden="true"></i>
					</div>
					<div>
						<h2 class="edu-form-section-title">Regras de Acesso e Certificação</h2>
						<p class="edu-form-section-desc">Defina a validade da matrícula para o aluno e os parâmetros de emissão do certificado.</p>
					</div>
				</div>

				<div class="row g-3">
					<div class="col-md-6 edu-form-field">
						<label class="edu-form-label edu-label-required" for="access-period-type">Tipo de Período de Acesso</label>
						<?php 
							$current_access_type = set_value(
								'access_period_type', 
								!empty($course) ? $course->get_access_period()->get_type() : 'limited_time'
							); 
						?>
						<select class="form-select edu-form-select" id="access-period-type" name="access_period_type" required>
							<option value="limited_time" <?= $current_access_type === 'limited_time' ? 'selected' : '' ?>>
								Prazo Determinado
							</option>
							<option value="lifetime" <?= $current_access_type === 'lifetime' ? 'selected' : '' ?>>
								Vitalício
							</option>
						</select>
						<span class="edu-form-hint">Regra de validade padrão aplicada no momento da matrícula do aluno.</span>
					</div>

					<div class="col-md-6 edu-form-field" id="access-days-container">
						<label class="edu-form-label edu-label-required" for="access-days">Dias de Acesso</label>
						<?php 
							$current_access_days = set_value(
								'access_days', 
								(!empty($course) && $course->get_access_period()->get_days() !== null) 
									? $course->get_access_period()->get_days() 
									: '365'
							); 
						?>
						<div class="input-group">
							<input type="number"
							       class="form-control edu-form-control"
							       id="access-days"
							       name="access_days"
							       min="1"
							       step="1"
							       value="<?= html_escape($current_access_days) ?>"
							       placeholder="365">
							<span class="input-group-text">dias</span>
						</div>
						<span class="edu-form-hint">Exemplos: 365 (1 ano), 180 (6 meses), 730 (2 anos).</span>
					</div>

					<div class="col-md-6 edu-form-field">
						<label class="edu-form-label edu-label-required" for="video-min-completion-percentage">Meta de Reprodução de Vídeos (%)</label>
						<?php
							$current_video_percentage = set_value(
								'video_min_completion_percentage',
								!empty($course) ? $course->get_video_min_completion_percentage() : 80
							);
						?>
						<div class="input-group">
							<input type="number"
							       class="form-control edu-form-control"
							       id="video-min-completion-percentage"
							       name="video_min_completion_percentage"
							       min="1"
							       max="100"
							       step="1"
							       value="<?= html_escape($current_video_percentage) ?>"
							       placeholder="80"
							       required>
							<span class="input-group-text">%</span>
						</div>
						<span class="edu-form-hint">Percentual mínimo assistido para considerar um vídeo como concluído automaticamente.</span>
					</div>

					<div class="col-12 edu-form-field">
						<div class="form-check form-switch pt-2">
							<?php
								$cert_checked = !empty($course) ? $course->is_certificate_enabled() : true;
								if (set_value('submitted')) {
									$cert_checked = (bool) set_value('certificate_enabled');
								}
							?>
							<input type="hidden" name="submitted" value="1">
							<input class="form-check-input"
							       type="checkbox"
							       role="switch"
							       id="certificate-enabled"
							       name="certificate_enabled"
							       value="1"
							       <?= $cert_checked ? 'checked' : '' ?>>
							<label class="form-check-label fw-semibold" for="certificate-enabled">
								Emitir certificado de conclusão automaticamente após a conclusão das aulas
							</label>
						</div>
					</div>
				</div>
			</div>

			<!-- Section 4: Dados Pedagógicos -->
			<div class="edu-form-section">
				<div class="edu-form-section-header">
					<div class="edu-form-section-icon">
						<i class="bi bi-mortarboard" aria-hidden="true"></i>
					</div>
					<div>
						<h2 class="edu-form-section-title">Dados Pedagógicos</h2>
						<p class="edu-form-section-desc">Carga horária para o certificado, ementa detalhada e pré-requisitos recomendados.</p>
					</div>
				</div>

				<div class="row g-3">
					<div class="col-md-4 edu-form-field">
						<label class="edu-form-label" for="course-workload">Carga Horária Estimada (Certificado)</label>
						<div class="input-group">
							<input type="number"
							       class="form-control edu-form-control"
							       id="course-workload"
							       name="workload_in_hours"
							       min="0"
							       step="1"
							       value="<?= html_escape(set_value('workload_in_hours', !empty($course) ? $course->get_workload_in_hours() : '')) ?>"
							       placeholder="Ex: 40">
							<span class="input-group-text">horas</span>
						</div>
						<span class="edu-form-hint">Carga horária oficial impressa no certificado.</span>
					</div>

					<div class="col-md-8 edu-form-field">
						<label class="edu-form-label" for="target-audience-tag-input">Público-Alvo (Perfis e Personas)</label>
						<div class="edu-tag-input-container" id="target-audience-tag-container">
							<div class="edu-tag-badges" id="target-audience-badges"></div>
							<input type="text"
							       class="edu-tag-field"
							       id="target-audience-tag-input"
							       placeholder="Digite um perfil e pressione Enter ou vírgula...">
							<input type="hidden"
							       id="course-target-audience"
							       name="target_audience"
							       value="<?= html_escape(set_value('target_audience', !empty($course) ? $course->get_target_audience() : '')) ?>">
						</div>
						<span class="edu-form-hint">Adicione personas ou perfis recomendados (Ex: Desenvolvedores Backend, Estudantes de TI).</span>
					</div>

					<div class="col-12 edu-form-field">
						<label class="edu-form-label" for="course-description">Ementa e Detalhes do Curso</label>
						<textarea class="form-control edu-form-control"
						          id="course-description"
						          name="description"
						          data-rich-editor="true"
						          rows="6"
						          placeholder="Apresentação detalhada da disciplina, metodologia e estrutura programática..."><?= set_value('description', !empty($course) ? $course->get_description() : '') ?></textarea>
						<span class="edu-form-hint">Utilize a barra de ferramentas para formatar o texto com títulos, negrito, listas e citações.</span>
					</div>

					<div class="col-12 edu-form-field">
						<label class="edu-form-label" for="course-requirements">Pré-requisitos Recomendados</label>
						<textarea class="form-control edu-form-control"
						          id="course-requirements"
						          name="requirements"
						          data-rich-editor="true"
						          rows="4"
						          placeholder="Conhecimentos prévios, ferramentas necessárias ou perfil acadêmico requerido..."><?= set_value('requirements', !empty($course) ? $course->get_requirements() : '') ?></textarea>
						<span class="edu-form-hint">Formate com marcadores os requisitos técnicos, experiências prévias ou ferramentas recomendadas.</span>
					</div>
				</div>
			</div>

			<!-- Action Buttons -->
			<div class="edu-form-actions">
				<a href="<?= base_url('admin/cursos') ?>" class="edu-btn edu-btn-secondary">Cancelar</a>
				<button type="submit" class="edu-btn edu-btn-primary" id="btn-submit">
					<i class="bi bi-check-lg me-1" aria-hidden="true"></i> Salvar Curso
				</button>
			</div>
		</form>
	</div>
</div>
